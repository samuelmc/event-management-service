<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city')
            ->add('title')
            ->add('description')
            ->add('active')
            ->add('maxParticipants')
            ->add('startTime', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm',
            ])
            ->add('endTime', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
