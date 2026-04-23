<template>
    <div>
        <div class="d-flex justify-content-between border-bottom align-content-center">
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link p-2 active mouse-pointer" v-on:click="showStartDetail()">
                        <span class="material-icons text-white">arrow_back</span>
                    </a>
                    <a class="nav-item nav-link p-2">
                        Start-Zustand: {{ initialState.description }}
                    </a>
                </div>
            </nav>
            <div class="dropdown px-2">
                <button class="btn btn-sm btn-light h-100" type="button" id="actionDropDownButton"
                        data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <span class="material-icons">more_vert</span>
                </button>
                <div class="dropdown-menu" aria-labelledby="actionDropDownButton">
                    <a v-on:click="$emit('onDeleteinitialState', initialState.id)" class="dropdown-item text-danger"
                       href="#">Löschen</a>
                </div>
            </div>
        </div>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0">
            <table class="table mb-0" style="font-size: 0.9rem">
                <tbody>
                <tr class="d-flex">
                    <td class="col-1 border-0 px-2 py-1"><i :class="'fas ' + initialState.image"
                                                            :style="'color:' + initialState.color"></i></td>
                    <td class="pr-0 col-11 border-0 px-2 py-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>{{ initialState.description }}</span>
                        </div>
                        <div><small class="text-muted">{{ initialState.min }} - {{ initialState.max }}</small></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';

export default {
    props: {
        initialStateId: String
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
    },
    computed: {
        ...mapGetters([
            'status_types',
            'action_types',
            'states',
            'ui'
        ]),
        initialState(){
            return this.states.find(ele => ele.id === this.initialStateId);
        },
        statusType () {
            return this.status_types.find(ele => ele.id === this.initialState.status_type_id);
        }
    },
};
</script>
