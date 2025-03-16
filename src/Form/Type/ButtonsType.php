<?php

namespace VeeZions\BuilderEngine\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $attr = ['onclick' => null];
        if (null !== $options['list_url'] && null !== $options['message']) {
            $attr['onclick'] = 'if(confirm("' . $options['message'] . '")) window.location.href = "' . $options['list_url'] . '";';
        }

        $builder
            ->add('save', SubmitType::class, [
                'label' => 'form.btn.save',
                'translation_domain' => 'BuilderEngineBundle-forms',
            ])
            ->add('save_and_stay', SubmitType::class, [
                'label' => 'form.btn.save.reload',
                'translation_domain' => 'BuilderEngineBundle-forms',
            ])
            ->add('back_to_list', ButtonType::class, [
                'label' => 'form.btn.back.list',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'attr' => $attr
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'list_url' => null,
            'message' => null,
        ]);
    }
}
