<?php
namespace App\Http\Controllers\V1\User;

use App\Http\Requests\User\Catalogue\UpdateRequest;
use App\Traits\Loggable;
use App\Http\Requests\User\Catalogue\StoreRequest;
use App\Http\Controllers\V1\BaseController;
use App\Services\Interfaces\User\UserCatalogueServiceInterface as UserCatalogueService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\User\UserCatalogueResource;
use Illuminate\Http\Request;

class UserCatalogueController extends BaseController {

    use Loggable;

    protected UserCatalogueService $userCatalogueService;

    public function __construct(
        UserCatalogueService $userCatalogueService
    ){
        $this->userCatalogueService = $userCatalogueService;
        parent::__construct(
            $userCatalogueService,
            UserCatalogueResource::class
        );
    }

    public function index(Request $request): JsonResponse
    {
        return $this->baseIndex($request);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->baseSave($request);
    }

    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        return $this->baseSave($request, $id);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        return $this->baseShow($id);
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->baseDestroy($id);
    }
}
