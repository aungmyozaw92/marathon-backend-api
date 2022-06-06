<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\StoreStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Web\Api\v1\StoreStatusRepository;

class StoreStatusTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function test_can_create_store_status()
    {
        $data = [
            'status' => 'Test',
            'status_mm' => 'test mm',
        ];

        $storeStatusRepository = new StoreStatusRepository(new StoreStatus);
        $storeStatus = $storeStatusRepository->create($data);

        $this->assertInstanceOf(StoreStatus::class, $storeStatus);
        $this->assertEquals($data['status'], $storeStatus->status);
        $this->assertEquals($data['status_mm'], $storeStatus->status_mm);
    }

    /** @test */
    public function test_can_show_store_status()
    {
        $storeStatus = factory(StoreStatus::class)->create();
        $storeStatusRepository = new StoreStatusRepository(new StoreStatus);
        $found = $storeStatusRepository->getById($storeStatus->id);

        $this->assertInstanceOf(StoreStatus::class, $found);
        $this->assertEquals($found->status, $storeStatus->status);
        $this->assertEquals($found->status_mm, $storeStatus->status_mm);
    }

    /** @test */
    public function test_can_update_store_status()
    {
        $storeStatus = factory(StoreStatus::class)->create();

        $data = [
            'status' => 'Test',
            'status_mm' => 'test mm',
        ];

        $storeStatusRepository = new StoreStatusRepository(new StoreStatus);
        $updatedStoreStatus = $storeStatusRepository->update($storeStatus, $data);

        $this->assertInstanceOf(StoreStatus::class, $updatedStoreStatus);
        $this->assertEquals($data['status'], $updatedStoreStatus->status);
        $this->assertEquals($data['status_mm'], $updatedStoreStatus->status_mm);
    }

    /** @test */
    public function test_can_delete_store_status()
    {
        $storeStatus = factory(StoreStatus::class)->create();
        $storeStatusRepository = new StoreStatusRepository(new StoreStatus);
        $delete = $storeStatusRepository->deleteById($storeStatus->id);

        $this->assertTrue($delete);
    }
}
