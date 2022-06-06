<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceMeta;
use App\Repositories\BaseRepository;

class FinanceMetaRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceMeta::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceMeta
     */
    public function create(array $data) : FinanceMeta
    {
        return FinanceMeta::create([
            'label' => $data['label'],
            'model' => $data['model'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceMeta  $finance_meta
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceMeta $finance_meta, array $data) : FinanceMeta
    {
        $finance_meta->label = $data['label'];
        $finance_meta->model = isset($data['model']) ? $data['model'] : $finance_meta->model;
    
        if($finance_meta->isDirty()) {
            $finance_meta->updated_by = auth()->user()->id;
            $finance_meta->save();
        }

        return $finance_meta->refresh();
    }

    /**
     * @param FinanceMeta $finance_meta
     */
    public function destroy(FinanceMeta $finance_meta)
    {
        $deleted = $this->deleteById($finance_meta->id);

        if ($deleted) {
            $finance_meta->deleted_by = auth()->user()->id;
            $finance_meta->save();
        }
    }
}

