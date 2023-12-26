<?php

namespace App\Form\Account;

use App\Entity\Account\User;
use App\Entity\Carrier\Carrier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserType extends AbstractType
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ){

    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('roles', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
            ])
            ->add('password', null, [
                'mapped' => false,
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('phone')
            ->add('carrier', EntityType::class, [
                'class' => Carrier::class,
'choice_label' => 'id',
            ])
        ;


        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var User */
            $user = $event->getData();
            $form = $event->getForm();

            $passwordForm = $form->get('password');
            $rawPassword = $passwordForm->getData();

            if ($rawPassword) {
               $hashedPassword = $this->userPasswordHasher->hashPassword($user, $rawPassword);
               $user->setPassword($hashedPassword);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
