<?php

namespace App\Form\Carrier;

use App\Entity\Carrier\Carrier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarrierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('description', null, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('emailAddress', EmailType::class, [])
            ->add('phoneNumber', TelType::class, [])
            ->add('operatorUser')
            ->add('logoImage', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('enabled');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carrier::class,
        ]);
    }
}
