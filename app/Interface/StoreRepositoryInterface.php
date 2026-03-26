<?php

namespace App\Interface;

interface StoreRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?bool $isVerified,
        ?int $limit,
        bool $execute,
    );

    public function getAllPaginated(
        ?string $search,
        ?bool $isVerified,
        ?int $rowPerPage
    );

    public function getById(
        ?string $id
    );

    // public funtion getByUsername(
    //     ?string $username
    // )

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

    public function updateVerifiedStatus(
        string $id,
        ?bool $isVerified
    );
}
