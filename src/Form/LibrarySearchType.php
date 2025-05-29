<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Enum\LibraryEnum;

class LibrarySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isOriginalFormTheme = ConfigConstant::CONFIG_DEFAULT_FORM_THEME === $options['form_theme'];

        $builder
            ->add('media_search_order', ChoiceType::class, [
                'label' => 'form.label.order.by',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'expanded' => true,
                'choices' => [
                    'form.label.order.by.asc' => 'asc',
                    'form.label.order.by.desc' => 'desc',
                ],
                'data' => 'asc',
            ])
            ->add('media_search_types', ChoiceType::class, [
                'label' => 'form.label.media.types',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'form.label.media.types.images' => LibraryEnum::IMAGE->value,
                    'form.label.media.types.videos' => LibraryEnum::VIDEO->value,
                    'form.label.media.types.documents' => LibraryEnum::DOCUMENT->value,
                    'form.label.media.types.unknown' => LibraryEnum::UNKNOWN->value,
                ],
                'data' => [
                    LibraryEnum::IMAGE->value,
                    LibraryEnum::VIDEO->value,
                    LibraryEnum::DOCUMENT->value,
                    LibraryEnum::UNKNOWN->value,
                ],
            ])
            ->add('media_search', SearchType::class, [
                'label' => 'form.label.media.search',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'required' => false,
                'attr' => [
                    'placeholder' => 'form.label.media.search.placeholder',
                ],
            ])
            ->add('media_search_button', SubmitType::class, [
                'label' => 'form.label.search',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'attr' => [
                    'data-veezions--builder-engine-bundle--veezions-builder-engine-bundle-media-target' => 'searchBtn',
                ],
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btn-row' : 'btn-row',
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'form_theme' => null,
        ]);
    }
}
