<template>
    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle border" type="button"
                id="dropDownColors" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span v-if="selected" class="material-icons" :style="'color:' + selected + ';'">brightness_1</span>
            <span v-if="!selected">Keine Farbe</span>
        </button>
        <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dropDownColors">
            <button class="dropdown-item" type="button" @click="onSelect('')">Keine Farbe</button>
            <button class="dropdown-item" type="button" @click="onSelect(color)" v-for="color in colors">
                <span class="material-icons mi-2x" :style="'color:' + color + ';'">brightness_1</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        selected: String
    },
    data() {
        return {
            colors: [
                '#c4f9a7',
                '#95e596',
                '#82e0aa',
                '#a7f9f9',
                '#98c3f2',
                '#72c6ff',
                '#a7aff9',
                '#bb8fce',
                '#e0a3dc',
                '#e28eb1',
                '#f9aba7',
                '#f9b689',
                '#f7dc6f',
                '#f9eea7',
                '#e9f9a7',
                '#f4f6f7',
                '#d7dbdd',
                '#aeb6bf',
            ]
        };
    },
    methods: {
        onSelect(icon) {
            this.$emit('on-select-color', icon);
        }
    },
};
</script>
