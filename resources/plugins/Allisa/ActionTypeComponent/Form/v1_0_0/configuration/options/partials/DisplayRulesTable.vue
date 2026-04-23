<template>
    <div>
        <template v-for="(rules, groupKey) in rulesByGroups()">
            <div class="bg-light p-1 mb-2">
                <span class="p-1 text-primary">
                    <small>{{ groupLabels[groupKey] }}</small>
                </span>
                <table class="table table-sm table-borderless m-0">
                    <tbody>
                    <template v-for="(rule, index) in rules">
                        <tr class="d-flex">
                            <td class="py-0 w-25 mb-0">
                                <span class="badge badge-light" style="font-size: 80%;">
                                    <span v-if="rule[1].startsWith('field|')">{{ rule[1].split('|')[1] }}</span>
                                    <span v-if="rule[1] === 'auth|process_role_npms'">Prozess-Rollen des Benutzers</span>
                                </span>
                            </td>
                            <td class="py-0 w-20 mb-0">{{ operatorLabels[rule[2]] || '???' }}</td>
                            <td class="py-0 w-45 mb-0">
                                <span v-if="(rule[3] || '').startsWith('field|')">
                                    <span class="badge badge-light" style="font-size: 80%;">
                                        <span class="material-icons">input</span> {{ rule[3].split('|')[1] }}
                                    </span>
                                </span>
                                <span v-else-if="(rule[3] || '') === ''"><i>Leere Zeichenkette</i></span>
                                <span v-else-if="(rule[3] || '') === 'js|true'"><i>TRUE</i></span>
                                <span v-else-if="(rule[3] || '') === 'js|false'"><i>FALSE</i></span>
                                <span v-else-if="(rule[3] || '') === 'js|empty_array_object'"><i>Leeres JSON Array/Objekt</i></span>
                                <span v-else-if="(rule[3] || '').startsWith('role|')">
                                    <span class="material-icons">person</span>
                                    {{ labelFromPipeSyntax(rule[3]) }}
                                </span>
                                <span v-else>{{ rule[3] }}</span>
                            </td>
                            <td class="py-0 w-10 mb-0">
                                <button class="btn btn-sm btn-light float-right" @click="$emit('delete-item', part, rule)" v-if="editable">
                                    <span class="material-icons text-danger">close</span>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="index !== rules.length - 1">
                            <td class="text-muted p-0 pl-1">
                                <small>und</small>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
            <span v-if="!lastIndex(Object.keys(rulesByGroups()), groupKey)"
                  class="text-muted d-block my-1 pl-2">
                <small>oder</small>
            </span>
        </template>
    </div>
</template>

<script>

export default {
    props: {
        part: String,
        operatorLabels: Object,
        groupLabels: Object,
        displayRules: Array,
        editable: Boolean
    },
    methods: {
        rulesByGroups() {
            let grouped = {};

            for (let i = 0; i < this.displayRules.length; i++) {
                let groupName = this.displayRules[i][0];

                if (grouped.hasOwnProperty(groupName)) {
                    grouped[groupName].push(this.displayRules[i]);
                } else {
                    grouped[groupName] = [this.displayRules[i]];
                }
            }

            // Nach Gruppen-Nummer sortieren
            grouped = Object.keys(grouped).sort().reduce((obj, key) => ({
                ...obj,
                [key]: grouped[key]
            }), {});

            return grouped;
        },
        lastIndex(items, key) {
            return items.indexOf(key) === items.length - 1;
        },
        labelFromPipeSyntax(syntax){
            let match = syntax.match(/(?<=\[).+?(?=])/g);

            return Array.isArray(match) && match.length === 1 ? match[0] : syntax;
        }
    }
};
</script>

