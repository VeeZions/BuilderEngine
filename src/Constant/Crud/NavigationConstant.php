<?php

namespace VeeZions\BuilderEngine\Constant\Crud;

use VeeZions\BuilderEngine\Enum\ColumnEnum;
use VeeZions\BuilderEngine\Enum\SearchEnum;

readonly class NavigationConstant
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
                    'queryKey' => 'n.id',
                    'colName' => 'id',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::NUMBER->value,
                    'columnType' => ColumnEnum::INTEGER->value,
                    'display' => false,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.name',
                    'queryKey' => 'n.content',
                    'colName' => 'content',
                    'jsonColumn' => 'name',
                    'searchType' => SearchEnum::TEXT->value,
                    'columnType' => ColumnEnum::TEXT->value,
                    'display' => true,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.locale',
                    'queryKey' => 'n.locale',
                    'colName' => 'locale',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::SELECT->value,
                    'columnType' => ColumnEnum::TEXT->value,
                    'display' => true,
                    'choices' => [],
                ],
            ],
            'actions' => [
                'edit' => ['path' => 'app_saas_navigation_edit', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
            ],
            'defaultOrder' => 'ASC',
        ];
    }
}
