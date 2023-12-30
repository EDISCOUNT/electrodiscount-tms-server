<?php

namespace App\Form\Texting;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipients', CollectionType::class, [
                'entry_type' => TelType::class,
                'required' => false, // change this as per your requirements
                'allow_add' => true, // allows adding new forms
                'allow_delete' => true, // allows deleting forms
                'constraints' => [
                    new Assert\Count(min: 1, max: 160),
                ]
            ])
            ->add('message', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\Length(min: 1, max: 160),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
