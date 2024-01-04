<?php

namespace App\Form\Shipment;

use App\Entity\Shipment\Shipment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Workflow\WorkflowInterface;

class BulkUpdateShipmentStatusType extends AbstractType
{

    public function __construct(
        #[Target('shipment_operation')]
        private WorkflowInterface $workflow,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $possibleTransitions =  $this->workflow->getMetadataStore()->getWorkflowMetadata(Shipment::class)->getInitialPlaces();
        // foreach($possibleTransitions as $transition){
        //     $name = $transition->getName();
        //     $choices[$name] = $name;
        // }

        $transitions = $this->workflow->getDefinition()->getTransitions();
        $choices = [];
        foreach ($transitions as $transition) {
            $name = $transition->getName();
            if($name == 'delivered'){
                continue;
            }
            $choices[$name] = $name;
        }

        $builder
            ->add('shipments', EntityType::class, [
                'class' => Shipment::class,
                'choice_label' => 'code',
                'multiple' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'You must specify at least one shipment',
                    ]),
                ],
            ])
            ->add('transition', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    ...$choices
                ]
            ]);


        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $transition = $form->get('transition')->getData();
            $shipments = $form->get('shipments')->getData();

            // TODO: ENSURE THAT DELIVERED TRANSITIONS ARE NOT BULK APPLIED

            // foreach($shipments as $shipment){
            //     $this->workflow->apply($shipment, $transition);
            // }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
