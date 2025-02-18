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

    public function findById(int $id = 0, array $relations = []): mixed
    {
        return $this->model->with($relations)->find($id);
    }

    public function findByEmail(string $email, array $relations = []): mixed
    {
        return $this->model->with($relations)->where('email', $email)->firstOrFail();
    }
}
