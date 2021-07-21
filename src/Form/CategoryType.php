<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('image', FileType::class, array(
//                'label' => 'main image',
//                'required' => false,
//                'mapped' => false
//            ))

            ->add('title', TextType::class, array(
                'label' => 'category title',
                'attr' => [
                    'placeholder' => 'enter title'
                ]
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'category description',
                'attr' => [
                    'placeholder' => 'enter description'
                ]
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'save',
                'attr' => [
                    'class' => 'btn btn-success float-left mr-2'
                ]
            ))
            ->add('delete', SubmitType::class, array(
                'label' => 'delete',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
