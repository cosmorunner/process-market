<?php

namespace App\FlashMessages;

/**
 * Eine Flash-Message mit einem Button um eine Aktion rückgängig zu machen.
 * Class GetButton
 * @package App\FlashMessages
 */
class MessageWithActionUndoButton extends FlashMessage {

    /**
     * Aktions-Id der Aktion die rückgängig gemacht werden soll.
     * @var string
     */
    public string $actionId;

    /**
     * MessageWithActionUndoButton constructor.
     * @param string $actionId
     * @param string $message
     * @param string $route
     * @param string $buttonLabel
     */
    public function __construct(string $actionId, string $message, string $route, string $buttonLabel) {
        parent::__construct($message, $route, $buttonLabel);
        $this->actionId = $actionId;
    }
}
