<?php

namespace App\Rules;

use App\ProcessType\Permission;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

/**
 * Class ValidPermissionIdent
 * @package App\Rules
 */
class ValidPermissionIdent implements ValidationRule {

    /**
     * Prüfen ob der "ident" ein Prozess-Ident oder Aktionstyp-Ident ist.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $ident = $value['ident'];
        $validPermissionIdents = collect(config('permissions'))->flatten(1)->pluck('ident');

        // process_type.action_type.6e88a6c6-2a66-435e-b303-8ac4c9922f21.execute -> process_type.action_type.*.execute
        $templateValue = Permission::identToTemplate($ident);

        if (!($validPermissionIdents->contains($ident) || $validPermissionIdents->contains($templateValue) || (Str::startsWith($ident, 'process_type.output.') && Str::endsWith($ident, '.view')))) {
            $fail('Ungültige Berechtigungs-Kennung.');
        }
    }

}
