<?php /** @noinspection PhpUnused */

namespace App;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

/**
 * Ermöglicht das Ermitteln einer weiteren Verarbeitungsstrategy für eine Programmablauf.
 * Wird bei der Aktionsverarbeitung genutzt im ActionExecutor.
 * Class Protocol
 * @package App\Executer
 */
class Protocol {

    const LEVEL_SUCCESS = 'SUCCESS';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_EXCEPTION = 'EXCEPTION';

    /**
     * Verarbeitungsstrategy des Protokolls.
     * @var int
     */
    private $level = self::LEVEL_SUCCESS;

    /**
     * Enthält alle Messages als Key => Value. Der Value kann ebenfalls ein Error sein.
     * @var MessageBag
     */
    private $messageBag;

    /**
     * Zusätzliche Daten, die dem Protokol mitgegeben werden, damit diese nach der Rückgabe zur Verfügung stehen.
     * @var Collection
     */
    public $payload;

    /**
     * Exception des Protokols.
     * @var Exception
     */
    public $exception;

    /**
     * Protocol constructor.
     * @param mixed $payload
     */
    public function __construct($payload = null) {
        $this->messageBag = new MessageBag();
        $this->payload = $payload;
    }

    /**
     * Fügt eine Message zum Protokoll hinzu.
     * @param $level
     * @param $value
     */
    public function addMessage($level, $value) {
        $this->messageBag->add($level, $value);
        $this->updateLevel();
    }

    /**
     * Fügt eine Success-Message hinzu.
     * @param $message
     */
    public function addSuccess($message) {
        $this->addMessage(self::LEVEL_SUCCESS, $message);
    }

    /**
     * Fügt eine Notice-Message hinzu.
     * @param $message
     */
    public function addNotice($message) {
        $this->addMessage(self::LEVEL_INFO, $message);
    }

    /**
     * Fügt eine Warning-Message hinzu.
     * @param $message
     */
    public function addWarning($message) {
        $this->addMessage(self::LEVEL_WARNING, $message);
    }

    /**
     * Setzt die Exception des Protokols.
     * @param Exception $exception
     */
    public function setException(Exception $exception) {
        $this->exception = $exception;
        $this->addMessage(self::LEVEL_EXCEPTION, $exception->getMessage());
        $this->updateLevel();
    }

    /**
     * Gibt die Strategy des Protokolls zurück.
     * @return int
     */
    public function level() {
        return $this->level;
    }

    /**
     * Ermittelt anhand der vorhandenen Messages die weitere Verarbeitungsstrategy.
     * Standardgemäß wird der Messagebag nach den Keys durchsucht.
     */
    private function updateLevel() {
        if ($this->exception instanceof Exception) {
            $this->level = self::LEVEL_EXCEPTION;

            return;
        }

        if (count($this->messageBag->get(self::LEVEL_WARNING)) > 0) {
            $this->level = self::LEVEL_WARNING;

            return;
        }

        if (count($this->messageBag->get(self::LEVEL_INFO)) > 0) {
            $this->level = self::LEVEL_INFO;
        }
    }

    /**
     * Gibt die Messages der ermittelten Strategie zurück.
     * @param null $level
     * @return array
     */
    public function levelMessages($level = null) {
        if (is_null($level)) {
            $level = $this->level;
        }

        return $this->messageBag->get($level);
    }

    /**
     * Gibt die erste Message der ermittelten Strategie zurück.
     */
    public function first() {
        return $this->levelMessages()[0] ?? '';
    }

    /**
     * Gibt alle Messages des Protokolls zurück, unabhängig davon welche Strategie gesetzt ist.
     * @return array
     */
    public function messages() {
        return $this->messageBag->getMessages();
    }

    /**
     * Prüft ob das Protokoll die Error-Strategie gesetzt hat.
     * @param string $code
     * @return bool
     */
    public function hasLevel(string $code) {
        return $this->level === $code;
    }

    /**
     * Prüft ob das Protokol eine Exception hat.
     * @return bool
     */
    public function failedWithException() {
        return $this->hasLevel(self::LEVEL_EXCEPTION) && $this->exception instanceof Exception;
    }

    /**
     * Gibt die erfasste Exception zurück.
     * @return Exception
     */
    public function getException() {
        return $this->exception;
    }

    /**
     * Prüft ob das Protokoll keinerlei Nachrichten hat.
     * @return bool
     */
    public function isEmpty() {
        return $this->messageBag->isEmpty();
    }

    /**
     * Setzt die Payload des Protokols.
     * @param $payload
     * @return Protocol
     */
    public function setPayload($payload) {
        $this->payload = $payload;

        return $this;
    }
}
