<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\Graph;

use App\Graph\ActionNode;
use App\Graph\StatusNode;
use App\ProcessType\StatusType;
use Database\Builder\Definition\ActionTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ActionNodeTest
 * @package Tests\Feature\Graph
 */
class ActionNodeTest extends TestCase {

    use RefreshDatabase;

    public function test_graph_action_node_has_an_id() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $actionNode = new ActionNode($actionType);
        $this->assertNotEmpty($actionNode->id);
    }

    public function test_graph_action_node_has_a_model() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $actionNode = new ActionNode($actionType);
        $this->assertEquals($actionNode->model, $actionType);
    }

    public function test_graph_action_node_has_a_parent() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $actionNode = new ActionNode($actionType, $parent);
        $this->assertEquals($actionNode->parent, $parent);
    }

    public function test_graph_action_node_has_a_status_type_id() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $actionNode = new ActionNode($actionType, null, $parent->model->id);
        $this->assertEquals($actionNode->statusTypeId, $parent->model->id);
    }

    public function test_graph_action_node_can_transform() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $data = (new ActionNode($actionType, $parent, $parent->model->id))->transform();

        $this->assertNotEmpty($data['data']['id']);
        $this->assertEquals($actionType->name, $data['data']['name']);
        $this->assertEquals('action', $data['data']['type']);
        $this->assertEquals($actionType->id, $data['data']['model_id']);
        $this->assertEquals($parent->id, $data['data']['parent']);
        $this->assertEquals($parent->model->id, $data['data']['status_type_id']);
        $this->assertEquals('node action', $data['classes']);
    }
}
