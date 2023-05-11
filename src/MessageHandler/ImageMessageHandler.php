<?php

declare(strict_types = 1);

namespace App\MessageHandler;

use App\Entity\ProductImage;
use App\EshopBundle\Message\ImageMessage;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Google\Cloud\Storage\StorageClient;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler]
class ImageMessageHandler implements MessageHandlerInterface
{
    private const BUCKET_NAME = 'twitch_image_storage';
    private const MAX_WIDTH = 900;
    private const MAX_HEIGHT = 600;
    private const THUBNAIL_WIDTH = 300;
    private const THUMBNAIL_HEIGHT = 200;

    private Imagine $imagine;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private string $rootPath,
        private string $googleKey
    )
    {
        $this->imagine = new Imagine();
    }

    public function __invoke(ImageMessage $imageMessage): void
    {
        $imagesDir = __DIR__ . '/../../public/uploads/images/';

        $storage = new StorageClient(['keyFilePath' => $this->rootPath . $this->googleKey]);


        $file = fopen($imagesDir . $imageMessage->getFile(), 'r');
        if ($file === false) {
            return;
        }
        $imageSize = getimagesize($imagesDir . $imageMessage->getFile());
        if ($imageSize === false) {
            throw new \InvalidArgumentException();
        }

        [$iwidth, $iheight] = $imageSize;
        $ratio = $iwidth / $iheight;
        $width = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $thumbnailWidth = self::THUBNAIL_WIDTH;
        $thumbnailHeight = self::THUMBNAIL_HEIGHT;
        if ($thumbnailWidth / $thumbnailHeight > $ratio) {
            $thumbnailWidth = $thumbnailHeight * $ratio;
        } else {
            $thumbnailHeight = $thumbnailWidth / $ratio;
        }

        $photo = $this->imagine->open($imagesDir . $imageMessage->getFile());
        $photo->resize(new Box((int) $width, (int) $height))->save();
        $thumbnail = $photo->thumbnail(new Box((int) $thumbnailWidth, (int) $thumbnailHeight));
        $thumbnail->save($imagesDir . 'thumbnail_' . $imageMessage->getFile());

        $file = fopen($imagesDir . $imageMessage->getFile(), 'r');
        if ($file === false) {
            throw new \InvalidArgumentException();
        }
        $bucket = $storage->bucket(self::BUCKET_NAME);
        $object = $bucket->upload($file, [
            'name' => $imageMessage->getFile(),
        ]);

        $fileThumbnail = fopen($imagesDir . 'thumbnail_' . $imageMessage->getFile(), 'r');
        if ($fileThumbnail === false) {
            throw new \InvalidArgumentException();
        }
        $bucket = $storage->bucket(self::BUCKET_NAME);
        $objectThumbnail = $bucket->upload($fileThumbnail, [
            'name' => 'thumbnail_' . $imageMessage->getFile(),
        ]);

        $thumbnailInfo = $objectThumbnail->info();
        $info = $object->info();
        $product = $this->productRepository->find($imageMessage->getProductId());
        if ($product === null) {
            return;
        }
        $productImage = new ProductImage($product->getProductImages()->first() ? $product->getProductImages()->first()->getId() : null, $product, $info['mediaLink'], $thumbnailInfo['mediaLink'], $info['name'], (int) $info['size']);
        /** @phpstan-ignore-next-line */
        $this->entityManager->merge($productImage);
        $this->entityManager->flush();
        unlink($imagesDir . $imageMessage->getFile());
        unlink($imagesDir . 'thumbnail_' . $imageMessage->getFile());
    }
}
