<?php

namespace VeeZions\BuilderEngine\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\IntlCallbackChoiceLoader;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VeeZions\BuilderEngine\Provider\LocaleProvider;

class LocaleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => function (Options $options) {
                if (!class_exists(Intl::class)) {
                    throw new LogicException(\sprintf('The "symfony/intl" component is required to use "%s". Try running "composer require symfony/intl".', static::class));
                }

                $choiceTranslationLocale = is_string($options['choice_translation_locale']) ? $options['choice_translation_locale'] : 'fr';
                $provider = new LocaleProvider();

                return ChoiceList::loader($this, new IntlCallbackChoiceLoader(static fn () => array_flip($provider->getProviderList($choiceTranslationLocale))), $choiceTranslationLocale);
            },
            'choice_translation_domain' => false,
            'choice_translation_locale' => null,
            'provider' => null,
            'invalid_message' => 'Please select a valid locale.',
        ]);

        $resolver->setAllowedTypes('choice_translation_locale', ['null', 'string']);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'locale';
    }
}
