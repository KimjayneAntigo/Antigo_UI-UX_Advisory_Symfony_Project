<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

class ProjectFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deliverable', FileType::class, [
                'label' => 'Deliverable File',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotNull(message: 'Please select a deliverable file to upload.'),
                    new File(
                        maxSize: '10M',
                        extensions: [
                            'pdf',
                            'png',
                            'jpg',
                            'jpeg',
                            'zip',
                            'fig' => ['application/octet-stream', 'application/zip', 'binary/octet-stream', 'application/x-zip-compressed'],
                        ],
                        maxSizeMessage: 'The file cannot exceed 10 MB in size.',
                        extensionsMessage: 'Allowed file formats are PDF, PNG, JPG, ZIP, and FIG.',
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
