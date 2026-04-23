<template>
    <div class="d-flex">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'Info')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Info' ? 'active' : '')" href="#">Information</a>
            </li>
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'Processes')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Processes' ? 'active' : '')" href="#">Prozesse</a>
            </li>
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'Groups')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Groups' ? 'active' : '')"
                   href="#">Gruppen</a>
            </li>
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'Users')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Users' ? 'active' : '')"
                   href="#">Benutzer</a>
            </li>
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'Bots')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Bots' ? 'active' : '')"
                   href="#">Bots</a>
            </li>
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'Relations')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Relations' ? 'active' : '')" href="#">Verknüpfungen</a>
            </li>
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'Connectors')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Connectors' ? 'active' : '')" href="#">Connectoren</a>
            </li>
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'PublicApis')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'PublicApis' ? 'active' : '')" href="#">Öffentliche APIs</a>
            </li>
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'Variables')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Variables' ? 'active' : '')" href="#">Variablen</a>
            </li>
            <li class="nav-item">
                <a @click="$emit('navigation-change', 'Tasks')"
                   :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Tasks' ? 'active' : '')" href="#">Aufgaben</a>
            </li>
        </ul>
    </div>

</template>

<script>

export default {
    props: {
        detailComponentName: String
    }
};

</script>
