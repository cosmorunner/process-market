<template>
    <div class="d-inline-block">
        <a :href="url" target="_blank">
        <span class="material-icons text-dark opacity-3 cursor-pointer mi-1-25x" title="Dokumentation">help_outline</span>
        </a>
    </div>
</template>

<script>
export default {
    props: {
        article: {
            type: String,
            default: 'overview'
        },
        section: {
            type: String,
            default: ''
        }
    },
    computed: {
        url() {
            return '/docs/de/' + this.article + (this.section ? '#' + this.section : '');
        }
    }
};
</script>

