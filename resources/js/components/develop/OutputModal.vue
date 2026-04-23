<template>
    <div class="modal-dialog modal-lg" role="document" id="data-modal">
        <div class="modal-content">
            <ModalHeader :title="createMode ? 'Datenfeld erstellen' : 'Datenfeld bearbeiten'"/>
            <div class="modal-body py-2">
                <div class="row d-flex">
                    <div class="col">
                        <form>
                            <div class="form-group mb-2">
                                <label for="name" class="mb-0">Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm"
                                           id="name" v-model="data.name" aria-describedby="name" required :readonly="!ui.editable">
                                    <div class="input-group-append" v-if="createMode">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            #
                                            <span class="sr-only">Toggle Syntax</span>
                                        </button>
                                        <div class="dropdown-menu scrollable-dropdown">
                                            <button class="dropdown-item" type="button" v-for="name in uniqueNames" @click="data.name = name">{{ name }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Kleingeschrieben, nur "a-z", "0-9" und "_".</small>
                                <div v-for="error in (ui.validationErrors.name || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description" class="mb-0">Beschreibung</label>
                                <textarea class="form-control form-control-sm" v-model="data.description" :readonly="!ui.editable"
                                          id="description" aria-describedby="description"></textarea>
                                <div v-for="error in (ui.validationErrors.description || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="default" class="mb-0">Typ</label>
                                <div class="mb-2 mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeDefaultValue" :disabled="!ui.editable"
                                               id="typeDefault" value="basic" v-model="outputType">
                                        <label class="form-check-label" for="typeDefault">Einfach</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeDefaultValue" :disabled="!ui.editable"
                                               id="typeArray" value="array" v-model="outputType">
                                        <label class="form-check-label" for="typeArray">JSON-Array</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeDefaultValue" :disabled="!ui.editable"
                                               id="typeObject" value="object" v-model="outputType">
                                        <label class="form-check-label" for="typeObject">JSON-Objekt</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="default" class="mb-0">Standard-Wert</label>
                                <small class="text-muted d-block">Wird genutzt wenn der Wert eine leere
                                    Zeichenkette/Liste/Objekt oder nicht vorhanden ist.</small>
                                <div v-if="outputType === 'basic'" class="mt-2">
                                    <div class="form-group input-group-sm mb-2">
                                        <textarea class="form-control" rows="1"
                                                  @input="data.default = $event.target.value"
                                                  v-bind:value="data.default"
                                                  :placeholder="data.default === null ? 'Kein Standard-Wert' : 'Leere Zeichenkette'"
                                                  :disabled="data.default === null || !ui.editable"></textarea>
                                        <OptionBadgesWithText :value="data.default" display-block hide-on-empty/>
                                    </div>
                                    <div class="form-group input-group-sm mb-2" v-if="ui.editable">
                                        <DropdownSelector
                                            :syntax-include="Object.keys(syntaxLoaderLabels())"
                                            :pipe-include="['environment_variables', 'environment_users', 'environment_groups', 'environment_bots', 'roles']"
                                            :action-type="actionType" @selected="onSelectDropdown" :dropdown-width="766"/>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                @click="data.default = (data.default === '' ? null : '')">
                                            {{ data.default === '' ? 'Kein Standard-Wert' : 'Leere Zeichenkette' }}
                                        </button>
                                    </div>
                                    <div v-for="error in (ui.validationErrors.default || [])">
                                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                    </div>
                                </div>
                                <div v-if="outputType === 'array' || outputType === 'object'">
                                    <small class="text-muted">
                                        <span>JSON-Eingabe - </span>
                                        <span class="material-icons">info</span>
                                        <span>Alle Zeichenketten-Werte werden getrimmt.</span>
                                    </small>
                                    <CodeEditor :code="JSON.stringify(data.default, null, 4)"
                                                @update-code="onCodeChange" :max-length="1500"
                                                :watch-code-prop="true"
                                                :editable="ui.editable"
                                    />
                                    <div>
                                        <small v-if="data.default.length > 1499" class="text-danger">Wert zu lang!</small>
                                        <small v-else-if="invalidCode" class="text-danger">Ungültiges JSON</small>
                                        <small v-else class="text-success">Gültiges JSON</small>
                                    </div>
                                    <DropdownSelector
                                        :syntax-include="Object.keys(syntaxLoaderLabels())"
                                        :pipe-include="['environment_variables']"
                                        @selected="copyText"
                                        :dropdown-width="766"
                                        v-if="ui.editable"
                                    />
                                    <span class="text-success" v-if="showCopied">Kopiert!</span>
                                </div>
                            </div>
                            <div v-if="actionContext">
                                <div class="form-group mb-2">
                                    <label class="mb-0">Validatoren</label>
                                    <div v-for="error in (ui.validationErrors.validation || [])">
                                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="row mt-1" v-if="data.type === 'array'">
                                        <div class="form-group col-3 mb-0">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-block btn-outline-primary dropdown-toggle"
                                                        :disabled="!ui.editable"
                                                        type="button" id="minAnzahl" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                    Min. Anzahl: {{ data.type_options.min || 0 }}
                                                </button>
                                                <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dropdownMenuButton">
                                                    <button type="button" class="dropdown-item"
                                                            v-for="index in [...Array(maxListeLength)].keys()"
                                                            @click="onMinChange(index)">{{ index }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-3 mb-0">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-block btn-outline-primary dropdown-toggle"
                                                        :disabled="!ui.editable"
                                                        type="button" id="maxAnzahl" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                    Max. Anzahl: {{ data.type_options.max }}
                                                </button>
                                                <div class="dropdown-menu scrollable-dropdown"
                                                     aria-labelledby="dropdownMenuButton">
                                                    <button type="button" class="dropdown-item"
                                                            v-for="index in [...Array(maxListeLength)].keys()"
                                                            @click="onMaxChange(index)">{{ index }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2" v-if="outputType === 'array'">
                                    <hr/>
                                    <small class="text-muted">JSON-Array: Die Validierungsregeln werden auf jeden Wert im Array
                                        angewendet.</small>
                                    <hr/>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="validator-required"
                                               :checked="ruleIsActive('required')" @click="toggleRequired" :disabled="!ui.editable">
                                        <label class="custom-control-label" for="validator-required">Pflichtfeld</label>
                                    </div>
                                    <small class="text-muted">Der Wert darf keine leere Zeichenkette oder Objekt sein.</small>
                                </div>
                                <div class="form-group mb-2" v-if="outputType === 'basic' || outputType === 'array'">
                                    <div class="mb-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-none" value="none"
                                                   @click="removeBasicRules"
                                                   :checked="!availableBasicRules.some((rule) => stringifiedRules.indexOf(rule) >= 0)"
                                                   :disabled="!ui.editable">
                                            <label class="form-check-label" for="validator-none">Keinen Validator</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-any" value="any"
                                                   @click="setInRule"
                                                   :checked="ruleIsActive('in')"
                                                   :disabled="!ui.editable">
                                            <label class="form-check-label" for="validator-any">Bestimmte Werte</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-regex"
                                                   value="regex" @click="setBasicRule('regex:/(.*?)/')"
                                                   :checked="ruleIsActive('regex')" :disabled="!ui.editable">
                                            <label class="form-check-label" for="validator-regex">Regex</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-numeric"
                                                   value="numeric" @click="setNumeric"
                                                   :checked="ruleIsActive('numeric')" :disabled="!ui.editable">
                                            <label class="form-check-label" for="validator-numeric">Zahlen</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-email"
                                                   value="email" @click="setBasicRule('email')"
                                                   :checked="ruleIsActive('email')" :disabled="!ui.editable">
                                            <label class="form-check-label" for="validator-email">E-Mail</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-date_format"
                                                   value="date_format" @click="setDateFormat"
                                                   :checked="ruleIsActive('date_format')" :disabled="!ui.editable">
                                            <label class="form-check-label" for="validator-date_format">Datum</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-time"
                                                   value="time" @click="setBasicRule('time')"
                                                   :checked="ruleIsActive('time')" :disabled="!ui.editable">
                                            <label class="form-check-label" for="validator-time">Uhrzeit</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-boolean"
                                                   value="boolean" @click="setBasicRule('boolean')"
                                                   :checked="ruleIsActive('boolean')" :disabled="!ui.editable">
                                            <label class="form-check-label" for="validator-boolean">Checkbox</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-model-pipe"
                                                   value="model_pipe" @click="setBasicRule('model_pipe')"
                                                   :checked="ruleIsActive('model_pipe')" :disabled="!ui.editable">
                                            <label class="form-check-label"
                                                   for="validator-model-pipe">Model-Pipe-Notation</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="base-rule"
                                                   id="validator-file"
                                                   value="file" @click="setFile"
                                                   :checked="ruleIsActive('file')" :disabled="!ui.editable">
                                            <label class="form-check-label" for="validator-file">Datei</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template v-if="actionContext && (outputType === 'basic' || outputType === 'array')">
                                <!-- Beliebige Zeichen -->
                                <template v-if="ruleIsActive('in')">
                                    <div class="row">
                                        <label for="validator-in-array" class="col-sm-5 col-form-label">Erlaubte Werte</label>
                                        <div class="col-sm-7">
                                            <div class="form-group mb-2">
                                                <textarea type="text" class="form-control form-control-sm" v-model="inArrayValue" rows="2"
                                                          id="validator-in-array" aria-describedby="validator-in-array"
                                                          :readonly="!ui.editable" @input="addInArrayRule"></textarea>
                                                <small class="text-muted">Kommasepariert</small>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <!-- Regex -->
                                <template v-if="ruleIsActive('regex')">
                                    <div class="row">
                                        <label for="validator-in-array" class="col-sm-5 col-form-label">Regex</label>
                                        <div class="col-sm-7">
                                            <div class="form-group mb-2">
                                                <textarea class="form-control form-control-sm" id="validator-regex" rows="2"
                                                          :readonly="!ui.editable"
                                                          v-model="regexValue" @input="onRegexInput"
                                                          aria-describedby="validator-regex"></textarea>
                                                <small class="text-muted">PCRE2 Syntax. Z.B. "/[a-zA-Z0-9]/" für alphanumerische
                                                    Zeichen.</small>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <!-- Zahlen -->
                                <template v-if="ruleIsActive('numeric')">
                                    <div class="row">
                                        <label for="validator-min-value" class="col-sm-5 col-form-label">Min. Wert</label>
                                        <div class="col-sm-7">
                                            <div class="form-group mb-2">
                                                <input type="number" step="0.01" v-model="minValue" @input="onMinValueInput"
                                                       class="form-control form-control-sm" :readonly="!ui.editable"
                                                       id="validator-min-value" aria-describedby="validator-min-value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="validator-max-value" class="col-sm-5 col-form-label">Max. Wert</label>
                                        <div class="col-sm-7">
                                            <div class="form-group mb-2">
                                                <input type="number" step="0.01" v-model="maxValue" @input="onMaxValueInput"
                                                       class="form-control form-control-sm" :readonly="!ui.editable"
                                                       id="validator-max-value" aria-describedby="validator-max-value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="validator-numeric-step" class="col-sm-5 col-form-label">Schritt</label>
                                        <div class="col-sm-7">
                                            <div class="form-group mb-0">
                                                <select class="form-control form-control-sm" id="validator-numeric-step" v-model="step"
                                                        :disabled="!ui.editable" @change="onStepChange">
                                                    <option value="0.001">0.001</option>
                                                    <option value="0.01">0.01</option>
                                                    <option value="0.1">0.1</option>
                                                    <option value="0.25">0.25</option>
                                                    <option value="0.5">0.5</option>
                                                    <option value="1">1</option>
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="100">100</option>
                                                    <option value="1000">1000</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <!-- E-Mail -->
                                <template v-if="ruleIsActive('email')">
                                    <div class="row">
                                        <div class="col">
                                            <div class="alert alert-info" role="alert">
                                                Der Wert muss eine gültige E-Mail Adresse sein.
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <!-- Datum -->
                                <template v-if="ruleIsActive('date_format')">
                                    <div class="row">
                                        <label for="validator-numeric-interval" class="col-sm-5 col-form-label">Format</label>
                                        <div class="col-sm-7">
                                            <div class="form-group mb-0">
                                                <select class="form-control form-control-sm" id="validator-numeric-interval"
                                                        v-model="dateFormat" @change="onDateFormatChange" :disabled="!ui.editable">
                                                    <option value="d.m.Y">DD.MM.YYYY (z.B. 03.08.2011)</option>
                                                    <option value="d.m.Y H:i">DD.MM.YYYY H:M (z.B. 03.08.2011 13:37)</option>
                                                    <option value="d.m.Y H:i:s">DD.MM.YYYY H:M:S (z.B. 03.08.2011 13:37:00)</option>
                                                    <option value="d-m-Y">DD-MM-YYYY (z.B. 03-08-2011)</option>
                                                    <option value="d-m-Y H:i">DD-MM-YYYY H:M (z.B. 03-08-2011 13:37)</option>
                                                    <option value="d-m-Y H:i:s">DD-MM-YYYY H:M:S (z.B. 03-08-2011 13:37:00)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="validator-min-date" class="col-sm-5 col-form-label">Frühestes Datum</label>
                                        <div class="col-sm-7">
                                            <AutocompleteSelector
                                                :items="afterDate ? [afterDate] : []"
                                                :add-only-from-autocomplete="false"
                                                :max-items="1"
                                                :editable="ui.editable"
                                                :syntax-include="['action.outputs', 'process.outputs', 'variables', 'reference.outputs', 'date']"
                                                @items-changed="onAfterDateChange"
                                            />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="validator-max-date" class="col-sm-5 col-form-label">Spätestes Datum</label>
                                        <div class="col-sm-7">
                                            <AutocompleteSelector
                                                :items="beforeDate ? [beforeDate] : []"
                                                :add-only-from-autocomplete="false"
                                                :max-items="1"
                                                :editable="ui.editable"
                                                :syntax-include="['action.outputs', 'process.outputs', 'variables', 'reference.outputs', 'date']"
                                                @items-changed="onBeforeDateChange"
                                            />
                                        </div>
                                    </div>
                                </template>
                                <!-- Uhrzeit -->
                                <template v-if="ruleIsActive('time')">
                                    <div class="row">
                                        <div class="col">
                                            <div class="alert alert-info" role="alert">
                                                Der Wert muss eine gültige Uhrzeit im 24-Stunden Format sein, z.B. "1:45" oder "13:37".
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <!-- Checkbox -->
                                <template v-if="ruleIsActive('boolean')">
                                    <div class="row">
                                        <div class="col">
                                            <div class="alert alert-info" role="alert">
                                                Der Wert muss true false, 1, 0, "1" oder "0". sein.
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <!-- Model-Pipe Notation -->
                                <template v-if="ruleIsActive('model_pipe')">
                                    <div class="row">
                                        <div class="col">
                                            <div class="alert alert-info" role="alert">
                                                Der Wert muss dem Format einer Model-Pipe-Notation entsprechen, z.B.
                                                "process|2a0fca7f-e438-49ee-b785-a5095e14fc37". Model und Id getrennt mit "|"-Symbol.
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <!-- Datei -->
                                <template v-if="ruleIsActive('file')">
                                    <div class="row">
                                        <label for="validator-min-value" class="col-sm-4 col-form-label">Erlaubte Dateiendungen,
                                            kommasepariert</label>
                                        <div class="col-sm-8">
                                            <div class="form-group mb-2">
                                                <input type="text" class="form-control form-control-sm" v-model="fileExtensions"
                                                       @input="onFileExtensionsInput" id="validator-min-value" :readonly="!ui.editable"
                                                       aria-describedby="validator-min-value">
                                                <small class="text-muted">Kommasepariert. Leer lassen für alle Dateiendungen. Z.B. "jpg,
                                                    png" für JPG und PNG Bilder.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="validator-size-max" class="col-sm-4 col-form-label">Max. Größe in Kilobytes.</label>
                                        <div class="col-sm-8">
                                            <div class="form-group mb-2">
                                                <input type="number" step="1" class="form-control form-control-sm" v-model="maxSize"
                                                       :readonly="!ui.editable" @input="onMaxSizeInput" id="validator-size-max"
                                                       aria-describedby="validator-size-max">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-if="outputType === 'array'">
                                    <hr>
                                    <div class="form-group mb-2">
                                        <label class="mb-0">Einzigartige Werte</label>
                                        <small class="text-muted d-block">JSON-Array Werte auf Einzigartigkeit prüfen.</small>
                                    </div>
                                    <div class="mb-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="distinct-values"
                                                   id="distinct-values-deactivated" value="distinct_values_deactivated" @click="removeDistinctValues"
                                                   :checked="!ruleIsActive('distinct_values')">
                                            <label class="form-check-label" for="distinct-values-deactivated">Deaktiviert</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="distinct-values"
                                                   id="distinct-values-activated" value="distinct_values_activated" @click="setDistinctValues"
                                                   :checked="ruleIsActive('distinct_values')">
                                            <label class="form-check-label" for="distinct-values-activated">Aktiviert</label>
                                        </div>
                                    </div>
                                </template>
                                <hr>
                                <div class="form-group mb-2">
                                    <label class="mb-0">Verknüpfungsprüfung</label>
                                    <small class="text-muted d-block">Prüfung auf verknüpfte Prozess-Instanzen.</small>
                                </div>
                                <div class="mb-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="relation-rule"
                                               id="relation-rule-none" value="none" @click="removeRelationExists" :disabled="!ui.editable"
                                               :checked="!ruleIsActive('relation_exists') && !ruleIsActive('relation_not_exists')">
                                        <label class="form-check-label" for="relation-rule-none">Deaktiviert</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="relation-rule"
                                               id="relation-rule-exists" value="relation_exists" @click="setRelationRule('relation_exists')" :disabled="!ui.editable"
                                               :checked="ruleIsActive('relation_exists')">
                                        <label class="form-check-label" for="relation-rule-exists">Verknüpfung muss existieren</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="relation-rule"
                                               id="relation-rule-does-not-exists" value="relation_not_exists"
                                               @click="setRelationRule('relation_not_exists')" :disabled="!ui.editable"
                                               :checked="ruleIsActive('relation_not_exists')">
                                        <label class="form-check-label" for="relation-rule-does-not-exists">Verknüpfung darf nicht existieren</label>
                                    </div>
                                </div>
                                <!-- Verknüpfung existiert -->
                                <template v-if="ruleIsActive('relation_exists') || ruleIsActive('relation_not_exists')">
                                    <div class="row">
                                        <label for="validator-min-date" class="col-sm-5 col-form-label">Verknüpfungstyp</label>
                                        <div class="col-sm-7">
                                            <AutocompleteSelector
                                                :items="relationExistsRelationType ? [relationExistsRelationType] : []"
                                                :add-only-from-autocomplete="true"
                                                :max-items="1"
                                                :editable="ui.editable"
                                                :pipe-include="['relation_types', 'graphs_relation_types']"
                                                @items-changed="onRelationExistsRelationTypeChange"
                                            />
                                        </div>
                                    </div>
<!--                                    <div class="row">-->
<!--                                        <label for="validator-numeric-step" class="col-sm-5 col-form-label">Operator</label>-->
<!--                                        <div class="col-sm-7">-->
<!--                                            <div class="form-group mb-0">-->
<!--                                                <select class="form-control form-control-sm" id="validator-numeric-step"-->
<!--                                                        v-model="relationExistOperator" :disabled="!ui.editable"-->
<!--                                                        @change="setRelationRule(ruleIsActive('relation_exists') ? 'relation_exists' : 'relation_not_exists')">-->
<!--                                                    <option value="">Keine zusätzliche Werteprüfung</option>-->
<!--                                                    <option value="==">Gleich</option>-->
<!--                                                    <option value="===">Gleich (strikt)</option>-->
<!--                                                    <option value="!=">Nicht gleich</option>-->
<!--                                                    <option value=">">Größer als</option>-->
<!--                                                    <option value=">=">Größer oder gleich</option>-->
<!--                                                    <option value="<">Kleiner als</option>-->
<!--                                                    <option value="<=">Kleiner oder gleich</option>-->
<!--                                                    <option value="equal_age">Gleich (Datum)</option>-->
<!--                                                    <option value="not_equal_age">Nicht gleich (Datum)</option>-->
<!--                                                    <option value="older">Älter (Datum)</option>-->
<!--                                                    <option value="older_or_equal">Älter oder gleich (Datum)</option>-->
<!--                                                    <option value="younger">Jünger (Datum)</option>-->
<!--                                                    <option value="younger_or_equal">Jünger oder gleich (Datum)</option>-->
<!--                                                </select>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="row" v-if="relationExistOperator">-->
<!--                                        <label for="validator-min-date" class="col-sm-5 col-form-label">Wert</label>-->
<!--                                        <div class="col-sm-7">-->
<!--                                            <AutocompleteSelector-->
<!--                                                :items="relationExistsValue ? [relationExistsValue] : []"-->
<!--                                                :add-only-from-autocomplete="true"-->
<!--                                                :max-items="1"-->
<!--                                                :editable="ui.editable"-->
<!--                                                :syntax-include="['reference.metas', 'reference.relation_data', 'reference.outputs', 'reference.status']"-->
<!--                                                @items-changed="onRelationExistsValueChange"-->
<!--                                            />-->
<!--                                        </div>-->
<!--                                    </div>-->
                                </template>
                                <hr>
                                <div class="form-group mb-2">
                                    <label class="mb-0">Wertevergleich</label>
                                    <small class="text-muted d-block">Wert des Datenfeldes bei Aktionsausführung mit anderen Wert vergleichen.</small>
                                </div>
                                <div class="mb-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="compare-value"
                                               id="compare-value-deactivated" value="compare_value_deactivated" @click="removeCompareValue"
                                               :checked="!ruleIsActive('compare_value')" :disabled="!ui.editable">
                                        <label class="form-check-label" for="compare-value-deactivated">Deaktiviert</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="compare-value"
                                               id="compare-value-activated" value="compdare_value_activated" @click="setCompareValue"
                                               :checked="ruleIsActive('compare_value')" :disabled="!ui.editable">
                                        <label class="form-check-label" for="compare-value-activated">Aktiviert</label>
                                    </div>
                                </div>
                                <template v-if="ruleIsActive('compare_value')">
                                    <div class="row">
                                        <label for="validator-numeric-step" class="col-sm-5 col-form-label">Operator</label>
                                        <div class="col-sm-7">
                                            <div class="form-group mb-0">
                                                <select class="form-control form-control-sm" id="validator-numeric-step"
                                                        v-model="compareValueOperator" :disabled="!ui.editable"
                                                        @change="setCompareValue">
                                                    <option value="==">Gleich</option>
                                                    <option value="===">Gleich (strikt)</option>
                                                    <option value="!=">Nicht gleich</option>
                                                    <option value=">">Größer als</option>
                                                    <option value=">=">Größer oder gleich</option>
                                                    <option value="<">Kleiner als</option>
                                                    <option value="<=">Kleiner oder gleich</option>
                                                    <option value="equal_age">Gleich (Datum)</option>
                                                    <option value="not_equal_age">Nicht gleich (Datum)</option>
                                                    <option value="older">Älter (Datum)</option>
                                                    <option value="older_or_equal">Älter oder gleich (Datum)</option>
                                                    <option value="younger">Jünger (Datum)</option>
                                                    <option value="younger_or_equal">Jünger oder gleich (Datum)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="validator-min-date" class="col-sm-5 col-form-label">Wert</label>
                                        <div class="col-sm-7">
                                            <AutocompleteSelector
                                                :items="compareValueValue ? [compareValueValue] : []"
                                                :add-only-from-autocomplete="false"
                                                :max-items="1"
                                                :editable="ui.editable"
                                                :syntax-include="['process.outputs', 'process.meta', 'process.status', 'date', 'variables', 'auth', 'reference.metas', 'reference.outputs', 'reference.relation_data', 'reference.status']"
                                                @items-changed="onCompareValueValueChange"
                                            />
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template v-if="ui.modal.data.method === 'StoreActionTypeOutput'">
                                <hr/>
                                <div class="form-group mb-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="include_in_process_data"
                                               :checked="data.include_in_process_data"
                                               @click="data.include_in_process_data = !data.include_in_process_data">
                                        <label class="custom-control-label" for="include_in_process_data">
                                            In Prozess-Daten aufnehmen?</label>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="include_in_input_data"
                                               :checked="data.include_in_input_data"
                                               @click="data.include_in_input_data = !data.include_in_input_data">
                                        <label class="custom-control-label" for="include_in_input_data">
                                            In Vorlade-Daten aufnehmen?</label>
                                    </div>
                                </div>
                                <div class="form-group mb-2 ml-4" v-if="data.include_in_input_data && data.include_in_process_data">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="load_process_data_field"
                                               :checked="data.load_process_data_field"
                                               @click="data.load_process_data_field = !data.load_process_data_field">
                                        <label class="custom-control-label" for="load_process_data_field">
                                            Lade Prozess-Datenfeld vor?</label>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="create_form_field"
                                               :checked="data.create_form_field"
                                               @click="data.create_form_field = !data.create_form_field">
                                        <label class="custom-control-label" for="create_form_field">Formular-Feld
                                            erzeugen?</label>
                                    </div>
                                </div>
                            </template>
                            <!-- Anzeigen durch welche Aktionen das Prozess-Datenfeld verändert wird -->
                            <template v-if="processContext && usedByActionTypes.length">
                                <hr/>
                                <div class="form-group mb-3">
                                    <label>Wird durch diese Aktionen aktualisiert:</label>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item py-2 px-1" v-for="actionType in usedByActionTypes">{{actionType.name}}</li>
                                    </ul>
                                </div>
                            </template>
                        </form>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="ui" v-on="$listeners" :on-save="onSave" :save-label="title"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";
import CodeEditor from "../config/CodeEditor";
import DropdownSelector from "../utils/DropdownSelector";
import AutocompleteSelector from "../utils/AutocompleteSelector";
import OptionBadgesWithText from "../utils/OptionBadgesWithText.vue";

// noinspection JSUnusedLocalSymbols
export default {
    components: {
        OptionBadgesWithText,
        AutocompleteSelector,
        DropdownSelector,
        CodeEditor,
        ModalHeader,
        ModalFooter
    },
    data() {
        return {
            showCopied: false,
            invalidCode: false,
            maxListeLength: 51,
            code: '',
            data: {
                name: '',
                description: '',
                default: '',
                validation: ['nullable'],
                include_in_process_data: false,
                include_in_input_data: false,
                load_process_data_field: false,
                create_form_field: false,
                type: 'basic',
                type_options: {}
            },
            availableBasicRules: [
                'regex',
                'numeric',
                'email',
                'currency',
                'date_format',
                'time',
                'boolean',
                'model_pipe',
                'assoc_array',
                'file',
                'in',
                'min',
                'max',
                'step',
                'mimes',
                'before',
                'after',
            ],
            availableArrayRules: [
                'distinct_values'
            ],
            inArrayValue: '',
            regexValue: '/(.*?)/',
            minValue: '',
            maxValue: '',
            step: '1',
            dateFormat: 'd.m.Y',
            afterDate: '',
            beforeDate: '',
            fileExtensions: '',
            maxSize: '',
            compareValueOperator: '==',
            compareValueValue: '',
            relationExistRelationType: '',
            relationExistOperator: '',
            relationExistValue: '',
            createMode: true,
            processContext: false,
            actionContext: false
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'outputs',
            'definition',
            'action_types',
            'environments',
            'relation_types_with_single_process',
            'graphs_output_names',
            'graphs_status_types'
        ]),
        actionTypeId() {
            return this.ui.modal.data.actionTypeId;
        },
        actionType() {
            return this.action_types.find(ele => ele.id === this.actionTypeId);
        },
        title() {
            return this.createMode ? 'Erstellen' : 'Speichern';
        },
        stringifiedRules() {
            return JSON.stringify(this.data.validation);
        },
        relationExistsRelationType() {
            if (this.ruleIsActive('relation_exists')) {
                let rule = this.data.validation.find(ele => ele.startsWith('relation_exists'));
                let params = rule.substring('relation_exists:' . length);

                return params.split(',')[0] || '';
            }
            if (this.ruleIsActive('relation_not_exists')) {
                let rule = this.data.validation.find(ele => ele.startsWith('relation_not_exists'));
                let params = rule.substring('relation_not_exists:' . length);

                return params.split(',')[0] || '';
            }

            return '';
        },
        relationExistsValue() {
            if (this.ruleIsActive('relation_exists')) {
                let rule = this.data.validation.find(ele => ele.startsWith('relation_exists'));
                let params = rule.substring('relation_exists:' . length);
                let paramsArr = params.split(',');

                // Werteprüfung oder nur Verknüpfungstyp
                return paramsArr.length === 3 ? paramsArr[2] : '';
            }
            if (this.ruleIsActive('relation_not_exists')) {
                let rule = this.data.validation.find(ele => ele.startsWith('relation_not_exists'));
                let params = rule.substring('relation_not_exists:' . length);
                let paramsArr = params.split(',');

                // Werteprüfung oder nur Verknüpfungstyp
                return paramsArr.length === 3 ? paramsArr[2] : '';
            }

            return '';
        },
        uniqueNames() {
            let actionTypeOutputNames = this.action_types.reduce(function (carry, ele) {
                return [
                    ...carry,
                    ...ele.outputs.map(ele => ele.name)
                ];
            }, []);

            let outputNames = this.outputs.map(ele => ele.name);

            return [
                ...new Set([
                    ...actionTypeOutputNames,
                    ...outputNames
                ])
            ].sort((a, b) => a > b);
        },
        outputType: {
            get() {
                return this.data.type;
            },
            set(val) {
                this.removeBasicRules();

                if (val === 'object') {
                    this.data.type = 'object';
                    this.data.default = {};
                    this.data.type_options = {};

                    // "Objekt" Validierung
                    this.addRule('assoc_array');
                }
                else if (val === 'array') {
                    this.data.type = 'array';
                    this.data.default = [];
                    this.data.type_options = {
                        min: 0,
                        max: 50
                    };
                }
                else {
                    this.removeArrayRules();
                    this.data.type = 'basic';
                    this.data.default = '';
                }
            }
        },
        usedByActionTypes(){
            // Only in process context when updating a value.
            if(this.actionContext || this.createMode) {
                return []
            }

            return this.action_types.filter(ele => ele.outputs.map(output => output.name).includes(this.data.name)).sort((a, b) => a.name.toLowerCase() > b.name.toLowerCase() ? 1 : -1)

        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let data = {
                ...this.data,
                name: this.data.name.toLowerCase(),
                action_type_id: this.actionTypeId,
                // _empty_ wird hier als Platzhalter für einen leeren String genutzt damit Laravel diesen nicht zu null casted.
                default: this.data.default === '' ? '_empty_' : this.data.default
            };

            if ([
                'UpdateActionTypeOutput',
                'UpdateProcessTypeOutput'
            ].includes(this.ui.modal.data.method)) {
                delete data.include_in_process_data;
                delete data.include_in_input_data;
                delete data.load_process_data_field;
                delete data.create_form_field;
            }

            if (this.ui.modal.data.method === 'StoreProcesTypeOutput') {
                delete data.validation;
            }

            this.patchDefinition(this.ui.modal.data.method, data, false)
                .then(this.closeModal)
                .then(this.clearSyntaxValues)
                .catch(() => {
                });
        },
        toggleRequired() {
            this.data.validation = this.data.validation.includes('required') ? [
                'nullable',
                ...this.data.validation.filter(ele => ele !== 'required')
            ] : [
                'required',
                ...this.data.validation.filter(ele => ele !== 'nullable')
            ];
        },
        // Remove "basic" specific rules from validation.
        removeBasicRules() {
            let availableBasicRules = this.availableBasicRules;

            this.data.validation = this.data.validation.filter(function (rule) {
                let name = rule.split(':')[0] || rule;

                return !availableBasicRules.includes(name);
            });

        },
        // Remove JSON array specific rules from validation.
        removeArrayRules() {
            let availableArrayRules = this.availableArrayRules;

            this.data.validation = this.data.validation.filter(function (rule) {
                let name = rule.split(':')[0] || rule;

                return !availableArrayRules.includes(name);
            });

        },
        ruleIsActive(rule) {
            return this.data.validation.some(item => item.startsWith(rule));
        },
        setBasicRule(basicRule) {
            this.removeBasicRules();
            this.addRule(basicRule);
        },
        addRule(items) {
            let arr = typeof items === 'string' ? [items] : items;
            this.data.validation = [...this.data.validation, ...arr];
        },
        removeRule(item) {
            let rules = [...this.data.validation];

            this.data.validation = rules.filter(function (rule) {
                return !rule.startsWith(item);
            });
        },
        addInArrayRule(e) {
            if (e.target.value.trim() === '') {
                this.data.validation = this.data.validation.filter(ele => !ele.startsWith('in:'));
            }
            else {
                this.removeBasicRules();
                let items = this.data.validation.filter(ele => !ele.startsWith('in:'));
                this.data.validation = [
                    ...items,
                    'in:' + e.target.value.split(',').map(ele => ele.trim()).join(',')
                ];
            }
        },
        onCodeChange(code) {
            this.code = code.trim();
            this.invalidCode = false;
            let obj = false;

            try {
                obj = JSON.parse(this.code);
            } catch (e) {
                this.invalidCode = true;
                return;
            }

            this.data.default = obj;
        },
        onMinChange(val) {
            if (val > this.data.type_options.max || 0) {
                this.data.type_options.max = +val;
            }

            this.data = {
                ...this.data,
                type_options: {
                    ...this.data.type_options,
                    min: +val
                }
            };
        },
        onMaxChange(val) {
            if (val < this.data.type_options.min || 0) {
                this.data.type_options.min = +val;
            }

            this.data = {
                ...this.data,
                type_options: {
                    ...this.data.type_options,
                    max: +val
                }
            };
        },
        onSelectDropdown(item) {
            this.data = {
                ...this.data,
                default: (this.data.default || '') + item.value_with_label
            };
        },
        copyText(item) {
            let that = this;

            this.copy(item.value_with_label).then(function () {
                that.showCopied = true;
            }).catch(() => console.log('error copying'));

            setTimeout(() => {
                this.showCopied = false;
            }, 1500);
        },
        copy(textToCopy) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(textToCopy);
            }
            else {
                let textArea = document.createElement("textarea");
                textArea.value = textToCopy;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.getElementById('data-modal').appendChild(textArea);
                textArea.focus();
                textArea.select();

                return new Promise((res, rej) => {
                    document.execCommand("copy") ? res() : rej();
                    textArea.remove();
                });
            }
        },
        setInRule() {
            let items = this.inArrayValue.trim()
                .split(',')
                .map(ele => ele.trim())
                .filter(ele => ele !== '')
                .join(',');

            this.removeBasicRules();
            this.addRule('in:' + items);
        },
        setNumeric() {
            let rules = ['numeric', 'step:' + this.step];

            if (this.minValue.trim()) {
                rules.push('min:' + this.minValue.trim());
            }
            if (this.maxValue.trim()) {
                rules.push('max:' + this.maxValue.trim());
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        setCompareValue() {
            this.removeRule('compare_value');

            let rule = 'compare_value:';

            if (this.compareValueOperator) {
                rule += this.compareValueOperator;
            }
            if (this.compareValueValue) {
                rule += ',' + this.compareValueValue;
            }

            this.addRule(rule);
        },
        setDistinctValues() {
            this.addRule('distinct_values');
        },
        setRelationRule(rule) {
            this.removeRule('relation_exists');
            this.removeRule('relation_not_exists');

            if (this.relationExistRelationType) {
                rule += ':' + this.relationExistRelationType;
            }
            if (this.relationExistRelationType && this.relationExistOperator) {
                rule += ',' + this.relationExistOperator;
            }
            if (this.relationExistRelationType && this.relationExistOperator && this.relationExistValue) {
                rule += ',' + this.relationExistValue;
            }

            this.addRule(rule);
        },
        setRelationNotExists(rule) {
            this.removeRule('relation_exists');
            this.removeRule('relation_not_exists');

            if (this.relationExistRelationType) {
                rule += ':' + this.relationExistRelationType;
            }
            if (this.relationExistRelationType && this.relationExistOperator) {
                rule += ',' + this.relationExistOperator;
            }
            if (this.relationExistRelationType && this.relationExistOperator && this.relationExistValue) {
                rule += ',' + this.relationExistValue;
            }

            this.addRule(rule);
        },
        removeRelationExists() {
            this.removeRule('relation_exists');
            this.removeRule('relation_not_exists');
        },
        removeCompareValue() {
            this.removeRule('compare_value');
        },
        removeDistinctValues() {
            this.removeRule('distinct_values');
        },
        setDateFormat() {
            let rules = ['date_format:' + this.dateFormat];

            if (this.afterDate.trim()) {
                rules.push('after_or_equal:' + this.afterDate.trim());
            }
            if (this.beforeDate.trim()) {
                rules.push('before_or_equal:' + this.beforeDate.trim());
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        setFile() {
            let rules = ['file'];

            if (this.fileExtensions) {
                rules.push('mimes:' + this.fileExtensions.trim().split(',').map(ele => ele.trim()).join(','));
            }
            if (this.maxSize.trim()) {
                rules.push('max:' + this.maxSize.trim());
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        onRegexInput(e) {
            let val = e.target.value.trim();

            if (!val.startsWith('/')) {
                val = '/' + val;
            }

            if (!val.endsWith('/')) {
                val = val + '/';
            }

            this.removeBasicRules();

            if (e.target.value.trim()) {
                this.addRule('regex:' + val.trim());
            }
            else {
                this.addRule('regex:/(.*?)/');
            }
        },
        onMinValueInput(e) {
            let rules = ['numeric'];
            let val = e.target.value.trim();

            if (val) {
                rules.push('min:' + val);
            }
            if (this.maxValue.trim()) {
                rules.push('max:' + this.maxValue.trim());
            }
            if (this.step) {
                rules.push('step:' + this.step);
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        onMaxValueInput(e) {
            let rules = ['numeric'];
            let val = e.target.value.trim();

            if (val) {
                rules.push('max:' + val);
            }
            if (this.minValue.trim()) {
                rules.push('min:' + this.minValue.trim());
            }
            if (this.step) {
                rules.push('step:' + this.step);
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        onStepChange(e) {
            let rules = ['numeric'];

            rules.push('step:' + e.target.value);

            if (this.minValue.trim()) {
                rules.push('min:' + this.minValue.trim());
            }
            if (this.maxValue.trim()) {
                rules.push('max:' + this.maxValue.trim());
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        onDateFormatChange(e) {
            let rules = ['date_format:' + e.target.value];

            if (this.afterDate.trim()) {
                rules.push('after_or_equal:' + this.afterDate.trim());
            }
            if (this.beforeDate.trim()) {
                rules.push('before_or_equal:' + this.beforeDate.trim());
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        onAfterDateChange(items) {
            let after = items.length ? items[0] : '';
            let rules = ['date_format:' + this.dateFormat];

            this.afterDate = after;

            if (after) {
                rules.push('after_or_equal:' + after);
            }
            if (this.beforeDate.trim()) {
                rules.push('before_or_equal:' + this.beforeDate.trim());
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        onRelationExistsRelationTypeChange(items) {
            this.relationExistRelationType = items.length ? items[0] : '';
            this.setRelationRule(this.ruleIsActive('relation_exists') ? 'relation_exists' : 'relation_not_exists');
        },
        onRelationExistsValueChange(items) {
            this.relationExistValue = items.length ? items[0] : '';
            this.setRelationRule(this.ruleIsActive('relation_exists') ? 'relation_exists' : 'relation_not_exists');
        },
        onCompareValueValueChange(items) {
            this.compareValueValue = items.length ? items[0] : '';
            this.setCompareValue();
        },
        onBeforeDateChange(items) {
            let before = items.length ? items[0] : '';
            let rules = ['date_format:' + this.dateFormat];

            this.beforeDate = before;

            if (before) {
                rules.push('before_or_equal:' + before);
            }
            if (this.afterDate.trim()) {
                rules.push('after_or_equal:' + this.afterDate.trim());
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        onFileExtensionsInput(e) {
            let rules = ['file'];
            let trimmed = e.target.value.trim().split(',').map(ele => ele.trim()).join(',');

            if (trimmed) {
                rules.push('mimes:' + trimmed);
            }
            if (this.maxSize.trim()) {
                rules.push('max:' + this.maxSize.trim());
            }

            this.removeBasicRules();
            this.addRule(rules);
        },
        onMaxSizeInput(e) {
            let rules = ['file'];
            let val = e.target.value.trim();

            if (val) {
                rules.push('max:' + val);
            }
            if (this.fileExtensions) {
                rules.push('mimes:' + this.fileExtensions.trim().split(',').map(ele => ele.trim()).join(','));
            }

            this.removeBasicRules();
            this.addRule(rules);
        }
    },
    watch: {
        data: {
            handler(newData) {
                if (this.ui.errorCode) {
                    this.clearError();
                }
                if (!newData.include_in_process_data || !newData.include_in_input_data){
                    this.data.load_process_data_field = false
                }
            },
            deep: true
        }
    },
    mounted() {
        if (this.ui.modal.data.output) {
            this.createMode = false;
            this.data = {
                ...this.data,
                ...this.ui.modal.data.output,
                old_name:this.ui.modal.data.output.name
            };

            let validationRules = this.ui.modal.data.output.validation;

            let inArray = validationRules.find(ele => ele.startsWith('in:'));
            let regex = validationRules.find(ele => ele.startsWith('regex:'));
            let min = validationRules.find(ele => ele.startsWith('min:'));
            let max = validationRules.find(ele => ele.startsWith('max:'));
            let step = validationRules.find(ele => ele.startsWith('step:'));
            let date = validationRules.find(ele => ele.startsWith('date_format:'));
            let before = validationRules.find(ele => ele.startsWith('before_or_equal:'));
            let after = validationRules.find(ele => ele.startsWith('after_or_equal:'));
            let mimes = validationRules.find(ele => ele.startsWith('mimes:'));
            let relationRule = validationRules.find(ele => ele.startsWith('relation_exists:')) || validationRules.find(ele => ele.startsWith('relation_not_exists:'));
            let compareValue = validationRules.find(ele => ele.startsWith('compare_value:'));

            if(relationRule) {
                let relationRuleString = relationRule.startsWith('relation_exists:') ? relationRule.substring('relation_exists:' . length) : relationRule.substring('relation_not_exists:' . length);
                let relationRuleParts = relationRuleString.split(',');

                this.relationExistRelationType = relationRuleParts[0];
                this.relationExistOperator = relationRuleParts[1] || '';
                this.relationExistValue = relationRuleParts[2] || '';
            }

            if(compareValue) {
                let compareValueString = compareValue.substring('compare_value:'.length)
                let compareValueParts = compareValueString.split(',');

                this.compareValueOperator = compareValueParts[0] || '==';
                this.compareValueValue = compareValueParts[1] || '';

            }

            this.inArrayValue = inArray ? inArray.substring('in:'.length) : '';
            this.regexValue = regex ? regex.substring('regex:'.length) : '/(.*?)/';
            this.minValue = min ? min.split(':')[1] : '';
            this.maxValue = max ? max.split(':')[1] : '';
            this.maxSize = max ? max.split(':')[1] : '';
            this.step = step ? step.split(':')[1] : '1';
            this.dateFormat = date ? date.substring('date_format:'.length) : 'd.m.Y';
            this.beforeDate = before ? before.split(':')[1] : '';
            this.afterDate = after ? after.split(':')[1] : '';
            this.fileExtensions = mimes ? mimes.substring('mimes:'.length) : '';
        }

        if (['UpdateActionTypeOutput', 'StoreActionTypeOutput'].includes(this.ui.modal.data.method)) {
            this.actionContext = true;
        }
        else {
            this.processContext = true;
        }
    }
};
</script>
