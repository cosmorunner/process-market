<template>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <ModalHeader title="Profilbild ändern"/>
            <div class="modal-body py-2">
                <cropper class="cropper" ref="cropper" classname="cropper-bg" backgroundClass="cropper-bg"
                         :src="image.src" :stencil-props="{aspectRatio: 1}" @change="change"/>

                <div class="d-flex justify-content-center mt-2">
                    <button class="btn btn-sm btn-primary" @click="$refs.file.click()">
                        <input type="file" ref="file" @change="loadImage($event)" accept="image/*">
                    </button>
                </div>
            </div>
            <ModalFooter :loading="saving" :only-cancel="false" :error-code="errorCode" :error-message="errorMessage"
                         @save="uploadImage" :save-label="'Speichern'"/>
        </div>
    </div>
</template>

<script>

import ModalHeader from "./ModalHeader";
import {Cropper} from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';
import ModalFooter from "./ModalFooter";

// This function is used to detect the actual image type,
function getMimeType(file, fallback = null) {
    const byteArray = (new Uint8Array(file)).subarray(0, 4);
    let header = '';
    for (let i = 0; i < byteArray.length; i++) {
        header += byteArray[i].toString(16);
    }
    switch (header) {
        case "89504e47":
            return "image/png";
        case "47494638":
            return "image/gif";
        case "ffd8ffe0":
        case "ffd8ffe1":
        case "ffd8ffe2":
        case "ffd8ffe3":
        case "ffd8ffe8":
            return "image/jpeg";
        default:
            return fallback;
    }
}

export default {
    components: {
        Cropper,
        ModalHeader,
        ModalFooter
    },
    props: {
        data: Object,
        onConfirm: Function,
        loading: Boolean,
        validationErrors: Array | Object,
        clearError: Function,
        editable: {
            type: Boolean,
            default: true
        },
    },
    data() {
        return {
            cropped: '',
            image: {
                src: this.data.src,
                type: null
            },
            showCropper: false,
            saving: false,
            error: null,
            errorCode: null,
            errorMessage: null
        };
    },
    computed: {},
    methods: {
        change({
                   coordinates,
                   canvas
               }) {
            this.cropped = canvas.toDataURL();
        },
        loadImage(event) {
            // Reference to the DOM input element
            const {files} = event.target;
            // Ensure that you have a file before attempting to read it
            if (files && files[0]) {
                // 1. Revoke the object URL, to allow the garbage collector to destroy the uploaded before file
                if (this.image.src) {
                    URL.revokeObjectURL(this.image.src);
                }
                // 2. Create the blob link to the file to optimize performance:
                const blob = URL.createObjectURL(files[0]);

                // 3. The steps below are designated to determine a file mime type to use it during the
                // getting of a cropped image from the canvas. You can replace it them by the following string,
                // but the type will be derived from the extension and it can lead to an incorrect result:
                //
                 this.image = {
                    src: blob,
                    type: files[0].type
                 }

                // Create a new FileReader to read this image binary data
                const reader = new FileReader();
                // Define a callback function to run, when FileReader finishes its job
                reader.onload = (e) => {
                    // Note: arrow function used here, so that "this.image" refers to the image of Vue component
                    this.image = {
                        // Set the image source (it will look like blob:http://example.com/2c5270a5-18b5-406e-a4fb-07427f5e7b94)
                        src: blob, // Determine the image type to preserve it during the extracting the image from canvas:
                        type: getMimeType(e.target.result, files[0].type),
                    };
                };
                // Start the reader job - read file as a data url (base64 format)
                reader.readAsArrayBuffer(files[0]);
            }
        },
        uploadImage() {
            this.saving = true;
            let that = this;

            const {canvas} = this.$refs.cropper.getResult();

            if (canvas) {
                const form = new FormData();

                canvas.toBlob(blob => {
                    form.append('file', blob);
                    form.append('_method', 'PATCH');
                    axios.post(this.data.endpoint, form)
                        .then((response) => window.location.href = response.data.redirect)
                        .catch(function(error){
                            that.saving = false;
                            that.errorCode = error.response.status;
                            that.errorMessage = error.response.data.errors.file[0] || '';
                        });
                    // Perhaps you should add the setting appropriate file format here
                }, 'image/jpg');
            }
        },
    }
};
</script>

<style scoped>

>>> .cropper-bg {
    background: #ffffff;
}

</style>