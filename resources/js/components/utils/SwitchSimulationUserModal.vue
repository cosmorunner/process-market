<template>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <ModalHeader title="Benutzer wechseln"/>
            <div class="modal-body py-2">
                <div class="d-flex justify-content-center" v-if="fetching">
                    <img src="/img/loading.gif" alt="Loading" width="30" height="30"/>
                </div>
                <div v-if="fetched && !error">
                    <!-- Es gibt weitere Benutzer -->
                    <ul class="list-group list-group-flush" v-if="otherUsers.length">
                        <li class="p-2 list-group-item d-flex justify-content-between" v-for="user in otherUsers">
                            <div>
                                <span class="d-block">{{ data.simulationAccessLabel(user.id) }}</span>
                                <span class="d-block text-muted">
                                    <span class="material-icons">group</span>
                                    <span>{{ data.groupLabel(user.id) }}</span>
                                </span>
                            </div>
                            <button v-if="!switchingUser" class="btn btn-sm btn-success" @click="switchUser(user.id)">
                                Auswählen
                            </button>
                            <img v-if="switchingUser === user.id" src="/img/loading.gif" alt="Loading" width="29"
                                 height="29"/>
                        </li>
                    </ul>
                    <!-- Es gibt KEINE weitere Benutzer -->
                    <ul class="list-group list-group-flush" v-else>
                        <li class="p-0 list-group-item">Es gibt keine weiteren Benutzer in dieser Demo.</li>
                    </ul>
                </div>
                <div v-if="fetched && error">
                    <span>Verzeihung, leider ist es bei der Anfrage zu einem Fehler gekommen.</span>
                </div>
                <!-- Der aktuelle Benutzer hat KEINEN Zugriff doch es kann zu einem Anderen Benutzer gewechselt werden.  -->
            </div>
            <ModalFooter :loading="loading" :only-cancel="true" :error-code="errorCode" :error-message="errorMessage"
                         :save-label="'Speichern'"/>
        </div>
    </div>
</template>

<script>

import ModalHeader from "./ModalHeader";
import ModalFooter from "./ModalFooter";

export default {
    components: {
        ModalHeader,
        ModalFooter
    },
    props: {
        data: Object,
        onConfirm: Function,
        loading: Boolean,
        errorCode: Number | null,
        errorMessage: String | null,
        validationErrors: Array | Object,
        clearError: Function,
    },
    data() {
        return {
            users: null,
            fetched: false,
            fetching: false,
            switchingUser: null,
            error: null
        };
    },
    computed: {
        otherUsers() {
            return this.users.filter(ele => ele.id !== this.data.allisaUserId)
        }
    },
    methods: {
        switchUser(userId) {
            this.switchingUser = userId;
            let that = this;
            axios.patch('/api/simulations/' + this.data.simulationId + '/switch-user', {
                'user_id': userId
            }).then(function () {
                window.location.reload();
            }).catch(function (error) {
                console.warn(error);
                that.error = error;
            });
        }
    },
    mounted() {
        let that = this;
        this.fetching = true;
        axios.get('/api/simulations/' + this.data.simulationId + '/users')
            .then(function (response) {
                that.users = response.data;
                that.fetching = false;
                that.fetched = true;
            }).catch(function (error) {
            that.fetching = false;
            that.fetched = true;
            that.error = error;
            console.warn(error);
        });
    }
};
</script>

