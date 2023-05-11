<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Interface\TwitchAuthenticatedInterface;
use App\Repository\SubscriberRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class DashboardController implements TwitchAuthenticatedInterface
{
    public function __construct(
        private Environment $twig,
        private Security $security,
        private SubscriberRepository $subscriberRepository,
    )
    {
    }

    #[Route('/{_locale}/dashboard', name: 'dashboard', requirements: ['_locale' => 'en|cs'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        $mySubs = $this->subscriberRepository->findBy(['twitchId' => $user->getUserId()]);
        return new Response($this->twig->render('pages/dashboard.html.twig', ['mySubs' => $mySubs, 'menuEnabled' => true]));

    }
}
