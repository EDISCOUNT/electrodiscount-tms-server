<?php

namespace App\Form\Shipment;

use App\Entity\Addressing\Address;
use App\Entity\Shipment\Shipment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('sourceId')
            ->add('idOnSorce')
            ->add('status')
            ->add('originAddress', EntityType::class, [
                'class' => Address::class,
'choice_label' => 'id',
            ])
            ->add('destinationAddress', EntityType::class, [
                'class' => Address::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shipment::class,
        ]);
    }
}
