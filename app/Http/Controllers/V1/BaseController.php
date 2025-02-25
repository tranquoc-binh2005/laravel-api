<?php
namespace App\Http\Controllers\V1;

use App\Enum\Config\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;

class BaseController extends Controller
{

    use Loggable;
    private $service;
    private $resource;

    public function __construct(
        $service, $resource
    )
    {
        $this->service = $service;
        $this->resource = $resource;
    }

    public function baseIndex(Request $request)
    {
        try {
            $response = $this->service->paginate($request);

            return ApiResource::ok($response, Common::SUCCESS);
        } catch (\Exception $e) {
            return $this->handleLogException($e);
        }
    }


    public function baseSave(Request $request, ?int $id = null): JsonResponse
    {
        try {
            $response = $this->service->save($request, $id);
            $resource = new $this->resource($response);
            return ApiResource::ok($resource, Common::SUCCESS);
        } catch (ModelNotFoundException $e) {
            return ApiResource::messages($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleLogException($e);
        }
    }

    public function baseShow(int $id): JsonResponse
    {
        try {
            $response = $this->service->show($id);
            $resource = new $this->resource($response);
            return ApiResource::ok($resource, Common::SUCCESS);
        } catch (ModelNotFoundException $e) {
            return ApiResource::messages($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleLogException($e);
        }
    }

    public function baseDestroy(int $id): JsonResponse
    {
        try {
            $response = $this->service->destroy($id);
            return ApiResource::messages(Lang::get('message.delete_success'));
        } catch (ModelNotFoundException $e) {
            return ApiResource::messages($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleLogException($e);
        }
    }
}

