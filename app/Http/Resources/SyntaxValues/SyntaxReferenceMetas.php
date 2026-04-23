<?php

namespace App\Http\Resources\SyntaxValues;

use App\Http\Resources\Cache\ProcessVersionCache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxReferenceMetas
 * Metadaten der referenzierten Prozesse.
 * @package App\Http\Resources
 */
class SyntaxReferenceMetas extends JsonResource {

    /**
     * @var ProcessVersionCache
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $items = [];
        $processVersionCaches = $this->additional['processVersionCaches'] ?? collect();

        foreach ($this->resource['process_version_simple']['relation_types'] as $relationType) {
            if (!in_array($relationType['connection_type'], ['n-1', '1-1'])) {
                continue;
            }

            $label = '[' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Id';
            $value = '[[process.references.' . $relationType['reference'] . '.meta.id]]';
            $items[] = (array) new Item($label, $value, 'reference.metas');

            $label = '[' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Model-Pipe-Notation';
            $value = '[[process.references.' . $relationType['reference'] . '.meta.pipe_notation]]';
            $items[] = (array) new Item($label, $value, 'reference.metas');

            $label = '[' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Name';
            $value = '[[process.references.' . $relationType['reference'] . '.meta.name]]';
            $items[] = (array) new Item($label, $value, 'reference.metas');

            $label = '[' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Beschreibung';
            $value = '[[process.references.' . $relationType['reference'] . '.meta.description]]';
            $items[] = (array) new Item($label, $value, 'reference.metas');

            $label = '[' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Tags';
            $value = '[[process.references.' . $relationType['reference'] . '.meta.tags]]';
            $items[] = (array) new Item($label, $value, 'reference.metas');

            $label = '[' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Icon';
            $value = '[[process.references.' . $relationType['reference'] . '.meta.image]]';
            $items[] = (array) new Item($label, $value, 'reference.metas');

            $label = '[' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Url';
            $value = '[[process.references.' . $relationType['reference'] . '.meta.url]]';
            $items[] = (array) new Item($label, $value, 'reference.metas');
        }

        // External relationtypes
        foreach ($processVersionCaches as $processVersionCache) {
            foreach ($processVersionCache['process_version_simple']['relation_types'] as $relationType) {
                // Here we switch the "n-1" to "1-n" because when the syntax ist used, it refers to the external relationtype.
                if (!in_array($relationType['connection_type'], ['1-n', '1-1'])) {
                    continue;
                }

                $namespace = $processVersionCache['process_version_simple']['full_namespace_without_version'];

                $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Id';
                $value = '[[process.references.' . $namespace . '::' . $relationType['reference'] . '.meta.id]]';
                $items[] = (array) new Item($label, $value, 'reference.metas');

                $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Model-Pipe-Notation';
                $value = '[[process.references.' . $namespace . '::' . $relationType['reference'] . '.pipe_notation]]';
                $items[] = (array) new Item($label, $value, 'reference.metas');

                $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Name';
                $value = '[[process.references.' . $namespace . '::' . $relationType['reference'] . '.meta.name]]';
                $items[] = (array) new Item($label, $value, 'reference.metas');

                $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Beschreibung';
                $value = '[[process.references.' . $namespace . '::' . $relationType['reference'] . '.meta.description]]';
                $items[] = (array) new Item($label, $value, 'reference.metas');

                $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Tags';
                $value = '[[process.references.' . $namespace . '::' . $relationType['reference'] . '.meta.tags]]';
                $items[] = (array) new Item($label, $value, 'reference.metas');

                $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Icon';
                $value = '[[process.references.' . $namespace . '::' . $relationType['reference'] . '.meta.image]]';
                $items[] = (array) new Item($label, $value, 'reference.metas');

                $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - ' . 'Prozess-Meta - Url';
                $value = '[[process.references.' . $namespace . '::' . $relationType['reference'] . '.meta.url]]';
                $items[] = (array) new Item($label, $value, 'reference.metas');
            }
        }

        return $items;
    }
}
