<?php

declare(strict_types = 1);

namespace App\EshopBundle\Component;

use App\EshopBundle\DTO\AddToCart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddToCartForm extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('quantity', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('product_id', HiddenType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'form.add_to_cart.submit',
            ])
            ->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddToCart::class,
            'empty_data' => null,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'cart_item',
        ]);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        $forms = iterator_to_array($forms);
        $forms['quantity']->setData($viewData ? $viewData->getQuantity() : 0);
        $forms['product_id']->setData($viewData ? $viewData->getProductId() : '');
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);
        $viewData = new AddToCart(
            $forms['quantity']->getData(),
            $forms['product_id']->getData()
        );
    }
}
