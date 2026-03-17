<?php

namespace App\Interface;

interface ProductCategoryInterface
{
    public function getAll(
        ?string $search,
        ?bool $isParent,
        ?int $limit,
        bool $execute,
    );

    public function getAllPaginated(
        ?string $search,
        ?bool $isParent,
        ?int $rowPerPage
    );

    public function getById(
        ?string $id
    );

    public function getBySlug(
        ?string $id
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
