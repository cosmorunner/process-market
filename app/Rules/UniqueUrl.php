<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

/**
 * Class UniqueUrl
 * @package App\Rules
 */
class UniqueUrl implements ValidationRule {

    /**
     * @var Organisation|User
     */
    private $owner;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        $ownerId = request('owner_id');
        $this->owner = Auth::user()->id === $ownerId ? Auth::user() : Organisation::find($ownerId);
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $value = rtrim($value, '/');

        if (!$this->owner) {
            $fail($this->message());
        }

        if ($this->owner->systems->pluck('url')->contains($value)) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Eine Allisa Plattform mit dieser URL wurde bereits hinzugefügt.';
    }
}
