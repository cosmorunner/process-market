<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit;

use App\Models\Process;
use App\Models\ProcessVersion;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ProcessDefinitionStatus
 * @package Tests\Unit
 */
class ProcessDefinitionStatusTest extends TestCase {
    use RefreshDatabase;

    public function test_process_definition_create_status_with_valid_values() {
        $user = $this->login();
        $definition = $definition ?? app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withProcessVersion([$processVersion])
            ->create(['latest_version_id' => $processVersion->id]);

        $payload = [
            "name" => "khv",
            "reference" => "khvb",
            "description" => "",
            "image" => "settings",
            "default" => "0.000",
            "smart" => [
                "type" => "related_status",
                "options" => [
                    "relation_type" => "relationType|ohudc[Verknüpfungstyp - obj]",
                    "status_type_reference" => "iovzhg"
                ]
            ],
            "namespace" => "allisa",
            "options" => [],
            "identifier" => "simple",
            "version" => "1.0.0",
            "sort" => 1,
            "size" => "6x1",
            "hidden" => false,
            "states" => []
        ];

        $this->patch(route('api.process_version.definition', $processVersion), [
            'command' => 'StoreStatusType',
            'payload' => $payload
        ])->assertOk();
    }

    public function test_process_definition_create_status_with_invalid_value_smart_type() {
        $user = $this->login();
        $definition = $definition ?? app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withProcessVersion([$processVersion])
            ->create(['latest_version_id' => $processVersion->id]);

        $payload = [
            "name" => "khv",
            "reference" => "khvb",
            "description" => "",
            "image" => "settings",
            "default" => "0.000",
            "smart" => [
                "type" => "related_statuse",
                "options" => [
                    "relation_type" => "relationType|ohudc[Verknüpfungstyp - obj]",
                    "status_type_reference" => "iovzhg"
                ]
            ],
            "namespace" => "allisa",
            "options" => [],
            "identifier" => "simple",
            "version" => "1.0.0",
            "sort" => 1,
            "size" => "6x1",
            "hidden" => false,
            "states" => []
        ];

        $this->patch(route('api.process_version.definition', $processVersion), [
            'command' => 'StoreStatusType',
            'payload' => $payload
        ])->assertUnprocessable();
    }

    public function test_process_definition_create_status_with_invalid_value_smart_related_status_status_reference() {
        $user = $this->login();
        $definition = $definition ?? app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withProcessVersion([$processVersion])
            ->create(['latest_version_id' => $processVersion->id]);

        $payload = [
            "name" => "khv",
            "reference" => "khvb",
            "description" => "",
            "image" => "settings",
            "default" => "0.000",
            "smart" => [
                "type" => "related_status",
                "options" => [
                    "relation_type" => "relationType|ohudc[Verknüpfungstyp - obj]",
                    "status_type_reference" => ""
                ]
            ],
            "namespace" => "allisa",
            "options" => [],
            "identifier" => "simple",
            "version" => "1.0.0",
            "sort" => 1,
            "size" => "6x1",
            "hidden" => false,
            "states" => []
        ];

        $this->patch(route('api.process_version.definition', $processVersion), [
            'command' => 'StoreStatusType',
            'payload' => $payload
        ])->assertUnprocessable();
    }

    public function test_process_definition_update_status_with_valid_value_smart_type() {
        $user = $this->login();
        $definition = $definition ?? app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withProcessVersion([$processVersion])
            ->create(['latest_version_id' => $processVersion->id]);

        $payload = [
            "name" => "khv",
            "reference" => "khvb",
            "description" => "",
            "image" => "settings",
            "default" => "0.000",
            "smart" => [
                "type" => "related_status",
                "options" => [
                    "relation_type" => "relationType|ohudc[Verknüpfungstyp - obj]",
                    "status_type_reference" => "iovzhg"
                ]
            ],
            "namespace" => "allisa",
            "options" => [],
            "identifier" => "simple",
            "version" => "1.0.0",
            "sort" => 1,
            "size" => "6x1",
            "hidden" => false,
            "states" => []
        ];

        $response = $this->patch(route('api.process_version.definition', $processVersion), [
            'command' => 'StoreStatusType',
            'payload' => $payload
        ])->json();

        $id = collect($response['definition']['status_types'])->where('reference', 'khvb')[0]['id'];
        $payload['id'] = $id;

        $this->patch(route('api.process_version.definition', $processVersion), [
            'command' => 'UpdateStatusType',
            'payload' => $payload
        ])->assertOk();
    }

    public function test_process_definition_update_status_with_invalid_value_smart_type() {
        $user = $this->login();
        $definition = $definition ?? app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withProcessVersion([$processVersion])
            ->create(['latest_version_id' => $processVersion->id]);

        $payload = [
            "name" => "khv",
            "reference" => "khvb",
            "description" => "",
            "image" => "settings",
            "default" => "0.000",
            "smart" => [
                "type" => "related_status",
                "options" => [
                    "relation_type" => "relationType|ohudc[Verknüpfungstyp - obj]",
                    "status_type_reference" => "iovzhg"
                ]
            ],
            "namespace" => "allisa",
            "options" => [],
            "identifier" => "simple",
            "version" => "1.0.0",
            "sort" => 1,
            "size" => "6x1",
            "hidden" => false,
            "states" => []
        ];

        $response = $this->patch(route('api.process_version.definition', $processVersion), [
            'command' => 'StoreStatusType',
            'payload' => $payload
        ])->json();

        $id = collect($response['definition']['status_types'])->where('reference', 'khvb')[0]['id'];
        $payload['id'] = $id;
        $payload['smart']['options']['status_type_reference'] = '';
        $this->patch(route('api.process_version.definition', $processVersion), [
            'command' => 'UpdateStatusType',
            'payload' => $payload
        ])->assertUnprocessable();
    }
}