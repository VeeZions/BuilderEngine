<?php

namespace VeeZions\BuilderEngine\Constant\Crud;

use VeeZions\BuilderEngine\Enum\ColumnEnum;
use VeeZions\BuilderEngine\Enum\SearchEnum;

readonly class CategoryConstant
{
    /**
     * @param array<int, string> $availableTranslations
     */
    public function getCrudConfig(): array
    {
        return [
            'keys' => [
                [
                    'label' => 'crud.header.id',
                    'queryKey' => 'c.id',
                    'colName' => 'id',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::NUMBER->value,
                    'columnType' => ColumnEnum::INTEGER->value,
                    'display' => false,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.title',
                    'queryKey' => 'c.title',
                    'colName' => 'title',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::TEXT->value,
                    'columnType' => ColumnEnum::TEXT->value,
                    'display' => true,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.slug',
                    'queryKey' => 'c.slug',
                    'colName' => 'slug',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::TEXT->value,
                    'columnType' => ColumnEnum::TEXT->value,
                    'display' => true,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.createdAt',
                    'queryKey' => 'c.createdAt',
                    'colName' => 'createdAt',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::DATE->value,
                    'columnType' => ColumnEnum::DATE_TIME->value,
                    'display' => true,
                    'choices' => [],
                ],
            ],
            'actions' => [
                'show' => ['path' => 'app_saas_categories_show', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
                'edit' => ['path' => 'app_saas_categories_edit', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
                'delete' => ['path' => 'app_saas_categories_delete', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
            ],
            'defaultOrder' => 'ASC',
        ];
    }
}
