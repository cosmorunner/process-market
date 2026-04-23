<template>
    <div>
        <template>
            <div class="form-group input-group-sm mb-4 d-flex flex-column">
                <small class="alert alert-warning">Dieser Prozessor ist experimentell.</small>
                <label class="mb-0" for="profile">Profil</label>
                <select class="form-control" id="profile" name="profile" @change="onChangeProfile"
                        :value="options.profile" :disabled="!ui.editable">
                    <option value="">Bitte wählen...</option>
                    <option value="Minimum">ZUGFeRD Minimum</option>
                    <option value="XRechnung30">X-Rechnung 3.0</option>
                    <option value="HGB">Handelsgesetzbuch (HGB)</option>
                    <!--<option value="Basic">Basic</option>-->
                </select>
            </div>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="action_data_output">Ausgabe</label>
                <select class="form-control" id="action_data_output"
                        @change="$emit('option-change', 'action_data_output', $event.target.value)"
                        :disabled="!ui.editable" :value="options.action_data_output">
                    <option value="">-</option>
                    <option v-for="output in sortedOutputs" :value="output.name">
                        {{ 'Aktions-Daten - ' + output.name }}
                    </option>
                </select>
                <small class="text-muted">Model-Pipe-Notation des Dokumentes auf ein Aktions-Datenfeld
                    schreiben.</small>
            </div>
            <div class="form-group input-group-sm mb-2">
                <div class="form-group input-group-sm mb-2 d-flex flex-column" v-for="attribute in profileOptions">
                    <label class="mb-0" v-bind:for="attribute.key">{{ attribute.label }}</label>
                    <div v-for="description in attribute.description">
                        <small v-if="description instanceof Object" class="text-muted mt-2">
                            <a v-bind:href="description.href">
                                {{ description.label }}
                            </a>
                        </small>
                        <small v-else class="text-muted mt-2">
                            {{ description }}
                        </small>
                    </div>
                    <textarea v-bind:id="attribute.key" class="form-control" rows="1"
                              @change="setOptionsValue(attribute.key, $event.target.value)" :readonly="!ui.editable"
                              v-bind:value="profile_parameters[attribute.key]"/>
                    <OptionBadgesWithText :value="profile_parameters[attribute.key]" display-block hide-on-empty/>

                    <DropdownSelector class="mt-2"
                                      :syntax-include="['process.outputs', 'process.meta', 'action.outputs', 'reference.outputs', 'date', 'system']"
                                      @selected="setOptionsValue(attribute.key, $event.value_with_label)"/>

                </div>
            </div>

        </template>
    </div>
</template>

<script>

import {mapGetters} from "vuex";
import utils from "../../../config-utils";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText.vue";
import OptionBadges from "../../utils/OptionBadges.vue";
import AutocompleteSelector from "../../utils/AutocompleteSelector.vue";
import DropdownSelector from "../../utils/DropdownSelector.vue";

