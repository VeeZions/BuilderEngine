<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;

class LibraryModalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isOriginalFormTheme = ConfigConstant::CONFIG_DEFAULT_FORM_THEME === $options['form_theme'];
        /** @var BuilderLibrary $media */
        $media = $options['file_data'];

        $builder
            ->add('id', HiddenType::class, [
                'data' => $media->getId(),
            ])
            ->add('media_modal_title', TextType::class, [
                'label' => 'form.label.media.modal.edit.title',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => $media->getTitle(),
            ])
            ->add('media_modal_legend', TextType::class, [
                'label' => 'form.label.media.modal.edit.legend',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => $media->getLegend(),
            ])
            ->add('media_modal_button', SubmitType::class, [
                'label' => 'form.label.media.save',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'attr' => [
                    'data-veezions--builder-engine-bundle--veezions-builder-engine-bundle-media-target' => 'save',
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
            'file_data' => null,
        ]);
    }
}
