<template>
    <div>
        <!-- Text -->
        <div class="input-group input-group-sm">
            <input v-if="!outputHasInRule(output) && !outputHasFileRule(output) && !outputHasBooleanRule(output)"
                   class="form-control form-control-sm" @input="setDataValue" :value="outputValue"
                   :disabled="disabled.includes(output.name)"/>
            <!-- In-Rule -->
            <select v-if="outputHasInRule(output)" class="custom-select" @change="setDataValue" :value="outputValue">
                <option :value="value" v-for="value in outputInValues(output)">{{ value }}</option>
            </select>
            <!-- Model Pipe Notation Dropdown -->
            <div v-if="outputHasModelPipeRule(output) && !outputHasInRule(output)" class="input-group-append">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Model-Pipe-Notation
                </button>
                <div class="dropdown-menu dropdown-max-height-400">
                    <div class="dropdown-header">
                        <span>Prozesse</span>
                    </div>
                    <button type="button" class="dropdown-item" v-if="simulationIsRunning"
                            v-for="process in mpnProcesses"
                            @click="$emit('set-data-value', output.name, process.value, isList, listIndex)">
                        <span class="material-icons pb-1">data_saver_off</span>
                        <span>{{ process.label }}</span>
                    </button>
                    <button type="button" class="dropdown-item" v-if="!simulationIsRunning" v-for="processes in environmentProcesses"
                            @click="$emit('set-data-value', output.name, 'process|'+processes.id, isList, listIndex)">
                        <span class="material-icons pb-1">data_saver_off</span>
                        <span>{{ processes.name }}</span>
                    </button>
                    <template v-if="environmentGroups.length">
                        <div class="dropdown-header">
                            <span>Gruppen</span>
                        </div>
                        <button type="button" class="dropdown-item" v-for="groups in environmentGroups"
                                @click="$emit('set-data-value', output.name, 'group|'+groups.aliases[0], isList, listIndex)">
                            <span class="material-icons pb-1">group</span>
                            <span>{{ groups.name }}</span>
                        </button>
                    </template>
                    <template v-if="environmentUsers.length">
                        <div class="dropdown-header">
                            <span>Benutzer</span>
                        </div>
                        <button type="button" class="dropdown-item" v-for="users in environmentUsers"
                                @click="$emit('set-data-value', output.name, 'user|'+users.aliases[0], isList, listIndex)">
                            <span class="material-icons pb-1">person</span>
                            <span>{{ users.first_name + ' ' + users.last_name }}</span>
                        </button>
                    </template>
                    <template v-if="environmentBots.length">
                        <div class="dropdown-header">
                            <span>Bots</span>
                        </div>
                        <button type="button" class="dropdown-item" v-for="bot in environmentBots"
                                @click="$emit('set-data-value', output.name, 'bot|'+bot.aliases, isList, listIndex)">
                            <span class="material-icons pb-1">smart_toy</span>
                            <span>{{ bot.first_name }}</span>
                        </button>
                    </template>
                    <template v-if="roles.length">
                        <div class="dropdown-header">
                            <span>Rollen</span>
                        </div>
                        <button type="button" class="dropdown-item" v-for="role in roles"
                                @click="$emit('set-data-value', output.name, 'role|' + role.id, isList, listIndex)">
                            <span class="material-icons pb-1">workspaces</span>
                            <span>{{ role.name }}</span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
        <!-- Boolean -->
        <div v-if="outputHasBooleanRule(output)" style="line-height: 29px;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" :checked="outputValue === '1'" @click="toggleCheckbox">
                <label class="form-check-label">
                    &nbsp;
                </label>
            </div>
        </div>
        <!-- File Dropdown -->
        <div v-if="outputHasFileRule(output)">
            <button class="btn btn-sm btn-block btn-outline-primary dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ outputValue ? filePresets[outputValue] : 'Keine Datei' }}
            </button>
            <div class="dropdown-menu scrollable-dropdown">
                <button class="dropdown-item" type="button"
                        @click="$emit('clear-item', output.name, isList, listIndex)">
                    <span>Keine Datei</span>
                </button>
                <button class="dropdown-item" type="button" v-for="(label, value) in filePresets"
                        @click="$emit('set-data-value', output.name, value, isList, listIndex)">
                    <span>{{ label }}</span>
                </button>
            </div>
        </div>
        <div class="" v-for="error in errors">
            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        output: Object,
        outputValue: String | null | Object | Array | Boolean,
        demoUserIdentityId: String,
        errors: {
            type: Array,
            default: () => []
        },
        roles: Array | Object,
        simulationIsRunning: Boolean,
        processModelPipeNotations: Array | Object,
        environmentValues: {
            type: Object,
            default: () => {
                return {
                    processes: [],
                    groups: [],
                    users: [],
                    bots: []
                };
            }
        },
        disabled: Array,
        isList: Boolean,
        listIndex: Number
    },
    data() {
        return {
            filePresets: {
                '{{dummy.file.pdf}}': '.pdf - Testdatei',
                '{{dummy.file.image}}': '.jpg - Testgrafik',
                '{{dummy.file.excel}}': '.xlsx - Test-Exceldatei',
            }
        };
    },
    computed: {
        environmentUsers() {
            return this.environmentValues.users;
        },
        environmentGroups() {
            return this.environmentValues.groups;
        },
        environmentProcesses() {
            return this.environmentValues.processes;
        },
        environmentBots() {
            return this.environmentValues.bots;
        },
        mpnProcesses() {
            return this.processModelPipeNotations.map(item => ({...item}));
        }
    },
    methods: {
        setDataValue(e) {
            this.$emit('set-data-value', this.output.name, e.target.value, this.isList, this.listIndex);
        },
        toggleCheckbox(e) {
            let value = e.target.checked ? '1' : '0';
            this.$emit('set-data-value', this.output.name, value, this.isList, this.listIndex);
        },
        outputHasInRule(output) {
            return output.validation.find(ele => ele.startsWith('in:'));
        },
        outputInValues(output) {
            let rule = output.validation.find(ele => ele.startsWith('in:'));
            let values = [];

            if (rule) {
                values = rule.substring(3).split(',').map(ele => ele.trim());
            }

            return values;
        },
        outputHasModelPipeRule(output) {
            return output.validation.find(ele => ele === 'model_pipe');
        },
        outputHasFileRule(output) {
            return output.validation.find(ele => ele === 'file');
        },
        outputHasBooleanRule(output) {
            return output.validation.find(ele => ele === 'boolean');
        }
    }
};
</script>
