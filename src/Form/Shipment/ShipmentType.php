<?php

namespace App\Form\Shipment;

use App\Entity\Addressing\Address;
use App\Entity\Carrier\Carrier;
use App\Entity\Inventory\Storage;
use App\Entity\Order\AdditionalService;
use App\Entity\Shipment\Shipment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ShipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', null, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 32, min: 6)
                ],
            ])
            ->add('bookedAt',)
            ->add('netWeight')
            ->add('volumetricWeight')
            ->add('codAmount')
            ->add('codCurrency')
            ->add('dimension', ShipmentDimensionType::class, [
                'required' => false,
            ])
            ->add('channelShipmentId')
            ->add('channelOrderId')
            ->add('originAddress', ShipmentAddressType::class, [])
            ->add('destinationAddress', ShipmentAddressType::class, [])
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
            ->add('fulfilment', ShipmentFulfilmentType::class, [])
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
