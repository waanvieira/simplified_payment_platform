<?php

namespace App\Repositories\Eloquent;

use App\Domain\Entities\Account;
use App\Domain\Entities\Entity;
use App\Domain\Repositories\AccountEntityRepositoryInterface;
use App\Domain\ValueObjects\CpfCnpj;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Uuid;
use App\Exceptions\NotFoundException;
use App\Models\Account as ModelAccount;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class AccountEloquentRepository implements AccountEntityRepositoryInterface
{
    protected $model;

    public function __construct(ModelAccount $model)
    {
        $this->model = $model;
    }

    public function insert(Entity $entity): Entity
    {
        $dataDb = $this->model->create([
            'id' => $entity->id,
            'name' => $entity->name,
            'cpf_cnpj' => $entity->cpfCnpj(),
            'email' => $entity->email(),
            'password' => $entity->password,
            'shopkeeper' => $entity->shopkeeper,
            'balance' => $entity->balance,
            'created_at' => $entity->createdAt(),
        ]);

        return $this->convertObjectToEntity($dataDb);
    }

    public function findById(string $id): Entity
    {
        $dataDb = $this->model->find($id);
        if (! $dataDb) {
            throw new NotFoundException("Register {$id} Not Found");
        }

        return $this->convertObjectToEntity($dataDb);
    }

    public function getIdsListIds(array $entitysIds = []): array
    {
        return $this->model
            ->whereIn('id', $entitysIds)
            ->pluck('id')
            ->toArray();
    }

    public function getAllPaginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15)
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

    public function update(Entity $entity): Entity
    {
        $dataDb = $this->findByIdEloquent($entity->id);
        $dataDb->update([
            'name' => $entity->name,
            'cpf_cnpj' => $entity->cpfCnpj,
            'email' => $entity->email,
            'password' => $entity->password,
            'shopkeeper' => $entity->shopkeeper,
            'balance' => $entity->balance,
            'created_at' => $entity->createdAt(),
        ]);

        $dataDb->refresh();

        return $this->convertObjectToEntity($dataDb);
    }

    public function delete(string $entityId): bool
    {
        $dataDb = $this->findByIdEloquent($entityId);

        return $dataDb->delete();
    }

    private function findByIdEloquent(string $id)
    {
        return $this->model->find($id);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByCpfCnpj(string $cpfCnpj)
    {
        return $this->model->where('cpf_cnpj', $cpfCnpj)->first();
    }

    private function convertObjectToEntity(Model $model): Entity
    {
        return Account::restore(
            id: new Uuid($model->id),
            name: $model->name,
            cpfCnpj: new CpfCnpj($model->cpf_cnpj),
            email: new Email($model->email),
            shopkeeper: $model->shopkeeper,
            balance: $model->balance,
            createdAt: new DateTime($model->created_at)
        );
    }
}
