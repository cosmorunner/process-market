<template>
    <div class="modal-header py-2">
        <h5 class="modal-title" id="exampleModalLabel">{{ title }}</h5>
        <div class="d-inline-block ml-2" v-if="docsArticle">
            <Docs :article="docsArticle" :section="docsSection" />
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>

<script>
import Docs from "../utils/Docs";
export default {
    components: {Docs},
    props: {
        docsArticle: {
            type: String,
            default: ''
        },
        docsSection: {
            type: String,
            default: ''
        },
        title: String
    }
};
</script>

