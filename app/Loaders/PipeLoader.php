<?php

namespace App\Loaders;

use App\Models\ProcessVersion;
use App\ProcessType\AbstractModel;
use App\ProcessType\Definition;
use App\ProcessType\Processor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;
use Str;

/**
 * Lädt Model(s) und Prozesstyp-Daten anhand einer einheitlichen Syntax.
 * Beispiele:
 * process|922442a8-a256-48d3-a0c6-95bc4d0e6a20 - Systemweit einzigartiges Model
 * process|922442a8-a256-48d3-a0c6-95bc4d0e6a20.created_at - Zugriff auf ein Attribut von dem Model
 * process|922442a8-a256-48d3-a0c6-95bc4d0e6a20.created_at[Test GmbH] - Optionale Bezeichnung des Models.
 * allisa/demo::relationTypes|98ea8685-a795-452a-b81e-6c45c24ab831 - Einzigartige Ermittlung eines Prozesstyp-Models
 * allisa/demo@1.0.0::relationTypes|98ea8685-a795-452a-b81e-6c45c24ab831 - Optionale Angabe einer Prozesstyp-Version
 * allisa/demo@1.0.0::relationTypes|98ea8685-a795-452a-b81e-6c45c24ab831.description - Zugriff auf ein Attribut von dem Model
 * Class PipeLoader
 * @package App\Loader
 */
class PipeLoader {

    /**
     * Regex zum Ermitteln des Names, z.b. "Test GmbH" bei "process|369aab1e-4fad-4a43-836c-8d44ea9759f6[Test GmbH]"
     */
    const NAME_REGEX = '/\[(.*?)]/';

    /**
     * Singleton Loader Instanz.
     * @var PipeLoader
     */
    private static $instance;

    /**
     * Bereits geladene Syntax-Ergebnisse.
     * @var array
     */
    private $cache = [];

    /**
     * Loader constructor.
     */
    private function __construct() {
    }

