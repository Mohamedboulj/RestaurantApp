<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Dish;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name')
            ->add('Attachment',FileType::class,['mapped'=>false,'attr'=>array('id'=>'form-file')])
            ->add('Description')
            ->add('Category', EntityType::class, ['class'=>Category::class, 'attr'=>array('class'=>'custom-select custom-select-md')])
            ->add('Price')
            ->add('Create', SubmitType::class,['attr'=>array('class'=>'btn btn-outline-danger float-right')]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dish::class,
        ]);
    }
}
