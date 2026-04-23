<template>
    <div>
        <div class="row">
            <div class="col">
                <component :is="templateToComponentName" v-if="$options.components[templateToComponentName]"
                           :alias-labels="aliasLabels" :data="listConfig.data" :support-data="supportData"
                           :used-column-aliases="usedColumnAliases" v-on="$listeners"/>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    components: {
        Processes: () => import("./query_templates/Processes"),
        Groups: () => import("./query_templates/Groups.vue"),
        Users: () => import("./query_templates/Users.vue"),
        GroupMembers: () => import("./query_templates/GroupMembers"),
        ProcessRelations: () => import("./query_templates/ProcessRelations"),
        ProcessActions: () => import("./query_templates/ProcessActions"),
        ProcessArtifacts: () => import("./query_templates/ProcessArtifacts"),
        Relation: () => import("./query_templates/Relation"),
        ConnectorRequest: () => import("./query_templates/ConnectorRequest"),
        ProcessIdentityRelations: () => import("./query_templates/ProcessIdentityRelations"),
    },
    props: {
        listConfig: Object,
        supportData: Object | null,
        aliasLabels: Object,
        usedColumnAliases: Array | Object
    },
    computed: {
        templateToComponentName: function () {
            let template = this.listConfig.template;
            return template.split('_').map(part => part.charAt(0).toUpperCase() + part.slice(1)).join('');
        }
    }
};
</script>
