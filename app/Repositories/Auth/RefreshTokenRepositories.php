<?php
namespace App\Repositories\Auth;

use App\Repositories\BaseRepositories;
use App\Models\RefreshToken;
class RefreshTokenRepositories extends BaseRepositories
{
    protected $model;
    public function __construct(
        RefreshToken $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function findValidRefreshToken(string $refreshToken = '')
    {
        return $this->model
            ->where('refresh_token', $refreshToken)
            ->whereDate('expires_at', '>', now())
            ->where('is_revoked', false)->first();
    }

    public function revokeAllUserRefreshTokenAll(int $userId): bool
    {
        return $this->model->where('user_id', $userId)
            ->where('is_revoked', false)->update(['is_revoked' => true]);
    }
}
