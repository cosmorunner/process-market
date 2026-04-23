<template>
    <div class="row">
        <div class="col">
            <list :fetch-on-mount-prop="true"
                  :endpoint-prop="listApiEndpoint"
                  :paginator-prop="{}"
                  :columns-prop="listConfig.data.columns"
                  :header-button-prop="listConfig.data.header_button"
                  :enable-search-prop="listConfig.data.enable_search"
                  :enable-pagination-prop="listConfig.data.enable_pagination"
                  :enable-total-count-prop="listConfig.data.enable_total_count"
                  :rows-per-page-prop="listConfig.data.rows_per_page"
                  :quick-filter-prop="listConfig.data.quick_filter"
                  :name-prop="listConfig.name"
                  :description-prop="listConfig.description"
            ></list>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        listConfig: Object,
        usedColumnAliases: Array,
        aliasLabels: Object,
        listApiEndpoint: String
    },
    data() {
        return {};
    },
    methods: {},
    computed: {},
};
</script>
