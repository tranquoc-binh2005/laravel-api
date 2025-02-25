<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
trait HasQuery
{
    public function scopeKeyword($query, array $keyword = [])
    {
        if (!empty($keyword['q'])) {
            foreach ($keyword['field'] as $field) {
                $query->orWhere($field, 'like', '%' . $keyword['q'] . '%');
            }
        }
        return $query;
    }

    public function scopeSimpleFilter($query, array $simpleFilter = [])
    {
        if(count($simpleFilter)){
            foreach ($simpleFilter as $key => $val){
                if($val !== 0 && !empty($val) && !is_null($val)){
                    $query->where($key, $val);
                }
            }
        }
        return $query;
    }

    public function scopeComplexFilter($query, array $complexFilter = [])
    {
        if(count($complexFilter)){
            foreach ($complexFilter as $field => $conditions){
                foreach($conditions as $operator => $val){
                    switch ($operator) {
                        case 'gt':
                            $query->where($field, '>', $val);
                            break;
                        case 'gte':
                            $query->where($field, '>=', $val);
                            break;
                        case 'lt':
                            $query->where($field, '<', $val);
                            break;
                        case 'lte':
                            $query->where($field, '<=', $val);
                            break;
                        case 'eq':
                            $query->where($field, '=', $val);
                            break;
                        case 'between':
                            [$min, $max] = explode(',', $val);
                            $query->whereBetween($field, [$min, $max]);
                            break;
                        case 'in':
                            [$field, $in] = explode('|', $val);
                            $whereIn = explode(',', $in);
                            if(count($whereIn)){
                                $query->whereIn($field, $whereIn);
                            }
                            break;
                    }
                }
            }
        }
        return $query;
    }

    public function scopeDataFilter($query, array $dateFilter = [])
    {
        if(count($dateFilter)){
            foreach ($dateFilter as $field => $conditions){
                foreach($conditions as $operator => $date){
                    switch ($operator) {
                        case 'gt':
                            $query->whereDate($field, '>', Carbon::parse($date)->startOf());
                            break;
                        case 'gte':
                            $query->whereDate($field, '>=', Carbon::parse($date)->startOf());
                            break;
                        case 'lt':
                            $query->whereDate($field, '<', Carbon::parse($date)->startOf());
                            break;
                        case 'lte':
                            $query->whereDate($field, '<=', Carbon::parse($date)->startOf());
                            break;
                        case 'eq':
                            $query->whereDate($field, '=', Carbon::parse($date)->startOf());
                            break;
                        case 'between':
                            [$startDate, $endDate] = explode(',', $date);
                            $query->whereBetween($field, [
                                Carbon::parse($startDate)->startOf(),
                                Carbon::parse($endDate)->endOfDay(),
                            ]);
                            break;
                    }
                }
            }
        }
        return $query;
    }
}
