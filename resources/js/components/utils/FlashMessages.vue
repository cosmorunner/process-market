<template>
    <div>
        <div v-if="flash_messages.successWithUndoButton" class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ flash_messages.successWithUndoButton.message }}</strong>
            <form :action="flash_messages.successWithUndoButton.route" method="POST" class="form-inline d-inline">
                <input type="hidden" :value="csrf">
                <input type="hidden" :value="flash_messages.successWithUndoButton.action_id" name="action_id"/>
                <button type="submit" class="btn btn-sm btn-outline-success">Rückgängig machen</button>
            </form>
        </div>
        <div v-if="flash_messages.success">
            <FlashMessage :message-prop="flash_messages.success" :class-prop="'success'"
                          :header-trans-prop="locals.notices"></FlashMessage>
        </div>
        <div v-if="flash_messages.error">
            <FlashMessage :message-prop="flash_messages.error" :class-prop="'danger'"
                          :header-trans-prop="locals.errors"></FlashMessage>
        </div>
        <div v-if="flash_messages.warning">
            <FlashMessage :message-prop="flash_messages.warning" :class-prop="'warning'"
                          :header-trans-prop="locals.warnings"></FlashMessage>
        </div>
        <div v-if="flash_messages.info">
            <FlashMessage :message-prop="flash_messages.info" :class-prop="'info'"
                          :header-trans-prop="locals.notices"></FlashMessage>
        </div>
    </div>
</template>

<script>
import FlashMessage from "./FlashMessage";
import {mapGetters} from "vuex";

export default {
    components: {FlashMessage},
    props: {
        locals: {
            required: true
        }
    },
    computed: {
        ...mapGetters([
            'flash_messages'
        ]),
        csrf: function () {
            return document.head.querySelector("[name=csrf-token][content]").content;
        }
    },
};
</script>
