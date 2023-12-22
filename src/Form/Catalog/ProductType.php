<?php

namespace App\Form\Catalog;

use App\Entity\Catalog\Product;
use App\Entity\Catalog\ProductPrice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('gtin')
            ->add('name')
            ->add('price', EntityType::class, [
                'class' => ProductPrice::class,
                'choice_label' => 'id',
            ])
            ->add('enabled');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
