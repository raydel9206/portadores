Ext.onReady(function () {

    let action = 'add';
    let tree_store = Ext.create('Ext.data.TreeStore', {
        fields: [
            {name: 'id', type: 'string'},
            {name: 'nombre', type: 'string'},
            {name: 'siglas', type: 'string'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad/loadTree'),
            reader: {
                type: 'json',
                rootProperty: 'children'
            }
        },
        autoLoad: true
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_persona_chofer',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            // {name: 'cargo'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/persona/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    let store_to_traslate = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_to_traslate',
        fields: [
            {name: 'id'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/readyTraslate'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            },
            load: function (This, records, successful, eOpts) {
                if(records.length > 0){
                    if(Ext.getCmp('btn_traslado')){
                        Ext.getCmp('btn_traslado').setStyle('borderColor', 'red');
                    }
                }else{
                    if(Ext.getCmp('btn_traslado')){
                        Ext.getCmp('btn_traslado').setStyle('borderColor', '#d8d8d8');
                    }
                }
            }
        }
    });

    let store_list_traslate = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_list_traslate',
        fields: [
            {name: 'id'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/listTraslate'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_area',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            // {name: 'cargo'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/area/loadArea'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_Combustible_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/tipocombustible/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    var tree_store_by_unidad = Ext.create('Ext.data.TreeStore', {
        fields: [
            {name: 'id', type: 'string'},
            {name: 'nombre', type: 'string'},
            {name: 'siglas', type: 'string'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad/loadTree'),
            reader: {
                type: 'json',
            }
        },
        root: {
            expanded: true,
            children: []
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation) {
                operation.setParams({
                    unidad_id: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_storectividad',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/actividad/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_marca_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/marcavehiculo/loadCombo'),
            reader: {
                rootProperty: 'rows',
                totalProperty: 'total'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_modelo',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'nmarca_vehiculoid'},
            {name: 'nnombremarca'}
        ],
        viewConfig: {forceFit: true},
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/modelovehiculo/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            load: function () {
                if (Ext.getCmp('id_grid_vehiculo') !== null) {
                    var selection = Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected();
                    if (selection !== undefined)
                        Ext.getCmp('nmodelo_vehiculoid').select(selection.data.nmodelo_vehiculoid);
                }
            }
        }
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_estadotecnico_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/estadotecnico/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_denominacionvehiculo_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/denominacionvehiculo/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_mantenimiento',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tipoMantenimiento/loadTipoMantenimientoCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true,
    });

    let temp, temp1 = false;

    Ext.define('Portadores.vehiculo.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        resizable: true,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: '100%',
                    height: '100%',
                    layout: 'column',
                    bodyStyle: 'padding:5px 5px 0',
                    fieldDefaults: {
                        labelAlign: 'top'
                    },
                    items: [
                        {
                            xtype: 'fieldcontainer',
                            items: [{
                                xtype: 'fieldset',
                                margin: '0 5 5 0',
                                items: [{
                                    xtype: 'radiogroup',
                                    fieldLabel: 'Defina el medio técnico',
                                    layout: 'anchor',
                                    margin: '10 10 10 10',
                                    items: [
                                        {
                                            boxLabel: 'Vehículo',
                                            id: 'veh',
                                            name: 'fav-medio',
                                            inputValue: 'vehiculo',
                                            checked: true,
                                            listeners: {
                                                change: function (radio, newValue, oldValue, eOpts) {
                                                    if (newValue) {
                                                        Ext.getCmp('matricula').setFieldLabel('Denominación');
                                                        Ext.getCmp('nro_serie_carreceria').setDisabled(false);
                                                        Ext.getCmp('nro_circulacion').setDisabled(false);
                                                        Ext.getCmp('nro_serie_motor').setDisabled(false);
                                                        Ext.getCmp('color').setDisabled(false);
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            boxLabel: 'Equipo Tecnológico',
                                            id: 'equipoTecn',
                                            name: 'fav-medio',
                                            inputValue: 'equipoTecn',
                                            listeners: {
                                                change: function (radio, newValue, oldValue, eOpts) {
                                                    if (newValue) {
                                                        Ext.getCmp('matricula').setFieldLabel('Denominación');
                                                        Ext.getCmp('nro_serie_carreceria').setDisabled(true);
                                                        Ext.getCmp('nro_circulacion').setDisabled(true);
                                                        Ext.getCmp('nro_serie_motor').setDisabled(false);
                                                        Ext.getCmp('color').setDisabled(false);
                                                    } else if (Ext.getCmp('embarcacion').getValue()) {
                                                        Ext.getCmp('nro_serie_carreceria').setDisabled(true);
                                                        Ext.getCmp('color').setDisabled(true);
                                                        Ext.getCmp('nro_serie_motor').setDisabled(true);
                                                        Ext.getCmp('nro_circulacion').setDisabled(true);
                                                        Ext.getCmp('matricula').setFieldLabel('Denominación');
                                                    } else {
                                                        Ext.getCmp('matricula').setFieldLabel('Matrícula');
                                                        Ext.getCmp('nro_circulacion').setDisabled(false);
                                                        Ext.getCmp('nro_serie_carreceria').setDisabled(false);
                                                        Ext.getCmp('color').setDisabled(false);
                                                        Ext.getCmp('nro_serie_motor').setDisabled(false);
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            boxLabel: 'Embarcación',
                                            id: 'embarcacion',
                                            name: 'fav-medio',
                                            inputValue: 'embarcacion',
                                            listeners: {
                                                change: function (radio, newValue, oldValue, eOpts) {
                                                    if (newValue) {
                                                        Ext.getCmp('nro_serie_carreceria').setDisabled(true);
                                                        Ext.getCmp('color').setDisabled(true);
                                                        Ext.getCmp('nro_serie_motor').setDisabled(true);
                                                        Ext.getCmp('nro_circulacion').setDisabled(true);
                                                        Ext.getCmp('matricula').setFieldLabel('Denominación');
                                                    } else if (Ext.getCmp('equipoTecn').getValue()) {
                                                        Ext.getCmp('matricula').setFieldLabel('Denominación');
                                                        Ext.getCmp('nro_serie_carreceria').setDisabled(true);
                                                        Ext.getCmp('nro_circulacion').setDisabled(true);
                                                        Ext.getCmp('nro_serie_motor').setDisabled(false);
                                                        Ext.getCmp('color').setDisabled(false);
                                                    } else {
                                                        Ext.getCmp('matricula').setFieldLabel('Matrícula');
                                                        Ext.getCmp('nro_circulacion').setDisabled(false);
                                                        Ext.getCmp('nro_serie_carreceria').setDisabled(false);
                                                        Ext.getCmp('color').setDisabled(false);
                                                        Ext.getCmp('nro_serie_motor').setDisabled(false);
                                                    }
                                                }
                                            }
                                        },
                                    ]
                                }]
                            }]
                        }, {
                            xtype: 'fieldset',
                            layout: 'hbox',
                            items: [
                                {
                                    xtype: 'fieldcontainer',
                                    columnWidth: 0.5,
                                    margin: '10 10 10 10',
                                    title: ' ',
                                    collapsible: false,

                                    defaults: {anchor: '100%'},
                                    layout: 'anchor',
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            name: 'matricula',
                                            id: 'matricula',
                                            fieldLabel: 'Matrícula',
                                            hidden: false,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            allowBlank: false, // requires a non-empty value
                                            maskRe: /[0-9a-zA-Z -]/,
                                            bodyPadding: 10
                                        },
                                        {
                                            xtype: 'numberfield',
                                            name: 'norma_fabricante',
                                            id: 'norma_fabricante',
                                            fieldLabel: 'Norma Fábrica',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            value: 0,
                                            minValue: 0,
                                            formatter: "number('0.00')",
                                            decimalSeparator: '.',
                                            decimalPrecision: 20,
                                            allowBlank: false,
                                        }, {
                                            xtype: 'numberfield',
                                            name: 'norma_far',
                                            id: 'norma_far',
                                            fieldLabel: 'Norma FAR',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            value: 0,
                                            minValue: 0,
                                            decimalSeparator: '.',
                                            allowBlank: false,
                                            listeners: {
                                                beforerender: function (This) {
                                                    temp = true;
                                                },
                                                change: function (This) {
                                                    if (!temp && !isNaN(This.value) && This.value !== 0 && This.value !== null) {
                                                        Ext.getCmp('norma').suspendEvents();
                                                        Ext.getCmp('norma').setValue(parseFloat((100 / This.value).toPrecision(4)));
                                                        Ext.getCmp('norma').resumeEvents();
                                                    }
                                                    temp = false;
                                                }
                                            }
                                        }, {
                                            xtype: 'numberfield',
                                            name: 'norma',
                                            id: 'norma',
                                            fieldLabel: 'Norma',
                                            hidden: true,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            value: 0,
                                            minValue: 0,
                                            formatter: "number('0.00')",
                                            decimalSeparator: '.',
                                            decimalPrecision: 20,
                                            allowBlank: false,
                                            listeners: {
                                                beforerender: function (This) {
                                                    temp1 = true;
                                                },
                                                change: function (This) {
                                                    if (!temp1 && !isNaN(This.value) && This.value !== 0 && This.value !== null) {
                                                        Ext.getCmp('norma_far').suspendEvents();
                                                        Ext.getCmp('norma_far').setValue(parseFloat((100 / This.value).toPrecision(4)));
                                                        Ext.getCmp('norma_far').resumeEvents();
                                                    }
                                                    temp1 = false;
                                                },
                                            }
                                        }, {
                                            xtype: 'numberfield',
                                            name: 'norma_lubricante',
                                            id: 'norma_lubricante',
                                            fieldLabel: 'Lubricante',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            value: 0,
                                            minValue: 0.001,
                                            decimalSeparator: '.',
                                            decimalPrecision: 3,
                                            allowBlank: false
                                        },
                                        // {
                                        //     xtype: 'datefield',
                                        //     //grow:true,
                                        //     anchor: '100%',
                                        //     name: 'fecha_expiracion_circulacion',
                                        //     id: 'fecha_expiracion_circulacion',
                                        //     fieldLabel: 'Fecha expiración circulación',
                                        //     allowBlank: true
                                        //
                                        // },
                                        // {
                                        //     xtype: 'datefield',
                                        //     //grow:true,
                                        //     anchor: '100%',
                                        //     name: 'fecha_expiracion_somaton',
                                        //     id: 'fecha_expiracion_somaton',
                                        //     fieldLabel: 'FICA Somatón',
                                        //     allowBlank: true
                                        // },
                                        {
                                            xtype: 'textfield',
                                            name: 'nro_orden',
                                            id: 'nro_orden',
                                            fieldLabel: 'No. Orden',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            allowBlank: false,
                                            maskRe: /[0-9a-zA-Z ]/
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'ntipo_combustibleid',
                                            id: 'ntipo_combustibleid',
                                            fieldLabel: 'Tipo de combustible',
                                            labelWidth: 140,
                                            store: Ext.getStore('id_store_tipo_Combustible_vehiculo'),
                                            displayField: 'nombre',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione tipo de combustible...',
                                            selectOnFocus: true,
                                            editable: true,
                                            allowBlank: false,
                                            listeners: {
                                                change: function (This, records, eOpts) {
                                                    var tipo_combustibleid = This.getValue();
                                                    Ext.getCmp('actividad').getStore().load({
                                                        params: {
                                                            tipo_combustibleid: tipo_combustibleid
                                                        }
                                                    });
                                                }
                                            }
                                        },
                                    ]
                                },
                                {
                                    xtype: 'fieldcontainer',
                                    columnWidth: 0.5,
                                    //margin: '0 0 0 10',
                                    margin: '10 10 10 10',
                                    title: ' ',
                                    collapsible: false,

                                    defaults: {anchor: '100%'},
                                    layout: 'anchor',
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            //grow      : true,
                                            //anchor    : '100%',
                                            name: 'nro_serie_carreceria',
                                            id: 'nro_serie_carreceria',
                                            fieldLabel: 'No. serie carrocería',
                                            // afterLabelTextTpl: [
                                            //     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            // ],
                                            // allowBlank: false, // requires a non-empty value
                                            maskRe: /[0-9a-zA-Z ]/
                                        }, {
                                            xtype: 'textfield',
                                            //grow      : true,
                                            //anchor    : '100%',
                                            name: 'nro_serie_motor',
                                            id: 'nro_serie_motor',
                                            fieldLabel: 'No. serie motor',
                                            // afterLabelTextTpl: [
                                            //     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            // ],
                                            // allowBlank: false, // requires a non-empty value
                                            maskRe: /[0-9a-zA-Z ]/
                                        }, {
                                            xtype: 'textfield',
                                            //grow      : true,
                                            //anchor    : '100%',
                                            name: 'color',
                                            id: 'color',
                                            fieldLabel: 'Color',
                                            // afterLabelTextTpl: [
                                            //     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            // ],
                                            // allowBlank: false, // requires a non-empty value
                                            maskRe: /[a-zA-Z ]/
                                        }, {
                                            xtype: 'textfield',
                                            //grow      : true,
                                            //anchor    : '100%',
                                            name: 'nro_circulacion',
                                            id: 'nro_circulacion',
                                            fieldLabel: 'No. circulación',
                                            // afterLabelTextTpl: [
                                            //     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            // ],
                                            // allowBlank: false, // requires a non-empty value
                                            maskRe: /[0-9-a-zA-Z]/
                                        }, {
                                            xtype: 'textfield',
                                            name: 'nro_inventario',
                                            id: 'nro_inventario',
                                            fieldLabel: 'No. inventario',
                                            // afterLabelTextTpl: [
                                            //     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            // ],
                                            // allowBlank: false, // requires a non-empty value
                                            maskRe: /[0-9a-zA-Z]/
                                        }, {
                                            xtype: 'textfield',
                                            //grow      : true,
                                            //anchor    : '100%',
                                            name: 'anno_fabricacion',
                                            id: 'anno_fabricacion',
                                            fieldLabel: 'Año fabricación',
                                            // afterLabelTextTpl: [
                                            //     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            // ],
                                            allowBlank: false, // requires a non-empty value
                                            maskRe: /[0-9]/,
                                            listeners: {

                                                blur: function (This, event, eOpts) {
                                                    var anno = Ext.getCmp('anno_fabricacion').getValue();
                                                    var ann_actual = new Date();
                                                    var annoqq = Ext.Date.format(ann_actual, 'Y-m-d');
                                                    var anno__ = annoqq.split("-");

                                                    if (anno > anno__) {
                                                        App.showAlert('El año no puede ser mayor que el actual', 'danger');
                                                        Ext.getCmp('anno_fabricacion').setValue(null)
                                                    }
                                                }

                                            }
                                        },
                                        // {
                                        //     xtype: 'datefield',
                                        //     anchor: '100%',
                                        //     name: 'fecha_expiracion_licencia_operativa',
                                        //     id: 'fecha_expiracion_licencia_operativa',
                                        //     fieldLabel: 'Licencia Operativa',
                                        //     allowBlank: true
                                        // },
                                        {
                                            xtype: 'combobox',
                                            //margin: '0 10 0 10',
                                            labelWidth: 140,
                                            name: 'odometro',
                                            id: 'odometro',
                                            store: Ext.create('Ext.data.JsonStore', {
                                                storeId: 'id_store_yes_no',
                                                fields: [
                                                    {name: 'id'},
                                                    {name: 'nombre'}
                                                ],
                                                data: [
                                                    {id: 'true', nombre: 'Si'},
                                                    {id: 'false', nombre: 'No'}
                                                ],
                                                proxy: {
                                                    type: 'memory',
                                                    reader: {
                                                        type: 'json'
                                                    }
                                                }
                                            }),
                                            fieldLabel: 'Od&oacute;metro',
                                            valueField: 'id',
                                            displayField: 'nombre',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione si o no...',
                                            editable: false
                                        },
                                    ]
                                },
                                {

                                    xtype: 'fieldcontainer',
                                    title: ' ',
                                    columnWidth: 0.51,
                                    margin: '10 10 10 10',
                                    collapsed: false, // fieldset initially collapsed
                                    layout: 'anchor',
                                    items: [
                                        {
                                            xtype: 'combobox',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            name: 'nunidadid',
                                            id: 'nunidadid',
                                            fieldLabel: 'Unidad',
                                            labelWidth: 60,
                                            displayField: 'nombre',
                                            store: tree_store_by_unidad,
                                            valueField: 'id',
                                            queryMode: 'remote',
                                            forceSelection: true,
                                            allowBlank: false,
                                            editable: false,
                                            anyMatch: true,
                                            emptyText: 'Seleccione la unidad...',
                                        },
                                        {
                                            xtype: 'combobox',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            name: 'area_id',
                                            id: 'areaid',
                                            fieldLabel: 'Area',
                                            labelWidth: 60,
                                            displayField: 'nombre',
                                            store: Ext.getStore('id_store_area'),
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione el área...',
                                            selectOnFocus: true,
                                            editable: true,
                                            allowBlank: false,
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'nmarca_vehiculoid',
                                            id: 'nmarca_vehiculoid',
                                            fieldLabel: 'Marca',
                                            labelWidth: 140,
                                            store: Ext.getStore('id_store_marca_vehiculo'),
                                            displayField: 'nombre',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione la Marca...',
                                            selectOnFocus: true,
                                            editable: true,
                                            allowBlank: false,
                                            listeners: {
                                                change: function (This, selected) {
                                                    Ext.getCmp('nmodelo_vehiculoid').setDisabled(selected === undefined);
                                                    Ext.getCmp('nmodelo_vehiculoid').getStore().removeAll();
                                                    if (selected !== undefined) {
                                                        Ext.getCmp('nmodelo_vehiculoid').getStore().load(
                                                            {
                                                                params: {marca: selected}
                                                            }
                                                        );
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'nmodelo_vehiculoid',
                                            id: 'nmodelo_vehiculoid',
                                            fieldLabel: 'Modelo',
                                            labelWidth: 140,
                                            store: Ext.getStore('id_store_modelo'),
                                            displayField: 'nombre',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            valueField: 'id',
//                                typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
//                                triggerAction: 'all',
                                            emptyText: 'Seleccione el Modelo...',
//                                selectOnFocus: true,
                                            editable: true,
                                            allowBlank: false,
                                            disabled: true
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'nestado_tecnicoid',
                                            id: 'nestado_tecnicoid',
                                            fieldLabel: 'Estado técnico ',
                                            labelWidth: 140,
                                            store: Ext.getStore('id_store_estadotecnico_vehiculo'),
                                            displayField: 'nombre',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione el estado...',
                                            selectOnFocus: true,
                                            editable: true,
                                            allowBlank: false
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'ndenominacion_vehiculoid',
                                            id: 'ndenominacion_vehiculoid',
                                            fieldLabel: 'Denominación ',
                                            labelWidth: 140,
                                            store: Ext.getStore('id_store_denominacionvehiculo_vehiculo'),
                                            displayField: 'nombre',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione la Denominación...',
                                            selectOnFocus: true,
                                            editable: true,
                                            allowBlank: false
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'actividad',
                                            id: 'actividad',
                                            fieldLabel: 'Actividad',
                                            labelWidth: 140,
                                            store: Ext.getStore('id_storectividad'),
                                            displayField: 'nombre',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione la Actividad...',
                                            selectOnFocus: true,
                                            editable: true,
                                            allowBlank: false
                                        }
                                    ]

                                }
                            ]
                        }
                    ]

                }
            ];

            this.callParent();
        },
        listeners: {
            afterrender: function (This, operation, eOpts) {
                Ext.getStore('id_store_tipo_Combustible_vehiculo').load();
                Ext.getStore('id_storectividad').load();
                Ext.getStore('id_store_marca_vehiculo').load();
                Ext.getStore('id_store_estadotecnico_vehiculo').load();
                Ext.getStore('id_store_denominacionvehiculo_vehiculo').load();
                tree_store_by_unidad.load();

                if (action === 'add') {
                    let selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                    Ext.getCmp('nunidadid').setValue(selected.id);
                }
            }
        }
    });

    Ext.define('Portadores.mantenimiento.Window', {
        extend: 'Ext.window.Window',
        width: 250,
        modal: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    bodyPadding: 10,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'combobox',
                            name: 'tipo_mantenimiento_id',
                            id: 'tipo_mantenimiento_id',
                            fieldLabel: 'Tipo de Mantenimiento',
                            store: Ext.getStore('id_store_tipo_mantenimiento'),
                            displayField: 'nombre',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione mantenimiento...',
                            selectOnFocus: true,
                            editable: true
                        },
                        {
                            xtype: 'numberfield',
                            name: 'km',
                            id: 'km',
                            fieldLabel: 'Kilómetros',
                            decimalSeparator: '.',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            listeners: {
                                change: function (This, newValue) {
                                    var selection = Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected();
                                    if (selection !== undefined) {
                                        Ext.getCmp('litros').suspendEvents();
                                        var norma = selection.data.norma_far;
                                        if (norma !== 0) {
                                            Ext.getCmp('litros').setValue(Ext.util.Format.round(newValue * norma / 100, 2))
                                        } else {
                                            norma = selection.data.norma;

                                            if (norma !== 0)
                                                Ext.getCmp('litros').setValue(Ext.util.Format.round(newValue / norma, 2));
                                        }
                                        Ext.getCmp('litros').resumeEvents();
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'numberfield',
                            id: 'litros',
                            fieldLabel: 'Litros',
                            decimalSeparator: '.',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            listeners: {
                                change: function (This, newValue) {
                                    var selection = Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected();
                                    if (selection !== undefined) {
                                        Ext.getCmp('km').suspendEvents();
                                        var norma = selection.data.norma_far;
                                        if (norma !== 0) {
                                            Ext.getCmp('km').setValue(Ext.util.Format.round(newValue * 100 / norma, 2))
                                        } else {
                                            norma = selection.data.norma;

                                            if (norma !== 0)
                                                Ext.getCmp('km').setValue(Ext.util.Format.round(newValue * norma, 2));
                                        }
                                        Ext.getCmp('km').resumeEvents();
                                    }
                                }
                            }
                        }
                    ]

                }
            ];
            this.callParent();
        },
        // listeners: {
        //     afterrender: function (This, operation, eOpts) {
        //         Ext.getStore('id_store_tipo_mantenimiento').load();
        //     }
        // }
    });

    Ext.define('Portadores.asignacion.Window', {
        extend: 'Ext.window.Window',
        width: 250,
        modal: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
                    bodyPadding: 10,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'combobox',
                            name: 'personaid',
                            itemId: 'personaid',
                            fieldLabel: 'Persona',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            store: Ext.getStore('id_store_persona_chofer'),
                            displayField: 'nombre',
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione la Persona...',
                            selectOnFocus: true,
                            editable: true
                        },
                        {
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'nro_licencia',
                            itemId: 'nro_licencia',
                            fieldLabel: 'No. licencia'
                        },
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Fecha expiración licencia',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'fecha_expiracion_licencia',
                            itemId: 'fecha_expiracion_licencia'
                        }
                    ]
                }
            ];
            this.callParent();
        },
        // listeners: {
        //     afterrender: function (This, operation, eOpts) {
        //         Ext.getStore('id_store_persona_chofer').load();
        //     }
        // }
    });

    let _btnGestionar = Ext.create('Ext.button.MyButton', {
        text: 'Menu',
        iconCls: 'fa fa-bars text-primary',
        menu: [{
            id: 'vehiculo_btn_add',
            text: 'Adicionar',
            glyph: 0xf0fe,
            width: 100,
            handler: function (This, e) {
                action = 'add';
                Ext.create('Portadores.vehiculo.Window', {
                    title: 'Adicionar medio técnico',
                    id: 'window_vehiculo_id',
                    buttons: [
                        {
                            text: 'Aceptar',
                            width: 70,
                            handler: function () {
                                var window = Ext.getCmp('window_vehiculo_id');
                                var form = window.down('form').getForm();
                                if (form.isValid()) {
                                    window.hide();
                                    App.request('POST', App.buildURL('/portadores/vehiculo/add'), form.getValues(), null, null,
                                        function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                form.reset();
                                                var selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                                                Ext.getCmp('nunidadid').setValue(selected.id);
                                                Ext.getCmp('id_grid_vehiculo').getStore().load();
                                            } else {
                                                if (response && response.hasOwnProperty('errors') && response.errors) {
                                                    window.down('form').getForm().markInvalid(response.errors);
                                                }
                                            }
                                            window.show();
                                        },
                                        function (response) { // failure_callback
                                            window.show();
                                        }
                                    );
                                }
                            }
                        },
                        {
                            text: 'Cancelar',
                            width: 70,
                            handler: function () {
                                Ext.getCmp('window_vehiculo_id').close()
                            }
                        }
                    ]
                }).show();
            }
        }, {
            id: 'vehiculo_btn_mod',
            text: 'Modificar',
            glyph: 0xf044,
            disabled: true,
            width: 100,
            handler: function (This, e) {
                action = 'mod';
                var selection = Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected();
                var window = Ext.create('Portadores.vehiculo.Window', {
                    title: 'Modificar vehículo',
                    id: 'window_vehiculo_id',
                    listeners: {
                        afterrender: function () {
                            // Ext.getCmp('nunidadid').setReadOnly(true);
                        }
                    },
                    buttons: [
                        {
                            text: 'Aceptar',
                            width: 70,
                            handler: function () {
                                var form = window.down('form').getForm();
                                if (form.isValid()) {
                                    window.hide();
                                    var obj = form.getValues();
                                    obj.id = selection.data.id;

                                    App.request('POST', App.buildURL('/portadores/vehiculo/mod'), obj, null, null,
                                        function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                window.close();
                                                Ext.getCmp('id_grid_vehiculo').getStore().loadPage(1);
                                            } else {
                                                if (response && response.hasOwnProperty('errors') && response.errors) {
                                                    window.down('form').getForm().markInvalid(response.errors);
                                                }
                                                window.show();
                                            }
                                        },
                                        function (response) { // failure_callback
                                            window.show();
                                        }
                                    );
                                }
                            }
                        },
                        {
                            text: 'Cancelar',
                            width: 70,
                            handler: function () {
                                Ext.getCmp('window_vehiculo_id').close();
                            }
                        }
                    ]
                });
                window.show();
                window.down('form').loadRecord(selection);
            }
        }, {
            id: 'vehiculo_btn_del',
            text: 'Eliminar',
            glyph: 0xf2ed,
            disabled: true,
            width: 100,
            handler: function (This, e) {
                selection = Ext.getCmp('id_grid_vehiculo').getSelection();
                Ext.Msg.show({
                    title: '¿Eliminar Medio Técnico?',
                    message: Ext.String.format('¿Está seguro que desea eliminar el vehículo <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('matricula')),
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.QUESTION,
                    fn: function (btn) {
                        if (btn === 'yes') {
                            App.request('DELETE', App.buildURL('/portadores/vehiculo/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    Ext.getCmp('id_grid_vehiculo').getStore().reload();
                                }
                            });
                        }
                    }
                });
            }
        }, {
            id: 'vehiculo_btn_print',
            glyph: 0xf1c3,
            text: 'Exportar',
            menu: [
                {
                    text: 'Exportar página',
                    width: 150,
                    handler: function (This, e) {
                        let obj = {};
                        let store = Ext.getCmp('id_grid_vehiculo').getStore();
                        if (store.data.length > 0) {
                            obj.mes = App.current_month;
                            obj.anno = App.current_year;
                            obj.group = Ext.getCmp('id_grid_vehiculo').getStore().groupField;
                            let send = [];
                            Ext.Array.each(store.data.items, function (valor) {
                                send.push(valor.data);
                            });
                            obj.store = Ext.encode(send);
                            App.request('POST', App.buildURL('/portadores/vehiculo/printPage'), obj, null, null,
                                function (response) { // success_callback
                                    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                                }
                            );
                        } else {
                            App.showAlert('No hay datos para exportar', 'warning', 3500);
                        }

                    }
                }, {
                    text: 'Exportar todo',
                    width: 150,
                    handler: function (This, e) {
                        let obj = {};
                        let store = Ext.getCmp('id_grid_vehiculo').getStore();

                        if (store.data.length > 0) {
                            obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                            obj.mes = App.current_month;
                            obj.anno = App.current_year;
                            obj.group = Ext.getCmp('id_grid_vehiculo').getStore().groupField;
                            App.request('POST', App.buildURL('/portadores/vehiculo/printAll'), obj, null, null,
                                function (response) { // success_callback
                                    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                                }
                            );
                        } else {
                            App.showAlert('No hay datos para exportar', 'warning', 3500);
                        }

                    }
                }

            ]
        }]
    });

    let _btnTraslado = Ext.create('Ext.button.MyButton', {
        text: 'Traslado',
        id: 'btn_traslado',
        glyph: 0xf362,
        menu: [{
            id: 'vehiculo_btn_trasladar',
            text: 'Trasladar',
            glyph: 0xf35a,
            disabled: true,
            handler: function (This, e) {
                let window = Ext.create('Ext.window.Window', {
                    width: 350,
                    id: 'window_traslado_id',
                    title: 'Trasladar medio técnico',
                    items: [
                        {
                            xtype: 'form',
                            frame: true,
                            defaultType: 'textfield',
                            id: 'form_traslado',
                            bodyPadding: 10,
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            fieldDefaults: {
                                msgTarget: 'side',
                                allowBlank: false
                            },
                            items: [
                                {
                                    xtype: 'datefield',
                                    name: 'fecha_traslado',
                                    id: 'fecha_traslado',
                                    editable: false,
                                    value: new Date(),
                                    format: 'd/m/Y',
                                    fieldLabel: 'Fecha',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ]
                                }, {
                                    xtype: 'treecombobox',
                                    fieldLabel: 'Traslado hacia',
                                    valueField: 'id',
                                    displayField: 'nombre',
                                    name: 'unidad_hacia',
                                    id: 'unidad_hacia',
                                    emptyText: 'Seleccione la unidad...',
                                    store: tree_store,
                                    queryMode: 'remote',
                                    forceSelection: true,
                                    allowBlank: false,
                                    editable: false,
                                    anyMatch: true,
                                    allowFolderSelect: true,
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    treeConfig: {
                                        maxHeight: 200,
                                        scrolling: true
                                    }
                                },
                                {
                                    xtype: 'textareafield',
                                    name: 'motivos',
                                    id: 'motivos',
                                    grow: false,
                                    fieldLabel: 'Motivos',
                                    labelAlign: 'top',
                                    width: '95%',
                                    margin: '0 0 0 5',
                                }, {
                                    xtype: 'hidden',
                                    name: 'unidad_desde',
                                    id: 'unidad_desde',
                                    value: Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected().data.nunidadid
                                }, {
                                    xtype: 'hidden',
                                    name: 'vehiculo',
                                    id: 'vehiculo',
                                    value: Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected().data.id
                                }
                            ]
                        }
                    ],
                    buttons: [
                        {
                            text: 'Aceptar',
                            width: 70,
                            handler: function () {
                                let form = window.down('form').getForm();
                                if (form.isValid()) {
                                    window.hide();
                                    App.request('POST', App.buildURL('/portadores/vehiculo/trasladar'), form.getValues(), null, null,
                                        function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                window.close();
                                                Ext.getCmp('id_grid_vehiculo').getStore().load();
                                                store_to_traslate.load();
                                            } else {
                                                if (response && response.hasOwnProperty('errors') && response.errors) {
                                                    window.down('form').getForm().markInvalid(response.errors);
                                                }
                                                window.show();
                                            }
                                        },
                                        function (response) { // failure_callback
                                            window.show();
                                        }
                                    );
                                }
                            }
                        }, {
                            text: 'Cancelar',
                            width: 70,
                            handler: function () {
                                window.close()
                            }
                        }
                    ]
                }).show();
            }
        }, {
            id: 'btn_ready_to_trasladar',
            text: 'Listo para traslado',
            glyph: 0xf35a,
            handler: function (This, e) {
                if (store_to_traslate.getData().length > 0) {
                    Ext.create('Ext.window.Window', {
                        id: 'ready_to_traslado',
                        title: 'Vehículos listos para traslado',
                        height: 400,
                        width: 750,
                        modal: true,
                        layout: 'fit',
                        items: [
                            {
                                xtype: 'form',
                                frame: true,
                                defaultType: 'textfield',
                                id: 'form_ready_to_traslado',
                                bodyPadding: 10,
                                layout: {
                                    type: 'vbox',
                                    align: 'stretch'
                                },
                                fieldDefaults: {
                                    msgTarget: 'side',
                                    allowBlank: false
                                },
                                items: [
                                    {
                                        xtype: 'gridpanel',
                                        id: 'grid_ready',
                                        height: 300,
                                        store: store_to_traslate,
                                        border: true,
                                        columns: [
                                            {
                                                text: '<strong>Medio Técnico</strong>',
                                                dataIndex: 'matricula',
                                                filter: 'string',
                                                flex: 1,
                                            },
                                            {
                                                text: '<strong>Fecha de traslado</strong>',
                                                dataIndex: 'fecha',
                                                flex: 1
                                            },
                                            {
                                                text: '<strong>Motivos</strong>',
                                                dataIndex: 'motivos',
                                                flex: 1
                                            },
                                            {
                                                text: '<strong>Origen</strong>',
                                                dataIndex: 'desde',
                                                flex: 1
                                            },
                                            {
                                                text: '<strong>Destino</strong>',
                                                dataIndex: 'hacia',
                                                flex: 1
                                            },
                                            {
                                                text: '<strong>Confirmado</strong>',
                                                dataIndex: 'aceptado',
                                                flex: 1,
                                                renderer: function (value) {
                                                    if (value) {
                                                        return '<strong><span class="badge badge-pill badge-success">Si</span></strong>';
                                                    } else {
                                                        return '<strong><span class="badge badge-pill badge-danger">No</span></strong>';
                                                    }
                                                }
                                            },
                                        ],
                                        listeners: {
                                            selectionchange: function (This, selected, e) {
                                                let unidad_selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                                if (selected) {
                                                    if (selected[0].data.haciaid === unidad_selected) {
                                                        if (Ext.getCmp('confirmar'))
                                                            Ext.getCmp('confirmar').setDisabled(selected.length === 0);
                                                    }
                                                }

                                            }
                                        }
                                    }
                                ]
                            }
                        ],
                        dockedItems: [{
                            xtype: 'toolbar',
                            dock: 'top',
                            items: [{
                                xtype: 'button',
                                text: 'Confirmar',
                                id: 'confirmar',
                                disabled: true,
                                width: 100,
                                glyph: 0xf058,
                                handler: function () {
                                    let sel = Ext.getCmp('grid_ready').getSelectionModel().getLastSelected();
                                    let window = Ext.create('Portadores.vehiculo.Window', {
                                        title: 'Confirmar traslado',
                                        id: 'window_vehiculo_id',
                                        buttons: [
                                            {
                                                text: 'Aceptar',
                                                width: 70,
                                                handler: function () {
                                                    var form = window.down('form').getForm();
                                                    if (form.isValid()) {
                                                        window.hide();
                                                        var obj = form.getValues();
                                                        obj.traslado = true;
                                                        obj.idtraslado = sel.data.id;

                                                        App.request('POST', App.buildURL('/portadores/vehiculo/add'), obj, null, null,
                                                            function (response) { // success_callback
                                                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                    window.close();
                                                                    Ext.getCmp('grid_ready').getStore().load();
                                                                    Ext.getCmp('id_grid_vehiculo').getStore().loadPage(1);
                                                                } else {
                                                                    if (response && response.hasOwnProperty('errors') && response.errors) {
                                                                        window.down('form').getForm().markInvalid(response.errors);
                                                                    }
                                                                    window.show();
                                                                }
                                                            },
                                                            function (response) { // failure_callback
                                                                window.show();
                                                            }
                                                        );
                                                    }
                                                }
                                            },
                                            {
                                                text: 'Cancelar',
                                                width: 70,
                                                handler: function () {
                                                    Ext.getCmp('window_vehiculo_id').close();
                                                }
                                            }
                                        ]
                                    });
                                    window.show();
                                    window.down('form').loadRecord(sel);
                                }
                            }]
                        }]
                    }).show();
                } else {
                    App.showAlert('No existen vehículos listos para traslado', 'warning', 3000)
                }
            }
        }, {
            id: 'btn_list_traslado',
            text: 'Listado de traslados',
            glyph: 0xf35a,
            handler: function (This, e) {
                Ext.create('Ext.window.Window', {
                    id: 'list_traslado',
                    title: 'Vehículos trasladados',
                    height: 500,
                    bodyPadding: 10,
                    modal: true,
                    width: 1000,
                    items: [
                        {
                            xtype: 'form',
                            frame: true,
                            id: 'form_ready_to_traslado',
                            bodyPadding: 5,
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            fieldDefaults: {
                                labelAlign: 'top',
                                msgTarget: 'side',
                                allowBlank: false
                            },
                            items: [
                                {
                                    xtype: 'gridpanel',
                                    id: 'grid_ready',
                                    store: store_list_traslate,
                                    border: true,
                                    height: 400,
                                    columns: [
                                        {
                                            text: '<strong>Medio Técnico</strong>',
                                            dataIndex: 'matricula',
                                            filter: 'string',
                                            flex: 1,
                                        },
                                        {
                                            text: '<strong>Fecha de traslado</strong>',
                                            dataIndex: 'fecha',
                                            flex: 1
                                        },
                                        {
                                            text: '<strong>Confirmado</strong>',
                                            dataIndex: 'aceptado',
                                            flex: 1,
                                            renderer: function (value) {
                                                if (value) {
                                                    return '<strong><span class="badge badge-pill badge-success">Si</span></strong>';
                                                } else {
                                                    return '<strong><span class="badge badge-pill badge-danger">No</span></strong>';
                                                }
                                            }
                                        }
                                    ],
                                    plugins: ['gridfilters', {
                                        ptype: 'rowexpander',
                                        rowBodyTpl: new Ext.XTemplate(
                                            '<div class="card p-1">',
                                            '   <div class="card">',
                                            '       <tpl>',
                                            '           <div class="card-header text-center">',
                                            '               <strong>Otros datos de interés</strong> <em class="text-muted"></em>',
                                            '           </div>',
                                            '       </tpl>',
                                            '       <table class="table table-bordered table-hover table-responsive-md mb-0">',
                                            '           <tpl if="Ext.isEmpty(id)">',
                                            '               <tr class="text-center">',
                                            '                   <td colspan="4"><span class="badge badge-secondary">No tiene mantenimientos asociados</span></td>',
                                            '                </tr>',
                                            '            <tpl else>',
                                            '            <thead class="text-center">',
                                            '               <tr>',
                                            '                   <th scope="col">Origen:</th>',
                                            '                   <th scope="col">Destino:</th>',
                                            '                   <th scope="col">Motivos:</th>',
                                            '                   <th scope="col">Combustible:</th>',
                                            '                   <th scope="col">Marca:</th>',
                                            '                   <th scope="col">No de Serie  del Motor:</th>',
                                            '                   <th scope="col">Color:</th>',
                                            '                   <th scope="col">Año de Fabricación:</th>',
                                            '               </tr>',
                                            '             </thead>',
                                            '             <tbody>',
                                            '               <tpl>',
                                            '                   <tr class="text-center">',
                                            '                       <td>{desde}</td>',
                                            '                       <td>{hacia}</td>',
                                            '                       <td>{motivos}</td>',
                                            '                       <td>{ntipo_combustible}</td>',
                                            '                       <td>{nmarca_vehiculo}</td>',
                                            '                       <td>{nro_serie_motor}</td>',
                                            '                       <td>{color}</td>',
                                            '                       <td>{anno_fabricacion}</td>',
                                            '                    </tr>',
                                            '                </tpl>',
                                            '              </tbody>',
                                            '           </tpl>',
                                            '       </table>',
                                            '   </div>',
                                            '</div>'
                                        )
                                    }],
                                    listeners: {
                                        selectionchange: function (This, selected, e) {
                                            if (Ext.getCmp('confirmar'))
                                                Ext.getCmp('confirmar').setDisabled(selected.length === 0);
                                        }
                                    }
                                }
                            ]
                        }
                    ],
                    buttons: [
                        {
                            text: 'Cerrar',
                            width: 70,
                            handler: function () {
                                Ext.getCmp('list_traslado').close()
                            }
                        }
                    ]
                }).show();
            }
        }]
    });

    let _tbar = Ext.getCmp('vehiculo_tbar');
    _tbar.add(_btnGestionar);
    _tbar.add('-');
    _tbar.add(_btnTraslado);
    _tbar.setHeight(36);

    let _btnAddVehiculoPersona = Ext.create('Ext.button.MyButton', {
        id: 'vehiculo_persona_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        disabled: true,
        handler: function () {
            Ext.create('Portadores.asignacion.Window', {
                title: 'Asignar chofer',
                id: 'window_vehiculoasignar_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var selection = Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected();
                            var window = Ext.getCmp('window_vehiculoasignar_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.vehiculoid = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/vehiculo/asignacion/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_vehiculo_persona').getStore().load();
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                window.down('form').getForm().markInvalid(response.errors);
                                            }
                                        }
                                        window.show();
                                    },
                                    function (response) { // failure_callback
                                        window.show();
                                    }
                                );
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_vehiculoasignar_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    let _btnModVehiculoPersona = Ext.create('Ext.button.MyButton', {
        id: 'vehiculo_persona_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_vehiculo_persona').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.asignacion.Window', {
                title: 'Modificar chofer',
                id: 'window_vehiculo_persona_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_vehiculo_persona_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                obj.vehiculoid = Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/vehiculo/asignacion/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_vehiculo_persona').getStore().load();
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                window.down('form').getForm().markInvalid(response.errors);
                                            }
                                            window.show();
                                        }
                                    },
                                    function (response) { // failure_callback
                                        window.show();
                                    }
                                );
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_vehiculo_persona_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    let _btn_DelVehiculoPersona = Ext.create('Ext.button.MyButton', {
        id: 'vehiculo_persona_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_vehiculo_persona').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Chofer?',
                message: '¿Está seguro que desea eliminar el Chofer?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/vehiculo/asignacion/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_vehiculo_persona').getStore().load();
                            }
                        });
                    }
                }
            });

        }
    });

    let _tbarVehiculoPersona = Ext.getCmp('vehiculo_persona_tbar');
    _tbarVehiculoPersona.add(_btnAddVehiculoPersona);
    _tbarVehiculoPersona.add('-');
    _tbarVehiculoPersona.add(_btnModVehiculoPersona);
    _tbarVehiculoPersona.add('-');
    _tbarVehiculoPersona.add(_btn_DelVehiculoPersona);
    _tbarVehiculoPersona.setHeight(36);
})
;
