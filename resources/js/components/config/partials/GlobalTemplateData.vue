<template>
    <div>
        <button class="btn btn-link px-0 py-0" type="button" data-toggle="collapse" data-target="#collapseExample"
                aria-expanded="false" aria-controls="collapseExample">
            Globale Daten
        </button>
        <div class="collapse mb-3" id="collapseExample">
            <table class="table table-sm mb-0">
                <tbody>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <small class="text-muted">Variablen-Name</small>
                    </td>
                    <td class="col-4 font-weight-normal border-0">
                        <small class="text-muted">Beschreibung</small>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>app_name</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Plattform-Name</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>app_description</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Plattform-Beschreibung</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>app_url</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Plattform-Url</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>app_image_url</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>URL des Plattform-Logos</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>action_data</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Assoziatives Array mit den Aktions-Daten als Keys und den Werten. Leer beim Smart-Status-Typ "Eigene Logik".</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>process_name</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Name der Prozess-Instanz, in der die Vorlage genutzt wird.</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>process_id</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Id der Prozess-Instanz, in der die Vorlage genutzt wird.</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>process_url</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Absolute URL zur der Prozess-Instanz, in der die Vorlage genutzt wird.</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>process_data</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Assoziatives Array mit den Prozess-Datenfeldern als Keys und den Werten.</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>process_situation</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Assoziatives Array mit den Status-Referenznamen als Keys und einem weiteren assoziativen Array mit den Status-Informationen als Werten.</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>action_type_name</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Name der Aktion, in der die Vorlage genutzt. Leer beim Smart-Status-Typ "Eigene Logik".</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>time_24</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Uhrzeit im 24-Stunden Format, z.B. "08:45".</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>date_ddmmyyyy</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Datum im "dd.mm.yyyy"-Format, z.B. "03.10.1980".</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>user_full_name</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Vor- und Nachname des Benutzers.</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>user_first_name</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Vorname des Benutzers. Leer beim Smart-Status-Typ "Eigene Logik".</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>user_last_name</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Nachname des Benutzers. Leer beim Smart-Status-Typ "Eigene Logik".</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>user_email</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>E-Mail des Benutzers. Leer beim Smart-Status-Typ "Eigene Logik".</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {};
</script>
