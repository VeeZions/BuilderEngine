<?php

namespace Vision\BuilderEngine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Vision\BuilderEngine\Form\Type\SeoType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vision\BuilderEngine\Entity\BuilderCategory;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**@var BuilderCategory $entity*/
        $entity = $builder->getData();
        if (!$entity instanceof BuilderCategory) {
            throw new InvalidConfigurationException('Entity must be an instance of BuilderCategory');
        }
        $libraries = $entity->getLibraries()->toArray();
        $id = $entity->getId();

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
        ;
        $builder
            ->add('parent', EntityType::class, [
                'class' => BuilderCategory::class,
                'query_builder' => function (EntityRepository $er) use ($id): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->where('c.id != :id')
                        ->setParameter('id', null !== $id ? $id : 0);
                },
                'choice_label' => 'title',
                'label' => 'form.label.categories.parent',
                'translation_domain' => 'BuilderEngineBundle-forms',
                'data' => $entity->getParent(),
                'placeholder' => 'form.label.categories.parent.placeholder',
            ])
        ;
        $builder
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
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'builder_engine_category';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'locale_fallback' => null,
        ]);
    }
}
