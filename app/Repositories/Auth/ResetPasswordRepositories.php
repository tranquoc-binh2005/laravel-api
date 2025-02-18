<?php
namespace App\Repositories\Auth;

use App\Repositories\BaseRepositories;
use App\Models\ResetPassword;
class ResetPasswordRepositories extends BaseRepositories
{
    protected $model;
    public function __construct(
        ResetPassword $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function findValidToken(string $refreshToken = '')
    {
        return $this->model->where('token', $refreshToken)->first();
    }

    public function updatePassword(object $object = null, string $password = ''): void
    {
        $object->update(['password' => bcrypt($password)]);
    }
}
