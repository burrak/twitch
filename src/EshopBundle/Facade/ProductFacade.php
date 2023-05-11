<?php

declare(strict_types = 1);

namespace App\EshopBundle\Facade;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Entity\User;
use App\EshopBundle\Message\ImageMessage;
use App\EshopBundle\Service\FileUploader;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class ProductFacade
{
    private const IMAGES_DIR = '/uploads/images';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private FileUploader $fileUploader,
        private MessageBusInterface $bus
    )
    {
    }

    public function create(FormInterface $form, User $user, ?Uuid $productToEdit = null): void
    {
        /** @var \App\EshopBundle\DTO\NewProduct $productForm */
        $productForm = $form->getData();
        $product = new Product(
            $productToEdit,
            $user,
            $productForm->getTitle(),
            (int) ($productForm->getPrice() * 100),
            (int) ($productForm->getPriceVat() * 100),
            $productForm->getVat(),
            $productForm->getDescription(),
            $productForm->isSubscriber(),
            $productForm->getCumulativeMonths(),
            $productForm->getCurrentStreak(),
            $productForm->getMaxStreak(),
            $productForm->getGiftedTotal(),
            $productForm->getTier(),
            $productForm->getOrderLimit(),
            $productForm->getTotalLimit(),
            $productForm->getDateFrom(),
            $productForm->getDateTo(),
            $productForm->isActive(),
            null
        );

        /** @phpstan-ignore-next-line */
        $this->entityManager->merge($product);
        $this->entityManager->flush();

        /** @var Product $product */
        $product = $this->productRepository->find($product);
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $file */
        $file = $form->get('images')->getData();

        if ($file === null || $file->getSize() === false || $file->getSize() === 0) {
            return;
        }

        $fileSize = $file->getSize();
        $fileName = $this->fileUploader->upload($file);
        $productImage = new ProductImage($product->getProductImages()->first() ? $product->getProductImages()->first()->getId() : null, $product, self::IMAGES_DIR . '/' . $fileName, self::IMAGES_DIR . '/' . $fileName, $file->getBasename(), (int) $fileSize);
        /** @phpstan-ignore-next-line */
        $this->entityManager->merge($productImage);
        $this->entityManager->flush();
        $message = new ImageMessage($product->getId(), $fileName);
        $this->bus->dispatch($message);
    }

}
