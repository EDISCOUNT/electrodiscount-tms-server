<?php

namespace App\Form\Carrier;

use App\Entity\Account\User;
use App\Entity\Carrier\Carrier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CarrierLogoImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '10024k',
                        'mimeTypes' => [
                            // 'application/pdf',
                            // 'application/x-pdf',
                            //
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carrier::class,
        ]);
    }
}
