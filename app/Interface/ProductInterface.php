<?php

namespace App\Interface;

interface ProductInterface
{
    public function getAll(
        ?string $search,
        ?string $productCategoryid,
        ?int $limit,
        bool $execute,
    );

    public function getAllPaginated(
        ?string $search,
        ?string $productCategoryid,
        ?int $rowPerPage
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
