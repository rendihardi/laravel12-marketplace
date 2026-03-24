<?php

namespace App\Repositories;

use App\Interface\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(array $data)
    {
        DB::beginTransaction();
        try {
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();
            $user->assignRole($data['role']);

            if ($data['role'] == 'buyer') {
                $user->buyer()->create([
                    'name' => null,
                    'phone' => null,
                ]);
            }
            $user->token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function login(array $data)
    {
        DB::beginTransaction();

        try {
            if (! Auth::guard('web')->attempt($data)) {
                throw new \Exception('Unauthorized', 401);
            }

            $user = Auth::user();
            $user->token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function me()
    {
        try {
            if (! Auth::check()) {
                throw new \Exception('Unauthorized', 401);
            }

            $user = Auth::user();
            $user->permissions = $user->roles
                ->flatMap->permissions
                ->pluck('name');

            return $user;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function logout()
    {
        DB::beginTransaction();
        try {
            if (! Auth::check()) {
                throw new \Exception('Unauthorized', 401);
            }

            $user = Auth::user();
            $user->tokens()->delete();

            DB::commit();

            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
