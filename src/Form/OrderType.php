<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Table;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',DateType::class,[
                'widget' => 'single_text'
            ])
            ->add('timeFrom',TimeType::class,[
                'widget' => 'single_text'
            ])
            ->add('timeTo',TimeType::class,[
                'widget' => 'single_text'
            ])
            ->add('userPhone')
            ->add('userEmail')
            ->add('userName')
            ->add('tables')
            ->add('restaurant')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
