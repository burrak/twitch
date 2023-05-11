<?php

declare(strict_types = 1);

namespace App\EshopBundle\Controller;

use App\Controller\ControllerTrait;
use App\EshopBundle\Component\AddToCartForm;
use App\EshopBundle\Exception\CartColissionException;
use App\EshopBundle\Facade\CartFacade;
use App\EshopBundle\Model\Grid\ProductGrid;
use App\Interface\TwitchAuthenticatedInterface;
use App\Repository\EshopConfigRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriberRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class ProductController implements TwitchAuthenticatedInterface
{
    use ControllerTrait;

    public function __construct(
        private ProductRepository $productRepository,
        private SubscriberRepository $subscriberRepository,
        private EshopConfigRepository $eshopConfigRepository,
        private Security $security,
        private Environment $twig,
        private FormFactoryInterface $formBuilder,
        private CartFacade $cartFacade,
        private RouterInterface $router,
        private ProductGrid $productGrid
    )
    {
    }

    #[Route('/{_locale}/{channelName}/product/detail/{productId}', name: 'product_detail')]
    #[IsGranted('ROLE_USER')]
    public function productDetail(string $channelName, string $productId, Request $request): Response
    {
        $productId = base64_decode($productId);
        $product = $this->productRepository->find($productId);
        if ($product === null) {
            return new Response('Product not found', Response::HTTP_NOT_FOUND);
        }
        $eshopConfig = $this->eshopConfigRepository->findOneBy(['streamer' => $product->getStreamer()]);
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        /** @var \App\Entity\Subscriber|null $subscriber */
        $subscriber = $this->subscriberRepository->findOneBy(['twitchId' => $user->getUserId(), 'streamer' => $product->getStreamer()]);

        if ($subscriber === null) {
            return new Response($this->twig->render('@Eshop/notSubscribed.html.twig', ['streamer' => $product->getStreamer(), 'menuEnabled' => true]));
        }

        $cart = $user->getCarts()->filter(function ($element) use ($product) {
            return $element->getProduct() === $product;
        });
        $cartQuantity = $cart->first() ? $cart->first()->getQuantity() : 0;
        $addToCartForm = $this->formBuilder->create(AddToCartForm::class);
        $addToCartForm->handleRequest($request);
        if ($addToCartForm->isSubmitted() && $addToCartForm->isValid()) {
            try
            {
                $this->cartFacade->create($addToCartForm->getData(), $user);
            } catch (NotFoundHttpException $exception) {
                $session = new Session();
                $session->getFlashBag()->add('error', 'error_product_not_found');

                $uri = $this->router->generate('product_detail', ['productId' => base64_encode($productId)]);
                return new RedirectResponse($uri);
            } catch (CartColissionException $exception) {
                $session = new Session();
                $session->getFlashBag()->add('error', 'error_cart_multiple_streamer');

                $uri = $this->router->generate('product_detail', ['productId' => base64_encode($productId)]);
                return new RedirectResponse($uri);
            }

            $uri = $this->router->generate('product_detail', ['productId' => base64_encode($productId)]);
            return new RedirectResponse($uri);
        }

        $productGrid = $this->createGridDb($request->query);
        $items = $this->productGrid->getResult($productGrid->getFilters(), $productGrid->getSorters(), $productGrid->getPager());

        return new Response(
            $this->twig->render(
                '@Eshop/product.detail.html.twig',
                [
                    'eshopConfig' => $eshopConfig,
                    'product' => $product,
                    'form' => $addToCartForm->createView(),
                    'quantity' => $cartQuantity,
                    'menuEnabled' => true,
                    'items' => $items,
                    'itemsPerPage' => $productGrid->getPager()->getItemsPerPage(),
                ]
            )
        );
    }

}
