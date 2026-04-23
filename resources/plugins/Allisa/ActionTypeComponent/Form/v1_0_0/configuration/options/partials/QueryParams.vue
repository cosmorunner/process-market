<template>
    <div>
        <div class="mb-3">
            <template v-for="(actionDataField, queryParam) in queryParams">
                <div class="input-group input-group-sm mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">{{ queryParam }}</span>
                    </div>
                    <input type="text" readonly class="form-control bg-white" aria-label="Label" :value="actionDataField" />
                    <div class="input-group-append" v-if="editable">
                        <button class="btn btn-outline-danger" @click="onDeleteParam(queryParam)">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </div>
            </template>
            <div class="d-flex justify-content-start" v-if="editable">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <input type="text" aria-describedby="Query Parameter"
                               class="form-control form-control-sm rounded-0 rounded-left"
                               style="border-top-left-radius: 0.25rem !important; border-bottom-left-radius: 0.25rem !important;" v-model="newParamName" aria-label="Query Parameter">
                    </div>
                    <select class="form-control form-control-sm rounded-0 rounded-left" v-model="newParamValue">
                        <option value="">Bitte wählen...</option>
                        <option :value="name" v-for="name in inputOutputNames">{{ name }}</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-success" @click="onAddParam" :disabled="newParamName.trim() === '' || newParamValue === ''">
                            <span class="material-icons">add</span>
                        </button>
                    </div>
                </div>
            </div>
            <small class="text-muted">Datenfelder auf URL-Parameter mappen. Datenfeld-Wert muss eine Zeichenkette sein. Leere Werte werden ignoriert.</small>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        queryParams: Array | Object,
        inputOutputNames: Array,
        editable: Boolean
    },
    data() {
        return {
            newParamName: '',
            newParamValue: ''
        };
    },
    methods: {
        onAddParam() {
            if (!this.newParamName.trim() || !this.newParamValue.trim()) {
                return;
            }

            this.$emit('update-params', {
                ...this.queryParams,
                [this.newParamName]: this.newParamValue
            });

            this.newParamName = '';
            this.newParamValue = '';
        },
        onDeleteParam(key) {
            let params = {...this.queryParams};
            delete params[key];

            this.$emit('update-params', params);
        },
    }
};
</script>

