<?php

namespace App\Repositories\Eloquent;

use App\Domain\Entities\Message;
use App\Domain\Repositories\MessageEntityRepositoryInterface;
use App\Models\Message as MessageModel;
use Illuminate\Database\Eloquent\Model;

class MessageEloquentRepository extends AbstractDtoModelRepository implements MessageEntityRepositoryInterface
{
    protected $model;

    public function __construct(MessageModel $model)
    {
        $this->model = $model;
    }

    public function insert(Message $message): Message
    {
        $dataDb = $this->model->create([
            'id' => $message->id,
            'newsletter_id' => $message->newsLetterId,
            'title' => $message->title,
            'message' => $message->message,
            'created_at' => $message->createdAt,
        ]);

        return $this->convertToEntity($dataDb);
    }

    public function delete(string $id): bool
    {
        $messageDb = $this->model->findOrFail($id);
        return $messageDb->delete();
    }

    public function findById(string $id): Message
    {
        $dataDb = $this->model->findOrFail($id);
        return $this->convertToEntity($dataDb);
    }

    public function update(Message $message): Message
    {
        $messageDb = $this->model->findOrFail($message->id);
        $messageDb->update([
            'title' => $message->title,
            'message' => $message->message,
            'newsLetterId' => $message->newsLetterId,
        ]);
        $messageDb->refresh();
        return $this->convertToEntity($messageDb);
    }

    public function getAll(string $filter = '', $order = 'DESC'): array
    {
        $dataDb = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('title', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('title', $order)
            ->get();

        return $dataDb->toArray();
    }

    public function getAllPaginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15)
    {
        $query = $this->model;
        if ($filter) {
            $query = $query->where('title', 'LIKE', "%{$filter}%");
        }
        $query = $query->orderBy('title', $order);
        $dataDb = $query->paginate($totalPage);

        return $dataDb;
    }

    public function model(): Model
    {
        return $this->model;
    }

    private function convertToEntity(MessageModel $model): Message
    {
        return Message::restore(
            id: $model->id,
            title: $model->title,
            message: $model->message,
            newsLetterId: $model->newsletter_id,
            createdAt: $model->created_at
        );
    }
}
