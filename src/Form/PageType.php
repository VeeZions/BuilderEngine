<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use VeeZions\BuilderEngine\Form\Type\SeoType;
use VeeZions\BuilderEngine\Form\Type\BuilderType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Entity\BuilderPage;
use VeeZions\BuilderEngine\Form\Type\LocaleType;
use Symfony\Component\Form\Event\PostSubmitEvent as FormEvent;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**@var BuilderPage $entity*/
        $entity = $builder->getData();
        if (!$entity instanceof BuilderPage) {
            throw new InvalidConfigurationException('Entity must be an instance of BuilderPage');
        }
        $libraries = $entity->getLibraries()->toArray();
        
        $builder
            ->add('locale', LocaleType::class, [
                'label' => 'form.label.locale',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => $entity->getLocale() ?? $options['locale_fallback'],
                'choice_label' => function ($choice, string $key, mixed $value): TranslatableMessage|string {
                    return mb_convert_case($key, MB_CASE_TITLE, 'UTF-8');
                },
            ])
            ->add('title', TextType::class, [
                'label' => 'form.label.title',
                'translation_domain' => 'BuilderEngineBundle-forms',
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'form.label.published',
                'required' => false,
                'translation_domain' => 'BuilderEngineBundle-forms',
            ])
        ;
        if (null === $entity->getRoute()) {
            $builder
                ->add('route', TextType::class, [
                    'label' => 'form.label.route',
                    'translation_domain' => 'crud',
                ]);
        }
        $builder
            ->add('content', BuilderType::class, [
                'label' => 'form.label.builder',
                'required' => false,
                'data' => $entity->getContent(),
            ])
            ->add('libraries', HiddenType::class, [
                'data' => !empty($libraries) ? $libraries[0]->getId() : null,
                'mapped' => false,
                'required' => false,
            ])
            ->add('seo', SeoType::class, [
                'label' => 'form.label.seo',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => $entity->getSeo(),
                'required' => true,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm()->getData();
                if ($options['authors'] !== null && !empty($options['authors'])) {
                    $data = $event->getData();
                    if ($data->getCreatedAt() === null) {
                        $data->setCreatedBy($options['user_id']);
                    } else {
                        $data->setUpdatedBy($options['user_id']);
                    }
                    $event->setData($data);
                }
                $content = $event->getForm()->get('content')->getData();
                if ($form instanceof BuilderPage && is_array($content)) {
                    $content = $content['builder'] ?? [];
                    if (!str_starts_with((string) $form->getRoute(), 'builder_')) {
                        $form->setRoute('builder_'.$form->getRoute());
                    }
                    $content = json_decode($content, true);
                    $form->setContent(is_array($content) ? $content : []);
                }
            })
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'builder_engine_page';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'authors' => null,
            'locale_fallback' => null,
            'user_id' => null,
        ]);
    }
}
