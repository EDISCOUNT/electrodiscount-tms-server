<?php

namespace App\Form\Shipment;

use App\Entity\Addressing\Address;
use App\Entity\Carrier\Carrier;
use App\Entity\Channel\Channel;
use App\Entity\Inventory\Storage;
use App\Entity\Order\AdditionalService;
use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentFulfilment;
use App\Entity\Shipment\ShipmentItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            // ->add('sourceId')
            // ->add('idOnSorce')
            // ->add('status')
            // ->add('channelOrderId')
            ->add('channelShipmentId')
            ->add('originAddress', EntityType::class, [
                'class' => Address::class,
                'choice_label' => 'id',
            ])
            ->add('destinationAddress', EntityType::class, [
                'class' => Address::class,
                'choice_label' => 'id',
            ])
            ->add('storage', EntityType::class, [
                'class' => Storage::class,
                'choice_label' => 'id',
            ])
            // ->add('channel', EntityType::class, [
            //     'class' => Channel::class,
            //     'choice_label' => 'id',
            // ])
            ->add('carrier', EntityType::class, [
                'class' => Carrier::class,
                'choice_label' => 'id',
            ])
            ->add('fulfilment', ShipmentFulfilmentType::class, [
            ])
            ->add('additionalServices', EntityType::class, [
                'class' => AdditionalService::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('items',  CollectionType::class, [
                'entry_type' => ShipmentItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shipment::class,
        ]);
    }
}
