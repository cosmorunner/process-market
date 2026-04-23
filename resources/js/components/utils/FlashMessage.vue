<template>
    <div>
        <div v-if="Array.isArray(messageProp)" :class="'alert-block mb-0 alert alert-' + classProp">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong v-if="messageProp.length === 1">{{ messageProp[0] }}</strong>
            <div v-else :id="classProp + '-accordion'">
                <div :class="'border-0 card alert-' + classProp">
                    <div :class="'p-0 border-0 card-header alert-' + classProp" :id="classProp + '-flash-heading'">
                        <span class="mb-0">
                            <button :class="'btn p-0 border-0 alert-' + classProp" data-toggle="collapse" :data-target="'#' + classProp + '-flash-accordion'"
                                    aria-expanded="true" aria-controls="collapseOne">
                                <strong class="dropdown-toggle">{{messageProp.length}} {{headerTransProp}}</strong>
                            </button>
                        </span>
                    </div>
                    <div :id="classProp + '-flash-accordion'" class="collapse hide" :aria-labelledby="classProp + '-flash-heading'"
                         :data-parent="'#' + classProp + '-accordion'">
                        <div class="card-body p-2">
                            <ul class="pl-2 m-0">
                                <li v-for="message in messageProp">{{message}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="typeof messageProp === 'string'" :class="'alert-block mb-0 alert alert-' + classProp">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{messageProp}}</strong>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            messageProp: {
                required: true
            },
            classProp: {
                required: true
            },
            headerTransProp: {
                required: true
            }
        }
    }
</script>
