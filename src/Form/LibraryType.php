<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use VeeZions\BuilderEngine\Constant\AssetConstant;
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

        $extensions = array_merge(
            AssetConstant::IMAGE_EXTENSIONS,
            AssetConstant::VIDEO_EXTENSIONS,
            AssetConstant::DOCUMENT_EXTENSIONS
        );

        $isOriginalFormTheme = ConfigConstant::CONFIG_DEFAULT_FORM_THEME === $options['form_theme'];

        $maxFilesize = is_string($options['max_upload_size']) ? $options['max_upload_size'] : '2M';

        $builder
            ->add('file', FileType::class, [
                'label' => 'form.label.libraries.upload',
                'label_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-action-btn-secondary vbe-form-theme-button' : 'file-upload-link',
                ],
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'builder-libraries-row vbe-form-theme-btn-row' : 'builder-libraries-row btn-row',
                ],
                'translation_domain' => 'BuilderEngineBundle-forms',
                'mapped' => false,
                'attr' => [
                    'accept' => implode(',', $accepted),
                    'style' => 'display:none;',
                    'data-veezions--builder-engine-bundle--veezions-builder-engine-bundle-media-target' => 'input',
                    'data-maxFileSize' => AssetConstant::convertToBytes($maxFilesize),
                ],
                'help' => 'form.label.libraries.help',
                'help_translation_parameters' => ['%size%' => $maxFilesize],
                'help_html' => true,
                'help_attr' => [
                    'class' => 'vbe-builder-libraries-help',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => $maxFilesize,
                        'extensions' => $extensions,
                        'extensionsMessage' => $options['error_extensions_message'],
                        'maxSizeMessage' => $options['error_max_size_message'],
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.btn.upload',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btn-row' : 'btn-row',
                ],
                'attr' => [
                    'data-veezions--builder-engine-bundle--veezions-builder-engine-bundle-media-target' => 'upload',
                    'disabled' => true,
                ],
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
            'error_extensions_message' => null,
            'error_max_size_message' => null,
            'max_upload_size' => null
        ]);
    }
}
