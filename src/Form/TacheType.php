<?php

namespace App\Form;

use App\Entity\Tache;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description', TextareaType::class)
            ->add('statut')
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'html5'=> false,
                'format' => 'yyyy-MM-dd',
                'translation_domain' => false])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
                'html5'=> false,
                'format' => 'yyyy-MM-dd',
                'translation_domain' => false
                ])
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('user', EntityType::class, [
                'class'=> User::class,
                'choice_label'=>'nom',
                'expanded'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
