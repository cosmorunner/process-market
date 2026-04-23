<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\Graph;

use App\Graph\ActionNode;
use App\Graph\Edge;
use App\Graph\StatusNode;
use App\Graph\StatusRuleNode;
use App\ProcessType\StatusRule;
use App\ProcessType\StatusType;
use Database\Builder\Definition\ActionTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class EdgeTest
 * @package Tests\Feature\Graph
 */
class EdgeTest extends TestCase {

    use RefreshDatabase;

    public function test_graph_edge_has_an_id() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $actionNode = new ActionNode($actionType, $parent, $parent->model->id);
        $statusRule = StatusRule::make();
        $statusRuleNode = new StatusRuleNode($statusRule, $parent);

        $edge = new Edge($actionNode, $statusRuleNode, 'status-rule-edge');
        $this->assertequals($actionNode->id . ' ' . $statusRuleNode->id, $edge->id);
    }

    public function test_graph_edge_has_a_souce() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $actionNode = new ActionNode($actionType, $parent, $parent->model->id);
        $statusRule = StatusRule::make();
        $statusRuleNode = new StatusRuleNode($statusRule, $parent);

        $edge = new Edge($actionNode, $statusRuleNode, 'status-rule-edge');
        $this->assertequals($actionNode, $edge->source);
    }

    public function test_graph_edge_has_a_target() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $actionNode = new ActionNode($actionType, $parent, $parent->model->id);
        $statusRule = StatusRule::make();
        $statusRuleNode = new StatusRuleNode($statusRule, $parent);

        $edge = new Edge($actionNode, $statusRuleNode, 'status-rule-edge');
        $this->assertequals($statusRuleNode, $edge->target);
    }

    public function test_graph_edge_has_a_type() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $actionNode = new ActionNode($actionType, $parent, $parent->model->id);
        $statusRule = StatusRule::make();
        $statusRuleNode = new StatusRuleNode($statusRule, $parent);

        $edge = new Edge($actionNode, $statusRuleNode, 'status-rule-edge');
        $this->assertequals('status-rule-edge', $edge->type);
    }

    public function test_graph_edge_has_a_model_id() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $actionNode = new ActionNode($actionType, $parent, $parent->model->id);
        $statusRule = StatusRule::make();
        $statusRuleNode = new StatusRuleNode($statusRule, $parent);

        $edge = new Edge($actionNode, $statusRuleNode, 'status-rule-edge', $statusRule->id);
        $this->assertequals($statusRule->id, $edge->modelId);
    }

    public function test_graph_edge_has_classes() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $actionNode = new ActionNode($actionType, $parent, $parent->model->id);
        $statusRule = StatusRule::make();
        $statusRuleNode = new StatusRuleNode($statusRule, $parent);

        $edge = new Edge($actionNode, $statusRuleNode, 'status-rule-edge', null, 'classes-name');
        $this->assertequals('classes-name', $edge->classes);
    }

    public function test_graph_edge_can_transform() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $parent = new StatusNode(StatusType::make());
        $actionNode = new ActionNode($actionType, $parent, $parent->model->id);
        $statusRule = StatusRule::make();
        $statusRuleNode = new StatusRuleNode($statusRule, $parent);

        $data = (new Edge($actionNode, $statusRuleNode, 'status-rule-edge', $statusRule->id, 'classes-name'))->transform();
        $this->assertEquals($actionNode->id . ' ' . $statusRuleNode->id, $data['data']['id']);
        $this->assertEquals($actionNode->id, $data['data']['source']);
        $this->assertEquals($statusRuleNode->id, $data['data']['target']);
        $this->assertEquals($actionNode->model?->id, $data['data']['source_model_id']);
        $this->assertEquals($statusRuleNode->model?->id, $data['data']['target_model_id']);
        $this->assertEquals('status-rule-edge', $data['data']['type']);
        $this->assertEquals($statusRule->id, $data['data']['model_id']);
        $this->assertEquals('classes-name', $data['classes']);
    }
}
