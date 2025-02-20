<?php
namespace App\Services\Impl\User;

use App\Traits\Loggable;
use App\Services\Interfaces\User\UserCatalogueServiceInterface;
use App\Services\Impl\BaseService;
use App\Repositories\User\UserCatalogueRepositories;
use Illuminate\Http\Request;
class UserCatalogueService extends BaseService implements UserCatalogueServiceInterface{
    use Loggable;

    private $repositories;
    public function __construct(
        UserCatalogueRepositories $repositories
    ){
        $this->repositories = $repositories;
        parent::__construct($repositories);
    }
    protected function prepareModelData(Request $request): self
    {
        return $this->initializeBasicData( $request);
    }

    protected function initializeBasicData(Request $request): self{
        $fillAble = $this->repositories->getFillAble();
        $this->modelData = $request->only($fillAble);
        return $this;
    }
}
