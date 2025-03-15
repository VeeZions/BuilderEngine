<?php

namespace VeeZions\BuilderEngine\Twig;

use Symfony\Component\Form\FormInterface;
use Twig\Markup;
use Twig\Environment;

final class GlobalVariables
{
    public function __construct(
        private Environment $twig,
        private string $extended_template,
        private ?string $form_theme,
        private array $customRoutes,
        private array $pagination_templates,
        private array $crud_buttons
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
    
    public function getRoutes(): ?array
    {
        return $this->customRoutes;
    }
    
    public function getPagination_first_button(): Markup
    {
        return new Markup($this->twig->render($this->pagination_templates['first_button']), 'UTF-8');
    }
    
    public function getPagination_previous_button(): Markup
    {
        return new Markup($this->twig->render($this->pagination_templates['previous_button']), 'UTF-8');
    }
    
    public function getPagination_last_button(): Markup
    {
        return new Markup($this->twig->render($this->pagination_templates['last_button']), 'UTF-8');
    }
    
    public function getPagination_next_button(): Markup
    {
        return new Markup($this->twig->render($this->pagination_templates['next_button']), 'UTF-8');
    }
    
    public function getCrud_edit_button(): Markup
    {
        return new Markup($this->twig->render($this->crud_buttons['edit_label']), 'UTF-8');
    }
    
    public function getCrud_delete_button(): Markup
    {
        return new Markup($this->twig->render($this->crud_buttons['delete_label']), 'UTF-8');
    }
}
