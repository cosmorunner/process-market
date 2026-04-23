<?php

namespace App\ProcessType;

use Illuminate\Contracts\Support\Arrayable;
use Str;

/**
 * Class AbstractModel
 * @package App\ProcessType
 */
abstract class AbstractModel implements Arrayable {

    /**
     * Initialisiert Collections für definierte Eigenschaften des Models.
     * @var array
     */
    protected $collections = [];

    /**
     * AbstractModel constructor.
     * @param array $properties
     */
    public function __construct(array $properties) {
        foreach ($properties as $key => $value) {

            if (property_exists($this, $key) || property_exists($this, Str::camel($key))) {
                // Prüfen, ob eine Collection an Models erzeugt werden soll.
                if (is_array($value) && array_key_exists(Str::camel($key), $this->collections)) {
                    $key = Str::camel($key);
                    $this->$key = collect(array_map(function ($item) use ($key) {
                        return new $this->collections[$key]($item, $this);
                    }, $value));

                }
                else {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Wird manuel nach dem Definition-Konstruktur aufgerufen und setzt weitere Relationen, z.B. jene zum Parent Element.
     * z.B. $actionType->processType.
     */
    public function afterConstruct() {
        foreach ($this->collections as $propertyName => $modelName) {
            // Due to the "typed properties" here must be checked for "isset" and initialize if necessary.
            // Otherwise "Fatal error: Uncaught Error: Typed property Example::$name must not be accessed before initialization in ...
            if (!isset($this->$propertyName)) {
                $this->$propertyName = collect();
            }

            foreach ($this->$propertyName as $model) {
                if ($model instanceof AbstractModel) {
                    $model->setup($this);
                    $model->afterConstruct();
                }
            }
        }

        return $this;
    }

    /**
     * Individuelles Setup einer Klasse, z.B. Setzen der weiteren Relationen.
     * @param AbstractModel|Definition $parent
     */
    protected function setup($parent) {
        // Wird von Child-Klassen genutzt.
    }

    /**
     * Model zum Array konvertieren damit dieses in der Datenbank gespeichert werden kann.
     */
    abstract public function toArray(): array;

}
