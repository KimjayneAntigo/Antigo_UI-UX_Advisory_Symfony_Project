<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Current Password',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Enter your current account password',
                    'autocomplete' => 'current-password',
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter your current password.'),
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'The new password confirmation does not match.',
                'first_options' => [
                    'label' => 'New Password',
                    'attr' => [
                        'placeholder' => 'Minimum 8 characters',
                        'autocomplete' => 'new-password',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm New Password',
                    'attr' => [
                        'placeholder' => 'Re-enter your new password',
                        'autocomplete' => 'new-password',
                    ],
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter a new password.'),
                    new Length(
                        min: 8,
                        minMessage: 'Your new password must be at least {{ limit }} characters long.',
                        max: 4096,
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
