<?php

namespace App\Infrastructure\Symfony\Repository\Trait;

use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
trait PaginationTrait
{
    private function paginate(ORMQueryBuilder $qb, int $page, int $limit): array {
        $result = [];
        $paginator = new Paginator($qb);
        $data = $paginator->getQuery()->getResult();
        if(empty($data)) {
            return $result;
        }

        $total = $paginator->count();

        // on calcule le nombre de résultats
        $pages = ceil($total / $limit);
        $result = [
            'data' => $data,
            'total' => $paginator->count(),
            'pages' => $pages,
            'page' => $page,
            'limit' => $limit
        ];

        return $result;
    }
}