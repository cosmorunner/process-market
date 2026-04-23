<template>
    <div class="row">
        <div class="col-12">
            <App/>
        </div>
    </div>
</template>

<script>

import App from "./list_builder/App";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        App
    },
    watch: {
        data: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    }
};
</script>
