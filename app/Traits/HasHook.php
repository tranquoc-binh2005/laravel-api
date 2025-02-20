<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;
trait HasHook
{
    protected function beginTransaction(): self{
        DB::beginTransaction();
        return $this;
    }

    protected function commitTransaction(): self{
        DB::commit();
        return $this;
    }

    protected function rollbackTransaction(): self{
        DB::rollBack();
        return $this;
    }

    public function saveModel(?int $id = null): self{
        if($id){
            $this->model = $this->baseRepository->update($id, $this->modelData);
        } else{
            $this->model = $this->baseRepository->create($this->modelData);
        }
        $this->result = $this->model;
        return $this;
    }

    protected function beforeSave(): self{
        return $this;
    }

    protected function handleRelations(): self{
        return $this;
    }

    protected function afterSave(): self{
        return $this;
    }

    protected function getResult(): mixed{
        return $this->result;
    }
}
