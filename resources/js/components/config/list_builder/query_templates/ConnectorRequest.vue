<template>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label for="from">Connector-Request</label>
                    </div>
                    <select class="form-control form-control-sm" id="from" :value="connectorRequest"
                            @change="onChangeRequest">
                        <template v-for="(requestIdentifiers, connectorIdentifier) in requests">
                            <option v-for="requestIdentifier in requestIdentifiers"
                                    :value="connectorIdentifier + '/' + requestIdentifier">
                                {{ connectorIdentifier }} / {{ requestIdentifier }}
                            </option>
                        </template>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label for="from">Listen-Root</label>
                    </div>
                    <input class="form-control form-control-sm" type="text" v-model="listRoot"/>
                    <small class="text-muted">Pfad zur Ergebnisliste in der Request-Antwort via "Dot-Notation", z.B.
                        "response.data.items".</small>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label for="from">Select - Datenfelder</label>
                    </div>
                    <table class="table table-sm" v-if="select.length">
                        <thead>
                        <tr class="d-flex">
                            <th class="border-0 font-weight-normal text-muted">
                                <small>Connector-Datenfeld</small></th>
                            <th class="border-0 font-weight-normal text-muted"><small>Alias</small></th>
                            <th class="border-0 font-weight-normal"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in select">
                            <td>{{ item.data }}</td>
                            <td>{{ item.alias }}</td>
                            <td>
                                <button class="btn btn-sm btn-light float-right" @click="deleteSelect(item)">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Konnektor-Datenfeld"
                               v-model="newSelect.data"
                               aria-label="Datenfeld">
                        <input type="text" class="form-control" placeholder="Alias..."
                               v-model="newSelect.alias"
                               aria-label="Alias">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-outline-success" @click="addSelect">
                                <span class="material-icons">add</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="mb-2">
                        <label class="d-block mb-0" for="from">Listensuche Parameter-Mapping</label>
                        <small class="text-muted">Mappen Sie die Parameter-Namen der Allisa Listen-Api auf die
                            Parameter-Namen der Konnektor-Anfrage. Standardgemäß werden jene aus der Allisa Listen-API
                            übernommen.</small>
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">search</span>
                        </div>
                        <input type="text" class="form-control form-control-sm" placeholder="search"
                               v-model="queryMapping.search.name"
                               @change="updateQueryMapping"
                               aria-label="search"
                               aria-describedby="select">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">columns</span>
                        </div>
                        <input type="text" class="form-control form-control-sm"
                               placeholder="columns"
                               aria-label="columns"
                               @change="updateQueryMapping"
                               aria-describedby="columns"
                               v-model="queryMapping.columns.name"
                        >
                        <div class="input-group-append">
                            <input type="text" class="form-control form-control-sm"
                                   placeholder=","
                                   aria-label="separator"
                                   @change="updateQueryMapping"
                                   aria-describedby="separator"
                                   v-model="queryMapping.columns.separator"
                            >
                        </div>
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">page</span>
                        </div>
                        <input type="text" class="form-control form-control-sm"
                               placeholder="page"
                               aria-label="page"
                               aria-describedby="page"
                               @change="updateQueryMapping"
                               v-model="queryMapping.page.name"
                        >
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">rows_per_page</span>
                        </div>
                        <input type="text" class="form-control form-control-sm"
                               placeholder="rows_per_page"
                               aria-label="rows_per_page"
                               aria-describedby="rows_per_page"
                               @change="updateQueryMapping"
                               v-model="queryMapping.rows_per_page.name"
                        >
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <div>
                                <label>Sortierung</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" placeholder="sort"
                                   v-model="queryMapping.sort_column.name"
                                   aria-label="sort_column"
                                   @change="updateQueryMapping"
                                   aria-describedby="sort_column"
                            >
                        </div>
                        <div class="col-3">
                            <div>
                                <label>Richtung</label>
                            </div>
                            <input type="text" class="form-control form-control-sm"
                                   aria-label="sort_direction"
                                   aria-describedby="sort_direction"
                                   @change="updateQueryMapping"
                                   v-model="queryMapping.sort_direction.name"
                            >
                        </div>
                        <div class="col-3">
                            <div>
                                <label>Aufsteigend</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" placeholder="asc"
                                   aria-label="sort_direction"
                                   aria-describedby="sort_direction"
                                   @change="updateQueryMapping"
                                   v-model="queryMapping.sort_direction.asc"
                            >
                        </div>
                        <div class="col-3">
                            <div>
                                <label>Absteigend</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" placeholder="desc"
                                   aria-label="sort_direction"
                                   aria-describedby="sort_direction"
                                   @change="updateQueryMapping"
                                   v-model="queryMapping.sort_direction.desc"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    props: {
        supportData: Object | null,
        data: Object,
        usedColumnAliases: Array | Object
    },
    data() {
        return {
            connectorRequest: this.data.source.connector_identifier + '/' + this.data.source.request_identifier,
            listRoot: this.data.source.list_root || '',
            select: [...this.data.source.select] || [],
            queryMapping: {
                search: {...this.data.source.query_mapping.search},
                columns: {...this.data.source.query_mapping.columns},
                page: {...this.data.source.query_mapping.page},
                rows_per_page: {...this.data.source.query_mapping.rows_per_page},
                sort_column: {...this.data.source.query_mapping.sort_column},
                sort_direction: {...this.data.source.query_mapping.sort_direction},
            },
            newSelect: {
                data: '',
                alias: ''
            }
        };
    },
    computed: {
        ...mapGetters([
            'environments'
        ]),
        requests() {
            let simplifiedRequests = {};

            this.environments.forEach(function (env) {
                let connectors = env.blueprint.connectors;
                let requests = env.blueprint.requests;

                connectors.forEach(function (conn) {
                    let connectorRequestIdentifiers = requests.filter(ele => ele.connector_id === conn.id).map(ele => ele.identifier);

                    if (!connectorRequestIdentifiers.length) {
                        return;
                    }
                    if (simplifiedRequests.hasOwnProperty(conn.identifier)) {
                        simplifiedRequests[conn.identifier] = [
                            ...new Set([
                                [...requests[conn.identifier]],
                                [...connectorRequestIdentifiers]
                            ])
                        ];
                    }
                    else {
                        simplifiedRequests[conn.identifier] = [...connectorRequestIdentifiers];
                    }
                });
            });

            return simplifiedRequests;
        }
    },
    methods: {
        addSelect() {
            if (this.newSelect.data.trim() === '' || this.newSelect.alias.trim() === '') {
                return;
            }

            this.select = [
                ...this.select,
                this.newSelect
            ];

            this.newSelect = {
                data: '',
                alias: ''
            };
        },
        deleteSelect(item) {
            let select = [...this.select];

            this.select = select.filter(ele => ele.alias !== item.alias);
        },
        onChangeRequest(e) {
            let connectorRequest = e.target.value;
            let connectorIdentifier = connectorRequest.split('/')[0];
            let requestIdentifier = connectorRequest.split('/')[1];

            this.connectorRequest = connectorRequest;

            this.$emit('update-multiple-source', {
                request_identifier: requestIdentifier,
                connector_identifier: connectorIdentifier
            });
        },
        updateQueryMapping() {
            this.$emit('update-source', 'query_mapping', this.queryMapping);
        }
    },
    watch: {
        listRoot(newVal) {
            this.$emit('update-source', 'list_root', newVal);
        },
        select(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }
            this.$emit('update-source', 'select', [...this.select]);
        },
    }
};
</script>

