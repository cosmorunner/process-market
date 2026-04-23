<?php

namespace App\Interfaces;

use App\Models\License;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Legt fest, dass die Entität lizensierbar ist, d.h. es können Lizenzen für diese Entität (Prozess, Plugin, Lösung) erstellt werden.
 */
interface Licensable {

    /**
     * Alle Lizenzen der Entität.
     * @return MorphMany
     */
    public function licenses(): MorphMany;

    /**
     * Flagge ob angefragte Lizenz-Optionen korrekt sind mit konfigurierten Lizenz-Optionen der Entität.
     * Wenn z.B. ein Prozess nur kostenpflichtige, readonly Lizenzen erlaube, ist eine "open-source"-Anfrage nicht gültig.
     * @param array $requested
     * @return bool
     */
    public function validLicenseRequest(array $requested): bool;

    /**
     * Erstellt für die Entität eine Lizenz.
     * @param $options
     * @param User|Organisation $receiver
     * @return mixed
     */
    public function createLicense($options, User|Organisation $receiver): License;

    /**
     * Gibt den Pfad zum Profil (Lizenzen-Tab) des Eigentümers (Person oder Organisation zurück).
     * @return string
     * @noinspection PhpUnused
     */
    public function authorProfileLicensesPath(): string;

    /**
     * Flagge, ob die Entität eine Open-Source Lizenz hat.
     * @return bool
     * @noinspection PhpUnused
     */
    public function hasOpenSourceLicense(): bool;

    /**
     * Gibt eine Collection aller Benutzer zurück, die auf die Entität Zugriff haben.
     * Entweder der Benutzer der Author ist oder weil ein Benutzer Teil der Organisation ist zu dem der Prozess gehört
     * oder der Benutzer eine Lizenz für den Prozess hat.
     * @return Collection
     */
    public function accessibleUsers(): Collection;

    /**
     * Speichern einer Lizenz für eine Resource.
     * @param array $validatedData
     * @return string Redirect-Url
     */
    public static function storeLicense(array $validatedData): string;

}
