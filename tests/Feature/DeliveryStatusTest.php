<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\DeliveryStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Web\Api\v1\DeliveryStatusRepository;

class DeliveryStatusTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function test_can_create_delivery_status()
    {
        $data = [
            'status' => 'Test',
            'status_mm' => 'test mm',
        ];

       $deliveryStatusRepository = new DeliveryStatusRepository(new DeliveryStatus);
       $deliveryStatus =$deliveryStatusRepository->create($data);

        $this->assertInstanceOf(DeliveryStatus::class,$deliveryStatus);
        $this->assertEquals($data['status'],$deliveryStatus->status);
        $this->assertEquals($data['status_mm'],$deliveryStatus->status_mm);
    }

    /** @test */
    public function test_can_show_delivery_status()
    {
       $deliveryStatus = factory(DeliveryStatus::class)->create();
       $deliveryStatusRepository = new DeliveryStatusRepository(new DeliveryStatus);
        $found =$deliveryStatusRepository->getById($deliveryStatus->id);

        $this->assertInstanceOf(DeliveryStatus::class, $found);
        $this->assertEquals($found->status,$deliveryStatus->status);
        $this->assertEquals($found->status_mm,$deliveryStatus->status_mm);
    }

    /** @test */
    public function test_can_update_delivery_status()
    {
       $deliveryStatus = factory(DeliveryStatus::class)->create();

        $data = [
            'status' => 'Test',
            'status_mm' => 'test mm',
        ];

       $deliveryStatusRepository = new DeliveryStatusRepository(new DeliveryStatus);
        $updatedDeliveryStatus =$deliveryStatusRepository->update($deliveryStatus, $data);

        $this->assertInstanceOf(DeliveryStatus::class, $updatedDeliveryStatus);
        $this->assertEquals($data['status'], $updatedDeliveryStatus->status);
        $this->assertEquals($data['status_mm'], $updatedDeliveryStatus->status_mm);
    }

    /** @test */
    public function test_can_delete_delivery_status()
    {
        $deliveryStatus = factory(DeliveryStatus::class)->create();
        $deliveryStatusRepository = new DeliveryStatusRepository(new DeliveryStatus);
        $delete =$deliveryStatusRepository->deleteById($deliveryStatus->id);

        $this->assertTrue($delete);
    }
}
