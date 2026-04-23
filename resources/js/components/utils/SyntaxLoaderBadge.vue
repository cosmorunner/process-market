<template>
    <BaseBadge :label="label" :disable-tooltip="disableTooltip" :tooltip="tooltip" :icon="icon"/>
</template>

<script>

import BaseBadge from "./BaseBadge";

export default {
    name: 'SyntaxLoaderBadge',
    components: {BaseBadge},
    props: {
        disableTooltip: {
            default: false,
            type: Boolean
        },
        value: String,
        label: String,
    },
    computed: {
        tooltip() {
            return '';
        },
        icon() {
            if (this.value.startsWith('process.outputs') || this.value.startsWith('context.outputs')) {
                return 'grain';
            }
            if (this.value.startsWith('process.artifacts')) {
                return 'description';
            }
            if (this.value.startsWith('process.actions')) {
                return 'tag';
            }
            if (this.value.startsWith('process.urls') || this.value.startsWith('context.urls')) {
                return 'link';
            }
            if (this.value === 'process.path' || this.value === 'context.path') {
                return 'link';
            }
            if (this.value.startsWith('process.pipe_notation')) {
                return 'info';
            }
            if (this.value.startsWith('process.meta') || this.value.startsWith('context.meta')) {
                return 'info';
            }
            if (this.value.startsWith('process.status') || this.value.startsWith('context.status')) {
                return 'grid_view';
            }
            if (this.value.startsWith('process_type.')) {
                return 'grid_view';
            }
            if (this.value.startsWith('action.outputs.')) {
                return 'flash_on';
            }
            if (this.value.startsWith('action.processors.')) {
                return 'settings';
            }
            if (this.value.startsWith('action.artifacts')) {
                return 'description';
            }
            if (this.value === 'action.path') {
                return 'link';
            }
            if (this.value.startsWith('auth.')) {
                return 'account_circle';
            }
            if (this.value.startsWith('date.')) {
                return 'today';
            }
            if (this.value.startsWith('faker.')) {
                return 'shuffle';
            }
            if (this.value.startsWith('url.query.')) {
                return 'link';
            }
            if (this.value.startsWith('system.variables')) {
                return 'tag';
            }
            if (this.value.startsWith('system.meta')) {
                return 'settings_system_daydream';
            }

            if (this.value.startsWith('system.public_apis')) {
                return 'link';
            }

            if (this.value.startsWith('process.references')) {
                let reference = this.value.split('.')[2];

                if (this.value.startsWith('process.references.' + reference + '.outputs')) {
                    return 'grain';
                }
                if (this.value.startsWith('process.references.' + reference + '.data')) {
                    return 'settings_ethernet';
                }
                if (this.value.startsWith('process.references.' + reference + '.urls')) {
                    return 'link';
                }
                if (this.value.startsWith('process.references.' + reference + '.meta')) {
                    return 'info';
                }
                if (this.value.startsWith('process.references.' + reference + '.status')) {
                    return 'grid_view';
                }
            }

            return 'help';
        }
    }

};
</script>
