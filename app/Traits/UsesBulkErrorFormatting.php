<?php

namespace App\Traits;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

/**
 * Used for bulk command rules. A bulk command rule calls the corresponding non-bulk command rule
 * which an unwanted error format.
 * Trait UsesBulkErrorFormatting
 * @package App\Traits
 */
trait UsesBulkErrorFormatting {

    /**
     * When using the non-bulk command rule in this context, the error messages have an undesired
     * format:
     * #messages: array:1 [
     *      "value.0" => array:1 [
     *          0 => "{"min":["Ung\u00fcltiger Wert."], "max":["Ung\u00fcltiger Wert."]}"
     *      ]
     *          "value.1" => array:1 [
     *          0 => "{"min":["Ung\u00fcltiger Wert."]}"
     *      ]
     * ]
     * "value.0" represents the "0"-Index of the bulk rows.
     * Desired format:
     * [
     *      "value.0" => array:1 [
     *          "min" => ["Ung\u00fcltiger Wert."],
     *          "max" => ["Ung\u00fcltiger Wert."],
     *          "name" => ["Name beinhaltet einen bereits vorhandenen Wert."],
     *      ],
     *      "value.1" => array:1 [
     *          "min" => ["Ung\u00fcltiger Wert."]
     *          "name" => ["Name beinhaltet einen bereits vorhandenen Wert."],
     *      ],
     * ]
     * @param MessageBag $messageBag
     * @return array
     */
    private function reformatErrorMessages(MessageBag $messageBag): array {
        return collect($messageBag->messages())->mapWithKeys(function ($msg, $key) {
            if (Str::isJson($msg[0])) {
                $value = json_decode($msg[0], true);
            }
            else {
                $value = is_string($msg) ? [$msg] : $msg;
            }

            return [$key => $value];
        })->toArray();
    }
}
