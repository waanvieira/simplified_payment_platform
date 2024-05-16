<?php

namespace App\Repositories\Eloquent;

use App\Domain\Entities\NewsLetter;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\Domain\ValueObjects\Uuid;
use App\Models\NewsLetter as NewsLetterModel;
use Illuminate\Database\Eloquent\Model;

class NewsLetterEloquentRepository implements NewsletterEntityRepositoryInterface
{
    protected $model;

    public function __construct(NewsLetterModel $model)
    {
        $this->model = $model;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function insert(NewsLetter $newsLetter): NewsLetter
    {
        $dataDb = $this->model->create([
            'id' => $newsLetter->id,
            'name' => $newsLetter->name,
            'description' => $newsLetter->description,
            'created_at' => $newsLetter->createdAt,
        ]);

        return $this->convertToEntity($dataDb);
    }

    public function findById(string $NewsLetterId): NewsLetter
    {
        $dataDb = $this->model()->findOrFail($NewsLetterId);

        return $this->convertToEntity($dataDb);
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

    public function getAll(string $filter = '', $order = 'DESC'): array
    {
        $query = $this->model;
        if ($filter) {
            $query = $query->where('name', 'LIKE', "%{$filter}%");
        }
        $query = $query->orderBy('name', $order);

        return $query->all()->toArray();
    }

    public function update(NewsLetter $newsLetter): NewsLetter
    {
        $newsLetterDb = $this->model->findOrFail($newsLetter->id);
        $newsLetterDb->update([
            'name' => $newsLetterDb->name,
            'description' => $newsLetterDb->description,
        ]);
        $newsLetterDb->refresh();

        return $this->convertToEntity($newsLetterDb);
    }

    public function delete(string $NewsLetterId): bool
    {
        $newsLetterDb = $this->model->findOrFail($NewsLetterId);

        return $newsLetterDb->delete();
    }

    public function registerUserOnList(string $newsLetterId, string $idUser): void
    {
        $newsletter = $this->model->find($newsLetterId);

        if (! $newsletter->users->where('id', $idUser)->first()) {
            $newsletter->users()->attach($idUser);
        }
    }

    private function convertToEntity(NewsLetterModel $model): NewsLetter
    {
        return NewsLetter::restore(
            id: new Uuid($model->id),
            name: $model->name,
            description: $model->description,
            createdAt: $model->created_at
        );
    }
}
