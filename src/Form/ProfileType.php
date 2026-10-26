<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Full Name',
                'attr' => [
                    'placeholder' => 'e.g. Maria Santos',
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter your full name.'),
                    new Length(
                        max: 120,
                        maxMessage: 'Name cannot exceed {{ limit }} characters.',
                    ),
                ],
            ])
            ->add('company', TextType::class, [
                'label' => 'Company / Organization',
                'required' => false,
                'attr' => [
                    'placeholder' => 'e.g. Pesolink Technologies',
                ],
                'constraints' => [
                    new Length(
                        max: 120,
                        maxMessage: 'Company name cannot exceed {{ limit }} characters.',
                    ),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone Number',
                'required' => false,
                'attr' => [
                    'placeholder' => 'e.g. +63 917 000 1234',
                ],
                'constraints' => [
                    new Length(
                        max: 30,
                        maxMessage: 'Phone number cannot exceed {{ limit }} characters.',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
