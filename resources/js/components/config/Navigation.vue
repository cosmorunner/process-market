<template>
    <div class="d-flex justify-content-between">
        <ul class="nav nav-tabs" style="font-size: 0.9em;">
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'ActionTypes')"
                   :class="'nav-link px-2 ' + (panelName === 'ActionTypes' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Aktionen</a>
            </li>
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update','nav', 'ActionTypeCategories')"
                   :class="'nav-link px-2 ' + (panelName === 'ActionTypeCategories' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Aktions-Kategorien</a>
            </li>
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'StatusTypes')"
                   :class="'nav-link px-2 ' + (panelName === 'StatusTypes' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Status</a>
            </li>
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'Lists')"
                   :class="'nav-link px-2 ' + (panelName === 'Lists' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Listen</a>
            </li>
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'MenuItems')"
                   :class="'nav-link px-2 ' + (panelName === 'MenuItems' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Menü</a>
            </li>
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'RelationTypes')"
                   :class="'nav-link px-2 ' + (panelName === 'RelationTypes' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Verknüpfungstypen</a>
            </li>
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'Templates')"
                   :class="'nav-link px-2 ' + (panelName === 'Templates' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Vorlagen</a>
            </li>
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'Events')"
                   :class="'nav-link px-2 ' + (panelName === 'Events' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Events</a>
            </li>
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'Listeners')"
                   :class="'nav-link px-2 ' + (panelName === 'Listeners' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Listeners</a>
            </li>
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'Javascript')"
                   :class="'nav-link px-2 ' + (panelName === 'Javascript' ? 'active bg-white border-bottom-white' : '')"
                   href="#">JavaScript</a>
            </li>
        </ul>
        <ul class="nav nav-tabs flex-grow-1 justify-content-end" style="font-size: 0.9em;">
            <li class="nav-item">
                <a style="padding:0.5rem 0.8rem" @click="$emit('navigation-update', 'nav', 'Environments')"
                   :class="'nav-link px-2 ' + (panelName === 'Environments' ? 'active bg-white border-bottom-white' : '')"
                   href="#">Demo-Umgebungen</a>
            </li>
        </ul>
    </div>

</template>

<script>

export default {
    props: {
        panelName: true
    }
};

</script>
