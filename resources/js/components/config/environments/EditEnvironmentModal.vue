<template>
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <component v-if="processVersionId && environment" :is="detailComponentName" :environment="environment"
                       :process-version-id="processVersionId" :add-mode="addMode" :data="data"
                       :detail-component-name="detailComponentName" @navigation-change="onNavigationChange"
                       @blueprint-change="onBlueprintChange" @change-attribute="onChangeAttribute" @cancel="onCancel"
                       @default-user-change="onDefaultUserChange"
                       @save="onSave"></component>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../../store/develop-and-config";
import Info from "./Info";
import Processes from "./Processes";
import Process from "./Process";
import Connector from "./Connector";
import Users from "./Users";
import Bots from "./Bots";
import Groups from "./Groups";
import Relations from "./Relations";
import Connectors from "./Connectors";
import Request from "./Request";
import PublicApis from "./PublicApis";
import Variables from "./Variables";
import Variable from "./Variable";
import Tasks from "./Tasks";
import Task from "./Task";

export default {
    components: {
        Info,
        Processes,
        Process,
        Users,
        Bots,
        Groups,
        Relations,
        Connectors,
        Connector,
        Request,
        Variables,
        Variable,
        PublicApis,
        Tasks,
        Task
    },
    data() {
        return {
            processVersionId: null,
            environment: null,
            originalEnvironment: null,
            addMode: false,
            data: null,
            detailComponentName: 'Info',
            shouldSave: false,
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'environments',
            'graphs'
        ]),
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onNavigationChange(detailComponentName, data = null) {
            this.detailComponentName = detailComponentName;
            this.data = data;
        },
        onChangeAttribute(part, value) {
            this.environment = {
                ...this.environment,
                [part]: value
            };
            this.patchEnvironment(this.environment, true);
        },
        onBlueprintChange(blueprint) {
            this.environment = {
                ...this.environment,
                blueprint: {...blueprint}
            };
        },
        onDefaultUserChange(defaultUser) {
            this.environment = {
                ...this.environment,
                default_user: defaultUser
            };
        },
        onSave() {
            this.shouldSave = true;
            this.closeModal();
        },
        onCancel() {
            this.closeModal();
        },
        /**
         * Hier werden die Änderungen gespeichert oder verworfen, je nachdem ob der Speichern-Button "shouldSave = true" geklickt wurde
         * oder der Cancel-Button
         */
        onParentModalClose() {
            // Falls beim Neuanlegen der x-Button im Modal gewählt wird.
            if (this.addMode) {
                if (!this.shouldSave) {
                    this.deleteEnvironment(this.environment.id);
                }
            }
            else {
                if (this.shouldSave) {
                    this.patchEnvironment(this.environment);
                }
                else {
                    this.patchEnvironment(this.originalEnvironment, true);
                }
            }
        }
    },
    mounted() {
        this.addMode = this.ui.modal.data.addMode || false;
        this.environment = {...this.environments.find(ele => ele.id === this.ui.modal.data.environmentId)};
        this.originalEnvironment = {...this.environments.find(ele => ele.id === this.ui.modal.data.environmentId)};
        this.processVersionId = this.ui.modal.data.processVersionId;

        if (!this.graphs.length) {
            this.fetchUserGraphs();
        }
    },
};
</script>
