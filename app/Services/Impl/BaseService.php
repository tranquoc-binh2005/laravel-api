<?php
namespace App\Services\Impl;
use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HasHook;
use Illuminate\Support\Facades\Lang;

abstract  class BaseService implements BaseServiceInterface{

    use HasHook;

    protected $modelData;
    protected $model;
    protected $result;
    protected $baseRepository;
    protected const PERPAGE = 15;

    protected $fieldSearch = ['name'];
    protected $simplerFilter = ['publish'];
    protected $complexFilter = ['id'];
    protected $dateFilter = ['created_at', 'updated_at'];

    public function __construct(
        $baseRepository
    ){
        $this->baseRepository = $baseRepository;
    }

    protected abstract function prepareModelData(Request $request): self;

    public function buildFilter(Request $request, array $filters = []): array
    {
        $conditions = [];
        if(count($filters)){
            foreach ($filters as $filter){
                if($request->has($filter)){
                    $conditions[$filter] = $request->{$filter};
                }
            }
        }
        return $conditions;
    }

    public function specifications($request): array
    {
        return [
            'type' => $request->type === 'all',
            'perpage' => $request->perpage ?? self::PERPAGE,
            'sort' => $request->sort ? explode(',', $request->sort) : ['id', 'DESC'],
            'keyword' => [
                'q' => $request->keyword,
                'field' => $this->fieldSearch,
            ],
            'filters' => [
                'simple' => $this->buildFilter($request, $this->simplerFilter),
                'complex' => $this->buildFilter($request, $this->complexFilter),
                'date' => $this->buildFilter($request, $this->dateFilter),
            ]
        ];
    }
    public function paginate(Request $request)
    {
        $specifications = $this->specifications($request);
        return $this->baseRepository->paginate($specifications);
    }

    /**
     * @throws \Exception
     */
    public function save(Request $request, ?int $id = null): Model
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
        } catch (ModelNotFoundException $e) {
            $this->rollbackTransaction();
            throw new ModelNotFoundException($e);
        } catch(\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    public function show(int $id = 0): Model
    {
        try {
            if(!$this->model = $this->baseRepository->findById($id)){
                throw new ModelNotFoundException(Lang::get('message.not_found'));
            }
            return $this->model;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e);
        } catch(\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function destroy(int $id = 0): bool
    {
        try {
            if(!$this->model = $this->baseRepository->findById($id)){
                throw new ModelNotFoundException(Lang::get('message.not_found'));
            }
            return $this->baseRepository->delete($this->model);
        } catch(\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }
}
