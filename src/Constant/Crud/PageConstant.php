<?php

namespace Vision\BuilderEngine\Constant\Crud;

use Vision\BuilderEngine\Enum\ColumnEnum;
use Vision\BuilderEngine\Enum\SearchEnum;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class PageConstant
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
                    'queryKey' => 'p.id',
                    'colName' => 'id',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::NUMBER->value,
                    'columnType' => ColumnEnum::INTEGER->value,
                    'display' => false,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.title',
                    'queryKey' => 'p.title',
                    'colName' => 'title',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::TEXT->value,
                    'columnType' => ColumnEnum::TEXT->value,
                    'display' => true,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.route',
                    'queryKey' => 'p.route',
                    'colName' => 'route',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::TEXT->value,
                    'columnType' => ColumnEnum::TEXT->value,
                    'display' => true,
                    'choices' => [],
                ],
                [
                    'label' => 'crud.header.published',
                    'queryKey' => 'p.published',
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
                    'queryKey' => 'p.createdAt',
                    'colName' => 'createdAt',
                    'jsonColumn' => null,
                    'searchType' => SearchEnum::DATE->value,
                    'columnType' => ColumnEnum::DATE_TIME->value,
                    'display' => true,
                    'choices' => [],
                ],
            ],
            'actions' => [
                'show' => ['path' => 'app_saas_pages_show', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
                'edit' => ['path' => 'app_saas_pages_edit', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
                'delete' => ['path' => 'app_saas_pages_delete', 'query' => ['id'], 'role' => 'ROLE_SAAS'],
            ],
            'defaultOrder' => 'ASC',
        ];
    }
}
