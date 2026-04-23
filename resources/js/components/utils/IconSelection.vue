<template>
    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle border" type="button"
                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false" :disabled="!editable" :style="'background-color:' + backgroundColor + ';'">
            <span v-if="selected" class="material-icons" :style="'color:#555555'">{{ selected }}</span>
            <span v-if="!selected" :style="'color:#555555'">Kein Icon</span>
        </button>
        <div class="dropdown-menu scrollable-dropdown" :style="'min-width: ' + dropdownMinimumWidth" aria-labelledby="dropdownMenuButton">
            <button v-if="!requireSelection" class="dropdown-item d-inline-block" style="width:100px;padding:0.25rem 1.0rem;"
                    type="button" @click="onSelect('')">Kein Icon
            </button>
            <div v-if="!requireSelection" class="dropdown-divider"></div>
            <button class="dropdown-item d-inline-block" style="width:60px;padding:0.25rem 1.0rem;"
                    type="button" @click="onSelect(iconName)" v-for="iconName in favorites">
                <span class="material-icons mi-2x">{{ iconName }}</span>
            </button>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item d-inline-block" style="width:60px;padding:0.25rem 1.0rem;"
                    type="button" @click="onSelect(iconName)" v-for="iconName in icons">
                <span class="material-icons mi-2x">{{ iconName }}</span>
            </button>
        </div>
    </div>
</template>

<script>

import {icons, favorites} from "../../icons";

export default {
    props: {
        selected: String,
        requireSelection: {
            type: Boolean,
            default: false
        },
        editable: {
            type: Boolean,
            default: true
        },
        backgroundColor : {
            type: String,
            default: ''
        },
        dropdownMinimumWidth: {
            type: String,
            default: '20rem'
        }
    },
    data() {
        return {
            icons: icons,
            favorites: favorites
        };
    },
    methods: {
        onSelect(icon) {
            this.$emit('on-select-icon', icon);
        }
    },
};
</script>
