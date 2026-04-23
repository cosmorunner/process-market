<?php

namespace App\Rules;

use App\Interfaces\Licensable;
use App\Models\License;
use App\Models\Organisation;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReceiverDoesNotHaveLicense
 * Prüft ob der Empfänger der Lizenz diese noch nicht hat.
 * @package App\Rules
 */
class ReceiverDoesNotHaveLicense implements ValidationRule {

    /**
     * Wert mit dem verglichen wird.
     */
    private ?Licensable $licensable;

    private ?string $level;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        $modelId = request('resource_id');
        $className = request('resource_type');

        if (class_exists($className) && ($model = new $className()) instanceof Model) {
            /* @var Builder $model */
            $this->licensable = $model::find($modelId);
            $this->level = request('license.level', '');
        }
    }

    /**
     * Prüfen ob es bereits eine Gruppen-Einladung zu der E-Mail Adresse gibt.
     * @param string $attribute
     * @param mixed $value receiverNamespace
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->licensable instanceof Licensable) {
            $fail('Der Empfänger besitzt bereits diese Lizenz.');
        }

        if (!in_array($this->level, License::LEVELS)) {
            $fail('Der Empfänger besitzt bereits diese Lizenz.');
        }

        /* @var User|Organisation $receiver */
        $receiver = User::whereNamespace($value)->first() ?? Organisation::whereNamespace($value)->first();

        if (!$receiver) {
            $fail('Der Empfänger besitzt bereits diese Lizenz.');
        }

        // Es darf noch keine Lizenz geben.
        if (License::identify($this->licensable, $receiver, $this->level) !== null) {
            $fail('Der Empfänger besitzt bereits diese Lizenz.');
        }
    }

}
