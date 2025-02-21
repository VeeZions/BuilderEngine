<?php

namespace XenoLab\XenoEngine\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NavigationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->checkOptionsIntegrity($options);
    }

    public function getBlockPrefix(): string
    {
        return 'xeno_engine_navigation';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }

    private function checkOptionsIntegrity(array $options): void
    {
    }
}
