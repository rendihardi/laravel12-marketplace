<?php

namespace App\Interface;

use Illuminate\Http\UploadedFile;

interface WithdrawalInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
    );

    public function getAllPaginated(
        ?string $search,
        ?int $rowPerPage
    );

    public function getById(
        ?string $id
    );

    public function create(
        array $data
    );

    public function approve(
        ?string $id,
        ?UploadedFile $proof
    );
}
