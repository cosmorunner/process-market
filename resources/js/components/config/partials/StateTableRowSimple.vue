<template>
    <tr class="d-flex">
        <td class="col-1 px-2 py-1 border-0">
            <span class="material-icons" :style="'color:' + state.color">{{ state.image }}</span>
        </td>
        <td class="px-2 py-1 pr-0 col-11 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <span>{{ state.description }}</span>
            </div>
            <div><small class="text-muted">{{ state.min }} - {{ state.max }}</small></div>
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
