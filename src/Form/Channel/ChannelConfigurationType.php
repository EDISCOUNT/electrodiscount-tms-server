<?php

namespace App\Form\Channel;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Url;

class ChannelConfigurationType extends AbstractType
{
    public const CHANNEL_TYPE_BOL = 'app.shipment.sourcing.source.bol_dot_com';
    public const CHANNEL_TYPE_WOO_COMMERCE = 'app.shipment.sourcing.source.woo_commerce';
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder;


        if ($options['channel_type'] === self::CHANNEL_TYPE_WOO_COMMERCE) {
            $builder
                ->add('base_url', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new NotNull(),
                        new NotBlank(),
                        new Url(),
                    ]
                ])
                ->add('client_id', TextType::class, [
                    'required' => true,
                ])
                ->add('client_secret', PasswordType::class, [
                    'required' => true,
                ]);
        }

        if ($options['channel_type'] === self::CHANNEL_TYPE_BOL) {
            $builder
                ->add('client_id', TextType::class, [
                    'required' => true,
                ])
                ->add('client_secret', PasswordType::class, [
                    'required' => true,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'channel_type' => '',//self::CHANNEL_TYPE_BOL,
            // Configure your form options here
        ]);
    }
}
