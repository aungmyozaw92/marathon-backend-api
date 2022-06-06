<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CallStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Web\Api\v1\CallStatusRepository;

class CallStatusTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function test_can_create_call_status()
    {
        $data = [
            'status' => 'Test',
            'status_mm' => 'test mm',
        ];

        $callStatusRepository = new CallStatusRepository(new CallStatus);
        $callStatus = $callStatusRepository->create($data);

        $this->assertInstanceOf(CallStatus::class, $callStatus);
        $this->assertEquals($data['status'], $callStatus->status);
        $this->assertEquals($data['status_mm'], $callStatus->status_mm);
    }

    /** @test */
    public function test_can_show_call_status()
    {
        $callStatus = factory(CallStatus::class)->create();
        $callStatusRepository = new CallStatusRepository(new CallStatus);
        $found = $callStatusRepository->getById($callStatus->id);

        $this->assertInstanceOf(CallStatus::class, $found);
        $this->assertEquals($found->status, $callStatus->status);
        $this->assertEquals($found->status_mm, $callStatus->status_mm);
    }

    /** @test */
    public function test_can_update_call_status()
    {
        $callStatus = factory(CallStatus::class)->create();

        $data = [
            'status' => 'Test',
            'status_mm' => 'test mm',
        ];

        $callStatusRepository = new CallStatusRepository(new CallStatus);
        $updatedCallStatus = $callStatusRepository->update($callStatus, $data);

        $this->assertInstanceOf(CallStatus::class, $updatedCallStatus);
        $this->assertEquals($data['status'], $updatedCallStatus->status);
        $this->assertEquals($data['status_mm'], $updatedCallStatus->status_mm);
    }

    /** @test */
    public function test_can_delete_call_status()
    {
        $callStatus = factory(CallStatus::class)->create();
        $callStatusRepository = new CallStatusRepository(new CallStatus);
        $delete = $callStatusRepository->deleteById($callStatus->id);

        $this->assertTrue($delete);
    }
}
