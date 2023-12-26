<?php

namespace App\Form\Shipment;

use App\Entity\Shipment\ShipmentDimension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentDimensionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('length')
            ->add('width')
            ->add('height')
            ->add('unit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShipmentDimension::class,
        ]);
    }
}
