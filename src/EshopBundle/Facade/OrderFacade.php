<?php

declare(strict_types = 1);

namespace App\EshopBundle\Facade;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\EshopBundle\DTO\Address;
use App\Repository\CartRepository;
use App\Repository\EshopConfigRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderFacade
{
    public function __construct(
        private CartRepository $cartRepository,
        private OrderRepository $orderRepository,
        private EshopConfigRepository $eshopConfigRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function create(Address $address, User $user, User $streamer): void
    {
        $cart = $this->cartRepository->findBy(['user' => $user]);
        /** @var \App\Entity\EshopConfig $eshopConfig */
        $eshopConfig = $this->eshopConfigRepository->findOneBy(['streamer' => $streamer]);
        $order = new Order(
            null,
            null,
            'created',
            $address->getFirstName(),
            $address->getSurname(),
            $address->getStreet(),
            $address->getCity(),
            $address->getZip(),
            $address->getCountry(),
            $address->getNote(),
            $eshopConfig->getDeliveryPrice(),
            $user,
            $streamer,
            $eshopConfig->getCurrency(),
            null
        );
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        /** @var Order $order */
        $order = $this->orderRepository->find($order);
        foreach ($cart as $item) {
            $orderItem = new OrderItem(
                null,
                $item->getProduct(),
                $item->getQuantity(),
                $order
            );
            $this->entityManager->persist($orderItem);
            $this->entityManager->flush();
        }

        $this->cartRepository->removeByUserAndStreamer($user, $streamer);
    }

}
