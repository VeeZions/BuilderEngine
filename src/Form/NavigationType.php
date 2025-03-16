<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use VeeZions\BuilderEngine\Form\Type\ButtonsType;

class NavigationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('data', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('buttons', ButtonsType::class, [
                'mapped' => false,
                'label' => false,
                'list_url' => $options['list_url'],
                'message' => $options['message'],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'builder_engine_navigation';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'locale_fallback' => null,
            'data_class' => null,
            'list_url' => null,
            'message' => null
        ]);
    }
}
