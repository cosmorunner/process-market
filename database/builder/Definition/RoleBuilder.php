<?php

namespace Database\Builder\Definition;

use App\Enums\ProcessRolePermissions;
use App\ProcessType\Role;
use Database\Builder\AbstractBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Ramsey\Uuid\Uuid;

/**
 * Class RoleBuilder
 * @package Database\Builder
 */
class RoleBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'name' => 'Maintainer',
            'description' => '',
            'permissions' => []
        ];
    }

    /**
     * @param array $attributes
     * @return Role
     */
    public function make(array $attributes = []) {
        return new Role(array_merge($this->state, $attributes));
    }

    /**
     * @param array $permissions
     * @return $this
     */
    public function withPermissions(array $permissions) {
        return $this->state([
            'permissions' => $this->convertObjectToArray($permissions)
        ]);
    }

    /**
     * @param array $idents
     * @return RoleBuilder
     */
    public function withPermissionIdents(array $idents) {
        $permissions = collect($idents)->map(fn($ident) => app(PermissionBuilder::class)->withIdent($ident)->make())->toArray();

        return $this->withPermissions($permissions);
    }

    /**
     * Fügt der Rolle das Recht hinzu, alle Aktionen in einer Prozess-Instanz ausführen zu dürfen.
     * @return RoleBuilder
     * @throws BindingResolutionException
     */
    public function withActionExecutingPermissions() {
        $permission = app(PermissionBuilder::class)
            ->withName('Aktionen ausführen')
            ->withIdent(ProcessRolePermissions::ExecuteActions->value)
            ->make();

        return $this->state([
            'permissions' => $this->convertObjectToArray([$permission])
        ]);

    }

    /**
     * Fügt alle Rechte zu der Prozess-Rolle hinzu.
     * @return RoleBuilder
     * @throws BindingResolutionException
     */
    public function withAllPermissions() {
        $permissions = [
            app(PermissionBuilder::class)
                ->withName('Aktionen ausführen')
                ->withIdent(ProcessRolePermissions::ExecuteActions->value)
                ->make(),
            app(PermissionBuilder::class)
                ->withName('Aktionen rückgängig machen')
                ->withIdent(ProcessRolePermissions::RevertActions->value)
                ->make(),
            app(PermissionBuilder::class)
                ->withName('Prozessdaten einsehen')
                ->withIdent(ProcessRolePermissions::ViewData->value)
                ->make(),
            app(PermissionBuilder::class)
                ->withName('Situation einsehen')
                ->withIdent(ProcessRolePermissions::ViewSituation->value)
                ->make(),
            app(PermissionBuilder::class)
                ->withName('Historie einsehen')
                ->withIdent(ProcessRolePermissions::ViewHistory->value)
                ->make(),
            app(PermissionBuilder::class)
                ->withName('Listen einsehen')
                ->withIdent(ProcessRolePermissions::ViewLists->value)
                ->make(),
            app(PermissionBuilder::class)
                ->withName('Alle Menüpunkte sehen')
                ->withIdent(ProcessRolePermissions::ViewMenuItems->value)
                ->make(),
            app(PermissionBuilder::class)
                ->withName('Artefakte einsehen')
                ->withIdent(ProcessRolePermissions::ViewArtifacts->value)
                ->make()
        ];

        return $this->state([
            'permissions' => $this->convertObjectToArray($permissions)
        ]);
    }

}
