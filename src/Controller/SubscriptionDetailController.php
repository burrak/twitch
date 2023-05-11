<?php

declare(strict_types = 1);

namespace App\Controller;

use App\EshopBundle\Component\AddToCartForm;
use App\EshopBundle\Exception\CartColissionException;
use App\EshopBundle\Facade\CartFacade;
use App\Exception\NotSubscribedException;
use App\Interface\TwitchAuthenticatedInterface;
use App\Repository\EshopConfigRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriberRepository;
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

class SubscriptionDetailController implements TwitchAuthenticatedInterface
{
    public function __construct(
        private Environment $twig,
        private Security $security,
        private SubscriberRepository $subscriberRepository,
        private EshopConfigRepository $eshopConfigRepository,
        private UserRepository $userRepository,
        private RouterInterface $router,
        private CartFacade $cartFacade,
        private FormFactoryInterface $formFactory,
        private ProductRepository $productRepository
    )
    {
    }

    #[Route('/{_locale}/{channelName}/subscription/detail', name: 'subscription_detail', requirements: ['_locale' => 'en|cs'])]
    #[IsGranted('ROLE_USER')]
    public function index(string $channelName, Request $request): Response
    {
        $streamer = $this->userRepository->findOneBy(['userName' => $channelName]);
        if ($streamer === null) {
            throw new NotFoundHttpException();
        }
        $eshopConfig = $this->eshopConfigRepository->findOneBy(['streamer' => $streamer]);
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();

        $subscriber = $this->subscriberRepository->findOneBy(['streamer' => $streamer, 'twitchId' => $user->getUserId()]);
        if ($subscriber === null && $user !== $streamer) {
            throw new NotSubscribedException();
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

                $uri = $this->router->generate('subscription_detail', ['channelName' => $channelName]);
                return new RedirectResponse($uri);
            } catch (CartColissionException $exception) {
                $session = new Session();
                $session->getFlashBag()->add('error', 'error_cart_multiple_streamer');

                $uri = $this->router->generate('subscription_detail', ['channelName' => $channelName]);
                return new RedirectResponse($uri);
            }

            $uri = $this->router->generate('subscription_detail', ['channelName' => $channelName]);
            return new RedirectResponse($uri);
        }

        return new Response($this->twig->render('pages/subscription.detail.html.twig', ['eshopConfig' => $eshopConfig, 'subscriber' => $subscriber, 'products'=> $this->productRepository->getActiveProductsByStreamer($streamer) ,'menuEnabled' => true, 'addToCartForm' => $addToCartForm, 'user' => $user, 'streamer' => $streamer]));
    }
}
