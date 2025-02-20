<?php
namespace App\Services\Impl\User;

use App\Traits\Loggable;
use App\Services\Interfaces\User\UserServiceInterface;
use App\Services\Impl\BaseService;
use App\Repositories\User\UserRepositories;
use Illuminate\Http\Request;
class UserService extends BaseService implements UserServiceInterface{
    use Loggable;

    private $repositories;
    public function __construct(
        UserRepositories $repositories
    ){
        $this->repositories = $repositories;
        parent::__construct($repositories);
    }
    protected function prepareModelData(Request $request): self
    {
        //return $this->initializeBasicData($request)->handleSomething(); // truong hop neu muon handle them mot vai chuc nang rieng
        return $this->initializeBasicData($request);
    }

    protected function initializeBasicData(Request $request): self{
        $fillAble = $this->repositories->getFillAble();
        $this->modelData = $request->only($fillAble);
        return $this;
    }

    protected function handleSomething(): self{
        $this->modelData['something'] = 'something';
        return $this;
    }
}
