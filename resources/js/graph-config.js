/**
 * Styling für den Cytoscape Graphen
 */
export const graphStyle = [
    {
        "selector": "core",
        "css": {}
    },
    {
        "selector": "edge",
        "css": {
            "width": "3px",
            "arrow-scale": 2,
            "target-arrow-fill": "fill",
        }
    },
    {
        "selector": "node",
        "css": {
            "cursor": "pointer"
        }
    },
    {
        "selector": ".compound",
        "css": {
            "text-transform": "uppercase",
            "font-weight": "bold",
            "padding": "20px",
            "border-opacity": 0.5
        }
    },
    {
        "selector": ".status",
        "css": {
            "shape": "round-rectangle",
            "font-weight": "bold",
            "padding": "30px",
            "padding-top": "35px",
            "background-color": "#f5f6f9",
            "border-color": "#2885d4",
            "border-width": "1px",
            "border-opacity": 0.6,
            "font-size": "1.5em",
            "text-wrap": "none",
            "text-margin-y": "35px",
            "text-halign": "middle",
            "text-valign": "top",
            "content": "data(name)",
            "text-max-width": "120px",
            "color": "#1f1f1f",
            "width": "20px",
            "height": "20px"
        }
    },
    {
        "selector": ".compound-level-one",
        "css": {
            "shape": "round-rectangle",
            "background-color": "#e9f4ff",
            "border-color": "#c3e1ff",
            "border-width": "1px",
            "border-opacity": 1,
        }
    },
    {
        "selector": ".compound-level-two",
        "css": {
            "shape": "round-rectangle",
            "background-color": "#d7e7fc",
            "border-color": "#c3e1ff",
            "border-width": "1px",
            "border-opacity": 1,
        }
    },
    {
        "selector": ".state",
        "css": {
            "shape": "round-rectangle",
            "border-width": "5px",
            "border-opacity": 0,
            "opacity": 0
        }
    },
    {
        "selector": ".statusrule-node",
        "css": {
            "shape": "round-rectangle",
            "background-color": "#3f8ed8",
            "border-color": "#ffffff",
            "border-width": "3px",
            "border-opacity": 1,
            "background-size": "cover",
            "background-image": "/img/statusrule-node-icon-white.svg",
            "width": "50px",
            "height": "50px",
            "text-valign": "bottom",
            "text-margin-y": "10px",
            "text-outline-width": 0,

        }
    },
    {
        "selector": ".action",
        "css": {
            "shape": "round-rectangle",
            "border-width": "5px",
            "border-opacity": 0,
            "opacity": 0
        }
    },
    {
        "selector": ".action.highlighted",
        "css": {
            "border-color": "#ffb200",
            "border-width": "5px",
            "border-opacity": 1,
            "opacity": 1
        }
    },
    {
        "selector": "edge[type='action-rule-edge'].highlighted",
        "css": {
            "line-color": "#e07c01",
            "target-arrow-color": "#ed8401"
        }
    },
    {
        "selector": "edge[type='status-rule-edge'].highlighted",
        "css": {
            "line-color": "#6f98cf",
            "target-arrow-color": "#6f98cf"
        }
    },
    {
        "selector": ".action:selected",
        "css": {
            "border-color": "#ffb200",
            "border-width": "5px",
            "border-opacity": 1,
            "opacity": 1
        }
    },
    {
        "selector": ".status:selected",
        "css": {
            "border-color": "#3f8ed8",
            "border-width": "3px",
            "border-opacity": 1,
        }
    },
    {
        "selector": ".state:selected",
        "css": {
            "border-color": "#ffb200",
            "border-width": "5px",
            "border-opacity": 1,
            "opacity": 1
        }
    },
    {
        "selector": "edge:selected",
        "css": {
            "line-color": "#ffe700",
            "target-arrow-color": "#ffe700"
        }
    },
    {
        "selector": "edge",
        "css": {
            "line-color": "#bcbcbc",
            "target-arrow-color": "#aeaeae",
            "target-arrow-shape": "triangle"
        }
    },
    {
        "selector": "edge.straight",
        "css": {
            "curve-style": "bezier",
        }
    },
    {
        "selector": "edge.cornered",
        "css": {
            "curve-style": "taxi",
        }
    },
    {
        "selector": "edge.action-rule-edge",
        "css": {
            "line-color": "#e9c393",
            "target-arrow-color": "#d29b5c",
        }
    },
    {
        "selector": "edge.status-rule-edge",
        "css": {
            "line-color": "#3f8ed8",
            "target-arrow-color": "#7aa0d2",
        }
    },
    {
        "selector": ".executable",
        "css": {
            "border-color": "#34b53a",
            "border-width": "5px",
            "border-opacity": 1,
            "opacity": 1
        }
    },
    {
        "selector": ".inaccessible",
        "css": {
            "border-color": "#FFC65E",
            "border-width": "5px",
            "border-opacity": 1,
            "opacity": 1
        }
    },
    {
        "selector": ".inactive",
        "css": {
            "border-color": "#E64A69",
            "border-width": "5px",
            "border-opacity": 1,
            "opacity": 1
        }
    },
    {
        "selector": ".active-state",
        "css": {
            "border-color": "#ffe700",
            "border-width": "5px",
            "border-opacity": 1,
            "opacity": 1
        }
    }
];

