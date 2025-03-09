<?php

namespace VeeZions\BuilderEngine\Twig;

use Symfony\Component\Form\FormInterface;

final class GlobalVariables
{
    public function __construct(
        private string $extended_template,
        private ?string $form_theme,
    )
    {

    }

    public function getExtended_template(): string
    {
        return $this->extended_template;
    }

    public function getForm_theme(): ?string
    {
        return $this->form_theme;
    }
}
