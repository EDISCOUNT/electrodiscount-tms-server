<?php

namespace App\Form\Mailing;

use App\Entity\Mailing\EmailAddress;
use App\Entity\Mailing\Message;
use App\Entity\Shipment\Shipment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject')
            ->add('message')
            ->add('recipients', CollectionType::class, [
                'entry_type' => EmailAddressType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Assert\Count(min: 1),
                ],
            ])
            ->add('ccRecipients', CollectionType::class, [
                'entry_type' => EmailAddressType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('bccRecipients', CollectionType::class, [
                'entry_type' => EmailAddressType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('saveAsTemplate', CheckboxType::class, [
                'required' => false,
                'mapped'   => false,
            ])
            ->add('label', TextType::class, [
                'required' => false,
                'mapped'   => false,
            ])
            ->add('shipment', EntityType::class, [
                'class' => Shipment::class,
                'required' => false,
                'mapped' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
