<?php
namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Traits\Loggable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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


    public function baseSave(Request $request, ?int $id = null): JsonResponse
    {
        try {
            $response = $this->service->save($request, $id);
            $resource = new $this->resource($response);
            return ApiResource::ok($resource);
        } catch (\Exception $e) {
            $this->handleLogException($e);
        }
    }


}

