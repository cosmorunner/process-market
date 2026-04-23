<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Class InvalidKonfigurationException
 * Wird geworfen wenn es zu einem Fehler bei der Prozesstyp-Konfiguration gekommen ist.
 * Z.b. wenn eine Statusregel ein Wert von einem Feld erwartet, dieses Feld jedoch nicht existiert.
 * @package App\Exceptions
 */
class InvalidConfigurationException extends Exception {

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param Throwable|null $previous [optional] The previous throwable used for the exception chaining.
     * @since 5.1
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        if(!$message) {
            $message = 'Ungültige Konfiguration';
        }
        parent::__construct($message, $code, $previous);
    }
}
