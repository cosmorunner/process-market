<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot() {
        $this->registerMacros();

        // Bind guzzle client with config
        $this->app->bind(Client::class, fn($app) => new Client(config('app.guzzle')));
    }

    /**
     * Registriert Model-Events.
     */
    private function registerMacros() {
        // Ermöglicht das Filtern einer Kollektion mit einer Wildcard bei einem bestimmten Attribut.
        // Beispielsweise bei "Permissions" können jene gefiltert werden, die zu einem
        // bestimmten Prozesstyp gehören: "process_types.7836166d-46d0-4c5e-a14d-7d3baca092c5.*"
        // Es wird erst ab einer "query"-Länge von 3 Zeichen gefiltert.
        Collection::macro('queryAttr', function(string $attr, string $query, bool $endsWith = false) {
            if ($query === '' || $query === '*' || strlen($query) <= 3) {
                return $this;
            }

            if (Str::contains($query, '*')) {
                $parts = explode('.', $query);

                // foo.*
                // foo.*
                if (Str::endsWith($query, '*')) {
                    $search = substr($query, 0, -2);

                    /* @var Collection $this */
                    return collect($this->filter(function($item) use ($search, $parts, $attr) {
                        return Str::startsWith($item->$attr, $search) && count(explode('.', $item->$attr)) >= count($parts);
                    }));
                }

                // *.bar
                if (Str::startsWith($query, '*')) {
                    $search = substr($query, 2);

                    /* @var Collection $this */
                    return collect($this->filter(function($item) use ($search, $parts, $attr) {
                        return Str::endsWith($item->$attr, $search) && count(explode('.', $item->$attr)) >= count($parts);
                    }));
                }

                // foo.*.bar
                if (!Str::startsWith($query, '*') && !Str::endsWith($query, '*')) {
                    $wildcardIndex = array_search('*', $parts);
                    $startPart = implode('.', array_slice($parts, 0, $wildcardIndex));
                    $endPart = implode('.', array_slice($parts, -(count($parts) - (1 + $wildcardIndex))));

                    /* @var Collection $this */
                    return collect($this->filter(function($model) use ($startPart, $endPart, $parts, $attr) {
                        return Str::startsWith($model->$attr, $startPart) && Str::endsWith($model->$attr, $endPart) && count(explode('.', $model->$attr)) === count($parts);
                    }));
                }

                return $this;
            }

            /* @var Collection $this */
            return collect($this->filter(function($item) use ($query, $endsWith, $attr) {
                if ($endsWith) {
                    return Str::endsWith($item->$attr, $query);
                }

                return Str::startsWith($item->$attr, $query);
            }));
        });
    }
}
