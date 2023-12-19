<?php
namespace App\Sourcing\Repository;

use Pagerfanta\Pagerfanta;
use App\Sourcing\Exception\EntityNotFoundException;

/**
 * @template T
 * @template ID
 */
interface RepositoryInterface{

    /**
     * @param int $page
     * @param int $limit
     * @param array $criteria
     * @param array $orderBy
     * @return Pagerfanta<T>
     */
    public function paginate(
        int $page = 1,
        int $limit = 10,
        array $criteria = [],
        array $orderBy = []
    ): Pagerfanta;

    /**
     * @param ID $id
     * @return T
     * @throws EntityNotFoundException
     */
    
    public function getById(string|int $id): mixed;
    
}