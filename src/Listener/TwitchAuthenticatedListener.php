<?php

declare(strict_types = 1);

namespace App\Listener;

use App\Interface\TwitchAuthenticatedInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class TwitchAuthenticatedListener implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private RouterInterface $router
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (!$controller instanceof TwitchAuthenticatedInterface) {
            return;
        }
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        //var_dump($user); die();
        if ($user !== null && $user->isTwitchAuthorized() === true) {
            return;
        }

        $session = new Session();
        $session->getFlashBag()->add('error', 'twitch_reauth_required');

        $uri = $this->router->generate('app_logout');
        $response = new RedirectResponse($uri);
        $response->send();

    }
}
