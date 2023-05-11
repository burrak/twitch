<?php

declare(strict_types = 1);

namespace App\EshopBundle\Controller;

use App\Entity\User;
use App\EshopBundle\Component\AddressForm;
use App\EshopBundle\Component\AddToCartForm;
use App\EshopBundle\Exception\CartColissionException;
use App\EshopBundle\Facade\CartFacade;
use App\EshopBundle\Facade\OrderFacade;
use App\Interface\TwitchAuthenticatedInterface;
use App\Repository\CartRepository;
use App\Repository\EshopConfigRepository;
use App\Repository\UserRepository;
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

class CartController implements TwitchAuthenticatedInterface
{
    public function __construct(
        private Security $security,
        private CartRepository $cartRepository,
        private EshopConfigRepository $eshopConfigRepository,
        private UserRepository $userRepository,
        private Environment $twig,
        private RouterInterface $router,
        private OrderFacade $orderFacade,
        private CartFacade $cartFacade,
        private FormFactoryInterface $formFactory
    )
    {
    }

    #[Route('/{_locale}/{channelName}/cart', name: 'cart')]
    #[IsGranted('ROLE_USER')]
    public function cart(string $channelName, Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        $streamer = $this->userRepository->findOneBy(['userName' => $channelName]);
        $cart = $this->cartRepository->findBy(['user' => $user, 'streamer' => $streamer]);
        $eshopConfig = null;
        if ($cart !== null) {
            $streamer = $this->userRepository->findOneBy(['userName' => $channelName]);
            $eshopConfig = $this->eshopConfigRepository->findOneBy(['streamer' => $streamer]);
        }


        $addToCartForm = $this->formFactory->create(AddToCartForm::class);
        $addToCartForm->handleRequest($request);
        if ($addToCartForm->isSubmitted() && $addToCartForm->isValid()) {
            try
            {
                $this->cartFacade->create($addToCartForm->getData(), $user);
            } catch (NotFoundHttpException $exception) {
                $session = new Session();
                $session->getFlashBag()->add('error', 'error_product_not_found');

                $uri = $this->router->generate('cart');
                return new RedirectResponse($uri);
            } catch (CartColissionException $exception) {
                $session = new Session();
                $session->getFlashBag()->add('error', 'error_cart_multiple_streamer');

                $uri = $this->router->generate('cart');
                return new RedirectResponse($uri);
            }

            $uri = $this->router->generate('cart');
            return new RedirectResponse($uri);
        }

        return new Response($this->twig->render('@Eshop/cart.html.twig', ['cart' => $cart, 'addToCartForm' => $addToCartForm, 'eshopConfig' => $eshopConfig, 'menuEnabled' => true]));
    }

    #[Route('/{_locale}/{channelName}/cart/address', name: 'cart_address')]
    #[IsGranted('ROLE_USER')]
    public function cartAddress(string $channelName, Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        $streamer = $this->userRepository->findOneBy(['userName' => $channelName]);

        $addressForm = $this->formFactory->create(AddressForm::class);
        $addressForm->handleRequest($request);
        if ($addressForm->isSubmitted() && $addressForm->isValid() && $user instanceof User && $streamer instanceof User) {
            $this->orderFacade->create($addressForm->getData(), $user, $streamer);
            $uri = $this->router->generate('cart');

            return new RedirectResponse($uri);
        }

        return new Response($this->twig->render('@Eshop/cartAddress.html.twig', ['addressForm' => $addressForm->createView(), 'menuEnabled' => true]));
    }
}
