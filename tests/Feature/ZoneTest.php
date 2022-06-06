<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\Zone;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Web\Api\v1\ZoneRepository;

class ZoneTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function test_can_create_zone()
    {
        $city = factory(City::class)->states('locked_by')->create();

        $data = [
            'name' => 'Test',
            'delivery_rate' => 123, 
            'city_id' => $city->id
        ];

        $zoneRepository = new ZoneRepository(new Zone);
        $zone = $zoneRepository->create($data);

        $this->assertInstanceOf(Zone::class, $zone);
        $this->assertEquals($data['name'], $zone->name);
        $this->assertEquals($data['delivery_rate'], $zone->delivery_rate);
        $this->assertEquals($data['city_id'], $zone->city_id);
    }

    /** @test */
    public function test_can_show_zone()
    {
        $zone = factory(Zone::class)->states('city_id')->create();
        $zoneRepository = new ZoneRepository(new Zone);
        $found = $zoneRepository->getById($zone->id);

        $this->assertInstanceOf(Zone::class, $found);
        $this->assertEquals($found->name, $zone->name);
        $this->assertEquals($found->delivery_rate, $zone->delivery_rate);
        $this->assertEquals($found->city_id, $zone->city_id);
    }

    /** @test */
    public function test_can_update_zone()
    {
        $city = factory(City::class)->states('locked_by')->create();
        $zone = factory(Zone::class)->states('city_id')->create();

        $data = [
            'name' => 'Test',
            'delivery_rate' => 123, 
            'city_id' => $city->id
        ];

        $zoneRepository = new ZoneRepository(new Zone);
        $updatedZone = $zoneRepository->update($zone, $data);

        $this->assertInstanceOf(Zone::class, $updatedZone);
        $this->assertEquals($data['name'], $updatedZone->name);
        $this->assertEquals($data['delivery_rate'], $updatedZone->delivery_rate);
        $this->assertEquals($data['city_id'], $updatedZone->city_id);
    }

    /** @test */
    public function test_can_delete_zone()
    {
        $zone = factory(Zone::class)->states('city_id')->create();
        $zoneRepository = new ZoneRepository(new Zone);
        $delete = $zoneRepository->deleteById($zone->id);

        $this->assertTrue($delete);
    }
}
