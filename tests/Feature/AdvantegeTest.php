<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdvantegeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $advantages = [
            [
                'name' => 'Advantage 1',
                'description' => 'Description for Advantage 1',
            ],
            [
                'name' => 'Advantage 2',
                'description' => 'Description for Advantage 2',
            ],
            // Add more advantages as needed
        ];
        $response = $this->postJson('/add_advantages',[
            'advantages' => $advantages,
            'project_id'=>1
        ]);

        $response->assertStatus(200);
    }
}
