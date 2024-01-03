<?php

namespace App\Form\Order;

use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentFulfilmentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportShipmentOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('carrier', EntityType::class, [
                'class' => 'App\Entity\Carrier\Carrier',
                'choice_label' => 'name',
                'label' => 'Carrier',
                'required' => false,
            ])
            ->add('fulfilmentType', EnumType::class, ['class' => ShipmentFulfilmentType::class])
            ->add('notify', CheckboxType::class, [
                'label' => 'Notify client',
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Shipment::class,
        ]);
    }
}
