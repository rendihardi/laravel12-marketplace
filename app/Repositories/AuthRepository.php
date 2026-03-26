<?php

namespace App\Repositories;

use App\Interface\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

            // 🔥 buat token
            $user->token = $user->createToken('auth_token')->plainTextToken;

            // 🔥 load relasi
            $user->load(['roles.permissions', 'buyer', 'store']);

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
            $user = User::with(['roles.permissions', 'store', 'buyer'])
                ->where('email', $data['email'])
                ->first();

            if (! $user || ! Hash::check($data['password'], $user->password)) {
                throw new \Exception('Email atau password salah', 401);
            }

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

            $user = User::with(['roles', 'permissions', 'store', 'buyer'])->find(Auth::id());
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
