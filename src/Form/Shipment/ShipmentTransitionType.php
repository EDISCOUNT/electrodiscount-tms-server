<?php

namespace App\Form\Shipment;

use App\Entity\Shipment\Shipment;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
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
            foreach($possibleTransitions as $transition){
                $name = $transition->getName();
                $choices[$name] = $name;
            }
        }

        $builder
            ->add('transition', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    ...$choices
                ]
            ])
            ->add('attachments',CollectionType::class,[
                'entry_type' => ShipmentAttachmentType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shipment::class,
        ]);
    }
}
