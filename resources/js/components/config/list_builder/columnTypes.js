export const defaultTypeOptions = {
    button: {
        action: 'link',
        url: '/processes/$',
        bindings: [],
        label: 'Auswählen',
        hide: [],
        color: 'primary'
    },
    icon: {
        mapping: {},
        colors: {},
        hide: [],
        size: 'normal'
    },
    progress: {
        min: '0',
        max: '100',
        color: '',
        type: 'progress_bar',
        image: 'circle'
    },
    tags: {},
    text: {
        hide: []
    },
    currency: {
        currency_code: 'EUR',
        show_decimals: true,
        show_currency_code: true
    },
    url: {
        label: '',
        url: '/processes/$',
        bindings: [],
        target: '_self',
    },
    input: {
        placeholder: '',
        type: 'text',
        custom_data_name: '',
        default: ''
    },
    select: {
        custom_data_name: '',
        default: '',
        items: []
    },
    rowSelection: {},
    arrayData: {
        placeholder: ''
    },
    template: {
        template_id: ''
    },
};
