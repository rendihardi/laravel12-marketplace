<?php

namespace App\Interface;

interface ProductImageInterface
{
    public function create(array $data);

    // public function update(array $data, ?string $id);

    public function delete(?string $id);
}
