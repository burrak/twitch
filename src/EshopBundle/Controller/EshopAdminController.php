<?php

declare(strict_types = 1);

namespace App\EshopBundle\Controller;

use App\Controller\ControllerTrait;
use App\Entity\EshopConfig;
use App\EshopBundle\Component\AddProductForm;
use App\EshopBundle\Component\EshopConfigForm;
use App\EshopBundle\DTO\EshopConfig as EshopConfigDTO;
use App\EshopBundle\DTO\NewProduct;
use App\EshopBundle\Facade\ProductFacade;
use App\EshopBundle\Model\Grid\ProductGrid;
use App\Interface\TwitchAuthenticatedInterface;
use App\Repository\EshopConfigRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class EshopAdminController implements TwitchAuthenticatedInterface
{
    use ControllerTrait;

    public function __construct(
        private Environment $twig,
        private Security $security,
        private ProductRepository $productRepository,
        private EshopConfigRepository $eshopConfigRepository,
        private UserRepository $userRepository,
        private RouterInterface $router,
        private ProductFacade $productFacade,
        private FormFactoryInterface $formFactory,
        private ProductGrid $productGrid
    )
    {
    }

    #[Route('/{_locale}/{channelName}/channel/eshop', name: 'eshop_admin')]
    #[IsGranted('ROLE_STREAMER')]
    public function index(string $channelName, Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_' . $channelName)) {
            throw new AccessDeniedException();
        }
        $user = $this->security->getUser();
        $streamer = $this->userRepository->findOneBy(['userName' => $channelName]);
        $products = $this->productRepository->findBy(['streamer' => $user]);
        $eshopConfig = $this->eshopConfigRepository->findOneBy(['streamer' => $user]);

        $productGrid = $this->createGridDb($request->query);
        $items = $this->productGrid->getResult($productGrid->getFilters(), $productGrid->getSorters(), $productGrid->getPager());

        return new Response(
            $this->twig->render(
                '@Eshop/admin.index.html.twig',
                [
                    'eshopConfig' => $eshopConfig,
                    'products' => $products,
                    'menuEnabled' => true,
                    'items' => $items,
                    'itemsPerPage' => $productGrid->getPager()->getItemsPerPage(),
                ]
            )
        );
    }

    #[Route('/{_locale}/{channelName}/channel/eshop/config', name: 'eshop_admin_config')]
    #[IsGranted('ROLE_STREAMER')]
    public function eshopConfig(string $channelName, Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_' . $channelName)) {
            throw new AccessDeniedException();
        }
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        $streamer = $this->userRepository->findOneBy(['userName' => $channelName]);

        /** @var EshopConfig $config */
        $config = $this->eshopConfigRepository->findOneBy(['streamer' => $user]);
        $configForm = $this->formFactory->create(EshopConfigForm::class, new EshopConfigDTO($config->getCurrency(), $config->getDeliveryPrice()));

        $configForm->handleRequest($request);
        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $data = $configForm->getData();
            $config = new EshopConfig($config->getId(), $user, $configForm->getData()->getCurrency(), (int) ($configForm->getData()->getDeliveryPrice() * 100));
            $this->eshopConfigRepository->save($config, true);

            $uri = $this->router->generate('eshop_admin_config');
            return new RedirectResponse($uri);
        }

        return new Response($this->twig->render('@Eshop/admin.eshop.config.html.twig', ['configForm' => $configForm->createView(), 'config' => $config, 'menuEnabled' => true]));
    }

    #[Route('/{_locale}/{channelName}/channel/eshop/product/add', name: 'eshop_product_add')]
    #[IsGranted('ROLE_STREAMER')]
    public function addProduct(string $channelName, Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_' . $channelName)) {
            throw new AccessDeniedException();
        }
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        $streamer = $this->userRepository->findOneBy(['userName' => $channelName]);
        $products = $this->productRepository->findBy(['streamer' => $user]);

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();
        $addProductForm = $formFactory->create(AddProductForm::class);
        $addProductForm->handleRequest($request);
        if ($addProductForm->isSubmitted() && $addProductForm->isValid()) {
            $this->productFacade->create($addProductForm, $user);

            $uri = $this->router->generate('eshop_admin');
            return new RedirectResponse($uri);
        }

        return new Response($this->twig->render('@Eshop/admin.addProduct.html.twig', ['addForm' => $addProductForm->createView(), 'menuEnabled' => true]));
    }

    #[Route('/{_locale}/{channelName}/channel/eshop/product/edit/{id}', name: 'eshop_product_edit')]
    #[IsGranted('ROLE_STREAMER')]
    public function editProduct(string $id, string $channelName, Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_' . $channelName)) {
            throw new AccessDeniedException();
        }
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        $streamer = $this->userRepository->findOneBy(['userName' => $channelName]);
        $products = $this->productRepository->findBy(['streamer' => $user]);
        $productToEdit = $this->productRepository->findOneBy(['id' => base64_decode($id), 'streamer' => $user]);
        if ($productToEdit === null) {
            return new Response('Product not found', Response::HTTP_NOT_FOUND);
        }
        $productEditDto = new NewProduct(
            $productToEdit->getTitle(),
            ($productToEdit->getPrice()/100),
            ($productToEdit->getPriceVat()/100),
            $productToEdit->getVat(),
            $productToEdit->getDescription(),
            $productToEdit->isSubscriber(),
            $productToEdit->getCumulativeMonths(),
            $productToEdit->getCurrentStreak(),
            $productToEdit->getMaxStreak(),
            $productToEdit->getGiftedTotal(),
            $productToEdit->getTier(),
            $productToEdit->getOrderLimit(),
            $productToEdit->getTotalLimit(),
            $productToEdit->getDateFrom(),
            $productToEdit->getDateTo(),
            $productToEdit->isActive()
        );

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();
        $addProductForm = $formFactory->create(AddProductForm::class, $productEditDto);
        $addProductForm->handleRequest($request);
        if ($addProductForm->isSubmitted() && $addProductForm->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $this->productFacade->create($addProductForm, $user, $productToEdit->getId());
            /*$file = $addProductForm->get('images')->getData();
            if ($file !== null) {
                $fileName = $this->fileUploader->upload($file);
                $message = new ImageMessage($productToEdit->getId(), $fileName);
                $this->bus->dispatch($message);
            }


            /** @var NewProduct $task */
            /*$task = $addProductForm->getData();
            $product = new Product(
                $productToEdit->getId(),
                $user,
                $task->getTitle(),
                (int) ($task->getPrice() * 100),
                (int) ($task->getPriceVat() * 100),
                $task->getVat(),
                $task->getDescription(),
                $task->isSubscriber(),
                $task->getCumulativeMonths(),
                $task->getCurrentStreak(),
                $task->getMaxStreak(),
                $task->getGiftedTotal(),
                $task->getTier(),
                $task->getOrderLimit()
            );
            $this->entityManager->merge($product);
            $this->entityManager->flush();*/

            $uri = $this->router->generate('eshop_admin', ['channelName' => $channelName]);
            return new RedirectResponse($uri);
        }

        return new Response($this->twig->render('@Eshop/admin.addProduct.html.twig', ['addForm' => $addProductForm->createView(), 'product' => $productToEdit, 'menuEnabled' => true]));
    }
}
