<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\HeroBadge;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;

class HeroBadgeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return HeroBadge::class;
    }

    /**
     * @param array $data
     *
     * @return HeroBadge
     */
    public function create(array $data) : HeroBadge
    {
        $logo = $data['logo'];

        if (gettype($logo) == 'string') {
            $logo_name = 'logo_' . time() . '.' . 'png';
            $logo_content = base64_decode($logo);
        } else {
            $logo_name = 'logo_' . time() . '_' . $logo->getClientOriginalName();
            $logo_content = file_get_contents($logo);
        }
        Storage::disk('dospace')->put('hero_badge_logo/' . $logo_name, $logo_content);
        Storage::setVisibility('hero_badge_logo/' . $logo_name, "public");

        return HeroBadge::create([
            'name'                => $data['name'],
            'logo'                => $logo_name,
            'description'         => $data['description'],
            'multiplier_point'    => $data['multiplier_point'],
            'maintainence_point'  => $data['maintainence_point']
        ]);
    }

    /**
     * @param HeroBadge  $heroBadge
     * @param array $data
     *
     * @return mixed
     */
    public function update(HeroBadge $heroBadge, array $data) : HeroBadge
    {
        if (request()->has('logo')) {
            $logo = $data['logo'];
            Storage::disk('dospace')->delete('hero_badge_logo/' . $heroBadge->logo);

            if (gettype($logo) == 'string') {
                $logo_name = 'logo_' . time() . '.' . 'png';
                $logo_content = base64_decode($logo);
            } else {
                $logo_name = 'logo_' . time() . '_' . $logo->getClientOriginalName();
                $logo_content = file_get_contents($logo);
            }
            Storage::disk('dospace')->put('hero_badge_logo/' . $logo_name, $logo_content);
            Storage::setVisibility('hero_badge_logo/' . $logo_name, "public");
        }

        $heroBadge->name =  isset($data['name']) ? $data['name'] : $heroBadge->name;
        $heroBadge->logo =  isset($data['logo']) ? $logo_name :  $heroBadge->logo;
        $heroBadge->description = isset($data['description']) ? $data['description'] : $heroBadge->description ;
        $heroBadge->multiplier_point = isset($data['multiplier_point']) ? $data['multiplier_point'] : $heroBadge->multiplier_point;
        $heroBadge->maintainence_point = isset($data['maintainence_point']) ? $data['maintainence_point'] : $heroBadge->maintainence_point;

        if ($heroBadge->isDirty()) {
            $heroBadge->save();
        }

        return $heroBadge->refresh();
    }

    /**
     * @param HeroBadge $heroBadge
     */
    public function destroy(HeroBadge $heroBadge)
    {
        Storage::disk('dospace')->delete('hero_badge_logo/' . $heroBadge->logo);
        $deleted = $this->deleteById($heroBadge->id);

        if ($deleted) {
            $heroBadge->save();
        }
    }
}
