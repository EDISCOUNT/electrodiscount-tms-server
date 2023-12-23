<?php

namespace App\Form\Shipment;

use App\Entity\Catalog\Product;
use App\Entity\Inventory\Storage;
use App\Entity\Order\OrderItem;
use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentFulfilment;
use App\Entity\Shipment\ShipmentItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('quantityReturned')
            ->add('internalOrderItemId')
            ->add('name')
            ->add('product', EntityType::class, [
                'class' => Product::class,
'choice_label' => 'id',
            ])
            ->add('orderItem', EntityType::class, [
                'class' => OrderItem::class,
'choice_label' => 'id',
            ])
            ->add('storage', EntityType::class, [
                'class' => Storage::class,
'choice_label' => 'id',
            ])
//             ->add('shipment', EntityType::class, [
//                 'class' => Shipment::class,
// 'choice_label' => 'id',
//             ])
            ->add('fulfilment', EntityType::class, [
                'class' => ShipmentFulfilment::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShipmentItem::class,
        ]);
    }
}
