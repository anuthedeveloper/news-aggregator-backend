<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PreferenceTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
    
    public function testSavePreferences()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');

        $data = [
            'categories' => ['tech', 'health'],
            'sources' => ['source1', 'source2'],
            'authors' => ['author1', 'author2']
        ];

        $response = $this->json('POST', '/api/preferences', $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['categories' => ['tech', 'health']]);
    }

}
