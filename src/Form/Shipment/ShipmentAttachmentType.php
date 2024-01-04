<?php

namespace App\Form\Shipment;

use App\Entity\Shipment\ShipmentAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ShipmentAttachmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Assert\File([
                        // 'maxSize' => '1024k',
                        'maxSize' => '10024k',
                        'mimeTypes' => [
                            // 'application/pdf',
                            // 'application/x-pdf',
                            //
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document or an Image',
                    ])
                ],
            ])
            ->add('type')
            // ->add('size')
            ->add('caption');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShipmentAttachment::class,
        ]);
    }
}
