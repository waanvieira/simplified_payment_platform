<?php

namespace App\Repositories\Eloquent;

use App\Domain\Entities\Entity;
use App\Domain\Entities\Transaction;
use App\Domain\Enum\TransactionStatus;
use App\Domain\Enum\TransactionType;
use App\Domain\Repositories\TransactionEntityRepositoryInterface;
use App\Domain\ValueObjects\Uuid;
use App\Exceptions\NotFoundException;
use App\Models\Transaction as TransactionModel;
use Illuminate\Database\Eloquent\Model;

class TransactionEloquentRepository implements TransactionEntityRepositoryInterface
{
    public function __construct(
        protected TransactionModel $model
    ) {
        $this->model = $model;
    }

    public function insert(Entity $entity): Entity
    {
        $dataDb = $this->model->create([
            "id" => $entity->id(),
            "transaction_type" => $entity->transactionType(),
            "payer_id" => $entity->payerId(),
            "payee_id" => $entity->payeeId(),
            "value" => $entity->value,
            "transaction_status" => $entity->transactionStatus(),
            "created_at" => $entity->createdAt()
        ]);

        return $this->convertObjectToEntity($dataDb);
    }

    public function findById(string $id): Entity
    {
        $dataDb = $this->model->find($id);
        if (!$dataDb) {
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
        $dataDb = $this->findByIdEloquent($entity->id());
        $dataDb->update([
            "id" => $entity->id(),
            "transaction_type" => $entity->transactionType(),
            "payer_id" => $entity->payerId(),
            "payee_id" => $entity->payeeId(),
            "value" => $entity->value,
            "transaction_status" => $entity->transactionStatus(),
            "confirmation_at" => $entity->confirmationAt()
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
        $transaction = $this->model->find($id);
        if (!$transaction) {
            throw new NotFoundException("Transaction {$id} not found!");
        }

        return $transaction;
    }


    public function updateBalance(Entity $entity): bool
    {
        $dataDb = $this->findByIdEloquent($entity->id);
        return $dataDb->update([
            'balance' => $entity->balance,
        ]);
    }

    public function transactionAproved(Transaction $transaction): bool
    {
        return false;
    }

    public function transactionReproved(Transaction $transaction): bool
    {
        return false;
    }

    private function convertObjectToEntity(Model $model): Transaction
    {
        return Transaction::restore(
            id: new Uuid($model->id),
            transactionType: TransactionType::TRANSFER,
            payerId: new Uuid($model->payer_id),
            payeeId: new Uuid($model->payee_id),
            value: $model->value,
            transactionStatus: TransactionStatus::PROCESSING,
            createdAt: $model->created_at
        );
    }
}
