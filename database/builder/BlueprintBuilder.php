<?php /** @noinspection PhpUnused */

namespace Database\Builder;


use App\Environment\Blueprint;
use App\Environment\Connector;
use App\Environment\Process;
use App\Environment\Relation;
use App\Environment\Request;
use App\Environment\User;
use App\ProcessType\RelationType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Class ProcessBuilder
 * @package Tests\Setup
 */
class BlueprintBuilder {

    use WithFaker;

    /**
     * @var Process|null
     */
    protected $withProcess;

    /**
     * @var User|null
     */
    protected $withUser;

    /**
     * @var Relation|null
     */
    protected $withRelation;

    /**
     * @var Connector|null
     */
    protected $withConnector;

    /**
     * @var Request|null
     */
    protected $withRequest;

    public function __construct() {
        $this->setUpFaker();
    }

    /**
     * @param array $attributes
     * @return Blueprint
     */
    public function make(array $attributes = []) {
        $blueprint = Blueprint::make($attributes);

        if ($this->withProcess) {
            $blueprint->processes->add($this->withProcess);
        }

        if ($this->withUser) {
            $blueprint->users->add($this->withUser);
        }

        if ($this->withRelation) {
            $blueprint->relations->add($this->withRelation);
        }

        if ($this->withConnector) {
            $blueprint->connectors->add($this->withConnector);
        }

        if ($this->withRequest) {
            $blueprint->requests->add($this->withRequest);
        }

        return $blueprint;
    }

    /**
     * @param string $fullNamespaceWithVersion
     * @param string $name
     * @return BlueprintBuilder
     */
    public function withProcess(string $fullNamespaceWithVersion, string $name = 'Demo') {
        $this->withProcess = Process::make([
            'process_type' => $fullNamespaceWithVersion,
            'name' => $name
        ]);

        return $this;
    }

    /**
     * @param string $fullNamespaceWithVersion
     * @return BlueprintBuilder
     */
    public function withUser(string $fullNamespaceWithVersion) {
        $this->withUser = User::make([
            'identity_process_type' => $fullNamespaceWithVersion,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'locale' => 'de',
            'email' => $this->faker->email,
            'email_verified_at' => Carbon::now()->toString(),
            'account_completed_at' => Carbon::now()->toString(),
            'password' => 'dummy'
        ]);

        return $this;
    }

    /**
     * @param Process $left
     * @param RelationType $relationType
     * @param Process $right
     * @return $this
     */
    public function withRelation(Process $left, RelationType $relationType, Process $right) {
        $this->withRelation = Relation::make([
            'left' => $left->id,
            'relation_type' => $relationType->id,
            'right' => $right->id
        ]);

        return $this;
    }

    /**
     * @param string $identifier
     * @return $this
     */
    public function withConnector(string $identifier) {
        $this->withConnector = Connector::make([
            'name' => $identifier,
            'identifier' => $identifier,
            'mode' => 'debug'
        ]);

        return $this;
    }

    /**
     * @param string $connectorId
     * @param string $identifier
     * @param array $debugOptions
     * @return $this
     */
    public function withRequest(string $connectorId, string $identifier, array $debugOptions = []) {
        $this->withRequest = Request::make([
            'name' => $identifier,
            'connector_id' => $connectorId,
            'identifier' => $identifier,
            'debug_options' => $debugOptions
        ]);

        return $this;
    }
}
