<?php

namespace App\Form;

use App\Entity\ProjectMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
                'label' => 'Your Message',
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Type a message or sprint feedback...',
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter a message before sending.'),
                    new Length(
                        max: 5000,
                        maxMessage: 'Message cannot exceed {{ limit }} characters.',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectMessage::class,
        ]);
    }
}
