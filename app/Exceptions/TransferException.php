<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Wird vom App/Transer/Manager geworfen, wenn es beim Import oder Export zu einem Fehler gekommen ist.
 * Class TransferException
 * @package App\Exceptions
 */
class TransferException extends Exception {

    /**
     * Construct the exception.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param Throwable|null $previous [optional] The previous throwable used for the exception chaining.
     * @since 5.1
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        if(!$message){
            $message = __('exceptions.transfer_cancelled');
        }
        parent::__construct($message, $code, $previous);
    }
}
