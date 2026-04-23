<?php

namespace App\ProcessType;

use App\Interfaces\Iconable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Class Role
 * @package App
 */
class Role extends AbstractModel implements Iconable {

    public string $id;
    public string $name;
    public string $description;
    public Collection $permissions;
    public bool $active = true;
    public Definition $definition;

    protected $collections = [
        'permissions' => Permission::class
    ];

    /**
     * @param Definition $parent
     */
    protected function setup($parent) {
        $this->definition = $parent;
    }

    /**
     * Prüft, ob die Rolle ein bestimmtes Recht ausüben kann.
     * @param string $permissionIdent
     * @return bool
     */
    public function can(string $permissionIdent) {
        return $this->permissions->where('ident', '=', $permissionIdent)->isNotEmpty();
    }

    /**
     * Prüft, ob die Rolle eines der angegebenen Rechte ausüben kann.
     * @param Collection $permissionIdents
     * @return bool
     * @noinspection PhpUnused
     */
    public function canAny(Collection $permissionIdents) {
        return $this->permissions->pluck('ident')->intersect($permissionIdents)->count() > 0;
    }

    /**
     * @inheritDoc
     */
    public static function icon(): string {
        return 'assignment_ind';
    }

    /**
     * Erzeugt ein neues Role-Object mit Standardwerten.
     * @param array $options
     * @return Role
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()
                ->toString(),
            'name' => trim($options['name'] ?? 'Demo-Name'),
            'description' => trim($options['description'] ?? ''),
            'permissions' => collect($options['permissions'] ?? [])->map(function ($permission) {
                $ident = $permission['ident'];
                $availableOptions = collect(config('permissions'))->flatten(1);
                $options = $availableOptions->firstWhere('ident', '=', Permission::identToTemplate($ident));

                if ($options) {
                    $options['ident'] = $ident;
                    $options['conditions'] = $permission['conditions'];

                    return Permission::make($options);
                }

                // Im Falle einer Output-Permission gibt es keine UUID sondern den Output-Namen.
                if (Str::startsWith($ident, 'process_type.output.') && Str::endsWith($ident, '.view')) {
                    $options = $availableOptions->firstWhere('ident', '=', 'process_type.output.*.view');

                    if ($options) {
                        $options['ident'] = $ident;
                        $options['conditions'] = $permission['conditions'];

                        return Permission::make($options);
                    }
                }

                return null;
            })->filter(fn($permission) => $permission instanceof Permission)->values()
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'permissions' => $this->permissions->map(fn(Permission $permission) => $permission->toArray())->toArray()
        ];
    }


}

