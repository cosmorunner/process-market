<?php


namespace App\Traits;


use Illuminate\Support\Str;

/**
 * Trait UsesAliasString
 * Wird genutzt um SQL-Aliases für Prozesstyp-Listenkonfigurationen zu erzeugen.
 * Wird bei den "SupportData..."-Resourcen genutzt.
 * @package App\Traits
 */
trait UsesAliasString {

    /**
     * @param $string
     * @return string
     */
    protected function toAlias($string) {
        $string = strtolower($string);
        $umlaute = ['/ä/', '/ö/', '/ü/', '/Ä/', '/Ö/', '/Ü/', '/ß/'];
        $replace = ['ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss'];
        $string = preg_replace($umlaute, $replace, $string);
        $string = preg_replace('/[^a-z0-9]/', '_', $string);
        $string = trim($string, '\_');

        return Str::snake($string);
    }
}
