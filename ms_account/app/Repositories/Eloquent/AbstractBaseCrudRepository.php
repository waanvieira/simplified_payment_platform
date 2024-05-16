<?php

namespace App\Repositories\Eloquent;

use App\Domain\Repositories\BaseCrudInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractBaseCrudRepository implements BaseCrudInterface
{
    protected $model;

    abstract public function model(): Model;

    public function __construct()
    {
        $this->model = $this->model();
    }

    public function insert(array $input): Model
    {
        return $this->model->create($input);
    }

    public function findById(string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function update(array $input, string $id): Model
    {
        $response = $this->findById($id);
        $response->update($input);
        $response->refresh();

        return $response;
    }

    public function delete(string $id): bool
    {
        $response = $this->findById($id);

        return $response->delete();
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

    public function getAllPaginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15)
    {
        $query = $this->model;
        if ($filter) {
            $query = $query->where('name', 'LIKE', "%{$filter}%");
        }
        $query = $query->orderBy('name', $order);
        $dataDb = $query->paginate($totalPage);

        return $dataDb;
    }
}