export const contextMenuOptions = {
    // Customize event to bring up the context menu
    // Possible options https://js.cytoscape.org/#events/user-input-device-events
    evtType: 'cxttap',
    menuItems: [
        {
            id: 'add-action', // ID of menu item
            content: 'Neue Aktion', // Display content of menu item
            tooltipText: 'Neue Aktion', // Tooltip text for menu item
            // image: {src : "remove.svg", width : 12, height : 12, x : 6, y : 4}, // menu icon
            // Filters the elements to have this menu item on cxttap
            // If the selector is not truthy no elements will have this menu item on cxttap
            selector: '',
            onClickFunction: function (e) { // The function to be executed on click
                e.cy.app.openModal({
                    componentName: 'ActionTypeModal',
                    data: {
                        position: e.position,
                        actionTypeId: null
                    }
                });
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: true
        },
        {
            id: 'edit-status-type',
            content: 'Bearbeiten',
            tooltipText: 'Bearbeiten',
            selector: '.status',
            onClickFunction: function (e) {
                e.cy.app.openModal({
                    componentName: 'StatusTypeModal',
                    data: {
                        position: e.position,
                        statusTypeId: e.target.data('model_id')
                    }
                });
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
        {
            id: 'add-action-rule',
            content: 'Neue Aktionsregel',
            tooltipText: 'Neue Aktionsregel',
            selector: '.status',
            onClickFunction: function (e) {
                e.cy.app.openModal({
                    componentName: 'ActionRuleModal',
                    data: {
                        statusTypeId: e.target.data('model_id'),
                        position: e.position
                    }
                });
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
        {
            id: 'add-status-rule',
            content: 'Neue Statusregel',
            tooltipText: 'Neue Statusregel',
            selector: '.status',
            onClickFunction: function (e) {
                e.cy.app.openModal({
                    componentName: 'StatusRuleModal',
                    data: {
                        statusTypeId: e.target.data('model_id'),
                        position: e.position
                    }
                });
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
        {
            id: 'swap-edge-style',
            content: 'Verbindungsstil ändern',
            tooltipText: 'Verbindungsstil ändern',
            selector: '.edge',
            onClickFunction: function (e) {
                if (e.target.hasClass("straight")) {
                    e.target.addClass("cornered");
                    e.target.removeClass("straight");
                }
                else {
                    e.target.addClass("straight");
                    e.target.removeClass("cornered");
                }
                cy.app.save([
                    {
                        id: e.target.id(),
                        classes: e.target.classes().join(' ')
                    }
                ]);
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
        {
            id: 'edit-action-rule-on-action',
            content: 'Bearbeiten',
            tooltipText: 'Bearbeiten',
            selector: '.action-rule-edge',
            onClickFunction: function (e) {
                let actionRuleId = e.target.data('model_id');
                let actionTypeId = e.target.data('target_model_id');
                let actionType = e.cy.app.action_types.find(ele => ele.id === actionTypeId);
                let actionRule = actionType.action_rules.find(ele => ele.id === actionRuleId);

                e.cy.app.openModal({
                    componentName: 'ActionRuleModal',
                    data: {
                        statusTypeId: actionRule.status_type_id,
                        position: e.position,
                        actionTypeId: actionTypeId,
                        actionRuleId: actionRuleId
                    }
                });
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
        {
            id: 'edit-status-rule-on-action',
            content: 'Bearbeiten',
            tooltipText: 'Bearbeiten',
            selector: '.status-rule-edge',
            onClickFunction: function (e) {
                let statusRuleId = e.target.data('model_id');
                let actionTypeId = e.target.data('source_model_id');
                let actionType = e.cy.app.action_types.find(ele => ele.id === actionTypeId);
                let statusRule = actionType.status_rules.find(ele => ele.id === statusRuleId);

                e.cy.app.openModal({
                    componentName: 'StatusRuleModal',
                    data: {
                        statusTypeId: statusRule.status_type_id,
                        position: e.position,
                        actionTypeId: actionTypeId,
                        statusRuleId: statusRuleId
                    }
                });
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
        {
            id: 'add-status',
            content: 'Neuer Status',
            tooltipText: 'Neuer Status',
            selector: '',
            onClickFunction: function (e) {
                e.cy.app.openModal({
                    componentName: 'StatusTypeModal',
                    data: {
                        position: e.position,
                        statusTypeId: null
                    }
                });
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: true
        },
        {
            id: 'add-state',
            content: 'Neuer Zustand',
            tooltipText: 'Neuer Zustand',
            selector: '.status',
            onClickFunction: function (e) {
                e.cy.app.openModal({
                    componentName: 'StateModal',
                    data: {
                        position: e.position,
                        statusTypeId: e.target.data('model_id'),
                        state: null
                    }
                });
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
        {
            id: 'delete-status',
            content: 'Löschen',
            tooltipText: 'Löschen',
            selector: '.status',
            onClickFunction: function (e) {
                e.cy.app.onDeleteStatusType(e.target.data('model_id'));
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
        {
            id: 'delete-action-rule',
            content: 'Löschen',
            tooltipText: 'Löschen',
            selector: '.action-rule-edge',
            onClickFunction: function (e) {
                let actionTypeId = e.target.data('target_model_id');
                let statusTypeId = e.target.source().data('status_type_id');
                e.cy.app.onDeleteActionRule(actionTypeId, statusTypeId);
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
        {
            id: 'delete-status-rule',
            content: 'Löschen',
            tooltipText: 'Löschen',
            selector: '.status-rule-edge',
            onClickFunction: function (e) {
                let actionTypeId = e.target.data('source_model_id');
                let statusTypeId = e.target.target().data('status_type_id');
                e.cy.app.onDeleteStatusRule(actionTypeId, statusTypeId);
            },
            disabled: false,
            show: true,
            hasTrailingDivider: false,
            coreAsWell: false
        },
    ], // css classes that menu items will have
    menuItemClasses: [
        'btn',
        'btn-sm',
        'btn-light',
        'btn-block',
        'm-0',
        'rounded-0'
    ], // css classes that context menu will have
    contextMenuClasses: [
        'cy-context-menu-custom',
    ]
};

export const gridOptions = {
    // On/Off Modules
    /* From the following four snap options, at most one should be true at a given time */
    snapToGridOnRelease: false, // Snap to grid on release
    snapToGridDuringDrag: true, // Snap to grid during drag
    snapToAlignmentLocationOnRelease: false, // Snap to alignment location on release
    snapToAlignmentLocationDuringDrag: false, // Snap to alignment location during drag
    distributionGuidelines: false, // Distribution guidelines
    geometricGuideline: true, // Geometric guidelines
    initPosAlignment: false, // Guideline to initial mouse position
    centerToEdgeAlignment: false, // Center to edge alignment
    resize: false, // Adjust node sizes to cell sizes
    parentPadding: false, // Adjust parent sizes to cell sizes by padding
    drawGrid: false, // Draw grid background

    // General
    gridSpacing: 20, // Distance between the lines of the grid.
    snapToGridCenter: true, // Snaps nodes to center of gridlines. When false, snaps to gridlines themselves. Note that
                            // either snapToGridOnRelease or snapToGridDuringDrag must be true.

    // Draw Grid
    zoomDash: true, // Determines whether the size of the dashes should change when the drawing is zoomed in and out if
                    // grid is drawn.
    panGrid: true, // Determines whether the grid should move then the user moves the graph if grid is drawn.
    gridStackOrder: -1, // Namely z-index
    gridColor: '#dee2e6', // Color of grid lines
    lineWidth: 1.0, // Width of grid lines

    // Guidelines
    guidelinesStackOrder: 0, // z-index of guidelines
    guidelinesTolerance: 2.00, // Tolerance distance for rendered positions of nodes' interaction.
    guidelinesStyle: { // Set ctx properties of line. Properties are here:
        strokeStyle: "#8b7d6b", // color of geometric guidelines
        geometricGuidelineRange: 250, // range of geometric guidelines
        range: 100, // max range of distribution guidelines
        minDistRange: 10, // min range for distribution guidelines
        distGuidelineOffset: 10, // shift amount of distribution guidelines
        horizontalDistColor: "#ff0000", // color of horizontal distribution alignment
        verticalDistColor: "#00ff00", // color of vertical distribution alignment
        initPosAlignmentColor: "#0000ff", // color of alignment to initial mouse location
        lineDash: [
            0,
            0
        ], // line style of geometric guidelines
        horizontalDistLine: [
            0,
            0
        ], // line style of horizontal distribution guidelines
        verticalDistLine: [
            0,
            0
        ], // line style of vertical distribution guidelines
        initPosAlignmentLine: [
            0,
            0
        ], // line style of alignment to initial mouse position
    },

    // Parent Padding
    parentSpacing: -1 // -1 to set paddings of parents to gridSpacing
};
