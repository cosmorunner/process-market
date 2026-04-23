<?php

namespace Tests\Unit\Environment;

use App\Environment\PublicApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class PublicApiTest
 * @package Tests\Unit\Environment
 */
class PublicApiTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_public_api() {
        $id = Str::uuid()->toString();
        $options = [
            'id' => $id,
            'name' => '',
            'slug' => '',
            'type' => '',
        ];

        $blueprint = PublicApi::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['name'], $options['name']);
        $this->assertEquals($blueprint['slug'], $options['slug']);
        $this->assertEquals($blueprint['type'], $options['type']);
    }
}
