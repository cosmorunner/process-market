/**
 * Standard-Optionen für Felder.
 * @param type
 * @returns {*}
 */
export function customFieldOptions(type) {
    return {
        datepicker: {
            output_format: 'DD.MM.YYYY',
            highlighted: false
        },
        select: {
            items: [
                {
                    'label': 'Bitte wählen...',
                    'value': ''
                }
            ],
            list_config: {
                id: '',
                apply_context_parameter: false
            },
            computed_input: '',
            highlighted: false,
            mapping: {},
            variable: '',
        },
        radio: {
            items: [],
            display_as_rows: false,
            computed_input: '',
            highlighted: false,
            variable: '',
        },
        checkbox: {
            computed_input: '',
            highlighted: false
        },
        hidden: {
            computed_input: ''
        },
        alert: {
            alert_type: 'info',
            computed_input: '',
            highlighted: false
        },
        link: {
            url: '',
            link_label: '',
            icon: 'link',
            style: 'link', // link, button, button-block
            target: '_blank',
            bindings: [],
            computed_input: '',
            highlighted: false
        },
        paragraph: {
            computed_input: '',
            highlighted: false
        },
        text: {
            computed_input: '',
            highlighted: false
        },
        autocomplete: {
            max_items: 3,
            as_string: false,
            items: [],
            list_config: {
                id: '',
                apply_context_parameter: false
            },
            auto_mapping_on_single_list_entry: false,
            auto_mapping_on_load: false,
            allow_custom_values: false,
            highlighted: false,
            mapping: {},
            variable: '',
        },
        listmodal: {
            size: 'm',
            list_config: {
                id: '',
                apply_context_parameter: false
            },
            value_mapping: [
                '',
                ''
            ],
            mapping: {},
            button_label: 'Öffnen',
            highlighted: false
        },
        textarea: {
            rows: 3,
            computed_input: '',
            highlighted: false
        },
        icon: {
            default: 'settings',
            value_field: '',
            mapping: {},
            colors: {},
            highlighted: false
        },
        box: {
            name: '',
            highlighted: false
        },
        currency: {
            default: '',
            computed_input: '',
            currency: 'EUR',
            highlighted: false,
            showDecimals: true,
            showCurrencyCode: true
        }
    }[type] || {};
}

/**
 * Standard-Optionen für ein neues Feld.
 * @returns {{default: string, name: string, width: number, label: string, type: string, helper_text: string,
 *     computed_input: string}}
 */
export function newFieldOptions(name) {
    return {
        type: 'text',
        name: name || 'new_field',
        label: '',
        helper_text: '',
        default: '',
        width: 12,
        computed_input: ''
    };
}

/**
 * Icons pro Feld-Typ
 * @param type
 * @returns {*|string}
 */
export function iconForType(type) {
    let icons = {
        alert: 'announcement',
        autocomplete: 'local_offer',
        checkbox: 'check_box',
        currency: 'euro_symbol',
        datepicker: 'today',
        file: 'attach_file',
        hidden: 'visibility_off',
        icon: 'star',
        link: 'link',
        listmodal: 'chrome_reader_mode',
        paragraph: 'short_text',
        radio: 'radio_button_checked',
        select: 'view_list',
        text: 'create',
        textarea: 'title',
        timepicker: 'schedule',
        box: 'check_box_outline_blank'
    };

    return icons[type] || 'help';
}
