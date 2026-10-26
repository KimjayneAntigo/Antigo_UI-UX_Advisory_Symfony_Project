<?php

namespace App\Form;

use App\Entity\Inquiry;
use App\Enum\InquiryStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InquiryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Contact Name',
                'attr' => ['placeholder' => 'e.g. Ana Reyes'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'attr' => ['placeholder' => 'e.g. ana@cebutourism.ph'],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone Number',
                'required' => false,
                'attr' => ['placeholder' => 'e.g. +63 918 555 8899'],
            ])
            ->add('company', TextType::class, [
                'label' => 'Company / Organization',
                'required' => false,
                'attr' => ['placeholder' => 'e.g. Cebu Tourism Board'],
            ])
            ->add('service', TextType::class, [
                'label' => 'Requested Service',
                'attr' => ['placeholder' => 'e.g. Web Design & Design Systems'],
            ])
            ->add('budgetRange', TextType::class, [
                'label' => 'Budget ($ USD)',
                'attr' => ['placeholder' => 'e.g. $45,000'],
            ])
            ->add('timeline', TextType::class, [
                'label' => 'Estimated Timeline',
                'attr' => ['placeholder' => 'e.g. 6-8 weeks'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Project Description / Requirements',
                'attr' => ['rows' => 4, 'placeholder' => 'Describe project scope, goals, and key deliverables...'],
            ])
            ->add('status', EnumType::class, [
                'label' => 'Inquiry Status',
                'class' => InquiryStatus::class,
                'choice_label' => fn (InquiryStatus $status) => match ($status) {
                    InquiryStatus::NEW => 'New Lead',
                    InquiryStatus::REVIEWED => 'Reviewed',
                    InquiryStatus::CONTACTED => 'Contacted',
                    InquiryStatus::CONVERTED => 'Converted',
                    InquiryStatus::LOST => 'Lost',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inquiry::class,
        ]);
    }
}
