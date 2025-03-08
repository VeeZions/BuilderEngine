<?php

namespace VeeZions\BuilderEngine\Trait;

use Doctrine\ORM\QueryBuilder;

trait SearchTrait
{
    /**
     * @param array<string, string>                  $search
     * @param array<int, array<string, string|null>> $searchKeys
     */
    public function scopeSearch(QueryBuilder $query, array $search, array $searchKeys): QueryBuilder
    {
        foreach ($searchKeys as $row) {
            if (null !== $row['queryKey'] && isset($search[$row['queryKey']]) && strlen($search[$row['queryKey']]) > 0) {
                $var = str_replace('.', '', $row['queryKey']);
                $query->andWhere($row['queryKey'].' LIKE :'.$var)
                    ->setParameter($var, '%'.$search[$row['queryKey']].'%');
            }
        }

        return $query;
    }
}
