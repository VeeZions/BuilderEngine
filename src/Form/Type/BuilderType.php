<?php

namespace VeeZions\BuilderEngine\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

class BuilderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $content = $options['data'];
        $content = is_array($content) ? $content : [];
        $isOriginalFormTheme = $options['form_theme'] === ConfigConstant::CONFIG_DEFAULT_FORM_THEME;

        $builder
            ->add('builder', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'hidden',
                    'data-saas--builder-target' => 'hidden',
                    'readonly' => true,
                    'spellcheck' => 'false',
                    'autocomplete' => 'off',
                ],
                'data' => json_encode($content),
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($builder) {
                $builder = $event->getForm()->get('builder')->getData();
                $content = is_string($builder) ? json_decode($builder, true) : [];
                $event->setData($content);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'form_theme' => null
        ]);
    }
}
