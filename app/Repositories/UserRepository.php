<?php

namespace App\Repositories;

use App\Interface\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    ) {
        $query = User::latest()->where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->with('roles');

        if ($limit && $limit > 0) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;

    }

    public function getAllPaginated(?string $search, ?int $row_per_page)
    {
        $query = $this->getAll($search, $row_per_page, false);

        return $query->paginate($row_per_page);

    }

    public function getById(?string $id)
    {
        return User::with('roles')->find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();
            DB::commit();

            return $user->load('roles');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, ?string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            $user->name = $data['name'];
            if (isset($data['password'])) {
                $user->password = bcrypt($data['password']);
            }
            $user->save();
            DB::commit();

            return $user->load('roles');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(?string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            $user->delete();
            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
