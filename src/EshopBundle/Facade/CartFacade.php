<?php

declare(strict_types = 1);

namespace App\EshopBundle\Facade;

use App\Entity\Cart;
use App\Entity\User;
use App\EshopBundle\DTO\AddToCart;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartFacade
{
    public function __construct(
        private CartRepository $cartRepository,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @throws \App\EshopBundle\Exception\CartColissionException
     * @throws NotFoundHttpException
     */
    public function create(AddToCart $data, User $user): void
    {
        $activeCart = $this->cartRepository->findBy(['user' => $user]);
        $product = $this->productRepository->findOneBy(['id' => $data->getProductId()]);
        if ($product === null) {
            throw new NotFoundHttpException();
        }
        /*foreach ($activeCart as $row) {
            if ($row->getProduct()->getStreamer() !== $product->getStreamer()) {
                throw new CartColissionException();
            }
        }*/

        $oldCart = $this->cartRepository->findOneBy(['user' => $user, 'product' => $product]);
        if ($data->getQuantity() === 0 && $oldCart !== null) {
            $this->entityManager->remove($oldCart);
            $this->entityManager->flush();
            return;
        }
        $cart = new Cart($oldCart?->getId(), $user, $product, $product->getStreamer(), $data->getQuantity());
        /** @phpstan-ignore-next-line */
        $this->entityManager->merge($cart);
        $this->entityManager->flush();
    }

}
