<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Badge;
use App\Repositories\BaseRepository;

class BadgeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Badge::class;
    }

    /**
     * @param array $data
     *
     * @return Badge
     */
    public function create(array $data) : Badge
    {
        return Badge::create([
            'name' => $data['name'],
            'logo' => $data['logo'],
            'description' => $data['description'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param Badge  $badge
     * @param array $data
     *
     * @return mixed
     */
    public function update(Badge $badge, array $data) : Badge
    {
        $badge->name = $data['name'];
        $badge->logo = $data['logo'];
        $badge->description = $data['description'];

        if($badge->isDirty()) {
            $badge->updated_by = auth()->user()->id;
            $badge->save();
        }

        return $badge->refresh();
    }

    /**
     * @param Badge $badge
     */
    public function destroy(Badge $badge)
    {
        $deleted = $this->deleteById($badge->id);

        if ($deleted) {
            $badge->deleted_by = auth()->user()->id;
            $badge->save();
        }
    }
}

