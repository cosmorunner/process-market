<template>
    <div>
        <div class="modal-body py-2 mb-0">
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name"
                       @input="$emit('update-property', 'name', $event.target.value)" maxlength="80"
                       :readonly="!ui.editable"/>
                <div v-for="error in [...(ui.validationErrors.name || ['']), ...(ui.validationErrors.template || [])]"
                     class="d-inline-block mr-1">
                    <div class="invalid-feedback d-inline-block mt-0 w-auto">{{ error }}</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <span>Vorlage</span>
                </div>
            </div>
            <div class="container-fluid" :style="'overflow-y:scroll;max-height:' + (ui.modal.bodyHeight - 110) + 'px'">
                <div class="row" v-for="(temp, index) in templates">
                    <div class="col-12">
                        <div
                            :class="{'row p-4': true, 'bg-light border rounded border-primary': temp.id === template.template}">
                            <div class="col-3">
                                <img :src="temp.preview" style="max-width: 320px" class="card-img-top" alt=""/>
                            </div>
                            <div class="col-6">
                                <div>
                                    <span :class="'d-inline-block p-1 badge badge-' + templateTypeColor(temp)">{{
                                            templateTypeLabel(temp)
                                        }}</span>
                                    <h5 class="card-title my-2">{{ temp.name }}</h5>
                                    <p class="card-text mb-2">{{ temp.description }}</p>
                                </div>
                            </div>
                            <div class="col-3 d-flex justify-content-end align-items-end">
                                <button type="button"
                                        :class="{'btn btn-sm btn-outline-primary float-right' : true, 'btn-primary' : temp.id === template.template}"
                                        @click="$emit('update-property', 'template', temp.id)">
                                    <span>Auswählen</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12" v-if="index !== templates.length - 1">
                        <hr class="my-3"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';

export default {
    props: {
        template: Object
    },
    data() {
        return {
            templates: [],
            loading: false,
            error: null,
            errorMessage: '',
        };
    },
    computed: {
        ...mapGetters(['ui']),
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        templateTypeColor(template) {
            if (template.type === 'html') {
                return 'info';
            }
            if (template.type === 'custom_logic') {
                return 'secondary';
            }
            if (template.type === 'mustache_list_column') {
                return 'warning';
            }

            return 'info';
        },
        templateTypeLabel(template) {
            if (template.type === 'html') {
                return 'HTML';
            }
            if (template.type === 'custom_logic') {
                return 'EIGENE LOGIK';
            }
            if (template.type === 'mustache_list_column') {
                return 'HTML - MUSTACHE JS';
            }

            return '';
        },
    },
    mounted() {
        let that = this;

        that.loading = true;
        that.error = false;
        that.errorMessage = null;

        axios.get('/api/templates').then(function (response) {
            that.loading = false;
            that.templates = response.data;
        }).catch(function (error) {
            that.loading = false;
            that.error = true;
            this.setError({
                'code': that.errorMessage = error.response.data.code,
                'message': that.errorMessage = error.response.data.message
            });
        });
    },
    watch: {
        template: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    }

};
</script>
