<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use VeeZions\BuilderEngine\Form\Type\SeoType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Form\Type\LocaleType;
use Symfony\Component\Form\Event\PostSubmitEvent as FormEvent;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**@var BuilderArticle $entity*/
        $entity = $builder->getData();
        if (!$entity instanceof BuilderArticle) {
            throw new InvalidConfigurationException('Entity must be an instance of BuilderArticle');
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
            ->add('categories', EntityType::class, [
                'label' => 'form.label.categories',
                'required' => false,
                'translation_domain' => 'BuilderEngineBundle-forms',
                'class' => BuilderCategory::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('libraries', HiddenType::class, [
                'label' => 'form.label.libraries',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => !empty($libraries) ? $libraries[0]->getId() : null,
                'mapped' => false,
                'required' => false,
            ])
        ;

        if ($options['authors'] !== null && !empty($options['authors'])) {
            $builder
                ->add('author', ChoiceType::class, [
                    'label' => 'form.label.author',
                    'translation_domain' => 'BuilderEngineBundle-forms',
                    'required' => true,
                ])
            ;
        }

        $builder
            ->add('content', TextareaType::class, [
                'label' => 'form.label.content',
                'translation_domain' => 'BuilderEngineBundle-forms',
            ])
            ->add('seo', SeoType::class, [
                'label' => 'form.label.seo',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => $entity->getSeo(),
                'required' => true,
            ])

            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options) {
                if ($options['authors'] !== null && !empty($options['authors'])) {
                    $data = $event->getData();
                    if ($data->getCreatedAt() === null) {
                        $data->setCreatedBy($options['user_id']);
                    } else {
                        $data->setUpdatedBy($options['user_id']);
                    }
                    $event->setData($data);
                }
            })
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'builder_engine_article';
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
