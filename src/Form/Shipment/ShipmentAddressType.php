<?php

namespace App\Form\Shipment;

use App\Entity\Addressing\Address;
use App\Entity\Addressing\Coordinate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('street')
            ->add('city')
            ->add('postcode')
            ->add('countryCode')
            ->add('provinceName')
            ->add('provinceCode')
            ->add('company')
            ->add('phoneNumber')
            ->add('emailAddress')
            ->add('googlePlaceId')
//             ->add('coordinate', EntityType::class, [
//                 'class' => Coordinate::class,
// 'choice_label' => 'id',
//             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
