<?php

namespace App\Form\Shipment;

use App\Entity\Shipment\Shipment;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Workflow\WorkflowInterface;

class ShipmentTransitionType extends AbstractType
{
    public function __construct(
        #[Target('shipment_operation')]
        private WorkflowInterface $workflow,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];

        /** @var Shipment */
        $shipment = $builder->getData();

        if ($shipment) {
            $possibleTransitions =  $this->workflow->getEnabledTransitions($shipment);
            foreach ($possibleTransitions as $transition) {
                $name = $transition->getName();
                $choices[$name] = $name;
            }
        }

        $builder
            ->add('transition', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    ...$choices
                ],
            ])
            ->add('attachments', CollectionType::class, [
                'entry_type' => ShipmentAttachmentType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
            ])
            ->add('description', TextareaType::class, [
                'mapped' => false,
            ]);


        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            // $data = $event->getData();
            $form = $event->getForm();
            $transition = $form->get('transition')->getData();
            $attachments = $form->get('attachments')->getData();

            switch ($transition) {
                case 'delivered':
                    if (!$attachments) {
                        $form->get('attachments')->addError(new FormError("You must provide at least one attachment for a delivery"));
                    }
                    break;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shipment::class,
        ]);
    }
}
