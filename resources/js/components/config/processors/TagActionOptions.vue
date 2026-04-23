<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="tag">Tag</label>
            <input type="text" class="form-control" id="tag" name="tag" :value="options.tag"
                   @input="$emit('option-change', 'tag', $event.target.value)"
                   :readonly="!ui.editable" maxlength="40"/>
            <small class="text-muted">Kleingeschrieben, nur a-z, 0-9 und "_".</small>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapGetters} from "vuex";
import DropdownSelector from "../../utils/DropdownSelector";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText";

export default {
    components: {OptionBadgesWithText, DropdownSelector},
    props: {
        options: Object,
        actionType: Object,
    },
    computed: {
        ...mapGetters(['ui'])
    },
    methods: {
        ...utils
    }
};
</script>
