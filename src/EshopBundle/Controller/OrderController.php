<?php

declare(strict_types = 1);

namespace App\EshopBundle\Controller;

use App\Controller\ControllerTrait;
use App\EshopBundle\Model\Grid\OrderGrid;
use App\Interface\TwitchAuthenticatedInterface;
use App\Repository\EshopConfigRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class OrderController implements TwitchAuthenticatedInterface
{
    use ControllerTrait;

    public function __construct(
        private Security $security,
        private Environment $twig,
        private UserRepository $userRepository,
        private EshopConfigRepository $eshopConfigRepository,
        private OrderGrid $orderGrid
    )
    {
    }

    #[Route('/{_locale}/{channelName}/eshop/order', name: 'order_list')]
    public function order(string $channelName, Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_' . $channelName)) {
            throw new AccessDeniedException();
        }
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        $streamer = $this->userRepository->findOneBy(['userName' => $channelName]);
        $orders = $user->getStreamerOrders();
        $orderGrid = $this->createGridDb($request->query);
        $items = $this->orderGrid->getResult($orderGrid->getFilters(), $orderGrid->getSorters(), $orderGrid->getPager());
        $eshopConfig = $this->eshopConfigRepository->findOneBy(['streamer' => $streamer]);


        return new Response(
            $this->twig->render(
                '@Eshop/admin.order.html.twig',
                [
                    'orders' => $orders,
                    'menuEnabled' => true,
                    'items' => $items,
                    'itemsPerPage' => $orderGrid->getPager()->getItemsPerPage(),
                    'eshopConfig' => $eshopConfig,
                ]
            )
        );
    }
}
