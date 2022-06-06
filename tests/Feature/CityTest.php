<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\City;
use App\Models\Staff;
use App\Repositories\Web\Api\v1\CityRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_can_create_city()
    {
        // $this->withoutExceptionHandling();

        $staff = factory(Staff::class)->states('department_id', 'zone_id', 'courier_type_id')->create();

        $data = [
            'name' => 'Test',
            'delivery_rate' => 123,
            'is_active' => 1,
            'locking' => 0,
            'locked_by' => $staff->id,
        ];

        $cityRepository = new CityRepository(new City());
        $city = $cityRepository->create($data);

        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals($data['name'], $city->name);
        $this->assertEquals($data['delivery_rate'], $city->delivery_rate);
        $this->assertEquals($data['is_active'], $city->is_active);
        $this->assertEquals($data['locking'], $city->locking);
        $this->assertEquals($data['locked_by'], $city->locked_by);
    }

    /** @test */
    public function test_can_show_city()
    {
        $city = factory(City::class)->states('locked_by')->create();
        $cityRepository = new CityRepository(new City());
        $found = $cityRepository->getById($city->id);

        $this->assertInstanceOf(City::class, $found);
        $this->assertEquals($found->name, $city->name);
        $this->assertEquals($found->delivery_rate, $city->delivery_rate);
        $this->assertEquals($found->is_active, $city->is_active);
        $this->assertEquals($found->locking, $city->locking);
        $this->assertEquals($found->locked_by, $city->locked_by);
    }

    /** @test */
    public function test_can_update_city()
    {
        $city = factory(City::class)->states('locked_by')->create();
        $staff = factory(Staff::class)->states('department_id', 'zone_id', 'courier_type_id')->create();

        $data = [
            'name' => 'Test',
            'delivery_rate' => 123,
            'is_active' => 1,
            'locking' => 0,
            'locked_by' => $staff->id,
        ];

        $cityRepository = new CityRepository(new City());
        $updatedCity = $cityRepository->update($city, $data);

        $this->assertInstanceOf(City::class, $updatedCity);
        $this->assertEquals($data['name'], $updatedCity->name);
        $this->assertEquals($data['delivery_rate'], $updatedCity->delivery_rate);
        $this->assertEquals($data['is_active'], $updatedCity->is_active);
        $this->assertEquals($data['locking'], $updatedCity->locking);
        $this->assertEquals($data['locked_by'], $updatedCity->locked_by);
    }

    /** @test */
    public function test_can_delete_city()
    {
        $city = factory(City::class)->states('locked_by')->create();
        $cityRepository = new CityRepository(new City());
        $delete = $cityRepository->deleteById($city->id);

        $this->assertTrue($delete);
    }
}
