<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use App\Enum\ProjectPhase;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Project Title',
                'attr' => ['placeholder' => 'e.g. Pesolink Banking Redesign'],
            ])
            ->add('category', TextType::class, [
                'label' => 'Category / Service',
                'attr' => ['placeholder' => 'e.g. Fintech Mobile & Web Redesign'],
            ])
            ->add('clientName', TextType::class, [
                'label' => 'Client Name',
                'attr' => ['placeholder' => 'e.g. Maria Santos'],
            ])
            ->add('clientEmail', EmailType::class, [
                'label' => 'Client Email',
                'required' => false,
                'attr' => ['placeholder' => 'e.g. maria@pesolink.ph'],
            ])
            ->add('company', TextType::class, [
                'label' => 'Company / Organization',
                'required' => false,
                'attr' => ['placeholder' => 'e.g. Pesolink Technologies'],
            ])
            ->add('budget', MoneyType::class, [
                'label' => 'Budget ($ USD)',
                'currency' => 'USD',
                'attr' => ['placeholder' => '120000.00'],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Start Date (Optional)',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => false,
            ])
            ->add('dueDate', DateType::class, [
                'label' => 'Target Delivery / Due Date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('currentPhase', EnumType::class, [
                'label' => 'Initial Milestone Phase',
                'class' => ProjectPhase::class,
                'choice_label' => fn (ProjectPhase $phase) => sprintf('Phase %d: %s (%d%%)', $phase->value, $phase->label(), $phase->progress()),
            ])
            ->add('internalNotes', TextareaType::class, [
                'label' => 'Internal Studio Notes',
                'required' => false,
                'attr' => ['rows' => 3, 'placeholder' => 'Private studio briefing, sprint guidelines, or technical requirements...'],
            ])
            ->add('user', EntityType::class, [
                'label' => 'Assigned Client Portal Account',
                'class' => User::class,
                'choice_label' => 'name',
                'placeholder' => 'Select client account (optional)',
                'required' => false,
                // Beyond the lesson: filter user selection to clients only
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.role = :role')
                        ->setParameter('role', UserRole::CLIENT)
                        ->orderBy('u.name', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
