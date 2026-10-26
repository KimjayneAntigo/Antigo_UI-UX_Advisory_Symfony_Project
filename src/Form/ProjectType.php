<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('project_code')
            ->add('title')
            ->add('category')
            ->add('client_name')
            ->add('client_email')
            ->add('company')
            ->add('budget')
            ->add('start_date', null, [
                'widget' => 'single_text'
            ])
            ->add('due_date', null, [
                'widget' => 'single_text'
            ])
            ->add('current_phase')
            ->add('status_type')
            ->add('cancelled_at', null, [
                'widget' => 'single_text'
            ])
            ->add('cancellation_reason')
            ->add('invoice_sent_at', null, [
                'widget' => 'single_text'
            ])
            ->add('internal_notes')
            ->add('created_at', null, [
                'widget' => 'single_text'
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('cancelled_by', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
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
