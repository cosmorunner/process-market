<?php

namespace App;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * Hält die Key-Value Paare.
 * Wird genutzt um eine Aktions-Komponente mit Werten zu befüllen (beim Öffnen der Aktion) und um
 * beim Speichern einer Aktion Key-Value Paare bei den Aktionsdaten oder Prozessdaten abzuspeichern.
 * Class ValuesBag
 * @package App\Plugins
 */
class ValuesBag implements Arrayable {

    /**
     * Werte
     * @var array
     */
    private $values = [];

    /**
     * ValuesBag constructor.
     * @param array|Collection $values
     */
    final function __construct($values = []) {
        if(is_null($values)) {
            $values = [];
        }
        if ($values instanceof Collection) {
            $values = $values->toArray();
        }

        foreach ($values as $key => $value) {
            $this->apply([$key => $value]);
        }
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray() : array {
        return $this->castToArray($this->values);
    }

    /**
     * Rekursives casten eines Objektes zu einem Array.
     * @param $object
     * @return array
     */
    private function castToArray($object) {
        if ($object instanceof Arrayable) {
            $object = $object->toArray();
        }
        if (is_object($object)) {
            $object = (array) $object;
        }

        if (is_array($object)) {
            $arr = [];
            foreach ($object as $key => $val) {
                $arr[$key] = $this->castToArray($val);
            }
        }
        else {
            $arr = $object;
        }

        return $arr;
    }


    /**
     * Get the keys present in the message bag.
     * @return array
     */
    public function keys() : array {
        return array_keys($this->values);
    }

    /**
     * Add a message to the bag.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function add($key, $value) : void {
        if (!array_key_exists($key, $this->values)) {
            $this->apply([$key => $value]);
        }
    }

    /**
     * Setzt die Werte des ValuesBags mittels eines Arrays.
     * @param array|Collection $values
     */
    public function apply($values = []) {
        if(is_null($values)) {
            $values = [];
        }
        if ($values instanceof Collection) {
            $values = $values->toArray();
        }
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Setzt einen Wert. Vorhandener Wert wird überschrieben.
     * @param $key
     * @param $value
     */
    public function set($key, $value) {
        if (is_object($value) || is_array($value)) {
            $value = $this->castToArray($value);
        }

        $this->values[$key] = $this->escapeBackslashes($value);
    }

    /**
     * Wert escapen, z.B. für Backslashes.
     * @param $value
     * @return mixed
     */
    private function escapeBackslashes($value) {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->escapeBackslashes($item);
            }
        }

        if (is_object($value)) {
            $value = (array) $value;
            $this->escapeBackslashes($value);
        }

        return str_replace('\\', '\\\\', $value);
    }

    /**
     * Merge Werte in den ValuesBag. Keys werden überschrieben.
     * @param array $values
     * @return void
     */
    public function merge(array $values) : void {
        $this->apply($values);
    }

    /**
     * Prüft ob ein Key im ValuesBag existiert.
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool {
        return array_key_exists($key, $this->values);
    }

    /**
     * Gibt einen bestimmten Wert aus dem ValuesBag.
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function get(string $key, $default = null) {
        return $this->values[$key] ?? $default;
    }

    /**
     * Gibt alle Werte aus dem ValuesBag zurück.
     * @return Collection
     */
    public function all() {
        return collect($this->values);
    }

    /**
     * Prüft ob der ValuesBag leer ist.
     * @return bool
     */
    public function isEmpty() {
        return !$this->count() > 0;
    }

    /**
     * Prüft ob der ValuesBag nicht leer ist.
     * @return bool
     */
    public function isNotEmpty() {
        return $this->count() > 0;
    }

    /**
     * Gibt die Anzahl der Werte im ValuesBag zurück.
     * @return int
     */
    public function count() {
        return count($this->keys());
    }

    /**
     * Konvertiert das Objekt in seine JSON Repräsentation.
     * @param int $options
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Dynamischer Zugriff auf einen Wert.
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Dynamisches Setzen eines Wertes.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value) {
        $this->add($key, $value);
    }

    /**
     * Konvertiert das Objekt to einem String.
     * @return string
     */
    public function __toString() {
        return $this->toJson();
    }
}
