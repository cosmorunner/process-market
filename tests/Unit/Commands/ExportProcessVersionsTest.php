<?php

namespace Tests\Unit\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ExportProcessVersionsTest
 * @package Tests\Unit\Commands
 */
class ExportProcessVersionsTest extends TestCase {

    use RefreshDatabase;

    // Robert: Vorläufig keine Tests implementieren.
    //    public function test_export_process_versions() {
    //        $process = Process::factory()->create();
    //        $definition = $definition ?? app(DefinitionBuilder::class)->make();
    //        $processVersion = ProcessVersion::factory()->ofProcess($process)->withDefinition($definition)->create();
    //        $path = $processVersion->definitionExportFilePath();
    //
    //        $this->assertFalse(Storage::exists($path));
    //        $this->assertEquals(0, Artisan::call('app:export_process_versions'));
    //        $this->assertTrue(Storage::exists($path));
    //    }
}