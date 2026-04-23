<template>
    <div>
        <span>{{ description }}</span>
        <hr v-if="description"/>
        <small class="text-muted">Wir werden als "Demo Benutzer" eingeloggt und können die Lösung beliebig testen.</small>
        <form method="POST" :action="endpoint" @submit="start" class="mt-2">
            <input type="hidden" name="ref" :value="referenceUrl"/>
            <input type="hidden" name="organisation_id" :value="organisationId"/>
            <input type="hidden" name="license_id" :value="licenseId"/>
            <input type="hidden" name="_token" :value="csrf"/>
            <button type="submit" class="btn btn-sm btn-success float-right" v-if="!starting">
                <span>Starten</span>
            </button>
            <button type="button" :class="starting ? 'd-inline-block' : 'd-none'" class="btn btn-sm btn-link float-right">
                <img  src="/img/loading.gif" alt="Loading" width="18" height="18"/>
            </button>
        </form>
    </div>
</template>
<script>

export default {
    props: {
        endpoint: String,
        csrf: String,
        referenceUrl: String,
        description: String,
        organisationId: String | null,
        licenseId: String | null,
        environments: Array,
    },
    data() {
        return {
            role: this.defaultRoleId,
            starting: false,
            context: ''
        };
    },
    methods: {
        start(e) {
          e.preventDefault();
          this.starting = true;
          e.target.submit();
        }
    },
};
</script>
