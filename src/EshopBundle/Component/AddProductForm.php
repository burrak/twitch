<?php

declare(strict_types = 1);

namespace App\EshopBundle\Component;

use App\EshopBundle\DTO\NewProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddProductForm extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class, [
            'required' => true,
        ])
            ->add('price', NumberType::class, [
                'required' => true,
            ])
            ->add('vat', IntegerType::class, [
                'required' => true,
            ])
            ->add('price_vat', NumberType::class, [
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('subscriber', CheckboxType::class, [
                'required' => false,
            ])
            ->add('cumulative_months', IntegerType::class, [
                'required' => false,
            ])
            ->add('current_streak', IntegerType::class, [
                'required' => false,
            ])
            ->add('max_streak', IntegerType::class, [
                'required' => false,
            ])
            ->add('gifted_total', IntegerType::class, [
                'required' => false,
            ])
            ->add('tier', ChoiceType::class, [
                'required' => false,
                'placeholder' => '-',
                'choices' => [
                    1 => 1000,
                    2 => 2000,
                    3 => 3000,
                ],
            ])
            ->add('order_limit', IntegerType::class, [
                'required' => false,
            ])
            ->add('total_limit', IntegerType::class, [
                'required' => false,
            ])
            ->add('date_from', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'linkedPickers1',
                    'data-td-target' => '#linkedPickers1',
                ],
            ])
            ->add('date_to', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'linkedPickers2',
                    'data-td-target' => '#linkedPickers2',
                ],
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
            ])
            ->add('images', FileType::class, [
                'multiple' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
        ->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewProduct::class,
            'empty_data' => null,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ]);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        $forms = iterator_to_array($forms);
        $forms['title']->setData($viewData ? $viewData->getTitle() : '');
        $forms['price']->setData($viewData ? $viewData->getPrice() : null);
        $forms['price_vat']->setData($viewData ? $viewData->getPriceVat() : null);
        $forms['vat']->setData($viewData ? $viewData->getVat() : null);
        $forms['description']->setData($viewData ? $viewData->getDescription() : "");
        $forms['cumulative_months']->setData($viewData ? $viewData->getCumulativeMonths() : 0);
        $forms['current_streak']->setData($viewData ? $viewData->getCurrentStreak() : 0);
        $forms['max_streak']->setData($viewData ? $viewData->getMaxStreak() : 0);
        $forms['gifted_total']->setData($viewData ? $viewData->getGiftedTotal() : 0);
        $forms['tier']->setData($viewData ? $viewData->getTier() : 0);
        $forms['order_limit']->setData($viewData ? $viewData->getOrderLimit() : 0);
        $forms['total_limit']->setData($viewData ? $viewData->getTotalLimit() : null);
        $forms['date_from']->setData($viewData ? $viewData->getDateFrom()?->format(\DateTime::ATOM) : null);
        $forms['date_to']->setData($viewData ? $viewData->getDateTo()?->format(\DateTime::ATOM) : null);
        $forms['active']->setData($viewData ? $viewData->isActive() : 0);
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);
        $viewData = new NewProduct(
            $forms['title']->getData(),
            $forms['price']->getData(),
            $forms['price_vat']->getData(),
            $forms['vat']->getData(),
            $forms['description']->getData(),
            $forms['subscriber']->getData(),
            $forms['cumulative_months']->getData(),
            $forms['current_streak']->getData(),
            $forms['max_streak']->getData(),
            $forms['gifted_total']->getData(),
            $forms['tier']->getData(),
            $forms['order_limit']->getData(),
            $forms['total_limit']->getData(),
            $forms['date_from']->getData() ? \DateTime::createFromFormat('d. m. Y H:i', $forms['date_from']->getData()) : null,
            $forms['date_to']->getData() ? \DateTime::createFromFormat('d. m. Y H:i', $forms['date_to']->getData()) : null,
            $forms['active']->getData(),
        );
    }
}
