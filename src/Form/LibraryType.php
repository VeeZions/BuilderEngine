<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Constant\AssetConstant;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class LibraryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $accepted = [
            AssetConstant::IMAGE_TYPE,
            AssetConstant::VIDEO_TYPE,
            AssetConstant::DOCUMENT_TYPE,
        ];

        $builder
            ->add('file', FileType::class, [
                'label' => 'form.label.libraries.upload',
                'label_attr' => [
                    'class' => 'erp-link underline cursor-pointer',
                ],
                'translation_domain' => 'BuilderEngineBundle-forms',
                'mapped' => false,
                'attr' => [
                    'accept' => implode(',', $accepted),
                    'style' => 'display:none',
                    'data-saas--media-target' => 'input',
                ],
                'help' => 'form.label.libraries.help',
                'help_translation_parameters' => [],
                'help_html' => true,
                'help_attr' => [
                    'class' => 'builder-libraries-help',
                ],
                'row_attr' => [
                    'class' => 'builder-libraries-row',
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
        ]);
    }
}
