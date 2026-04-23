<template>
    <div>
        <button class="btn btn-light btn-sm dropdown-toggle border" type="button"
                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" :disabled="!editable">
            <span v-if="selected" class="material-icons">{{ selected }}</span>
            <span v-if="!selected">Kein Icon</span>
        </button>
        <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dropdownMenuButton">
            <button v-if="!requireSelection" class="dropdown-item d-inline-block"
                    style="width:100px;padding:0.25rem 1.0rem;"
                    type="button" @click="onSelect('')">Kein Icon
            </button>
            <div v-if="!requireSelection" class="dropdown-divider"></div>
            <button class="dropdown-item d-inline-block" style="width:60px;padding:0.25rem 1.0rem;"
                    type="button" @click="onSelect(iconName)" v-for="iconName in favorites" v-if="enableFavorites">
                <span class="material-icons mi-2x">{{ iconName }}</span>
            </button>
            <div class="dropdown-divider" v-if="enableFavorites"></div>
            <button class="dropdown-item d-inline-block" style="width:60px;padding:0.25rem 1.0rem;"
                    type="button" @click="onSelect(iconName)" v-for="iconName in icons">
                <span class="material-icons mi-2x">{{ iconName }}</span>
            </button>
        </div>
    </div>
</template>

<script>

import {icons, favorites} from '../icons';

export default {
    props: {
        selected: String,
        enableFavorites: {
            default: true,
            type: Boolean
        },
        requireSelection: {
            default: false,
            type: Boolean
        },
        manualIcons: {
            default: () => [],
            type: Array
        },
        manualFavorites: {
            default: () => [],
            type: Array
        },
        editable: {
            default: true,
            type: Boolean
        },
    },
    data() {
        let iconList = this.manualIcons.length ? this.manualIcons : icons;
        let favoritesList = this.manualFavorites.length ? this.manualFavorites : favorites;

        return {
            icons: iconList,
            favorites: favoritesList
        };
    },
    methods: {
        onSelect(icon) {
            this.$emit('on-select-icon', icon);
        }
    },
};
</script>
