<?php

namespace App\Interface;

interface ProductInterface
{
    public function getAll(
        ?string $search,
        ?string $storeId,
        ?string $productCategoryid,
        ?bool $random,
        ?int $limit,
        bool $execute,
        ?string $condition = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?string $sortBy = null
    );

    public function getAllPaginated(
        ?string $search,
        ?string $storeId,
        ?string $productCategoryid,
        ?bool $random,
        ?int $rowPerPage,
        ?string $condition = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?string $sortBy = null
    );

    public function getById(
        ?string $id
    );

    public function getBySlug(
        ?string $slug
    );

    public function create(
        array $data
    );

    public function update(
        array $data,
        ?string $id
    );

    public function delete(
        ?string $id
    );
}
