<?php

namespace VeeZions\BuilderEngine\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

class ButtonsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $attr = ['onclick' => null];
        if (null !== $options['list_url'] && null !== $options['message']) {
            $attr['onclick'] = 'if(confirm("' . $options['message'] . '")) window.location.href = "' . $options['list_url'] . '";';
        }
        $isOriginalFormTheme = $options['form_theme'] === ConfigConstant::CONFIG_DEFAULT_FORM_THEME;

        $builder
            ->add('save', SubmitType::class, [
                'label' => 'form.btn.save',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btn-row' : 'btn-row'
                ]
            ])
            ->add('save_and_stay', SubmitType::class, [
                'label' => 'form.btn.save.reload',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btn-row' : 'btn-row'
                ]
            ])
            ->add('back_to_list', ButtonType::class, [
                'label' => 'form.btn.back.list',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'attr' => $attr,
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btn-row' : 'btn-row'
                ]
            ])
        ;
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
