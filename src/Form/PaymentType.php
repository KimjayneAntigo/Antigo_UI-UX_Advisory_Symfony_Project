<?php

namespace App\Form;

use App\Enum\PaymentMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('method', EnumType::class, [
                'label' => 'Payment Method',
                'class' => PaymentMethod::class,
                'placeholder' => '-- Select Payment Method --',
                'choice_label' => fn (PaymentMethod $method) => match ($method) {
                    PaymentMethod::BANK_TRANSFER => 'Bank Transfer (USD Wire)',
                    PaymentMethod::CARD => 'Credit / Debit Card',
                    PaymentMethod::GCASH => 'GCash',
                    PaymentMethod::CASH => 'Cash Settlement',
                },
                'constraints' => [
                    new NotNull(message: 'Please select a valid payment method.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // No data class directly so amount cannot be bound from request
        ]);
    }
}
