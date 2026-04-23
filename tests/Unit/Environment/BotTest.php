<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment;

use App\Environment\Bot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class BotTest
 * @package Tests\Unit\Environment
 */
class BotTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_bot() {
        $options = [
            'id' => Str::uuid()->tostring(),
            'first_name' => 'jane',
            'aliases' => ['jane_doe'],
        ];

        $blueprint = Bot::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['first_name'], $options['first_name']);
        $this->assertEquals($blueprint['aliases'], $options['aliases']);
    }
}
