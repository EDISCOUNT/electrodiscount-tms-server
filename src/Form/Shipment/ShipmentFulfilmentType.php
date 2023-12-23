<?php

namespace App\Form\Shipment;

use App\Entity\Shipment\ShipmentFulfilment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentFulfilmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('method')
            ->add('distributionParty')
            ->add('latestDeliveryDate')
            ->add('exactDeliveryDate')
            ->add('expiryDate')
            ->add('timeFrameType')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShipmentFulfilment::class,
        ]);
    }
}
