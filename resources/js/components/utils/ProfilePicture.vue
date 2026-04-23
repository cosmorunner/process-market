<template>
    <div>
        <div :class="'position-relative p-1 ' + (editable ? 'edit-image mouse-pointer' : '')" @click="openModal">
            <div class="placeholder rounded-circle ml-auto mr-auto">
                <img :src="src" class="rounded-circle d-block d-inline-block" alt="" width="130" height="130"/>
            </div>
            <span class="icon material-icons position-absolute text-muted d-none" style="bottom:5px; right:5px" v-if="editable">edit</span>
        </div>
        <Modal v-if="modal" :modal="modal" :error-code="null" :error-message="''" :validation-errors="[]"
               :loading="false" :clear-error="() => null" @close-modal="closeModal"/>
    </div>
</template>

<script>

import Modal from "./Modal.vue";
import EditProfilePictureModal from "./EditProfilePictureModal.vue";

export default {
    components: {Modal},
    props: {
        src: String,
        endpoint: String,
        showEdit: {
            type: Boolean,
            default: false
        },
        editable: {
            type: Boolean,
            default: true
        },
    },
    data() {
        return {
            modal: null
        };
    },
    methods: {
        openModal() {
            if(!this.editable) {
                return;
            }

            this.modal = {
                component: EditProfilePictureModal,
                onConfirm: this.onConfirm,
                data: {
                    src: this.src,
                    endpoint: this.endpoint,
                    editable: this.editable
                }
            };
        },
        closeModal() {
            this.modal = null;
        }
    }
};
</script>

<style scoped>

>>> .placeholder {
    background: #efefef;
    width: 130px;
    height: 130px;
}

>>> .edit-image:hover {
    background: #efefef;
}

>>> .edit-image:hover .icon {
    display: inline-block !important;
}

</style>