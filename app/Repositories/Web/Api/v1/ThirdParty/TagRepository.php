<?php

namespace App\Repositories\Web\Api\v1\ThirdParty;

use App\Models\Tag;
use App\Repositories\BaseRepository;

class TagRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Tag::class;
    }

    /**
     * @param array $data
     *
     * @return Tag
     */
    public function create(array $data) : Tag
    {
        $string = str_replace(' ', '_', $data['name']);
        $string = str_replace('-', '_', $data['name']); 
        $name = preg_replace('/[^A-Za-z0-9\-]/', '_', $string); // Removes special chars.

        return Tag::create([
            'name' => $name,
            'merchant_id' => auth()->user()->id,
            'created_by_id' => auth()->user()->id,
            'created_by_type' => 'Merchant',
        ]);
    }

    /**
     * @param Tag  $tag
     * @param array $data
     *
     * @return mixed
     */
    public function update(Tag $tag, array $data) : Tag
    {
        if (isset($data['name'])) {
            $string = str_replace(' ', '_', $data['name']); // Replaces all spaces with hyphens.

            $name = preg_replace('/[^A-Za-z0-9\-]/', '_', $string); // Removes special chars.

        }
        $tag->name = isset($data['name']) ? $name : $tag->name;
        // $tag->merchant_id = isset($data['merchant_id']) ? $data['merchant_id'] : $tag->merchant_id;

        if($tag->isDirty()) {
            $tag->updated_by_id = auth()->user()->id;
            $tag->updated_by_type = 'Merchant';
            $tag->save();
        }

        return $tag->refresh();
    }

    /**
     * @param Tag $tag
     */
    public function destroy(Tag $tag)
    {
        $deleted = $this->deleteById($tag->id);

        foreach ($tag->product_tags as $d) {
            $del = $d->delete($d->id);
            if ($del) {
                $d->deleted_by_id = auth()->user()->id;
                $d->deleted_by_type = 'Merchant';
                $d->save();
            }
        }

        if ($deleted) {
            $tag->deleted_by_id = auth()->user()->id;
            $tag->deleted_by_type = 'Merchant';
            $tag->save();
        }
    }
}

