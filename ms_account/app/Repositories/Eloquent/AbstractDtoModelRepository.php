<?php

namespace App\Repositories\Eloquent;

use App\Domain\Repositories\BaseDtoInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractDtoModelRepository implements BaseDtoInterface
{
    protected $model;

    abstract public function model(): Model;

    public function __construct()
    {
        $this->model = $this->model();
    }

    public function insertModel(array $input): Model
    {
        return $this->model->create($input);
    }

    public function findByIdModel(string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function updateModel(array $input, string $id): Model
    {
        $response = $this->findByIdModel($id);
        $response->update($input);
        $response->refresh();

        return $response;
    }

    public function delete(string $id): bool
    {
        $response = $this->findByIdModel($id);

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
