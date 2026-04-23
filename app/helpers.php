<?php

use App\Loaders\PipeLoader;
use App\Models\ProcessVersion;
use App\ProcessType\AbstractModel;
use App\ProcessType\Definition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use League\Flysystem\FilesystemException;

/**
 * Verkürzt eine Zeichenkette.
 * @param string $string
 * @param int $length
 * @return string
 */
function shorten(string $string, int $length) {
    if (strlen($string) > $length) {
        return substr(trim($string), 0, $length);
    }

    return trim($string);
}

/**
 * Numerischer String Wert in Dezimal-Zahl umwandeln.
 * @param $value
 * @param $decimals
 * @return string
 */
function as_decimal($value, $decimals) {
    return number_format($value, $decimals, '.', '');
}

/**
 * @param Model|AbstractModel $model
 * @return string
 */
function pipe_notation($model) {
    return \Illuminate\Support\Str::camel(class_basename($model)) . '|' . $model->id;
}

/**
 * Datei-Pfad zu den Plugins.
 * @param $namespace
 * @return string
 */
function plugins_path($namespace) {
    return match (strtolower($namespace)) {
        config('app.plugins.internal_namespace') => resource_path() . '/plugins',
        default => storage_path('app') . '/plugins'
    };
}

/**
 * Gibt das Permission-"ident"-Attribut zurück, indem die Platzhalter befüllt werden.
 * Wird insbesondere bei den Policies genutzt.
 * @param string $format
 * @param array $args
 * @return string
 */
function ident(string $format, $args = []) {
    $args = func_get_args();
    array_shift($args);

    return sprintf($format, ...$args);
}

/**
 * Prüft ob der Request ein Request auf einer API-Route ist oder JSON als Rückgabe erwartet wird.
 */
function is_api_request() {
    if (!request()->route()) {
        return false;
    }

    $middlewares = collect(request()->route()->middleware());

    return request()->expectsJson() || $middlewares->contains('api');
}

/**
 * Explodes a namespace string with version to an array of the three parts "namespace", "identifier" and "version.
 * @return array{namespace: string, identifier: string, version: string|null}
 */
function namespace_parts(string $fullNamespaceWithVersion) {
    $fullNamespaceWithVersion = str_replace('_', '/', $fullNamespaceWithVersion);
    $fullNamespace = explode('@', $fullNamespaceWithVersion)[0];

    return [
        'namespace' => explode('/', $fullNamespace)[0],
        'identifier' => explode('/', $fullNamespace)[1],
        'version' => explode('@', $fullNamespaceWithVersion)[1] ?? null
    ];
}

/**
 * Converts a namespace, e.g. "allisa/demo@1.0.0" to the export json filename.
 * @param string $fullNamespaceWithVersion
 * @param string|null $version Flag to get the export file name with a specific process version, instead
 * of the original process version.
 * @return string
 */
function namespace_to_definition_export_file_name(string $fullNamespaceWithVersion, string|null $version = null): string {
    $parts = namespace_parts($fullNamespaceWithVersion);
    $version = $version ?: $parts['version'];

    return $parts['namespace'] . '_' . $parts['identifier'] . '@' . str_replace('.', '-', $version) . '.json';
}

/**
 * Converts a namespace, e.g. "allisa/demo@1.0.0" to the dependency export json filename.
 * @param string $fullNamespaceWithVersion
 * @return string
 */
function namespace_to_dependencies_export_file_name(string $fullNamespaceWithVersion): string {
    $parts = namespace_parts($fullNamespaceWithVersion);

    return $parts['namespace'] . '_' . $parts['identifier'] . '@' . str_replace('.', '-', $parts['version']) . '_dependencies.json';
}

/**
 * Entfernt doppelte Einträge aus einem Abhängigkeitsarray.
 * @param array $dependencies
 * @return array
 */
function remove_duplicates_from_dependencies(array $dependencies): array {
    foreach ($dependencies as $key => $values) {
        if (Arr::isList($values) && collect($values)->every(fn($item) => is_string($item))) {
            $dependencies[$key] = array_values(array_unique($values));
        }

        if (Arr::isAssoc($values)) {
            foreach ($values as $innerKey => $innerValue) {
                if (collect($innerValue)->every(fn($item) => is_string($item))) {
                    $values[$innerKey] = array_values(array_unique($innerValue));
                }
            }
            $dependencies[$key] = $values;
        }
    }

    return $dependencies;
}

