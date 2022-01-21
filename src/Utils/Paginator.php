<?php

namespace App\Utils;

use App\Entity\Post;
use ArrayIterator;
use Doctrine\ORM\QueryBuilder;

class Paginator
{
    private const PER_PAGE = 5;
    private ArrayIterator $posts;
    private int $numResult;
    private int $currentPage;

    public function __construct(
        private QueryBuilder $queryBuilder,
        private int $perPage = self::PER_PAGE,
    ) {}

    /**
     * @return ArrayIterator
     */
    public function getPosts(): ArrayIterator
    {
        return $this->posts;
    }

    /**
     * @return int
     */
    public function getNumResult(): int
    {
        return $this->numResult;
    }

    final public function getLastPage() {
        return (int) ceil($this->numResult / $this->perPage);
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    final public function pagination(int $page = 1 ): self
    {
        $this->currentPage = max(1, $page);
        $firstResult = ($this->currentPage - 1) * $this->perPage;

        $qb = $this->queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($this->perPage);

        $query = $qb->getQuery();
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query, true);

       // dd($paginator);

        $this->posts = $paginator->getIterator();
        $this->numResult = $paginator->count();

        return $this;
    }

    /**
     * @return int
     */
    public function hasPreviousPage(): int
    {
        return $this->currentPage > 1;
    }

    /**
     * @return mixed
     */
    public function getPreviousPage() {
        return max(1, $this->currentPage-1);
    }

    public function hasNextPage(): bool
    {
     return $this->currentPage < $this->getLastPage();
    }

    public function hasLastPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    public function hasToPaginate(): bool
    {
        return $this->numResult > $this->perPage;
    }

    public function nextPage():int
    {
        return min($this->currentPage + 1, $this->getLastPage());
    }
}