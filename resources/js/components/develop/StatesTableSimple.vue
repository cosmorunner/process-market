<template>
    <div class="card rounded-0 border-left-0 border-right-0">
        <table class="table mb-0" style="font-size: 0.9rem">
            <tbody>
            <template v-for="state in states">
                <StateTableRowSimple :state="state"/>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import StateTableRowSimple from "./StateTableRowSimple";

export default {
    components: {StateTableRowSimple},
    props: {
        states: Array
    }
};
</script>
