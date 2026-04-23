<template>
    <div ref="container" class="d-inline-block">
        <div class="d-none" ref="valueWrapper">{{ valueToCopy }}</div>
        <button type="button" :ref="buttonId" class="btn btn-sm btn-light py-0" @click="copy" data-toggle="tooltip" data-placement="top" title="Kopiert!">
            <span class="material-icons text-primary">content_copy</span>
            <span v-if="label">{{label}}</span>
        </button>
    </div>
</template>

<script>

export default {
    props: {
        value: {
            required: true,
            type: String
        },
        label: {
            default: '',
            type: String
        }
    },
    data() {
        return {
            buttonId: 'copyButton' + Math.floor(Math.random() * 10000),
            valueToCopy: this.value,
        };
    },
    methods: {
        copy() {
            const storage = document.createElement('textarea');
            storage.value = this.$refs.valueWrapper.innerHTML;
            this.$refs.container.appendChild(storage);
            storage.select();
            storage.setSelectionRange(0, 99999);
            document.execCommand('copy');
            this.$refs.container.removeChild(storage);

            // Show tooltip
            $(this.$refs[this.buttonId]).tooltip('enable');
            $(this.$refs[this.buttonId]).tooltip('show');

            // Hide after 2 seconds
            setTimeout(() => {
                $(this.$refs[this.buttonId]).tooltip('hide');
                $(this.$refs[this.buttonId]).tooltip('disable');
            }, 2000)
        }
    },
    mounted() {
        // Manual trigger tooltip, disable tooltip on mount.
        $(this.$refs[this.buttonId]).tooltip({trigger: 'manual'}).tooltip('disable');
    }
};
</script>
