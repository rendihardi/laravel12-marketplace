<?php

namespace App\Interface;

interface AuthRepositoryInterface
{
    public function register(
        array $data
    );

    public function login(
        array $data
    );

    public function me();

    public function logout();
}
