<?php

namespace VeeZions\BuilderEngine\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent as FormEvent;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;
use VeeZions\BuilderEngine\Form\Type\ButtonsType;
use VeeZions\BuilderEngine\Form\Type\LocaleType;
use VeeZions\BuilderEngine\Form\Type\SeoType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var BuilderArticle $entity */
        $entity = $builder->getData();
        if (!$entity instanceof BuilderArticle) {
            throw new InvalidConfigurationException('Entity must be an instance of BuilderArticle');
        }
        /** @var BuilderLibrary[] $libraries */
        $libraries = $entity->getLibraries()->toArray();
        $isOriginalFormTheme = ConfigConstant::CONFIG_DEFAULT_FORM_THEME === $options['form_theme'];

        $builder
            ->add('published', CheckboxType::class, [
                'label' => 'form.label.published',
                'required' => false,
                'translation_domain' => 'BuilderEngineBundle-forms',
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-published-row' : 'published-row',
                ],
            ])
            ->add('locale', LocaleType::class, [
                'label' => 'form.label.locale',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => $entity->getLocale() ?? $options['locale_fallback'],
                'choice_label' => function ($choice, string $key, mixed $value): string {
                    return mb_convert_case($key, MB_CASE_TITLE, 'UTF-8');
                },
            ])
            ->add('title', TextType::class, [
                'label' => 'form.label.title',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'attr' => [
                    'spellcheck' => 'false',
                    'autocorrect' => 'off',
                ],
            ])
        ;

        if (is_array($options['authors']) && !empty($options['authors'])) {
            $choices = [];
            foreach ($options['authors'] as $author) {
                $choices[$author['label']] = $author['id'];
            }
            $builder
                ->add('author', ChoiceType::class, [
                    'label' => 'form.label.author',
                    'translation_domain' => 'BuilderEngineBundle-forms',
                    'required' => true,
                    'choices' => $choices,
                    'data' => $entity->getAuthor(),
                ])
            ;
        }

        $builder
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
            ->add('content', TextareaType::class, [
                'label' => 'form.label.content',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'attr' => [
                    'rows' => 3,
                    'spellcheck' => 'false',
                    'autocorrect' => 'off',
                ],
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-textarea-row' : 'textarea-row',
                ],
            ])
            ->add('seo', SeoType::class, [
                'label' => 'form.label.seo',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => $entity->getSeo(),
                'required' => true,
                'form_theme' => $options['form_theme'],
                'attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-seo-container' : 'seo-container',
                ],
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-seo-row' : 'seo-row',
                ],
            ])
            ->add('buttons', ButtonsType::class, [
                'mapped' => false,
                'label' => false,
                'list_url' => $options['list_url'],
                'message' => $options['message'],
                'form_theme' => $options['form_theme'],
                'attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btns' : 'btns',
                ],
                'row_attr' => [
                    'class' => $isOriginalFormTheme ? 'vbe-form-theme-btns-container' : 'btns-container',
                ],
            ])

            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options) {
                if (null !== $options['authors'] && !empty($options['authors']) && is_int($options['user_id'])) {
                    $data = $event->getData();
                    /** @var BuilderArticle $form */
                    $form = $event->getForm()->getData();
                    /**@phpstan-ignore-next-line */
                    if (null === $data->getCreatedAt()) {
                        $form->setCreatedBy($options['user_id']);
                    } else {
                        $form->setUpdatedBy($options['user_id']);
                    }
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
            'list_url' => null,
            'message' => null,
            'form_theme' => null,
        ]);
    }
}
