<!--suppress JSUnresolvedVariable -->
<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8 mb-2 text-center" v-if="tags.length">
                        <ul class="nav nav-pills justify-content-center">
                            <li class="nav-item">
                                <button class="btn btn-lg py-1 nav-link" style="opacity: 1" :class="{'active': !selectedTags.length}" type="button" :disabled="!selectedTags.length"
                                        @click="clearTags">Alle
                                </button>
                            </li>
                            <li class="nav-item" v-for="tag in tags">
                                <button class="btn btn-lg py-1 nav-link mx-1 mb-2" style="opacity: 1" :class="{'active': selectedTags.includes(tag.value)}"
                                        type="button" @click="toggleTag(tag.value)" :disabled="selectedTags.includes(tag.value)">
                                    {{ tag.label }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="input-group max-width-250">
                    <div class="input-group-prepend">
                        <span
                            class="input-group-text rounded-0 border-top-0 border-left-0 border-right-0 bg-transparent">
                            <span class="material-icons mi-1-25x">search</span>
                        </span>
                    </div>
                    <input type="text" placeholder="Suche..." aria-label="Search" aria-describedby="search-button"
                           class="form-control bg-light border-top-0 border-left-0 border-right-0"
                           :disabled="disableSearchInput" v-model="search">
                    <div class="input-group-append" v-if="search.trim()">
                        <button type="button" class="btn text-danger border-0 rounded-0" @click="search = ''"><span
                            class="material-icons">clear</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div v-for="process in processes" class="col-6 col-lg-4 mb-4" :class="{'opacity-2': fetching}">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between border-bottom-0 p-2">
                    <span class="text-truncate font-weight-bold">
                        <a :href="process.public_path" class="d-inline-block mb-1">{{ process.short_title }}</a>
                        <small class="text-muted d-block">
                            <template v-if="process.has_open_source_license">
                                <span class="material-icons">favorite</span> Open-Source
                            </template>
                            <template v-else>
                                <span class="material-icons mi-1-25x">local_atm</span> Auf Anfrage
                            </template>
                        </small>
                    </span>
                    </div>
                    <div class="card-body p-2 d-flex flex-column justify-content-between">
                        <p v-if="process.short_description" class="card-text flex-grow-1 mb-2">
                            <span>{{ process.short_description }}</span>
                        </p>
                        <div>
                            <small v-for="tag in process.tags" class="text-muted">
                                <span class="material-icons" :style="{ 'color': tag.color }">lens</span>
                                {{ tag.name }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between bg-white p-2">
                        <small class="text-muted d-block text-truncate align-content-center d-flex">
                            <a :href="process.author_public_path">
                                <img :src="process.author_thumbnail_path" class="d-inline-block rounded-circle mr-1"
                                     height="25" alt=""/>
                            </a>
                            <span>
                                <a :href="process.author_public_path" class="text-muted">
                                    <span>{{ process.namespace }}</span>
                                </a>
                                <span> / {{ process.identifier }}</span>
                            </span>
                        </small>
                        <div>
                            <div class="bg-white px-1 border rounded d-flex" style="width: 90px; padding-top: 1px;"
                                 data-toggle="tooltip" data-placement="top" title="Komplexitäts-Wert">
                                <small class="font-weight-bold d-inline-block mr-1 align-content-center"
                                       :style="{'color': process.complexity_score.color}">{{
                                        process.complexity_score.value
                                    }}</small>
                                <span v-for="index in process.complexity_score.number_of_blocks"
                                      style="width:4px; height:10px; margin-left: 2px;"
                                      :style="{ 'background-color': process.complexity_score.color}"
                                      class="my-1 opacity-3"></span>
                            </div>
                        </div>
                    </div>
                </div>
                {{ process.title }}
            </div>
            <div v-if="showPlaceholder" v-for="index in itemsPerPage" class="col-4 mb-4">
                <div class="card h-100 opacity-1" style="opacity: 0.7">
                    <div class="card-header d-flex justify-content-between border-bottom-0 p-2"
                         style="height: 50px;"></div>
                    <div class="card-body p-2 d-flex flex-column justify-content-between" style="height: 185px;"></div>
                    <div class="card-footer d-flex justify-content-between bg-white p-2" style="height: 40px;">
                        <small class="text-muted d-block text-truncate align-content-center d-flex">
                        </small>
                        <div>
                            <div class="bg-white px-1 border rounded d-flex" style="width: 90px; padding-top: 1px;">
                                <span style="width:4px; height:10px; margin-left: 2px;" class="my-1 opacity-3"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showLoadMoreButton" class="row mb-2">
            <div class="col"></div>
            <div class="col text-center">
                <button class="btn btn-sm btn-outline-info" @click="loadMore">
                    <span class="material-icons">add</span> Mehr laden
                </button>
            </div>
            <div class="col"></div>
        </div>
        <div v-if="processes.length === 0 && showPlaceholder===false" class="row mb-2">
            <div class="col-12 my-2 text-center">
                <span>Keine Prozesse gefunden.</span>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        itemsPerPage: {
            type: Number,
            default: 6
        },
        tags: {
            type: Array,
            default() {
                return [];
            }
        },
        selected: {
            type: Array,
            default() {
                return [];
            }
        },
        endpoint: String,
        sort: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            processes: [],
            currentPage: 1,
            hasMoreData: false,
            showPlaceholder: true,
            debounce: null,
            disableSearchInput: false,
            search: '',
            selectedTags: this.selected,
            fetching: false
        };
    },
    computed: {
        showLoadMoreButton() {
            return this.showPlaceholder === false && this.hasMoreData === true;
        }
    },
    methods: {
        loadMore() {
            this.currentPage = this.currentPage + 1;
            this.loadData();
        },
        loadData(appendProcesses = false, showPlaceholder = false) {
            this.showPlaceholder = showPlaceholder;
            this.fetching = true;
            let that = this;

            let params = {
                items_per_page: this.itemsPerPage,
                page: appendProcesses ? 1 : this.currentPage,
                tags: this.selectedTags,
                search: this.search.trim(),
                sort: this.sort.trim()
            };

            // Remove null and empty strings
            Object.keys(params).forEach(key => (params[key] === null || params[key] === '') && delete params[key]);

            axios.get(this.endpoint, {params}).then(response => {
                // Response is a paginator object.
                if(appendProcesses) {
                    that.processes = response.data.data
                } else {
                    that.processes.push(...response.data.data);
                }

                that.currentPage = response.data.meta.current_page;
                that.hasMoreData = response.data.links.next !== null;
                that.showPlaceholder = false;
                that.fetching = false;
            }).catch(() => {
                that.fetching = false;
            });
        },
        toggleTag(tagValue) {
            this.selectedTags = this.selectedTags.includes(tagValue) ? this.selectedTags.filter(item => item !== tagValue) : [tagValue];
        },
        clearTags() {
            this.selectedTags = [];
        }
    },
    watch: {
        search(newValue) {
            clearTimeout(this.debounce);

            if (newValue.trim === '') {
                this.loadData(false, false);
            }
            else {
                this.debounce = setTimeout(() => this.loadData(true), 400);
            }
        },
        selectedTags() {
            this.loadData(true);
        }
    },
    mounted() {
        this.loadData(true, true);
    }
};
</script>
