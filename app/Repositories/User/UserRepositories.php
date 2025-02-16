<?php
namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepositories;
class UserRepositories extends BaseRepositories
{
    protected $model;
    public function __construct(
        User $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }
}
