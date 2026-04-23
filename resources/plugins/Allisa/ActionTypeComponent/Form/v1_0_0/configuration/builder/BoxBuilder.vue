<template>
    <div>
        <DefaultBuilderHeader :field="field"></DefaultBuilderHeader>
    </div>
</template>

<script>

import DefaultBuilderHeader from "./DefaultBuilderHeader";

export default {
    components: {DefaultBuilderHeader},
    props: {
        field: Object
    }
};
</script>
