<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Form\Type\ButtonsType;
use VeeZions\BuilderEngine\Form\Type\LocaleType;

class NavigationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var BuilderNavigation $entity */
        $entity = $builder->getData();
        if (!$entity instanceof BuilderNavigation) {
            throw new InvalidConfigurationException('Entity must be an instance of BuilderNavigation');
        }
        $isOriginalFormTheme = ConfigConstant::CONFIG_DEFAULT_FORM_THEME === $options['form_theme'];

        $builder
            ->add('data', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('locale', LocaleType::class, [
                'label' => 'form.label.locale',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => $entity->getLocale() ?? $options['locale_fallback'],
                'choice_label' => function ($choice, string $key, mixed $value): string {
                    return mb_convert_case($key, MB_CASE_TITLE, 'UTF-8');
                },
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'form.label.navigation-type',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'choices' => $options['navigation_types'],
                'choice_translation_domain' => 'BuilderEngineBundle-forms',
            ])
            ->add('buttons', ButtonsType::class, [
                'mapped' => false,
                'label' => false,
                'list_url' => $options['list_url'],
                'message' => $options['message'],
                'form_theme' => $options['form_theme'],
                'attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btns' : 'btns',
                ],
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btns-container' : 'btns-container',
                ],
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
            'data_class' => BuilderNavigation::class,
            'list_url' => null,
            'message' => null,
            'form_theme' => null,
            'navigation_types' => [],
        ]);
    }
}