/**
 * Export-Pfad
 * @param string $fileName Dateiname im Verzeichnis
 * @return string
 */
function process_types_path(string $fileName = ''): string {
    return config('app.process_types_dir') . '/' . $fileName;
}

/**
 * @param string $pipeNotation
 * @param Definition $definition
 * @param bool $withName
 * @return string|null
 */
function replace_id_with_reference_in_relation_type_pipe_notation(string $pipeNotation, Definition $definition, bool $withName = true) {
    $namespace = PipeLoader::getFullNamespaceWithVersion($pipeNotation);
    $id = PipeLoader::getKey($pipeNotation);

    if ($namespace && !str_ends_with($namespace, '@latest')) {
        $relationType = ProcessVersion::whereFullNamespace($namespace)->first()?->definition->relationType($id);
        $short = false;
    }
    else {
        $namespace = PipeLoader::getFullNamespace($pipeNotation);
        if ($namespace) {
            $relationType = ProcessVersion::where('full_namespace', 'LIKE', $namespace . '@%')
                ->latest()
                ->first()?->definition->relationType($id);
            $short = false;
        }
        // There is no namespace, that means the relation type is defined in the same process version.
        else {
            $relationType = $definition->relationType($id);
            $short = true;
        }
    }
    if ($relationType) {
        $name = $withName ? $relationType->name : '';

        return PipeLoader::toString($relationType, $name, '', true, 'reference', $short);
    }

    return null;
}

/**
 * @param string $pipeNotation
 * @param Definition $definition
 * @param bool $withName
 * @return string|null
 */
function replace_reference_with_id_in_relation_type_pipe_notation(string $pipeNotation, Definition $definition, bool $withName = true) {
    $namespace = PipeLoader::getFullNamespaceWithVersion($pipeNotation);
    $reference = PipeLoader::getKey($pipeNotation);

    if ($namespace && !str_ends_with($namespace, '@latest')) {
        $relationType = ProcessVersion::whereFullNamespace($namespace)
            ->first()?->definition->relationTypes->firstWhere('reference', '=', $reference);
        $short = false;
    }
    else {
        $namespace = PipeLoader::getFullNamespace($pipeNotation);
        if ($namespace) {
            $relationType = ProcessVersion::where('full_namespace', 'LIKE', $namespace . '@%')
                ->latest()
                ->first()?->definition->relationTypes->firstWhere('reference', '=', $reference);
            $short = false;
        }
        // There is no namespace, that means the relation type is defined in the same process version.
        else {
            $relationType = $definition->relationTypes->firstWhere('reference', '=', $reference);
            $short = true;
        }
    }
    if ($relationType) {
        $name = $withName ? $relationType->name : '';

        return PipeLoader::toString($relationType, $name, '', true, 'id', $short);
    }

    return null;
}

/**
 * Returns an asset path (download url) to an uploaded file,
 * which can be accessed publicly.
 * @param $path
 * @param string $default
 * @return string
 */
function public_storage_url($path, string $default = ''): string {
    if (empty($path)) {
        return $default;
    }

    // Local disc
    if (is_local_disc()) {
        $path = ltrim($path, '/');

        return config('filesystems.disks.local_public.url') . '/' . $path;
    }

    try {
        /** @noinspection PhpUndefinedMethodInspection */
        return Storage::disk(public_disc_name())->publicUrl($path);
    }
        /** @noinspection PhpRedundantCatchClauseInspection */
    catch (FilesystemException $exception) {
        report($exception);

        // Fallback to default logo
        return asset(config('app.logo'));
    }
}

/**
 * Returns the name of the public disk.
 * Public disc has "_public" as suffix, e.g. "local_public" or "s3_public".
 * @return string
 */
function public_disc_name() {
    return config('filesystems.default', 'local') . '_public';
}

/**
 * Flag if a local disk is configured.
 * @return bool
 */
function is_local_disc() {
    return config('filesystems.default', 'local') === 'local';
}