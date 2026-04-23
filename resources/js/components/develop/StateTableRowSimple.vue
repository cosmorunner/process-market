<template>
    <tr class="d-flex">
        <td class="px-2 py-1 pr-0 col-11 border-0">
            <span>{{ state.description }}</span>
            <small class="badge badge-pill badge-light ml-2" v-if="state.min === state.max">{{ state.min.endsWith('.000') ? parseInt(state.min) : state.min }}</small>
            <small class="badge badge-pill badge-light ml-2" v-else>{{ state.min.endsWith('.000') ? parseInt(state.min) : state.min }} - {{ state.max.endsWith('.000') ? parseInt(state.max) : state.max }}</small>
        </td>
    </tr>
</template>

<script>
export default {
    props: {
        state: Object
    }
};
</script>
