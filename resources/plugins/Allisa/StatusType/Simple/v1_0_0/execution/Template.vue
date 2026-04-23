<template>
    <table class="table table-borderless mb-0 h-100" style="table-layout: fixed;">
        <tbody>
        <tr>
            <td v-if="status.image"
                class="px-0 py-2 align-middle text-center w-15 rounded-bottom-left border-right"
                :style="'background:' + (status.color ? (status.color || 'transparent') : 'transparent')">
                <div>
                    <span class="material-icons mi-2x" style="color:#555555">{{ status.image }}</span>
                </div>
            </td>
            <td :class="'p-2 align-middle ' + (descriptionWidth)"
                style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;" data-toggle="tooltip"
                data-placement="top" :title="status.value">
                <span v-if="height === 1" :title="status.value">{{ status.text_value }}</span>
                <h5 v-if="height === 2" :title="status.value">{{ status.text_value }}</h5>
                <h4 v-if="height === 3" :title="status.value">{{ status.text_value }}</h4>
            </td>
            <td v-if="links.length" class="w-15 border-left align-middle p-0 text-center">
                <template v-if="links.length === 1">
                    <a :href="links[0].url" class="btn btn-sm">
                        <span class="material-icons text-secondary cursor-pointer">chevron_right</span>
                    </a>
                </template>
                <template v-if="links.length > 1">
                    <span class="material-icons text-secondary cursor-pointer align-middle" id="actionTypeLinks"
                          data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">chevron_right</span>
                    <div class="dropdown-menu" aria-labelledby="actionTypeLinks">
                        <template v-for="link in links">
                            <a class="dropdown-item" :href="link.url">{{ link.name }}</a>
                        </template>
                    </div>
                </template>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>

export default {
    props: {
        statusType: Object,
        options: Array | Object,
        label: String,
        width: Number,
        height: Number,
        status: Object,
        state: Object,
        disableLinks: {
            required: true,
            type: [
                String,
                Boolean
            ]
        }
    },
    computed: {
        links() {
            return this.state && !this.disableLinks ? (this.state.action_type_links || []) : [];
        },
        descriptionWidth() {
            if (this.state && this.state.image && this.links.length) {
                return 'w-70';
            }
            if (this.state && this.state.image && !this.links.length) {
                return 'w-85';
            }
            if (this.links.length && (!this.state || !this.state.image)) {
                return 'w-85';
            }
        }
    }
};
</script>
