<?php

namespace App\Interfaces;

/**
 * Interface Iconable
 * @package App\Interfaces
 */
interface Iconable {

    /**
     * Font-Awesome Icon-Klasse.
     * @return string
     */
    public static function icon() : string;
}
