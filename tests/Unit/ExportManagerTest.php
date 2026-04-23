<?php

namespace Tests\Unit;

use App\Http\Resources\ProcessFileDefinition;
use App\Models\ProcessVersion;
use App\Transfer\ExportManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class ExportManagerTest
 * @package Tests\Unit
 */
class ExportManagerTest extends TestCase {

    use RefreshDatabase;

    public function test_it_can_export_a_process_to_a_file() {
        /* @var ProcessVersion $processVersion */
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $fileName = ExportManager::exportToFile($processVersion->full_namespace);

        $this->assertIsString($fileName);
        $this->assertTrue(Storage::exists($fileName));
    }

    public function test_it_can_export_a_process_to_an_array() {
        /* @var ProcessVersion $processVersion */
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $jsonResource = ExportManager::exportToJson($processVersion->full_namespace);

        $this->assertInstanceOf(ProcessFileDefinition::class, $jsonResource);
        $this->assertIsArray($jsonResource->toArray(request()));
    }

}