    /**
     * Gibt die Loader Instanz zurück.
     * @return PipeLoader
     */
    public static function getInstance(): PipeLoader {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Gibt die entsprechenden Models aus einem String zurück, bei dem die Models semikolon-sepatiert sind und der Model-Name
     * und Key pipe-separiert. Beispielsweise "process|7b1a865f-ab48-40f9-865d-6ef816609380;process|35f744ab-87a3-4ca0-84d4-0a5e6b4e20c6"
     * gibt zwei Process-Models zurück von den entsprechenden Ids.
     * Wird insbesondere bei den Prozessoren genutzt, um Input-Werte aus den Aktionen zu verarbeiten. Dort werden beispielsweise
     * beim CreateRelation-Prozessor die zu verknüpfenden Prozesse in dem oben genannten Format übergeben.
     * @param string $pipeString
     * @param Collection|null $filterClasses
     * @return Collection
     */
    public function models(string $pipeString, Collection $filterClasses = null): Collection {
        // Eine zu Verknüpfende Instanz muss dem Format "model|4b94cf9d-eae7-4bfc-897f-585ff7ac2d28" entsprechen.
        $items = collect(explode(';', $pipeString))
            ->reject(fn($item) => empty(trim($item)) || !str_contains(trim($item), '|'))
            ->unique();

        // Model-Ids extrahieren
        $models = $items->map(function ($item) {
            $item = trim($item);
            $class = self::getEloquentClass($item);

            if ($class && class_exists($class) && new $class() instanceof Model && Uuid::isValid(self::getKey($item))) {
                return $this->cache[$item] ?? $class::find(self::getKey($item));
            }

            return null;

        })->filter(fn($model) => $model instanceof Model);

        if ($filterClasses instanceof Collection) {
            $models = $models->filter(fn(Model $model) => $filterClasses->contains($model::class))
                ->each(fn(Model $model) => $this->cache[self::toString($model)] = $model);
        }

        return $models;
    }

    /**
     * Gibt das entsprechende Model aus einem String zurück. Beispielsweise "process|7b1a865f-ab48-40f9-865d-6ef816609380"
     * @param string $pipeString
     * @param string|null $class
     * @return Model|null
     */
    public function model(string $pipeString, string $class = null): Model|null {
        if (!$class) {
            $class = $this->getEloquentClass($pipeString);
        }

        $models = self::models($pipeString, collect([$class]));

        return $models->isEmpty() ? null : $models->first();
    }

    /**
     * Prüft ob der String eine Model Pipe Notation eines Prozesstyp-Models ist.
     */
    public static function isProcessVersionModel($string): bool {
        if (!is_string($string)) {
            return false;
        }

        return self::getProcessVersionClass($string) !== null;
    }

    /**
     * Gibt bei einer Syntax mit Namespace und ohne Attribut das Model zurück.
     * @param string $syntax
     */
    public function processTypeModel($syntax): AbstractModel|null {
        if (!self::hasValidAbtractModelFormat($syntax)) {
            return null;
        }

        $namespace = self::getFullNamespaceWithVersion($syntax) ?? self::getFullNamespace($syntax);
        $processType = ProcessVersion::whereFullNamespace((string) $namespace);

        if (!$processType) {
            return null;
        }

        $method = self::getClassIdentifier($syntax);

        if (method_exists($processType->definition, $method)) {
            return $processType->definition->$method(self::getKey($syntax));
        }

        return null;
    }

    /**
     * Ein Model wird zu className|id, z.B. relationType|e1d25bf0-3177-49f8-8042-42d020db701a
     * @param Model|AbstractModel|string|null $value
     * @param string $name
     * @param string $attribute Optionale Angabe eines Attributs.
     * @param bool $noVersion Ignoriert bei einem Prozesstyp-Model die Versionsnummer.
     * @param string $identifier
     * @param bool $short
     * @return string
     */
    public static function toString($value, string $name = '', string $attribute = '', bool $noVersion = false, string $identifier = 'id', bool $short = false): string {
        // Eloquent-Model
        if ($value instanceof Model) {
            return Str::camel(class_basename($value)) . '|' . $value->{$identifier} . ($attribute ? '.' . $attribute : '') . ($name ? '[' . $name . ']' : '');
        }
        // Prozesstyp-Model
        if ($value instanceof AbstractModel && property_exists($value, $identifier) && property_exists($value, 'definition')) {
            /* @var Definition $definition */
            $definition = $value->definition;
            $classIdentifier = Str::camel(class_basename($value));
            $string = $classIdentifier . '|' . $value->{$identifier} . ($attribute ? '.' . $attribute : '') . ($name ? '[' . $name . ']' : '');

            if ($short) {
                return $string;
            }
            $namespace = $noVersion ? $definition->fullNamespace() : $definition->fullNamespaceWithVersion();

            return $namespace . '::' . $string;
        }

        return '';
    }

    /**
     * Ermittelt den Wert der Syntax und gibt diesen zurück.
     * @param $pipeString
     * @return Model|AbstractModel|Collection|null
     */
    public function value($pipeString): Model|AbstractModel|Collection|null {
        // Einzelnes Model
        if (self::isEloquentModelPipeNotation($pipeString)) {
            return self::getInstance()->model($pipeString);
        }

        // Mehrere Models
        if (self::isEloquentModelsPipeNotatation($pipeString)) {
            return collect(explode(';', $pipeString))->map(fn($item) => self::getInstance()->value($item));
        }

        // Einzelnes Prozesstyp-Model
        if (self::hasValidAbtractModelFormat($pipeString)) {
            return self::getInstance()->processTypeModel($pipeString);
        }

        return null;
    }

    /**
     * Gibt den Prozesstyp-Namespace von einem Syntax-String zurück, oder NULL wenn nicht vorhanden.
     * Z.B. "allisa/demo" bei "allisa/demo@1.0.1::relationTypes|430128f5-13e6-4f22-867e-e06b27f0610c"
     * Leerer String wenn das Format ungültig ist.
     * @param string $pipeNotation
     * @return string|null
     */
    public static function getFullNamespace($pipeNotation): string|null {
        if (!is_string($pipeNotation)) {
            return null;
        }

        if (str_contains($pipeNotation, '::')) {
            $namespace = explode('::', $pipeNotation)[0];

            // Ohne Version
            if (str_contains($namespace, '@') && Definition::validNamespace($namespace)) {
                return explode('@', $namespace)[0];
            }

            return $namespace;
        }

        return null;
    }

    /**
     * Gibt den Prozesstyp-Namespace mit Version von einem Syntax-String zurück, oder NULL wenn nicht vorhanden.
     * Z.B. "allisa/demo@1.0.0" bei "allisa/demo@1.0.1::relationTypes|430128f5-13e6-4f22-867e-e06b27f0610c"
     * @param string $pipeNotation
     * @return string|null
     */
    public static function getFullNamespaceWithVersion($pipeNotation): string|null {
        if (!is_string($pipeNotation)) {
            return null;
        }

        if (str_contains($pipeNotation, '::')) {
            $namespace = explode('::', $pipeNotation)[0];

            if (str_contains($namespace, '@') && Definition::validNamespace($namespace)) {
                return $namespace;
            }
        }

        return null;
    }

    /**
     * Gibt die Prozesstyp-Version von einem Syntax-String zurück, oder NULL wenn nicht vorhanden.
     * Z.B. "1.0.0" bei "allisa/demo@1.0.1::relationTypes|430128f5-13e6-4f22-867e-e06b27f0610c".
     * @param string $pipeNotation
     * @return string|null
     */
    public static function getVersion($pipeNotation): string|null {
        if (!is_string($pipeNotation) || !self::getFullNamespaceWithVersion($pipeNotation)) {
            return null;
        }

        return explode('@', self::getFullNamespaceWithVersion($pipeNotation))[1];
    }

    /**
     * Gibt den Namen von einem Syntax-String zurück, oder NULL wenn nicht vorhanden.
     * Z.B. "Test GmbH" bei "process|430128f5-13e6-4f22-867e-e06b27f0610c[Test GmbH]".
     * @param string $pipeNotation
     * @return string|null
     */
    public static function getName($pipeNotation): string|null {
        if (!is_string($pipeNotation)) {
            return null;
        }

        preg_match(self::NAME_REGEX, $pipeNotation, $matches);

        if (count($matches) === 2) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Gibt bei einer Pipe-Notation den Key hinter "className|" zurück.
     * Z.B. "430128f5-13e6-4f22-867e-e06b27f0610c" bei "process|430128f5-13e6-4f22-867e-e06b27f0610c"
     * @param $pipeNotation
     * @param string $default
     * @return string
     */
    public static function getKey($pipeNotation, $default = ''): string {
        if (!is_string($pipeNotation)) {
            return '';
        }

        // z.B. process|bfe552b4-3b77-4423-8d75-828e21db3b78, nicht process|...;process|...
        if (str_contains($pipeNotation, '|') && !str_contains($pipeNotation, ';')) {
            $keyWithRest = explode('|', $pipeNotation)[1];

            if (str_contains($keyWithRest, '[')) {
                $keyWithRest = explode('[', $keyWithRest)[0];
            }

            // Prüfen ob ein Attribut angegeben ist.
            if (str_contains($keyWithRest, '.')) {
                return explode('.', $keyWithRest)[0];
            }
            else {
                return $keyWithRest;
            }
        }

        return $default;
    }

    /**
     * Gibt die PipeLoader-Syntax ohne Title zurück.
     * Z.B. process|11818832-2cec-43c5-9627-94200b5a45f3[Test-Prozess] --> process|11818832-2cec-43c5-9627-94200b5a45f3
     * @param $syntax
     * @return string
     */
    public static function withoutTitle($syntax): string {
        if (!is_string($syntax)) {
            return '';
        }

        $result = self::getClassIdentifier($syntax) . '|' . self::getKey($syntax);

        if ($namespace = self::getFullNamespace($syntax)) {
            $result = $namespace . '::' . $result;
        }

        return $result;
    }

    /**
     * Gibt bei einer Pipe-Notation den Identifier vor "|4ea9c747-da85-418a-af1e-88887b23dae9" zurück.
     * Z.B. "process" bei "process|430128f5-13e6-4f22-867e-e06b27f0610c" oder "relationTypes"
     * bei "allisa/demo" bei "allisa/demo@1.0.1::relationTypes|430128f5-13e6-4f22-867e-e06b27f0610c"
     * @param $pipeNotation
     * @return string
     */
    public static function getClassIdentifier($pipeNotation): string {
        if (!is_string($pipeNotation)) {
            return '';
        }

        // Full-Namespace entfernen
        if (str_contains($pipeNotation, '::')) {
            $pipeNotation = explode('::', $pipeNotation)[1];
        }

        // z.B. process|bfe552b4-3b77-4423-8d75-828e21db3b78, nicht process|...;process|...
        if (str_contains($pipeNotation, '|') && !str_contains($pipeNotation, ';')) {
            return explode('|', $pipeNotation)[0] ?? '';
        }

        return '';
    }


    /**
     * Gibt den Klassennamen anhand einer Pipe-Notation zurück.
     * Z.B. App\Models\Process bei "process|abf2b983-7358-4f1b-b1f8-009ab2cd1362"
     * @param string $pipeNotation
     * @return null|string
     */
    public static function getEloquentClass(string $pipeNotation): string|null {
        if (!self::isEloquentModelPipeNotation($pipeNotation)) {
            return null;
        }

        $arr = explode('|', trim($pipeNotation));
        $class = 'App\\Models\\' . ucfirst($arr[0]);

        if (class_exists($class)) {
            return $class;
        }

        return null;
    }

    /**
     * Gibt den Klassennamen anhand einer Pipe-Notation zurück.
     * Z.B. App\Models\Process bei "process|abf2b983-7358-4f1b-b1f8-009ab2cd1362"
     * @param string $pipeNotation
     * @return null|string
     * @noinspection PhpUnused
     */
    public static function getProcessVersionClass(string $pipeNotation): string|null {
        $arr = explode('|', trim($pipeNotation));
        $class = 'App\\ProcessType\\' . ucfirst($arr[0]);

        if ($arr[0] && class_exists($class)) {
            return $class;
        }

        return null;
    }

    /**
     * Gibt zurück, ob der String ein gültiger Syntax-String ist.
     * @param $string
     * @return bool
     * @noinspection PhpUnused
     */
    public static function hasValidFormat($string): bool {
        return self::hasValidEloquentFormat($string) || self::hasValidAbtractModelFormat($string);
    }

    /**
     * Gibt zurück, ob der String im Pipe-Notation-Format formattiert ist.
     */
    public static function hasValidEloquentFormat($string): bool {
        return self::isEloquentModelsPipeNotatation($string);
    }

    /**
     * Gibt zurück, ob der String im Pipe-Notation-Format formattiert ist.
     */
    public static function hasValidAbtractModelFormat($string): bool {
        $identifier = self::getClassIdentifier($string);
        $className = 'App\\ProcessType\\' . ucfirst($identifier);

        if (!is_string($string) || !class_exists($className) || !self::getClassIdentifier($string) || !strlen(self::getKey($string)) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Prüft ob der String ein einzelnes Model referenziert.
     * String mit ";" wird als "false" bewertet.
     * @param $string
     * @param null $className Prüfung auf eine bestimmte Klasse.
     * @return bool
     */
    public static function isEloquentModelPipeNotation($string, $className = null): bool {
        if (!is_string($string)) {
            return false;
        }

        $class = 'App\\Models\\' . ucfirst(self::getClassIdentifier($string));

        if ($className && ($class !== $className)) {
            return false;
        }

        return class_exists($class) && new $class() instanceof Model && strlen(self::getKey($string)) === 36 && !str_contains($string, ';');
    }

    /**
     * Prüft für einen semikolonseparierten String, ob alle Einträge der Model-Pipe-Notation entsprechen.
     * @param string $string
     * @return bool
     */
    public static function isEloquentModelsPipeNotatation($string): bool {
        if (is_string($string)) {
            return false;
        }

        $items = explode(';', (string) $string);

        return collect($items)->reduce(fn($carry, $item) => $carry && self::isEloquentModelPipeNotation($item), true);
    }

    /**
     * Prüft ob der String die Prozessor-Klasse abbildet: processor|d07562fa-dceb-412f-9ab8-5276688aa71a (App\ProcessType\Processor).
     * Hier kann nicht "PipeLoader::isEloquentModelPipeNotation()" genutzt werden weil "App\ProcessType\Processor" kein Eloquent Model ist.
     * @param $string
     * @return bool
     * @noinspection PhpUnused
     */
    public static function isProcessorPipeNotation($string): bool {
        $arr = explode('|', trim((string) $string));
        $class = 'App\\ProcessType\\' . ucfirst($arr[0]);

        return class_exists($class) && ($class === Processor::class);
    }

    public function __clone() {
    }

    public function __wakeup() {
    }

}
