<?php

namespace App\Repositories\Eloquent;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Exceptions\NotFoundException;
use App\Models\User as UserModel;

class UserEloquentRepository implements UserEntityRepositoryInterface
{
    protected $model;

    public function __construct(UserModel $model)
    {
        $this->model = $model;
    }

    public function insert(User $user): User
    {
        $dataDb = $this->model->create([
            'id' => $user->id,
            'name' => $user->name,
            'cpf_cnpj' => $user->cpfCnpj(),
            'email' => $user->email(),
            'password' => $user->password,
            'shopkeeper' => $user->shopkeeper,
            'created_at' => $user->createdAt()
        ]);

        return $this->convertToEntity($dataDb);
    }

    public function findById(string $id): User
    {
        $dataDb = $this->model->find($id);
        if (!$dataDb) {
            throw new NotFoundException("Register {$id} Not Found");
        }

        return $this->convertToEntity($dataDb);
    }

    public function getIdsListIds(array $UsersIds = []): array
    {
        return $this->model
            ->whereIn('id', $UsersIds)
            ->pluck('id')
            ->toArray();
    }

    public function getAllPaginate(string $filter = '', $order = 'DESC')
    {
        $dataDb = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('name', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('name', $order)
            ->paginate(20);

        return $dataDb;
    }

    public function getAll(string $filter = '', $order = 'DESC'): array
    {
        $dataDb = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('name', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('name', $order)
            ->get();

        return $dataDb->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15)
    {
        $query = $this->model;
        if ($filter) {
            $query = $query->where('name', 'LIKE', "%{$filter}%");
        }
        $query = $query->orderBy('name', $order);
        $dataDb = $query->paginate($totalPage);

        return $dataDb;
    }

    public function update(User $user): User
    {
        $dataDb = $this->findByIdEloquent($user->id);
        $dataDb->update([
            'name' => $user->name,
            'cpf_cnpj' => $user->cpfCnpj,
            'email' => $user->email,
            'password' => $user->password,
            'shopkeeper' => $user->shopkeeper,
            'created_at' => $user->createdAt()
        ]);

        $dataDb->refresh();
        return $this->convertToEntity($dataDb);
    }

    public function delete(string $UserId): bool
    {
        $dataDb = $this->findByIdEloquent($UserId);
        return $dataDb->delete();
    }

    private function findByIdEloquent(string $id)
    {
        return $this->model->findOrFail($id);
    }

    private function convertToEntity(UserModel $model): User
    {
        return User::restore(
            id: $model->id,
            name: $model->name,
            cpfCnpj: $model->cpf_cnpj,
            email: $model->email,
            shopkeeper: $model->shopkeeper,
            createdAt: $model->created_at
        );
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByCpfCnpj(string $cpfCnpj)
    {
        return $this->model->where('cpf_cnpj', $cpfCnpj)->first();
    }
}
