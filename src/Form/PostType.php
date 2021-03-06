<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', FileType::class, array(
                'label' => 'main image',
                'required' => false,
                'mapped' => false
            ))
            ->add('category', EntityType::class, array(
                'label' => 'category',
                'class' => Category::class,
                'choice_label' => 'title'
            ))
            ->add('title', TextType::class, array(
                'label' => 'post title',
                'attr' => [
                    'placeholder' => 'enter title'
                ]
            ))
            ->add('content', TextareaType::class, array(
                'label' => 'post content',
                'attr' => [
                    'placeholder' => 'enter content'
                ]
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'save',
                'attr' => [
                    'class' => 'btn btn-success btn-lg btn-block mt-2'
                ]
            ))
            ->add('delete', SubmitType::class, array(
                'label' => 'delete',
                'attr' => [
                    'class' => 'btn btn-danger btn-lg btn-block mt-2',
                ]
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
