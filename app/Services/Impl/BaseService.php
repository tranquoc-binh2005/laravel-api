<?php
namespace App\Services\Impl;
use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HasHook;
abstract  class BaseService implements BaseServiceInterface{

    use HasHook;

    protected array $modelData;
    protected Model $model;
    protected mixed $result;
    protected $baseRepository;

    public function __construct(
        $baseRepository
    ){
        $this->baseRepository = $baseRepository;
    }

    protected abstract function prepareModelData(Request $request): self;

    /**
     * @throws \Exception
     */
    public function save(Request $request, ?int $id = null)
    {
        try {
            return $this->beginTransaction()
                        ->prepareModelData($request)
                        ->beforeSave()
                        ->saveModel($id)
                        ->handleRelations()
                        ->afterSave()
                        ->commitTransaction()
                        ->getResult();
        } catch(\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }
}
