<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CancelProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', TextareaType::class, [
                'label' => 'Cancellation Reason (optional)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Let Kimberly know why you would like to pause or cancel this project...',
                ],
                'constraints' => [
                    new Length(
                        max: 1000,
                        maxMessage: 'Reason cannot exceed {{ limit }} characters.',
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
