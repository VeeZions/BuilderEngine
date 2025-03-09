<?php

namespace VeeZions\BuilderEngine\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

trait PaginationTrait
{
    /**
     * @param array<string, mixed> $constant
     *
     * @return array<string, mixed>
     */
    public function getPaginationData(
        Request $request,
        string $entityClass,
        array $constant,
        EntityManagerInterface $entityManager,
        ?string $userType = null,
    ): array {
        $page = max($request->query->getInt('page', 1), 1);
        $queryToArray = $request->query->getIterator()->getArrayCopy();
        $search = $queryToArray['search'] ?? [];
        $order = $request->query->getString('order', 'asc');
        $columns = array_map(function ($key) {
            return is_array($key) ? $key['queryKey'] : '';
        }, (array) $constant['keys']);
        $searchKeys = array_map(function ($key) {
            return is_array($key)
                ? ['queryKey' => $key['queryKey']]
                : ['queryKey' => null];
        }, (array) $constant['keys']);
        /**@phpstan-ignore-next-line */
        $collection = $entityManager->getRepository($entityClass)->paginate($page, $search, $order, $columns, $searchKeys);

        return [
            'entityClass' => $entityClass,
            'collection' => $collection,
            'paginator' => [
                'maxPage' => (int) ceil($collection->getTotalItemCount() / 10),
                'page' => $page,
                'search' => $search,
                'order' => $order,
            ],
            'crud' => $constant,
        ];
    }
}
