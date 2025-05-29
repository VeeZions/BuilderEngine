<?php

namespace VeeZions\BuilderEngine\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

class SeoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $seo = $options['data'];
        $seo = is_array($seo) ? $seo : [];
        $isOriginalFormTheme = ConfigConstant::CONFIG_DEFAULT_FORM_THEME === $options['form_theme'];

        $builder
            ->add('title', TextType::class, [
                'label' => 'form.label.seo.title',
                'data' => $seo['title'] ?? null,
                'translation_domain' => 'BuilderEngineBundle-forms',
                'attr' => [
                    'spellcheck' => 'false',
                    'autocorrect' => 'off',
                ],
            ])
            ->add('keywords', TextType::class, [
                'label' => 'form.label.seo.keywords',
                'data' => $seo['keywords'] ?? null,
                'translation_domain' => 'BuilderEngineBundle-forms',
                'attr' => [
                    'spellcheck' => 'false',
                    'autocorrect' => 'off',
                ],
            ])
            ->add('desc', TextareaType::class, [
                'label' => 'form.label.seo.desc',
                'data' => $seo['desc'] ?? null,
                'translation_domain' => 'BuilderEngineBundle-forms',
                'attr' => [
                    'rows' => 3,
                    'spellcheck' => 'false',
                    'autocorrect' => 'off',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'form_theme' => null,
        ]);
    }
}
