<template>
    <div>
        <p>Informationen zu ihrer Lizenz.</p>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/utils';

export default {
    props: {
        license: Object | Array,
        processId: String
    },
    data() {
        let license = {...this.license};

        if (!license.level) {
            license.level = 'private';
            license.level_options = {};
        }

        return {
            error: null,
            errors: [],
            errorMessage: '',
            loading: false,
            options: license
        };
    },
    computed: {
        ...mapGetters([
            'flash_messages'
        ]),
        levelLabel() {
            switch (this.options.level) {
                case 'private':
                    return 'Privat';
                case 'open-source':
                    return 'Open-Source';
            }
        }
    },
    methods: {
        ...mapActions(reduxActions),
        submit() {
            let that = this;

            this.error = false;
            this.errorMessage = '';
            this.errors = [];
            this.loading = true;

            axios.patch('/api/processes/' + this.processId + '/license', this.options)
                .then(() => {
                    this.loading = false;

                    this.setFlashMessage({
                        type: 'success',
                        message: 'Gespeichert!'
                    });
                })
                .catch((error) => {
                    that.error = true;
                    that.loading = false;
                    that.errorMessage = error.response.data.message;
                    that.errors = error.response.data.errors || [];
                });
        },

    }
};
</script>
