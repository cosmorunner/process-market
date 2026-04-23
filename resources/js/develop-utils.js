/**
 * Event functions for any events in the control panel and in the graph and CRUD HTTP methods
 * @param actionTypeId
 */
import {contextMenuOptions, graphStyle} from "./graph-config";
import cytoscape from 'cytoscape';
import contextMenus from 'cytoscape-context-menus';
import cytoscapeDomNode from 'cytoscape-dom-node';
import $ from 'jquery';
import sharedUtils from './shared-utils';

export default {

    ...sharedUtils,

    /**
     * Initialize the Cytoscape instance.
     * @param {object} processVersion
     */
    initCy(processVersion) {
        try {
            // Published versions cannot be edited
            cytoscape.use(contextMenus, $);
            //cytoscape.use(gridGuide);
            cytoscape.use(cytoscapeDomNode);
        } catch (e) {
            // Ignore
        }

        // Convert HTML-String in Node
        processVersion.calculated.forEach((element) => {
            if (element.data.dom && typeof element.data.dom === 'string') {
                let template = document.createElement('template');
                template.innerHTML = element.data.dom.trim();
                element.data.dom = template.content.firstChild;
            }
        });

        window.cy = cytoscape({
            container: document.getElementById('cyGraph'),
            elements: processVersion.calculated,
            style: graphStyle,
            layout: {
                name: 'preset'
            },
            wheelSensitivity: 0.20
        });

        cy.domNode();

        cy.app = this;

        // Determine the center of the graph.
        this.layoutCenterPosition = this.calculateLayoutCenterPosition(processVersion.calculated);

        //cy.gridGuide(gridOptions);

        cy.maxZoom(1.5);
        cy.minZoom(0.40);
        cy.fit(null, cy.width() / 20);

        window.cyContextMenus = cy.contextMenus(contextMenuOptions);

        cy.layout({
            name: 'preset'
        }).run();

        cy.on('mouseover', 'node', function () {
            document.body.style.cursor = 'grab';
        });

        cy.on('mouseout', 'node', function () {
            document.body.style.cursor = 'default'; // reset cursor
        });

        cy.on('grab', 'node', function () {
            document.body.style.cursor = 'grabbing';
        });

        cy.on('free', 'node', function () {
            document.body.style.cursor = 'grab'; // Back to 'grab' after release
        });

        this.listenForDropdown();
    },

    /**
     * Determines the center point of the graph. Is used to save the graph when the position changes.
     * @param elements
     */
    calculateLayoutCenterPosition(elements) {
        let addedPosition = elements.map(ele => ele.position || {
            x: 0,
            y: 0
        }).reduce(function (carry, position) {
            return {
                ...carry,
                x: carry.x + position.x,
                y: carry.y + position.y
            };
        }, {
            x: 0,
            y: 0
        });

        addedPosition.x = addedPosition.x / elements.length;
        addedPosition.y = addedPosition.y / elements.length;

        return addedPosition;
    },

    /**
     * Saves the graph if there has been a position change.
     */
    saveOnPositionChange(target) {
        let newLayoutCenter = this.calculateLayoutCenterPosition(cy.json(true).elements);

        // With the HTML graph elements implementation, the graph is saved the first time an element in selected after
        // page reload. For unknown reason, the position of the HTML elements in not equal to the underlying Cytoscape
        // Nodes, which causes a marginal difference in the layout center position.
        let overToleranceX = !this.layoutCenterPosition.x || Math.abs(this.layoutCenterPosition.x - newLayoutCenter.x) > 0.1;
        let overToleranceY = !this.layoutCenterPosition.y || Math.abs(this.layoutCenterPosition.y - newLayoutCenter.y) > 0.1;

        if (overToleranceX || overToleranceY) {
            this.layoutCenterPosition = newLayoutCenter;

            const nodes = this.depthFirstSearchCyElements(target, cy.elements());
            const targets = cy.elements().filter(e => nodes.includes(e.id())).map(e => ({
                id: e.id(),
                position: e.position()
            }));

            this.save(targets);
        }
    },

    depthFirstSearchCyElements(target, nodes, result = []) {
        const id = typeof target.id === 'function' ? target.id() : target.id;
        result.push(id);

        const children = nodes.filter(e => e.data('parent') === id);

        children.forEach(c => {
            this.depthFirstSearchCyElements(c, nodes, result);
        });

        return result;
    },

    /**
     * Updates the Cytoscape elements
     * @param cyJsonElements
     * @param deleteHtml
     */
    setCyElements(cyJsonElements, deleteHtml = true) {
        if (deleteHtml) {
            // Delete existing HTML for nodes to avoid duplicates
            const initials = document.getElementsByClassName('cytoscape-initial');
            const states = document.getElementsByClassName('cytoscape-state');
            const actions = document.getElementsByClassName('cytoscape-action');

            const elements = [
                ...initials,
                ...states,
                ...actions
            ];

            // To avoid flickering or visible changes
            requestAnimationFrame(() => {
                elements.forEach((element) => {
                    element.remove();
                });
            });
        }

        cyJsonElements.forEach((element) => {
            if (element.data.dom && typeof element.data.dom === 'string') {
                let template = document.createElement('template');
                template.innerHTML = element.data.dom.trim();
                element.data.dom = template.content.firstChild;
            }
        });

        cy.json({
            elements: cyJsonElements
        });

        this.listenForDropdown();
    },

    onClickEle(e) {
        //      console.log(e.target);
        //        console.log(e.target.id());
        //        console.log(e.target.data('id'));
        //        console.log(e.target.data());
        //        console.log(e.target.classes());
        //        console.log(e.target.position());
        //        console.log(cy.pan());
    },

    /**
     * Delete an action type. Either in the Control Panel under the “...” icon or via the
     * context menu of an action type node.
     * @param id
     */
    onDeleteActionType(id) {
        this.clearElementDetails();
        this.patchDefinition('DeleteActionType', {id}).catch(() => {
        });
    },

    /**
     * Delete a process role.
     * @param id
     */
    onDeleteRole(id) {
        this.clearElementDetails();
        this.patchDefinition('DeleteRole', {id}).catch(() => {
        });
    },

    /**
     * Copy an action type.
     * @param id
     */
    onCopyActionType(id) {
        this.patchDefinition('CopyActionType', {id}).catch(() => {
        });
    },

    /**
     * Copy a role.
     * @param id
     */
    onCopyRole(id) {
        this.patchDefinition('CopyRole', {id}).catch(() => {
        });
    },

    /**
     * Undo a command.
     */
    undo() {
        this.clearError();
        this.startLoading();

        let that = this;
        let url = this.ui.urls.undo;
        let oldDefinition = this.definition;

        return new Promise((resolve, reject) => {
            axios.patch(url).then(function (response) {
                that.stopLoading();
                that.setDefinition(response.data.definition);
                that.setCyElements(response.data.elements, that.compareDefinitions(oldDefinition, response.data.definition));
                that.setEnabledUndo(response.data.undo);
                that.setEnabledRedo(response.data.redo);

                that.temporaryFlashMessage({
                    type: 'info',
                    message: 'Rückgängig gemacht'
                });

                jQuery('[data-toggle="tooltip"]').tooltip();

                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },

    /**
     * Redo a command
     */
    redo() {
        this.clearError();
        this.startLoading();

        let that = this;
        let url = this.ui.urls.redo;
        let oldDefinition = this.definition;

        return new Promise((resolve, reject) => {
            axios.patch(url).then(function (response) {
                that.stopLoading();
                that.setDefinition(response.data.definition);
                that.setCyElements(response.data.elements, that.compareDefinitions(oldDefinition, response.data.definition));
                that.setEnabledUndo(response.data.undo);
                that.setEnabledRedo(response.data.redo);

                // Only set if not set yet
                that.temporaryFlashMessage({
                    type: 'info',
                    message: 'Wiederhergestellt'
                });

                jQuery('[data-toggle="tooltip"]').tooltip();

                resolve(response);
            }).catch(function (error) {
                that.setError(error);
                reject(error);
            });
        });
    },

    /**
     * Recursively compares the old and the new definition to determine whether HTML nodes need to be deleted
     * @param oldDefinition
     * @param newDefinition
     * @returns {boolean}
     */
    compareDefinitions(oldDefinition, newDefinition) {
        // The keys that should not trigger node deletion on undo/redo when changed
        const keysToCheck = [
            'action_types',
            'default_role_id',
            'image',
            'outputs',
            'public_role_id',
            'reference_pattern',
        ];

        for (const key in oldDefinition) {
            if (keysToCheck.includes(key)) {
                const oldValue = oldDefinition[key];
                const newValue = newDefinition[key];
                // The name property must be treated differently when it comes to actions, process data, input data and
                // action data
                if ((key === 'action_types' || key === 'outputs') && Array.isArray(oldValue) && Array.isArray(newValue)) {
                    if (oldValue.length !== newValue.length) {
                        return key === 'action_types';
                    }

                    for (let i = 0; i < oldValue.length; i++) {
                        const oldActionTypeOrOutput = oldValue[i];
                        const newActionTypeOrOutput = newValue[i];

                        if (oldActionTypeOrOutput.name !== newActionTypeOrOutput.name) {
                            return key === 'action_types';
                        }

                        // Check 'inputs' and 'outputs' inside each action_type
                        if (oldActionTypeOrOutput.inputs && newActionTypeOrOutput.inputs) {
                            if (!this.compareInnerNames(oldActionTypeOrOutput.inputs, newActionTypeOrOutput.inputs)) {
                                return false;
                            }
                        }

                        if (oldActionTypeOrOutput.outputs && newActionTypeOrOutput.outputs) {
                            if (!this.compareInnerNames(oldActionTypeOrOutput.outputs, newActionTypeOrOutput.outputs)) {
                                return false;
                            }
                        }
                    }
                }
                else if (Array.isArray(oldValue) && Array.isArray(oldValue)) {
                    if (oldValue.length !== newValue.length) {
                        return false;
                    }

                    for (let i = 0; i < oldValue.length; i++) {
                        if (!this.compareDefinitions(oldValue[i], newValue[i])) {
                            return false;
                        }
                    }
                }
                else if (typeof oldValue === 'object' && typeof newValue === 'object') {
                    const oldKeys = Object.keys(oldValue);
                    const newKeys = Object.keys(newValue);

                    if (oldKeys.length !== newKeys.length) {
                        return false;
                    }

                    for (const key of oldValue) {
                        if (!this.compareDefinitions(oldValue[key], newValue[key])) {
                            return false;
                        }
                    }
                }
                else {
                    if (oldValue !== newValue) {
                        return false;
                    }
                }
            }
        }

        return true;
    },

    /**
     * Compares input & output names
     * @param oldItems
     * @param newItems
     */
    compareInnerNames(oldItems, newItems) {
        if (Array.isArray(oldItems) && Array.isArray(newItems)) {
            if (oldItems.length !== newItems.length) {
                return false;
            }

            for (let i = 0; i < oldItems.length; i++) {
                const oldItem = oldItems[i];
                const newItem = newItems[i];

                if (oldItem.name !== newItem.name) {
                    return false;
                }
            }
        }

        return true;
    },

    defaultSmartStatusOptions(identifier) {
        return {
            relation_type: {
                relation_type: '',
                relation_type_name: '',
                conditions: []
            },
            custom_logic: {
                template: ''
            },

        }[identifier] || {};
    },

    /**
     * Deleting an action type output
     * @param actionTypeId
     * @param outputName
     */
    onDeleteOutput(actionTypeId, outputName) {
        let method = actionTypeId ? 'DeleteActionTypeOutput' : 'DeleteProcessTypeOutput';
        let data = {name: outputName};

        if (method === 'DeleteActionTypeOutput') {
            data.action_type_id = actionTypeId;
        }

        this.patchDefinition(method, data, false).then(this.clearSyntaxValues).catch(() => {
        });
    },

    /**
     * Delete an action rule.
     * @param actionTypeId
     * @param statusTypeId
     */
    onDeleteActionRule(actionTypeId, statusTypeId) {
        let data = {
            action_type_id: actionTypeId,
            status_type_id: statusTypeId,
        };

        this.patchDefinition('DeleteActionRule', data).catch(() => {
        });
        this.showActionTypeDetail(actionTypeId);
    },

    /**
     * Delete a status rule.
     * @param actionTypeId
     * @param statusTypeId
     */
    onDeleteStatusRule(actionTypeId, statusTypeId) {
        let data = {
            action_type_id: actionTypeId,
            status_type_id: statusTypeId,
        };

        this.patchDefinition('DeleteStatusRule', data).catch(() => {
        });
        this.showActionTypeDetail(actionTypeId);
    },

    /**
     * Delete a status type. Either in the Control Panel under the “...” icon or via the
     * context menu of a status node.
     * @param id
     */
    onDeleteStatusType(id) {
        this.clearElementDetails();
        this.patchDefinition('DeleteStatusType', {id}).catch(() => {
        });
    },

    /**
     * Delete a status. Either in the Control Panel under the “...” icon or via the
     * context menu of a state node.
     * @param statusTypeId
     * @param stateId
     */
    onDeleteState(statusTypeId, stateId) {
        let data = {
            status_type_id: statusTypeId,
            state_id: stateId
        };

        this.patchDefinition('DeleteState', data).then(() => this.showElementDetails({
            'type': 'status',
            'model': statusTypeId
        })).catch(() => {
        });
    },

    /**
     * Saves the current state of the graph when e.g. moving an element.
     * The existing graph is overwritten (PATCH).
     */
    save(targets) {
        let that = this;
        that.startLoading();
        that.clearError();

        // Highlight, Locked und Selection entfernen
        cy.elements().unselect();
        cy.elements().unlock();
        cy.$('.highlighted').removeClass('highlighted');

        axios.patch('/api/process-versions/' + this.ui.processVersionId, ({targets: targets})).then(function (response) {
            if (response.data.elements) {
                that.setCyElements(response.data.elements, false);
            }
            that.stopLoading();
        }).catch(function (error) {
            that.stopLoading();
            that.setError(error);
        });
    },

    /**
     * Saves the demo data for the simulation of a graph.
     */
    saveDemoData(data) {
        let that = this;

        return new Promise((resolve, reject) => {
            axios.patch('/api/process-versions/' + that.ui.processVersionId + '/demo-data', {
                demo_data: data,
            }).then(function () {
                that.setDemoData(data);
                resolve();
            }).catch(function (error) {
                that.setError(error);
                reject();
            });
        });
    },

    /**
     * When clicking on any Node or Edge
     * @param event
     */
    handleOnTapend(event) {
        cy.$('.highlighted').removeClass('highlighted');

        // Background
        if (event.target === cy) {
            this.onClickBackground(event.target);
        }
        // Graph element
        else {
            this.onClickEle(event);
            this.onClickElement(event.target);
            this.saveOnPositionChange(event.target.data());
        }
    },

    /**
     * Click/tap on the background to clear the detailed display.
     */
    onClickBackground() {
        this.clearElementDetails();
        this.updateNavigation({
            nav: !this.ui.navigation.nav ? 'actionTypes' : this.ui.navigation.nav,
            sub: null
        });
    },

    /**
     * Click on a graph element. See handleOnTapend()
     * @param element
     * @property data.model_id
     * @property data.target_model_id
     * @property data.source_model_id
     */
    onClickElement(element) {
        let data = element.data();

        switch (data.type) {
            case 'action':
            case 'liberal-action':
                this.showActionTypeDetail(data.model_id);
                break;
            case 'status':
                this.showStatusTypeDetail(data.model_id);
                break;
            case 'state':
                this.showStateDetail(data.model_id);
                break;
            case 'action-rule-edge':
                this.showActionRuleDetail(data.model_id);
                break;
            case 'status-rule-edge':
                this.showStatusRuleDetail(data.model_id);
                break;
            case 'initial-state-edge':
                // Model-Id is the Id of the state.
                this.showInitialStateDetail(data.target_model_id);
                break;
            case 'start':
                this.showStartDetail();
                break;
            case 'compound':
                return;
        }
    },

    /**
     * Display of an action type in the detailed display.
     * @param actionTypeId
     */
    showActionTypeDetail(actionTypeId) {
        this.updateNavigation({
            nav: 'actionTypes',
            details: {
                type: 'action',
                model: actionTypeId
            }
        });

        cy.elements().unselect();
        cy.$('.highlighted').removeClass('highlighted');
        cy.nodes('[model_id="' + actionTypeId + '"][type="action"]').addClass('highlighted');
        cy.nodes('[model_id="' + actionTypeId + '"][type="liberal-action"]').addClass('highlighted');
    },
    /**
     * Display of a status type in the detailed display
     * @param statusTypeId
     */
    showStatusTypeDetail(statusTypeId) {
        this.updateNavigation({
            nav: 'statusTypes',
            details: {
                type: 'status',
                model: statusTypeId
            }
        });

        cy.elements().unselect();
        cy.$('.highlighted').removeClass('highlighted');
        cy.nodes('[model_id="' + statusTypeId + '"][type="status"]').select();
    },
    /**
     * Display of a status in the detailed display
     * @param stateId
     */
    showStateDetail(stateId) {
        this.updateNavigation({
            nav: 'statusTypes',
            details: {
                type: 'state',
                model: stateId
            }
        });

        cy.elements().unselect();
        cy.$('.highlighted').removeClass('highlighted');
        cy.nodes('[model_id="' + stateId + '"][type="state"]').select();
    },
    /**
     * Display of an action rule in the detailed display
     * @param actionRuleId
     */
    showActionRuleDetail(actionRuleId) {
        this.updateNavigation({
            nav: 'actionTypes',
            details: {
                type: 'action-rule-edge',
                model: actionRuleId
            }
        });
    },
    /**
     * Anzeige einer Aktionsregel in der Detailanzeige
     * @param roleId
     */
    showRoleDetail(roleId) {
        this.updateNavigation({
            nav: 'roles',
            details: {
                type: 'role',
                model: roleId
            }
        });
    },
    /**
     * Anzeige eines Statusregel in der Detailanzeige
     * @param statusRuleId
     */
    showStatusRuleDetail(statusRuleId) {
        this.updateNavigation({
            nav: 'actionTypes',
            details: {
                type: 'status-rule-edge',
                model: statusRuleId
            }
        });
    },

    /**
     * Display of an initial status in the detailed display
     * @param stateId
     */
    showInitialStateDetail(stateId) {
        this.updateNavigation({
            nav: 'statusTypes',
            details: {
                type: 'initial-state-edge',
                model: stateId
            }
        });
    },

    /**
     * Display of an initial status in the detailed display
     */
    showStartDetail() {
        this.updateNavigation({
            nav: 'actionTypes',
            details: {
                type: 'start',
                model: null
            }
        });
    },

    /**
     * Clears the detail view by going back to a panel overview.
     */
    clearElementDetailView() {
        cy.elements().unselect();
        cy.$('.highlighted').removeClass('highlighted');

        this.clearElementDetails();
    },

    /**
     * Checks whether the process type has an initial action. If yes and this action type outputs, the modal is opened.
     * @param actionData
     * @param {object} options Simulation options, such as role or simulation environment.
     */
    handleStartSim(actionData = {}, options = {}) {
        let environment = this.environments.find(ele => ele.id === options.environment_id);

        // There is no environment, a simulation is simply created.
        if (!environment) {
            return this.startSim(actionData, options);
        }

        let actionTypeId = environment.initial_action_type_id || null;
        let actionType = this.definition.action_types.find(ele => ele.id === actionTypeId) || null;
        let actionTypeOutputs = actionType ? actionType.outputs : [];

        // There is an environment and an initial action
        if (actionType && actionTypeOutputs.length) {
            this.openModal({
                componentName: 'ExecuteActionModal',
                data: {
                    'actionTypeId': actionTypeId,
                    'role_id': options.role_id || null,
                    'environment_id': environment.id,
                    'organisation_id': options.organisation_id,
                    'user_email': options.user_email
                }
            });

            return;
        }

        // There is an environment but no initial action
        return this.startSim(actionData, options);
    },

    /**
     * Specify simulation options.
     */
    openSimulationOptions(organisationId = null) {
        this.openModal({
            componentName: 'SimulationOptionsModal',
            data: {
                'organisationId': organisationId
            }
        });
    },

    /**
     * Starts the simulation.
     * @param {object} actionData
     * @param {object} options Simulation options, such as role or simulation environment.
     */
    startSim(actionData = {}, options = {}) {
        let that = this;
        that.clearError();
        that.startingSimulation();

        options.role_id = options.role_id || this.definition.default_role_id || this.definition.public_role_id;

        return new Promise((resolve, reject) => {
            axios.post('/api/simulations/process-version/' + this.ui.processVersionId, {
                // Bei Initialaktion Outputwerte der Aktion mitgeben.
                action_data: actionData, ...options
            }).then(function (response) {
                that.showElementDetails({
                    'type': 'simulation',
                    'model': null
                });
                that.initSimulation(response.data);
                that.setViewMode('simulation');
                resolve();
            }).catch(function (error) {
                that.clearStarting();
                that.setError(error);
                reject();
            });
        });
    },

    /**
     * Loads the input values of an action.
     */
    fetchActionTypeInputs(actionTypeId, successCb, errorCb) {
        let that = this;

        axios.get('/api/simulations/' + this.simulation.id + '/actiontypes/' + actionTypeId, {}).then(function (response) {
            if (successCb) {
                successCb(response);
            }
        }).catch(function (error) {
            if (errorCb) {
                errorCb(error);
                return;
            }
            that.setError(error);
        });
    },

    /**
     * Loads a system list of the Allisa application.
     */
    fetchList(slug) {
        let that = this;

        return new Promise(resolve => {
            axios.get('/api/simulations/' + this.simulation.id + '/lists/' + slug, {})
                .then(resolve)
                .catch(error => that.setError(error));
        });
    },

    /**
     * Stops the running simulation.
     */
    stopSim() {
        let that = this;
        that.clearError();
        that.stoppingSimulation();

        axios.patch('/api/simulations/' + this.simulation.id).then(function (response) {
            // Check whether a simulation or a definition was returned. If, for example, in a new tag
            // another simulation has been started.
            if (response.data.allisa_id) {
                that.showElementDetails({
                    'type': 'simulation',
                    'model': null
                });
                that.initSimulation(response.data);
                that.setViewMode('simulation');
            }
            else {
                that.stopSimulation();
                that.setViewMode('viewing');
                that.clearElementDetails();
                that.setDefinition(response.data.definition);
                that.setCyElements(response.data.elements, false);
            }
        }).catch(function (error) {
            that.setError(error);
        });
    },

    /**
     * Adds the styling for the active actions and states and sets the event handlers for the active
     * actions.
     * @param mode Either simulation or editing. Graph styling, events and context menus are set accordingly.
     */
    setViewMode(mode) {
        this.removeActiveActionTypesAndStates();

        let that = this;

        // Only click and dragfree so that the context menu events are not removed.
        cy.removeListener('tapend');
        cy.nodes().unselect();

        switch (mode) {
            case 'viewing':
                document.getElementById('cyGraph').classList.remove('simulation');

                // In readonly mode, elements cannot be selected and the context menus do not exist.
                if (this.ui.editable) {
                    cy.elements().unselect();
                    cy.elements().selectify();
                    cy.elements().grabify();
                    cy.on('tapend', e => this.handleOnTapend(e));

                    // Show context menus
                    contextMenuOptions.menuItems.forEach(ele => cyContextMenus.showMenuItem(ele.id));
                }
                else {
                    cy.elements().unselect();
                    cy.elements().unselectify();
                    cy.elements().ungrabify();
                    cy.nodes().lock();

                    // Hide context menus
                    contextMenuOptions.menuItems.forEach(ele => cyContextMenus.hideMenuItem(ele.id));
                }

                break;
            case 'simulation':
                document.getElementById('cyGraph').classList.add('simulation');

                cy.elements().unselect();
                cy.elements().unselectify();
                cy.elements().ungrabify();
                cy.nodes().lock();

                cy.on('tapend', '.executable', function (e) {
                    let actionTypeId = e.target.data('model_id');
                    let actionType = cy.app.action_types.find(ele => ele.id === actionTypeId);

                    if (actionType) {
                        that.handleExecAction(actionType);
                    }
                });

                // Hide context menus
                contextMenuOptions.menuItems.forEach(ele => cyContextMenus.hideMenuItem(ele.id));

                // Mark active action types and states
                that.setActiveActionTypesAndStates();
        }

    },

    /**
     * Highlights the actions and active states that can be performed
     */
    setActiveActionTypesAndStates() {
        let that = this;

        Array.from(document.getElementsByClassName('cytoscape-node')).forEach(item => {
            if (that.active_action_type_ids.includes(item.id) && !that.inaccessible_action_type_ids.includes(item.id)) {
                item.classList.add('executable');
            }
            if (that.inaccessible_action_type_ids.includes(item.id)) {
                item.classList.add('inaccessible');
            }
            if (item.classList.contains('cytoscape-action') && !that.active_action_type_ids.includes(item.id) && !that.inaccessible_action_type_ids.includes(item.id)) {
                item.classList.add('inactive');
            }
            if (that.active_state_ids.includes(item.id)) {
                item.classList.add('active-state');
            }
        });

    },

    /**
     * Removes the styling for the active actions and states and removes the event handlers for the active
     * actions.
     */
    removeActiveActionTypesAndStates() {
        Array.from(document.getElementsByClassName('cytoscape-node')).forEach(item => {
            item.classList.remove('executable');
            item.classList.remove('active-state');
            item.classList.remove('inaccessible');
            item.classList.remove('inactive');
        });
    },

    /**
     * Checks whether the action requires input data from the ExecuteActionModal.
     * @param {object} actionType
     */
    handleExecAction(actionType) {
        if (actionType.outputs.length) {
            this.openModal({
                componentName: 'ExecuteActionModal',
                data: {
                    'actionTypeId': actionType.id
                }
            });
            return;
        }

        this.execAction(actionType)
            .then(response => cy.app.updateSimulation(response.data))
            .then(() => cy.app.setViewMode('simulation'))
            .catch(() => {
            })
    },

    /**
     * Execute action in the simulation
     * @param {object} actionType
     * @param {object} outputs
     */
    execAction(actionType, outputs = {}) {
        let that = this;

        that.clearError();
        that.setExecutingActionTypeId(actionType.id);
        that.startLoading();

        return new Promise((resolve, reject) => {
            axios.post('/api/simulations/' + this.simulation.id + '/actions', {
                'action_type_id': actionType.id,
                'data': outputs
            }).then(function (res) {
                that.stopLoading();
                resolve(res);
            }).catch(function (error) {
                that.clearExecuting();
                that.setError(error);
                reject(error);
            });
        });
    },

    /**
     * Change simulation user.
     */
    switchUser(userId) {
        let that = this;

        that.clearError();

        return new Promise((resolve, reject) => {
            axios.patch('/api/simulations/' + this.simulation.id + '/switch-user', {
                'user_id': userId
            }).then(resolve).catch(function (error) {
                that.clearExecuting();
                that.setError(error);
                reject(error);
            });
        });
    },

    /**
     * Get simulation
     */
    fetchSimulation() {
        let that = this;

        return new Promise((resolve, reject) => {
            if (this.simulation.id) {
                axios.get('/api/simulations/' + this.simulation.id)
                    .then(function (response) {
                        resolve(response);
                        that.initSimulation(response.data);
                    }).catch(function (error) {
                    that.setError(error);
                });
            }
            else {
                reject();
            }
        });
    },

    /**
     * Change process type definition
     */
    patchDefinition(command, payload = {}, deleteHtml = true) {
        this.clearError();
        this.startLoading();

        let that = this;
        let position = payload.position;

        delete payload.position;

        return new Promise((resolve, reject) => {
            axios.patch(this.ui.urls.definition, {
                command: command,
                payload: payload,
                position: position,

                // Send the current viewport (graph area the user currently sees) to the server
                // to be used for node positioning.
                view_port: {
                    x: cy.pan().x / cy.zoom(),
                    y: cy.pan().y / cy.zoom()
                }
            }).then(function (response) {
                that.stopLoading();
                that.setDefinition(response.data.definition);
                that.setCyElements(response.data.elements, deleteHtml);
                that.setEnabledUndo(true);
                that.setEnabledRedo(false);

                jQuery('[data-toggle="tooltip"]').tooltip();

                resolve(response);
            }).catch(function (error) {
                that.setError({
                    ...error,
                    lastCommand: command
                });
                reject(error);
            });
        });
    },

    /**
     * Undo a simulation action.
     * @param actionId
     */
    revertTo(actionId) {
        this.undoAction(actionId);
        this.startLoading();
        this.clearError();

        axios.delete('/api/simulations/' + this.simulation.id + '/actions/' + actionId)
            .then((response) => {
                this.stopLoading();
                this.initSimulation(response.data);
                this.setViewMode('simulation');
                this.clearUndo();
            }).catch((error) => {
            this.clearUndo();
            this.setError(error);
        });
    },

    /**
     * Listen for opening the dropdown menus
     */
    listenForDropdown() {
        document.querySelectorAll('#contextMenu, #roleDropdown').forEach(function (dropdown) {
            const button = dropdown.querySelector('button');
            const grandparent = dropdown.parentElement.parentElement;
            const originalZIndex = window.getComputedStyle(grandparent).zIndex;

            button?.addEventListener('click', function () {
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                setTimeout(function () {
                    if (dropdownMenu && window.getComputedStyle(dropdownMenu).display !== 'none') {
                        grandparent.style.zIndex = '15';
                        // Reset zIndex for all other dropdown grandparents
                        document.querySelectorAll('#contextMenu, #roleDropdown').forEach(function (otherDropdown) {
                            const otherGrandparent = otherDropdown.parentElement.parentElement;

                            if (otherGrandparent !== grandparent) {
                                otherGrandparent.style.zIndex = originalZIndex;
                            }
                        });
                    }
                    else {
                        grandparent.style.zIndex = originalZIndex;
                    }
                }, 0);
            });

            // Listener for clicks on Dropdown elements
            dropdown.querySelectorAll('.dropdown-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    setTimeout(function () {
                        grandparent.style.zIndex = originalZIndex;
                    }, 0);
                });
            });

            // Listener for clicks outside the Dropdown
            document.addEventListener('click', function (event) {
                if (!dropdown.contains(event.target)) {
                    setTimeout(function () {
                        grandparent.style.zIndex = originalZIndex;
                    }, 0);
                }
            });
        });
    }
};
