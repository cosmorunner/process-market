<template>
    <div class="d-inline-block">
        <button v-if="!error && loading"
                class="btn btn-warning btn-sm d-flex align-items-center justify-content-between" disabled>
            <span class="material-icons mr-2">settings</span>
            <span> Laden...</span>
        </button>
        <button v-if="error"
                class="btn btn-danger btn-sm d-flex align-items-center justify-content-between">
            <span class="material-icons mr-2">priority_high</span>
            <span> Error</span>
        </button>
        <button v-if="!loading && !error"
                class="btn btn-danger btn-sm d-flex align-items-center justify-content-between"
                @click="stopSim()">
            <span class="material-icons mr-2">stop</span>
            <span> Stop</span>
        </button>
    </div>
</template>

<script>

export default {
    props: {
        simulationId: String
    },
    data() {
        return {
            error: null,
            loading: false,
        };
    },
    methods: {
        stopSim() {
            let that = this;
            this.loading = true;

            axios.patch('/api/simulations/' + this.simulationId)
                .then(() => window.location.reload())
                .catch(() => that.error = true);
        },
    }
};
</script>
