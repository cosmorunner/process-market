<?php

namespace Tests\Unit\Environment;

use App\Environment\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SettingTest
 * @package Tests\Unit\Environment
 */
class SettingTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_setting() {
        $options = [
            'name' => '',
            'value' => '',
            'owner_id' => null,
            'owner_type' => null
        ];

        $blueprint = Setting::make($options)->toArray();

        $this->assertEquals($blueprint['name'], $options['name']);
        $this->assertEquals($blueprint['value'], $options['value']);
        $this->assertEquals($blueprint['owner_id'], $options['owner_id']);
        $this->assertEquals($blueprint['owner_type'], $options['owner_type']);
    }
}
