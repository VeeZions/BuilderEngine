<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Constant\AssetConstant;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

class LibraryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $accepted = [
            AssetConstant::IMAGE_TYPE,
            AssetConstant::VIDEO_TYPE,
            AssetConstant::DOCUMENT_TYPE,
        ];
        
        $isOriginalFormTheme = $options['form_theme'] === ConfigConstant::CONFIG_DEFAULT_FORM_THEME;

        $builder
            ->add('file', FileType::class, [
                'label' => 'form.label.libraries.upload',
                'label_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-action-btn-secondary vbe-form-theme-button' : 'file-upload-link',
                ],
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btn-row' : 'btn-row'
                ],
                'translation_domain' => 'BuilderEngineBundle-forms',
                'mapped' => false,
                'attr' => [
                    'accept' => implode(',', $accepted),
                    'style' => 'display:none;',
                    'data-veezions--builder-engine-bundle--veezions-builder-engine-bundle-media-target' => 'input',
                ],
                'help' => 'form.label.libraries.help',
                'help_translation_parameters' => [],
                'help_html' => true,
                'help_attr' => [
                    'class' => 'vbe-builder-libraries-help',
                ],
                'row_attr' => [
                    'class' => 'builder-libraries-row',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.btn.upload',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btn-row' : 'btn-row'
                ],
                'attr' => [
                    'data-veezions--builder-engine-bundle--veezions-builder-engine-bundle-media-target' => 'upload',
                ]
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'builder_engine_library';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'list_url' => null,
            'message' => null,
            'form_theme' => null,
        ]);
    }
}
