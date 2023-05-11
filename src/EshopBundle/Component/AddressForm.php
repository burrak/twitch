<?php

declare(strict_types = 1);

namespace App\EshopBundle\Component;

use App\EshopBundle\DTO\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressForm extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class)
            ->add('surname', TextType::class)
            ->add('street', TextType::class)
            ->add('city', TextType::class)
            ->add('zip', IntegerType::class)
            ->add('country', TextType::class)
            ->add('note', TextareaType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
            ->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'empty_data' => null,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'address_item',
        ]);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        $forms = iterator_to_array($forms);
        $forms['first_name']->setData($viewData ? $viewData->getFirstName() : '');
        $forms['surname']->setData($viewData ? $viewData->getSurname() : '');
        $forms['street']->setData($viewData ? $viewData->getStreet() : '');
        $forms['city']->setData($viewData ? $viewData->getCity() : '');
        $forms['zip']->setData($viewData ? $viewData->getZip() : null);
        $forms['country']->setData($viewData ? $viewData->getCountry() : '');
        $forms['note']->setData($viewData ? $viewData->getNote() : '');
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);
        $viewData = new Address(
            $forms['first_name']->getData(),
            $forms['surname']->getData(),
            $forms['street']->getData(),
            $forms['city']->getData(),
            $forms['zip']->getData(),
            $forms['country']->getData(),
            $forms['note']->getData()
        );
    }
}
