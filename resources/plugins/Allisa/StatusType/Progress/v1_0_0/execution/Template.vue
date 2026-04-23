<template>
    <table class="table table-borderless mb-0 h-100" style="table-layout: fixed;">
        <tbody>
        <tr>
            <td :class="'p-2 align-middle position-relative ' + (links.length ? 'w-70' : 'w-85')"
                style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">
                <div class="progress progress-bar-striped"
                     :style="'height:' + ((height > 2 ? 2 : height) * 23)  + 'px;'"
                     :title="status.value">
                    <div class="progress-bar" role="progressbar" :aria-valuenow="percentage"
                         :aria-label="statusType.name"
                         :aria-valuemin="options.min"
                         :aria-valuemax="options.min"
                         :style="'width:' + percentage + '%; background:' + (status.color || defaultColor)">
                        <span class="text-dark ml-1" v-if="percentage > 0">{{ percentage }} %</span>
                    </div>
                </div>
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
    data() {
        return {
            defaultColor: '#aaaafd',
            defaultImage: 'grain'
        };
    },
    computed: {
        percentage: function () {
            let value = Math.round((100 * parseFloat(this.status.value)) / this.options.max);
            value = value > 100 ? 100 : value;
            value = value < 0 ? 0 : value;

            return value;
        },
        links: function () {
            return this.state && !this.disableLinks ? (this.state.action_type_links || []) : [];
        }
    },
    created() {

    }
};
</script>
