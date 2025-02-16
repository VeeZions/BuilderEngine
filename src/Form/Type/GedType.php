<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class GedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->checkOptionsIntegrity($options);
    }

    public function getBlockPrefix(): string
    {
        return 'xeno_engine_ged';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }

    private function checkOptionsIntegrity(array $options): void
    {
        
    }
}
