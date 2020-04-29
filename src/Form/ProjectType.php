<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description', TextareaType::class)
            ->add('image_name', FileType::class, ['attr' =>
                ['placeholder' => 'Choisissez votre fichier']])
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'html5'=> false,
                'format'=> 'dd/MM/yyyy',
                'translation_domain' => false
            ])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
                'html5'=> false,
                'format'=> 'dd/MM/yyyy',
                'translation_domain' => false
            ])//            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
