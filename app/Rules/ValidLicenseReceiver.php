<?php

namespace App\Rules;

use App\Interfaces\Licensable;
use App\Models\Organisation;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ValidLicenseReceiver
 * @package App\Rules
 */
class ValidLicenseReceiver implements ValidationRule {

    /**
     * Wert mit dem verglichen wird.
     */
    private ?Licensable $licensable;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        $modelId = request('resource_id');
        $className = request('resource_type');

        /* @var Model|Builder $model */
        if (class_exists($className) && ($model = new $className()) instanceof Model) {
            $this->licensable = $model::find($modelId);
        }
    }

    /**
     * Ungültig wenn der $receiverNamespace identisch ist mit dem Author-Namespace
     * @param string $attribute
     * @param mixed $value receiverNamespace
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        /* @var User $user */
        $user = auth()->user();

        /* @var User|Organisation $receiver */
        $receiver = User::whereNamespace($value)->first() ?? Organisation::whereNamespace($value)->first();

        if (!$receiver) {
            $fail($this->message());
        }

        // Man kann keine Lizenz für den Author des Prozesses ausstellen.
        if ($receiver->namespace === $this->licensable?->author->namespace) {
            $fail($this->message());
        }

        // Man kann nur für eine Organisation eine Lizenz ausstellen, wenn man dort "licenses.manage"-Rechte hat.
        if ($receiver instanceof Organisation && !$user->can('manageLicenses', $receiver)) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Der Lizenz-Empfänger ist ungültig.';
    }
}