export default {
    data() {
        return {
            profile: '',
            profile_parameters: [],
        };
    },
    mounted() {
        this.profile = this.options.profile;
        this.profile_parameters = this.options.profile_parameters;
    },
    components: {
        DropdownSelector,
        AutocompleteSelector,
        OptionBadges,
        OptionBadgesWithText
    },
    props: {
        options: Object,
        actionType: Object,
    },
    computed: {
        ...mapGetters([
            'environments',
            'ui'
        ]),
        sortedOutputs() {
            return [...this.actionType.outputs].sort((a, b) => a.name.localeCompare(b.name));
        },
        profileOptions() {
            switch (this.profile) {
                case 'XRechnung30':
                    return this.xRechnung30Profile;
                case 'Minimum':
                    return this.minimalProfile;
                case 'HGB':
                    return this.hgbProfile;
                default:
                    return [];
            }
        },
        minimalProfile() {
            // as the minimal profile has just simple imput fields, this can be mapped 1on1
            return [
                {
                    label: 'Name des Dokuments',
                    key: 'file_name',
                    description: ['Name der Datei, welche durch diesen Prozessor erstellt wird.']
                },
                {
                    label: 'Number des Dokuments',
                    key: 'document_no',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Rechnungstyp',
                    key: 'document_type_code',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Ausstellungsdatum des Dokuments',
                    key: 'document_issue_date',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Währung des Dokuments',
                    key: 'document_currency',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Name des Verkäufers',
                    key: 'seller_name',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Land des Verkäufers',
                    key: 'seller_country',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Steuernummer (FC) des Verkäufers',
                    key: 'seller_tax_registration_fc',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Umsatzsteuer-ID (VA) des Verkäufers',
                    key: 'seller_tax_registration_va',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Name des Käufers',
                    key: 'buyer_name',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Gesamtbetrag (Brutto)',
                    key: 'summation_grand_total_amount',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Zu zahlender Betrag',
                    key: 'summation_due_total_payable_amount',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Gesamtsteuerbetrag',
                    key: 'summation_tax_total_amount',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Nettobetrag (Steuerbasis)',
                    key: 'summation_tax_basis_total_amount',
                    description: ['Pflichtfeld']
                },
            ];
        },
        xRechnung30Profile() {
            return [
                {
                    label: 'Name des Dokuments',
                    key: 'file_name',
                    description: [
                        'Pflichtfeld',
                        'Name der Datei, welche durch diesen Prozessor erstellt wird.'
                    ]
                },
                {
                    label: 'Nummer des Dokuments',
                    key: 'document_no',
                    description: [
                        'Pflichtfeld',
                        'Jedes Rechnungsdokument wird vom Aussteller mit einer Nummer Identifiziert.'

                    ]
                },
                {
                    label: 'Type des Dokuments',
                    key: 'document_type_code',
                    description: [
                        'Pflichtfeld',
                        'Hiermit wird angegeben, um was für ein Dokument es sich handelt.',
                        'Dies kann z.B. eine Rechnung (380), oder eine Gutschrift (381) sein.',
                        {
                            label: 'Dokumentation der unterschiedlichen Typen.',
                            href: 'https://github.com/horstoeko/zugferd/blob/master/src/codelists/ZugferdInvoiceType.php'
                        }
                    ]
                },
                {
                    label: 'Austellungsdatum des Dokuments',
                    key: 'document_issue_date',
                    description: [
                        'Pflichtfeld',
                        'Hiermit wird angegeben, wann das Dokument erstellt wurde.'
                    ]
                },
                {
                    label: 'Währung des Dokuments',
                    key: 'document_currency',
                    description: [
                        'Pflichtfeld',
                        'Hiermit wird angegeben, was die Hauptwährung des Dokuments ist.'
                    ]
                },
                {
                    label: 'Zahlungsmethode',
                    key: 'payment_means_type',
                    description: [
                        'Pflichtfeld',
                        'Wählen Sie eine der folgenden Zahlungsmethoden:',
                        '- \'credit_transfer\' (Überweisung)',
                        '- \'credit_transfer_no_sepa\' (Überweisung ohne SEPA)',
                        '- \'direct_debit\' (Lastschrift)',
                        '- \'direct_debit_no_sepa\' (Lastschrift ohne SEPA)',
                        '- \'payment_card\' (Zahlung per Karte)',
                        'Falls kein Wert angegeben wird, kann alternativ das Feld "Zahlungsmethode (Code)" genutzt werden.'
                    ]
                },
                {
                    label: 'Zahlungsmethode (Code)',
                    key: 'payment_means_type_code',
                    description: [
                        'Falls das Feld "Zahlungsmethode" nicht ausgefüllt ist, kann hier ein Code zur Ermittlung der Zahlungsmethode angegeben werden.',
                        'Diese Methode unterstützt jedoch nicht alle Zahlungsmöglichkeiten. Es wird daher empfohlen, nach Möglichkeit direkt eine der oben genannten Zahlungsmethoden anzugeben.'
                    ]
                },
                {
                    label: 'IBAN des Zahlungsempfängers',
                    key: 'payment_means_payee_iban',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'credit_transfer\' (SEPA-Überweisung)',
                        '- \'credit_transfer_no_sepa\' (Überweisung ohne SEPA)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'Zusätzliche Zahlungsinformationen',
                    key: 'payment_means_information',
                    description: [
                        'Wird verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'Kartentyp',
                    key: 'payment_means_card_type',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'payment_card\' (Kartenzahlung)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'Karten-ID',
                    key: 'payment_means_card_id',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'payment_card\' (Kartenzahlung)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'Name des Karteninhabers',
                    key: 'payment_means_card_holding_name',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'payment_card\' (Kartenzahlung)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'IBAN des Käufers',
                    key: 'payment_means_buyer_iban',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'direct_debit\' (SEPA-Lastschrift)',
                        '- \'direct_debit_no_sepa\' (Lastschrift ohne SEPA)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'Kontoname des Zahlungsempfängers',
                    key: 'payment_means_payee_account_name',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'credit_transfer\' (SEPA-Überweisung)',
                        '- \'credit_transfer_no_sepa\' (Überweisung ohne SEPA)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'Zahlungsempfänger-ID',
                    key: 'payment_means_payee_prop_id',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'credit_transfer\' (SEPA-Überweisung)',
                        '- \'credit_transfer_no_sepa\' (Überweisung ohne SEPA)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'BIC des Zahlungsempfängers',
                    key: 'payment_means_payee_bic',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'credit_transfer\' (SEPA-Überweisung)',
                        '- \'credit_transfer_no_sepa\' (Überweisung ohne SEPA)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'Gläubiger-Referenz-ID',
                    key: 'payment_means_creditor_reference_id',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'direct_debit\' (SEPA-Lastschrift)',
                        '- \'direct_debit_no_sepa\' (Lastschrift ohne SEPA)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'Zahlungsreferenz',
                    key: 'payment_means_payment_reference',
                    description: [
                        'Relevant für die folgenden Zahlungsmethoden:',
                        '- \'credit_transfer\' (SEPA-Überweisung)',
                        '- \'credit_transfer_no_sepa\' (Überweisung ohne SEPA)',
                        'Wird außerdem verwendet, wenn keine Zahlungsmethode angegeben wurde und diese über den Code ermittelt wird.'
                    ]
                },
                {
                    label: 'Beschreibung der Zahlungsbedingungen',
                    key: 'payment_term_description',
                    description: [
                        'Optional',
                        'Gibt eine textuelle Beschreibung der vereinbarten Zahlungsbedingungen an, z. B. "Zahlbar innerhalb von 30 Tagen netto".'
                    ]
                },
                {
                    label: 'Fälligkeitsdatum der Zahlung',
                    key: 'payment_term_due_date',
                    description: [
                        'Pflichtfeld',
                        'Das Datum, bis zu dem die Zahlung spätestens erfolgen muss.'
                    ]
                },
                {
                    label: 'Lastschriftmandatsreferenz',
                    key: 'payment_term_debit_mandate',
                    description: [
                        'Optional',
                        'Die Referenz des Lastschriftmandats, falls die Zahlung per Lastschrift erfolgt.'
                    ]
                },
                {
                    label: 'Name des Verkäufers',
                    key: 'seller_name',
                    description: [
                        'Pflichtfeld',
                        'Der vollständige Name oder die offizielle Bezeichnung des Verkäufers.'
                    ]
                },
                {
                    label: 'Verkäufer-ID',
                    key: 'seller_id',
                    description: [
                        'Optional',
                        'Eine interne Identifikationsnummer für den Verkäufer, die vom System oder Unternehmen vergeben wird.'
                    ]
                },
                {
                    label: 'Globale Verkäufer-ID',
                    key: 'seller_global_id',
                    description: [
                        'Optional',
                        'Eine weltweit eindeutige Identifikationsnummer für den Verkäufer, z. B. eine DUNS- oder GLN-Nummer.'
                    ]
                },
                {
                    label: 'Typ der globalen Verkäufer-ID',
                    key: 'seller_global_id_type',
                    description: [
                        'Optional',
                        'Der Typ der globalen Identifikationsnummer, z. B. "DUNS", "GLN" oder eine andere eindeutige ID-Kategorie.'
                    ]
                },
                {
                    label: 'Steuernummer des Verkäufers',
                    key: 'seller_tax_number',
                    description: [
                        'Pflichtfeld',
                        'Die nationale Steuernummer des Verkäufers, die zur Identifikation beim Finanzamt dient.'
                    ]
                },
                {
                    label: 'Umsatzsteuer-Identifikationsnummer',
                    key: 'seller_vat_registration',
                    description: [
                        'Optional',
                        'Die Umsatzsteuer-Identifikationsnummer (USt-IdNr.) des Verkäufers, erforderlich für innergemeinschaftlichen Handel in der EU.'
                    ]
                },
                {
                    label: 'Straße und Hausnummer des Verkäufers',
                    key: 'seller_address_line_one',
                    description: [
                        'Optional',
                        'Die erste Zeile der Adresse des Verkäufers, normalerweise bestehend aus Straße und Hausnummer.'
                    ]
                },
                {
                    label: 'Adresszusatz des Verkäufers',
                    key: 'seller_address_line_two',
                    description: [
                        'Optional',
                        'Eine optionale zweite Zeile für zusätzliche Adressinformationen, z. B. Gebäude, Abteilung oder Stockwerk.'
                    ]
                },
                {
                    label: 'Weitere Adressdetails des Verkäufers',
                    key: 'seller_address_line_three',
                    description: [
                        'Optional',
                        'Eine optionale dritte Zeile für weitere Adressinformationen, falls benötigt.'
                    ]
                },
                {
                    label: 'Postleitzahl des Verkäufers',
                    key: 'seller_address_line_post_code',
                    description: [
                        'Pflichtfeld',
                        'Die Postleitzahl des Verkäufers, die zur eindeutigen Lokalisierung der Adresse dient.'
                    ]
                },
                {
                    label: 'Stadt des Verkäufers',
                    key: 'seller_address_line_city',
                    description: [
                        'Pflichtfeld',
                        'Die Stadt, in der sich der Verkäufer befindet.'
                    ]
                },
                {
                    label: 'Ländercode des Verkäufers',
                    key: 'seller_address_line_country',
                    description: [
                        'Pflichtfeld',
                        'Das Land des Verkäufers, angegeben als ISO-3166-1 Alpha-2 Code (z. B. "DE" für Deutschland).',
                        {
                            label: 'Liste ISO-3166-1-Kodierliste',
                            href: 'https://de.wikipedia.org/wiki/ISO-3166-1-Kodierliste'
                        }
                    ]
                },
                {
                    label: 'Ansprechpartner des Verkäufers',
                    key: 'seller_contact_person',
                    description: [
                        'Optional',
                        'Der Name der Ansprechperson beim Verkäufer, z. B. für Rückfragen oder geschäftliche Kommunikation.'
                    ]
                },
                {
                    label: 'Abteilung des Verkäufers',
                    key: 'seller_contact_department',
                    description: [
                        'Optional',
                        'Die Abteilung, in der die Ansprechperson tätig ist, z. B. "Vertrieb" oder "Buchhaltung".'
                    ]
                },
                {
                    label: 'Telefonnummer des Verkäufers',
                    key: 'seller_contact_phone_no',
                    description: [
                        'Pflichtfeld',
                        'Die Telefonnummer, unter der der Verkäufer oder die Ansprechperson erreichbar ist.'
                    ]
                },
                {
                    label: 'Faxnummer des Verkäufers',
                    key: 'seller_contact_fax_no',
                    description: [
                        'Optional',
                        'Die Faxnummer des Verkäufers, falls verfügbar.'
                    ]
                },
                {
                    label: 'E-Mail-Adresse des Verkäufers',
                    key: 'seller_contact_email',
                    description: [
                        'Pflichtfeld',
                        'Die geschäftliche E-Mail-Adresse des Verkäufers oder der Ansprechperson.'
                    ]
                },
                {
                    label: 'Kommunikationsschema des Verkäufers',
                    key: 'seller_communication_scheme',
                    description: [
                        'Pflichtfeld',
                        'Das verwendete Kommunikationsprotokoll oder Schema für den Nachrichtenaustausch, z. B. "EDI", "PEPPOL", "EM" oder eine andere digitale Übertragungsmethode.'
                    ]
                },
                {
                    label: 'Kommunikations-URI des Verkäufers',
                    key: 'seller_communication_uri',
                    description: [
                        'Pflichtfeld',
                        'Die eindeutige Adresse (URI), über die der Verkäufer elektronisch erreichbar ist, z. B. eine PEPPOL-ID, Email-Adresse oder eine API-Endpunkt-URL.'
                    ]
                },
                {
                    label: 'Name des Käufers',
                    key: 'buyer_name',
                    description: [
                        'Pflichtfeld',
                        'Der vollständige Name oder die offizielle Bezeichnung des Käufers.'
                    ]
                },
                {
                    label: 'Käufer-ID',
                    key: 'buyer_id',
                    description: [
                        'Optional',
                        'Eine interne Identifikationsnummer für den Käufer, die vom System oder Unternehmen vergeben wird.'
                    ]
                },
                {
                    label: 'Straße und Hausnummer des Käufers',
                    key: 'buyer_address_line_one',
                    description: [
                        'Optional',
                        'Die erste Zeile der Adresse des Käufers, normalerweise bestehend aus Straße und Hausnummer.'
                    ]
                },
                {
                    label: 'Adresszusatz des Käufers',
                    key: 'buyer_address_line_two',
                    description: [
                        'Optional',
                        'Eine optionale zweite Zeile für zusätzliche Adressinformationen, z. B. Gebäude, Abteilung oder Stockwerk.'
                    ]
                },
                {
                    label: 'Weitere Adressdetails des Käufers',
                    key: 'buyer_address_line_three',
                    description: [
                        'Optional',
                        'Eine optionale dritte Zeile für weitere Adressinformationen, falls benötigt.'
                    ]
                },
                {
                    label: 'Postleitzahl des Käufers',
                    key: 'buyer_address_line_post_code',
                    description: [
                        'Pflichtfeld',
                        'Die Postleitzahl des Käufers, die zur eindeutigen Lokalisierung der Adresse dient.'
                    ]
                },
                {
                    label: 'Stadt des Käufers',
                    key: 'buyer_address_line_city',
                    description: [
                        'Pflichtfeld',
                        'Die Stadt, in der sich der Käufer befindet.'
                    ]
                },
                {
                    label: 'Ländercode des Käufers',
                    key: 'buyer_address_line_country',
                    description: [
                        'Pflichtfeld',
                        'Das Land des Käufers, angegeben als ISO-3166-1 Alpha-2 Code (z. B. "DE" für Deutschland).'
                    ]
                },
                {
                    label: 'Ansprechpartner des Käufers',
                    key: 'buyer_contact_person',
                    description: [
                        'Optional',
                        'Der Name der Ansprechperson beim Käufer, z. B. für Rückfragen oder geschäftliche Kommunikation.'
                    ]
                },
                {
                    label: 'Abteilung des Käufers',
                    key: 'buyer_contact_department',
                    description: [
                        'Optional',
                        'Die Abteilung, in der die Ansprechperson tätig ist, z. B. "Vertrieb" oder "Kundendienst".'
                    ]
                },
                {
                    label: 'Telefonnummer des Käufers',
                    key: 'buyer_contact_phone_no',
                    description: [
                        'Optional',
                        'Die Telefonnummer, unter der der Käufer oder die Ansprechperson erreichbar ist.'
                    ]
                },
                {
                    label: 'Faxnummer des Käufers',
                    key: 'buyer_contact_fax_no',
                    description: [
                        'Optional',
                        'Die Faxnummer des Käufers, falls verfügbar.'
                    ]
                },
                {
                    label: 'E-Mail-Adresse des Käufers',
                    key: 'buyer_contact_email',
                    description: [
                        'Optional',
                        'Die geschäftliche E-Mail-Adresse des Käufers oder der Ansprechperson.'
                    ]
                },
                {
                    label: 'Kommunikationsschema des Käufers',
                    key: 'buyer_communication_scheme',
                    description: [
                        'Pflichtfeld',
                        'Das verwendete Kommunikationsprotokoll oder Schema für den Nachrichtenaustausch, z. B. "EDI", "PEPPOL" oder eine andere digitale Übertragungsmethode.'
                    ]
                },
                {
                    label: 'Kommunikations-URI des Käufers',
                    key: 'buyer_communication_uri',
                    description: [
                        'Pflichtfeld',
                        'Die eindeutige Adresse (URI), über die der Käufer elektronisch erreichbar ist, z. B. eine PEPPOL-ID oder eine API-Endpunkt-URL.'
                    ]
                },
                {
                    label: 'Käuferreferenz',
                    key: 'buyer_reference',
                    description: [
                        'Pflichtfeld',
                        'Eine interne Referenznummer oder ein Code, der vom Käufer zur Identifikation der Transaktion oder Bestellung vergeben wird.'
                    ]
                },
                {
                    label: 'Lieferdatum',
                    key: 'supply_chain_event',
                    description: [
                        'Optional',
                        'Das Datum, wann die Lieferung stattgefunden hat.',
                    ]
                },
                {
                    label: 'Positionen',
                    key: 'document_lines',
                    description: [
                        'Pflichtfeld',
                        'Eine Sammlung von Zeilen (JSON-Array), die die Einzelposten des Dokuments darstellen, wie z. B. Produkte oder Dienstleistungen. Jede Zeile enthält detaillierte Informationen wie Produktname, Menge, Preis, Steuersatz und Steuerinformationen. Die Struktur der Daten sollte im JSON-Format vorliegen und wie folgt aufgebaut sein:',
                        '',
                        'Beispiel für eine Dokumentenposition:',
                        '{',
                        '   "line_no": "1",',
                        '   "product_name": "Einfacher Artikel",',
                        '   "product_description": "1 Stück",',
                        '   "product_seller_assigned_id": "EIN001",',
                        '   "price": "100.00",',
                        '   "quantity_billed": "1",',
                        '   "quantity_billed_unit_code": "H87",',
                        '   "line_tax_rate": "S",',
                        '   "line_tax_type": "VAT",',
                        '   "line_tax_applicable_percent": "20",',
                        '   "line_summation_total": "100.00"',
                        '}',
                        '',
                        'Erklärung der Felder:',
                        '- `line_no`: Die Nummer der Zeile zur Identifikation des Postens im Dokument. (Pflicht)',
                        '- `product_name`: Der Name des abgerechneten Produkts oder der Dienstleistung. (Pflicht)',
                        '- `product_description`: Eine kurze Beschreibung des Produkts oder der Dienstleistung. (Optional)',
                        '- `product_seller_assigned_id`: Eine vom Verkäufer zugewiesene ID für das Produkt. (Optional)',
                        '- `price`: Der Preis des Produkts oder der Dienstleistung. (Pflicht)',
                        '- `quantity_billed`: Die Anzahl der abgerechneten Einheiten. (Pflicht)',
                        '- `quantity_billed_unit_code`: Der Maßeinheitencode für die abgerechneten Einheiten. (Pflicht)',
                        '- `line_tax_rate`: Der angewendete Steuersatz, z. B. "S" für Standardsteuersatz. (Pflicht)',
                        '- `line_tax_type`: Der Typ der angewendeten Steuer, z. B. "VAT" für Mehrwertsteuer. (Pflicht)',
                        '- `line_tax_applicable_percent`: Der Prozentsatz der Steuer, der auf das Produkt angewendet wird. (Pflicht)',
                        '- `line_summation_total`: Der Gesamtbetrag für diese Zeile, einschließlich Preis und Steuern. (Pflicht)'
                    ]
                },
                {
                    label: 'Steuerinformationen',
                    key: 'tax_lines',
                    description: [
                        'Pflichtfeld',
                        'Eine Sammlung von Steuerinformationen (JSON-Array), die die angewendeten Steuern auf das Dokument darstellen. Jede Zeile enthält detaillierte Informationen wie den Steuersatz, den Steuerbetrag und die Basis für die Berechnung der Steuer. Die Struktur der Daten sollte im JSON-Format vorliegen und wie folgt aufgebaut sein:',
                        '',
                        'Beispiel für eine Steuerzeile:',
                        '{',
                        '   "tax_rate": "S",',
                        '   "tax_type": "VAT",',
                        '   "tax_basis_amount": "100.00",',
                        '   "tax_calculated_amount": "20.00",',
                        '   "tax_rate_applicable_percent": "20.00"',
                        '}',
                        '',
                        'Erklärung der Felder:',
                        '- `tax_rate`: Der angewendete Steuersatz, z. B. "S" für den Standardsteuersatz. (Pflicht)',
                        '- `tax_type`: Der Typ der angewendeten Steuer, z. B. "VAT" für Mehrwertsteuer. (Pflicht)',
                        '- `tax_basis_amount`: Der Betrag, auf dem die Steuer berechnet wird. (Pflicht)',
                        '- `tax_calculated_amount`: Der berechnete Steuerbetrag. (Pflicht)',
                        '- `tax_rate_applicable_percent`: Der Prozentsatz der angewendeten Steuer. (Pflicht)'
                    ]
                },
                {
                    label: 'Gesamtbetrag der Positionen',
                    key: 'summation_line_total_amount',
                    description: [
                        'Pflichtfeld',
                        'Der Gesamtbetrag der Positionen im Dokument, d.h. die Summe aller Artikel- oder Dienstleistungsbeträge ohne Steuern und ohne Abzüge oder Zuschläge.'
                    ]
                },
                {
                    label: 'Gesamtbetrag der Rabatte',
                    key: 'summation_allowance_total_amount',
                    description: [
                        'Optional',
                        'Der Gesamtbetrag aller gewährten Rabatte oder Preisnachlässe, die auf die Positionen angewendet wurden.'
                    ]
                },
                {
                    label: 'Gesamtbetrag der Zuschläge',
                    key: 'summation_charge_total_amount',
                    description: [
                        'Optional',
                        'Der Gesamtbetrag aller zusätzlichen Gebühren oder Zuschläge, die auf die Positionen angewendet wurden.'
                    ]
                },
                {
                    label: 'Gesamtbetrag (inkl. Zuschläge und Rabatte)',
                    key: 'summation_grand_total_amount',
                    description: [
                        'Pflichtfeld',
                        'Der endgültige Gesamtbetrag des Dokuments nach Anwendung aller Rabatte, Zuschläge und Positionen. Dies ist der Betrag, den der Käufer zu zahlen hat, einschließlich aller relevanten Steuern.'
                    ]
                },
                {
                    label: 'Gesamtbetrag der fälligen Zahlungen',
                    key: 'summation_due_total_payable_amount',
                    description: [
                        'Pflichtfeld',
                        'Der Betrag, der zum angegebenen Fälligkeitsdatum tatsächlich zu zahlen ist. Dies kann nach Berücksichtigung von fälligen Zahlungen und offenen Beträgen berechnet werden.'
                    ]
                },
                {
                    label: 'Gesamtbetrag der Steuern',
                    key: 'summation_tax_total_amount',
                    description: [
                        'Pflichtfeld',
                        'Der Gesamtbetrag der auf das Dokument angewendeten Steuern. Dies umfasst die Steuern auf alle Positionen, Rabatte und Zuschläge.'
                    ]
                },
                {
                    label: 'Gesamtbetrag der Steuerbemessungsgrundlage',
                    key: 'summation_tax_basis_total_amount',
                    description: [
                        'Pflichtfeld',
                        'Der Gesamtbetrag der Steuerbemessungsgrundlage, auf den die Steuer berechnet wird. Dieser Betrag bezieht sich auf den Gesamtbetrag ohne Steuern, aber unter Berücksichtigung von Rabatten und Zuschlägen.'
                    ]
                }

            ];
        },
        hgbProfile() {
            return [
                {
                    label: 'Name des Dokuments',
                    key: 'file_name',
                    description: [
                        'Pflichtfeld',
                        'Name der Datei, welche durch diesen Prozessor erstellt wird.'
                    ]
                },
                {
                    label: 'Dokumentennummer',
                    key: 'document_no',
                    description: [
                        'Pflichtfeld',
                        'Nummer die das Dokument identifiziert. z.B. Rechnungsnummer'
                    ]
                },
                {
                    label: 'Rechnungstyp',
                    key: 'document_type_code',
                    description: [
                        'Pflichtfeld',
                        'Dokumententyp der Rechnung zur unterscheidung von Gutschriften etc. Eine Rechnung hat den Code "380"'
                    ]
                },
                {
                    label: 'Ausstellungsdatum des Dokuments',
                    key: 'document_issue_date',
                    description: ['Pflichtfeld']
                }, {
                    label: 'Währung des Dokuments',
                    key: 'document_currency',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Name des Verkäufers (Rechungsersteller)',
                    key: 'seller_name',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Verkäufer Addresszeile 1',
                    key: 'seller_address_line_one',
                    description: ['Optional']
                },
                {
                    label: 'Verkäufer Addresszeile 2',
                    key: 'seller_address_line_two',
                    description: ['Optional']
                },
                {
                    label: 'Verkäufer Addresszeile 3',
                    key: 'seller_address_line_three',
                    description: ['Optional']
                },
                {
                    label: 'Verkäufer Postleitzahl',
                    key: 'seller_address_line_post_code',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Verkäufer Stadt',
                    key: 'seller_address_line_city',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Verkäufer Land',
                    key: 'seller_address_line_country',
                    description: ['Optional']
                },
                {
                    label: 'Steuernummer (FC) des Verkäufers',
                    key: 'seller_tax_registration_fc',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Umsatzsteuer-ID (VA) des Verkäufers',
                    key: 'seller_tax_registration_va',
                    description: ['Optional']
                },
                {
                    label: 'Name des Käufers',
                    key: 'buyer_name',
                    description: ['Optional']
                },
                {
                    label: 'Käufer Addresszeile 1',
                    key: 'buyer_address_line_one',
                    description: ['Optional']
                },
                {
                    label: 'Käufer Addresszeile 2',
                    key: 'buyer_address_line_two',
                    description: ['Optional']
                },
                {
                    label: 'Käufer Addresszeile 3',
                    key: 'buyer_address_line_three',
                    description: ['Optional']
                },
                {
                    label: 'Käufer Postleitzahl',
                    key: 'buyer_address_line_post_code',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Käufer Stadt',
                    key: 'buyer_address_line_city',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Käufer Land',
                    key: 'buyer_address_line_country',
                    description: ['Optional']
                },
                {
                    label: 'Beschreibung der Zahlungsbedingungen',
                    key: 'payment_term_description',
                    description: ['Optional']
                },
                {
                    label: 'Fälligkeitsdatum der Zahlung',
                    key: 'payment_term_due_date',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Lastschriftmandatsreferenz',
                    key: 'payment_term_debit_mandate',
                    description: ['Optional']
                },
                {
                    label: 'Dokumentenzeilen',
                    key: 'document_lines',
                    description: [
                        'Pflichtfeld',
                        'Eine Sammlung von Zeilen (JSON-Array), die die Einzelposten des Dokuments darstellen, wie z. B. Produkte oder Dienstleistungen. Jede Zeile enthält detaillierte Informationen wie Produktname, Menge, Preis, Steuersatz und Steuerinformationen. Die Struktur der Daten sollte im JSON-Format vorliegen und wie folgt aufgebaut sein:',
                        '',
                        'Beispiel für eine Dokumentenposition:',
                        '{',
                        '   "line_no": "1",',
                        '   "product_name": "Einfacher Artikel",',
                        '   "product_description": "1 Stück",',
                        '   "product_seller_assigned_id": "EIN001",',
                        '   "price": "100.00",',
                        '   "quantity_billed": "1",',
                        '   "quantity_billed_unit_code": "H87",',
                        '   "line_tax_rate": "S",',
                        '   "line_tax_type": "VAT",',
                        '   "line_tax_applicable_percent": "20",',
                        '   "line_summation_total": "100.00"',
                        '}',
                        '',
                        'Erklärung der Felder:',
                        '- `line_no`: Die Nummer der Zeile zur Identifikation des Postens im Dokument. (Plficht)',
                        '- `product_name`: Der Name des abgerechneten Produkts oder der Dienstleistung. (Plficht)',
                        '- `product_description`: Eine kurze Beschreibung des Produkts oder der Dienstleistung. (Optional)',
                        '- `product_seller_assigned_id`: Eine vom Verkäufer zugewiesene ID für das Produkt. (Optional)',
                        '- `price`: Der Preis des Produkts oder der Dienstleistung. (Plficht)',
                        '- `quantity_billed`: Die Anzahl der abgerechneten Einheiten. (Plficht)',
                        '- `quantity_billed_unit_code`: Der Maßeinheitencode für die abgerechneten Einheiten. (Plficht)',
                        '- `line_tax_rate`: Der angewendete Steuersatz, z. B. "S" für Standardsteuersatz. (Plficht)',
                        '- `line_tax_type`: Der Typ der angewendeten Steuer, z. B. "VAT" für Mehrwertsteuer. (Plficht)',
                        '- `line_tax_applicable_percent`: Der Prozentsatz der Steuer, der auf das Produkt angewendet wird. (Plficht)',
                        '- `line_summation_total`: Der Gesamtbetrag für diese Zeile, einschließlich Preis und Steuern. (Plficht)',

                    ]
                },
                {
                    label: 'Steuerzeilen',
                    key: 'tax_lines',
                    description: [
                        'Pflichtfeld',
                        'Eine Sammlung von Steuerinformationen (JSON-Array), die die angewendeten Steuern auf das Dokument darstellen. Jede Zeile enthält detaillierte Informationen wie den Steuersatz, den Steuerbetrag und die Basis für die Berechnung der Steuer. Die Struktur der Daten sollte im JSON-Format vorliegen und wie folgt aufgebaut sein:',
                        '',
                        'Beispiel für eine Steuerzeile:',
                        '{',
                        '   "tax_rate": "S",',
                        '   "tax_type": "VAT",',
                        '   "tax_basis_amount": "100.00",',
                        '   "tax_calculated_amount": "20.00",',
                        '   "tax_rate_applicable_percent": "20.00"',
                        '}',
                        '',
                        'Erklärung der Felder:',
                        '- `tax_rate`: Der angewendete Steuersatz, z. B. "S" für den Standardsteuersatz. (Plficht)',
                        '- `tax_type`: Der Typ der angewendeten Steuer, z. B. "VAT" für Mehrwertsteuer. (Plficht)',
                        '- `tax_basis_amount`: Der Betrag, auf dem die Steuer berechnet wird. (Plficht)',
                        '- `tax_calculated_amount`: Der berechnete Steuerbetrag. (Plficht)',
                        '- `tax_rate_applicable_percent`: Der Prozentsatz der angewendeten Steuer. (Plficht)',
                        '- `tax_exemption_reason`: Freier Text mit dem Grund der Freistellung der Steuer. (Optional)',
                        '- `tax_exemption_reason_code`: CEF VATEX Code. (Optional)'
                    ]
                },
                {
                    label: 'Lieferdatum',
                    key: 'supply_chain_event',
                    description: ['Optional']
                },
                {
                    label: 'Gesamtbetrag (Brutto)',
                    key: 'summation_grand_total_amount',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Zu zahlender Betrag',
                    key: 'summation_due_total_payable_amount',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Gesamtbetrag der Positionen',
                    key: 'summation_line_total_amount',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Gesamtsteuerbetrag',
                    key: 'summation_tax_total_amount',
                    description: ['Pflichtfeld']
                },
                {
                    label: 'Nettobetrag (Steuerbasis)',
                    key: 'summation_tax_basis_total_amount',
                    description: ['Pflichtfeld']
                },
            ];
        }
    },
    methods: {
        ...utils,
        onChangeProfile(e) {
            this.profile = e.target.value;
            this.profile_parameters = {};

            this.$emit('option-change', 'profile', e.target.value);
            this.$emit('option-change', 'profile_parameters', {});

            for (let option of this.profileOptions) {
                this.setOptionsValue(option.key, '');
            }
        },
        setOptionsValue(field, e) {
            this.profile_parameters = {
                ...this.profile_parameters,
                [field]: e,
            };
            this.$emit('option-change', 'profile_parameters', this.profile_parameters);
        },
    }
};
</script>
