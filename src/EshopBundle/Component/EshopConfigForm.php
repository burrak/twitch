<?php

declare(strict_types = 1);

namespace App\EshopBundle\Component;

use App\Entity\Currency;
use App\EshopBundle\DTO\EshopConfig;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EshopConfigForm extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('currency', EntityType::class, [
            'class' => Currency::class,
            'choice_label' => 'name',
        ])
            ->add('delivery_price', NumberType::class)
            ->add('submit', SubmitType::class)
            ->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EshopConfig::class,
            'empty_data' => null,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'eshop_config',
        ]);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        $forms = iterator_to_array($forms);
        $forms['currency']->setData($viewData->getCurrency());
        $forms['delivery_price']->setData(($viewData->getDeliveryPrice() / 100));
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);
        $viewData = new EshopConfig(
            $forms['currency']->getData(),
            $forms['delivery_price']->getData()
        );
    }
}
