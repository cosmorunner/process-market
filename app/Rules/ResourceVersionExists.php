<?php

namespace App\Rules;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Solution;
use App\Models\SolutionVersion;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Ramsey\Uuid\Uuid;

/**
 * Wird beim Erstellen von Lizenzen genutzt. Prüft, ob es eine Version (Graph oder SolutionVersion) mit dieser Version gibt.
 * Class ResourceVersionExists
 * @package App\Rules
 */
class ResourceVersionExists implements ValidationRule {

    /**
     * @var Process|Solution
     */
    private $resource;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        if (!Uuid::isValid(request('resource_id'))) {
            return;
        }

        if (request('resource_type') === Process::class) {
            $this->resource = Process::find(request('resource_id'));
        }

        if (request('resource_type') === Solution::class) {
            $this->resource = Solution::find(request('resource_id'));
        }
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->resource) {
            $fail($this->message());
        }

        if ($this->resource instanceof Process) {
            if (!$this->resource->version($value) instanceof ProcessVersion) {
                $fail($this->message());
            }

            return;
        }

        if ($this->resource instanceof Solution) {
            if (!$this->resource->version($value) instanceof SolutionVersion) {
                $fail($this->message());
            }

            return;
        }

        $fail($this->message());
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Die Resource besitzt nicht die Version "' . request('resource_version') . '".';
    }
}
