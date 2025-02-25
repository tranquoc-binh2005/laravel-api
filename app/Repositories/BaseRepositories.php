<?php

namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;
class BaseRepositories
{
    protected $model;
    public function __construct(
        Model $model
    )
    {
        $this->model = $model;
    }

    public function create(array $payload = []): Model
    {
        return $this->model->create($payload)->fresh();
    }

    public function update(int $id, array $payload = []): Model
    {
        $model = $this->findById($id);
        $model->fill($payload);
        $model->save();
        return $model;
    }
    public function findById(int $id = 0, array $relations = []): Model | null
    {
        return $this->model->with($relations)->find($id);
    }

    public function findByEmail(string $email, array $relations = []): mixed
    {
        return $this->model->with($relations)->where('email', $email)->firstOrFail();
    }

    public function getFillAble(): array
    {
        return $this->model->getFillable();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function paginate(array $specifications = [])
    {
        return $this->model
            ->keyword($specifications['keyword'] ?? [])
            ->simpleFilter($specifications['filters']['simple'])
            ->complexFilter($specifications['filters']['complex'])
            ->dateFilter($specifications['filters']['date'])
            ->orderBy($specifications['sort'][0], $specifications['sort'][1])
            ->when($specifications['type'],
                fn($q) => $q->get(),
                fn($q) => $q->paginate($specifications['perpage'])
            );
    }
}
