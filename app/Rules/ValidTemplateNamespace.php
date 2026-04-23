<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\Process;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Prüfung, ob die gewählte Vorlage beim Erstellen eines neuen Prozesses gültig ist. Die Vorlage ist gültig wenn
 * diese ein Prozess oder eine Lizenz von dem gewählten Namespace ist.
 * Class ValidTemplateNamespace
 * @package App\Rules
 */
class ValidTemplateNamespace implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        /* @var Process $process */
        /* @var User $user */
        /* @var User|Organisation $context */
        $templateProcess = Process::whereFullNamespace($value)->first();
        $user = auth()->user();
        $namespace = request('namespace');
        $context = $namespace === $user->namespace ? $user : $user->organisations->firstWhere('namespace', '=', $namespace);

        if (!$context || !$templateProcess) {
            $fail($this->message());
        }

        // Der Vorlagen-Prozess muss dem "context" (Benutzer oder Organisation) gehören oder diese
        // muss eine dafür besitzen, die "allowsCopy" true hat.
        $userProcesses = $user->publishedProcesses();
        $contextProcesses = $context->publishedProcesses();
        $contextLicensesProcesses = $context->licensedProcesses();
        $allowedNamespaces = collect([...$userProcesses, ...$contextProcesses, ...$contextLicensesProcesses])
            ->pluck('full_namespace')
            ->unique();

        if (!$allowedNamespaces->contains($value)) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Ungültige Vorlage.';
    }
}
