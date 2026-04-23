<?php

namespace App\FlashMessages;

/**
 * Abstrakte Klasse für Flash-Messages mit Button.
 * Class FlashMessage
 * @package App\FlashMessages
 */
abstract class FlashMessage {

    /**
     * Flash-Message
     * @var string
     */
    public $message = '';

    /**
     * Route für die Link bzw. Post-Formular
     * @var string
     */
    public $route;

    /**
     * Label für den Button.
     * @var string
     */
    public $buttonLabel;

    /**
     * FlashMessage constructor.
     * @param string $message
     * @param string $route
     * @param string $buttonLabel
     */
    public function __construct(string $message, string $route, string $buttonLabel) {
        $this->message = $message;
        $this->route = $route;
        $this->buttonLabel = $buttonLabel;
    }
}
