<?php

namespace App\Rules;

use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\Solution;
use App\Models\SolutionVersion;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Prüft ob beim Kaufen einer Lösungs-Lizenz für jeden Prozess eine Lizenz gewählt wurde.
 * Class NoMissingProcessLicenseForSolutionLicense
 * @package App\Rules
 */
class NoMissingProcessLicenseForSolutionLicense implements ValidationRule {

    /**
     * Lösung von der der Benutzer/Organisation eine Lizenz erhalten möchte.
     * @var Solution|null
     */
    private ?Solution $solution;

    /**
     * Lösungsversion als Kontext, von der der Benutzer/Organisation eine Lizenz erhalten möchte.
     * @var SolutionVersion|null
     */
    private ?SolutionVersion $solutionVersion;

    /**
     * Empfänger der Solution-Lizenz.
     * @var Organisation|User
     */
    private $receiverModel;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        $this->solution = Solution::find(request('resource_id'));
        $this->solutionVersion = $this->solution?->version(request('resource_version'));
        $this->receiverModel = User::whereNamespace(request('receiver'))
            ->first() ?? Organisation::whereNamespace(request('receiver'))->first();
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->solutionVersion || !$this->receiverModel) {
            $fail('Bitte wählen Sie für jeden Prozess eine Lizenz.');
        }

        $processes = $this->solutionVersion->processes();
        $ownNamespaces = $this->receiverModel->processes->pluck('full_namespace');
        $requiredNamespaces = $processes->pluck('full_namespace');

        // Erforderliche Namespaces minus Namespaces der eigenen Prozesse.
        $requiredNamespaces = $requiredNamespaces->filter(fn($ns) => !$ownNamespaces->contains($ns));

        $existingProcessLicenses = License::whereOwnerId($this->receiverModel->id)
            ->where('resource_type', '=', Process::class)
            ->whereIn('resource_id', $processes->pluck('id'))
            ->with('resource')
            ->get();

        $existingNamespaces = $existingProcessLicenses->pluck('resource')->pluck('full_namespace')->unique();
        $toBeAcquiredNamespaces = collect($value)->map(fn($item) => $item['full_namespace'])->unique();
        $accumulatedNamespaces = $existingNamespaces->concat($toBeAcquiredNamespaces->toArray())->unique();

        // Die Anzahl der erforderlichen Prozess-Namespaces für denen Prozess-Lizenzen erworben werden müssen
        // identisch mit der Anzahl der bereits erworbenen und in diesem Request angefragten Lizenzen sein.
        if ($requiredNamespaces->count() !== $accumulatedNamespaces->count()) {
            $fail('Bitte wählen Sie für jeden Prozess eine Lizenz.');
        }
    }

}
