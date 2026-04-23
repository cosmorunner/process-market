<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Class CommandDoesNotExist
 * Wird geworfen wenn eine Command nicht gefunden werden konnte.
 * @package App\Exceptions
 */
class CommandDoesNotExist extends Exception {

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string $name
     * @param int $code [optional] The Exception code.
     * @param Throwable|null $previous [optional] The previous throwable used for the exception chaining.
     * @since 5.1
     */
    public function __construct(string $name, $code = 0, Throwable $previous = null) {
        $message = 'Der Command "' . $name . '" existiert nicht.';

        parent::__construct($message, $code, $previous);
    }
}
