<?php

namespace App\Form\Order;

use App\Entity\Carrier\Carrier;
use App\Entity\Shipment\ShipmentFulfilmentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class BulkImportShipmentOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('carrier', EntityType::class, [
                'class' => Carrier::class,
                'choice_label' => 'name',
                'label' => 'Carrier',
                'required' => false,
            ])
            ->add('orders', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Count(min: 1),
                ]
            ])
            ->add('fulfilmentType', EnumType::class, ['class' => ShipmentFulfilmentType::class])
            ->add('notify', CheckboxType::class, [
                'label' => 'Notify client',
                'required' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
