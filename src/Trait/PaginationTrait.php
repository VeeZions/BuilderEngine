<?php

namespace Vision\BuilderEngine\Trait;

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
        $collection = match ($userType) {
            /**@phpstan-ignore-next-line */
            'currentAccounts' => $entityManager->getRepository($entityClass)->paginateCurrentAccounts($page, $search, $order, $columns, $searchKeys),
            /**@phpstan-ignore-next-line */
            'archivedAccounts' => $entityManager->getRepository($entityClass)->paginateArchivedAccounts($page, $search, $order, $columns, $searchKeys),
            /**@phpstan-ignore-next-line */
            'employees' => $entityManager->getRepository($entityClass)->paginateEmployees($page, $search, $order, $columns, $searchKeys),
            /**@phpstan-ignore-next-line */
            default => $entityManager->getRepository($entityClass)->paginate($page, $search, $order, $columns, $searchKeys),
        };

        return [
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
