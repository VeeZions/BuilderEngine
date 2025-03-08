<?php

namespace VeeZions\BuilderEngine\Constant\Crud;

use VeeZions\BuilderEngine\Enum\ColumnEnum;
use VeeZions\BuilderEngine\Enum\SearchEnum;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ArticleConstant
{
    /**
     * @param array<int, string> $availableTranslations
     */
    public function __construct(private TranslatorInterface $translator) 
    {
        
    }

    /**
     * @return array<string, mixed>
     */
    public function getCrudConfig(): array
    {
        return [
            'keys' => [
                [
                    'label' => 'crud.header.id',
                    'queryKey' => 'a.id',
                    'colName' => 'id',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::NUMBER->value,
                    'columnType' => ColumnEnum::INTEGER->value,
                    'display' => false,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.title',
                    'queryKey' => 'a.title',
                    'colName' => 'title',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::TEXT->value,
                    'columnType' => ColumnEnum::TEXT->value,
                    'display' => true,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.slug',
                    'queryKey' => 'a.slug',
                    'colName' => 'slug',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::TEXT->value,
                    'columnType' => ColumnEnum::TEXT->value,
                    'display' => true,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.published',
                    'queryKey' => 'a.published',
                    'colName' => 'published',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::SELECT->value,
                    'columnType' => ColumnEnum::BOOLEAN->value,
                    'display' => true,
                    'choices' => (function () {
                        return [
                            $this->translator->trans('crud.all.label', [], 'crud') => null,
                            $this->translator->trans('crud.yes.published.label', [], 'crud') => true,
                            $this->translator->trans('crud.no.published.label', [], 'crud') => false,
                        ];
                    })(),
                ],
                [
                    'label' => 'crud.header.createdAt',
                    'queryKey' => 'a.createdAt',
                    'colName' => 'createdAt',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::DATE->value,
                    'columnType' => ColumnEnum::DATE_TIME->value,
                    'display' => true,
                    'choices' => [],
                ],
            ],
            'actions' => [
                'show' => ['path' => 'app_saas_articles_show', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
                'edit' => ['path' => 'app_saas_articles_edit', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
                'delete' => ['path' => 'app_saas_articles_delete', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
            ],
            'defaultOrder' => 'ASC',
        ];
    }
}
