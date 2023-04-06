/**
 * Created by pfcadenas on 11/11/2016.
 */

Ext.onReady(function () {

    var store_vehiculo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'matricula'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/loadCombo'),
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
        }

    });

    var store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nro_tarjeta'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        beforeload: function (This, operation, eOpts) {
            operation.setParams({
                unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
            });
        },

    });

    var store_persona = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_persona',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
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
        beforeload: function (This, operation, eOpts) {
            operation.setParams({
                unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
            });
        },
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_Combustible_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tipocombustible/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    Ext.define('Portadores.distribucion_combustible.Window', {
        extend: 'Ext.window.Window',
        width: 300,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: 300,
                    defaultType: 'textfield',
                    bodyPadding: 10,
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
                            xtype: 'textfield',
                            name: 'denominacion',
                            id: 'denominacion',
                            fieldLabel: 'Denominación',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        }, {
                            xtype: 'combobox',
                            name: 'tipo_combustible_id',
                            id: 'tipo_combustible_id',
                            fieldLabel: 'Tipo de combustible',
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
                        }, {
                            xtype: 'datefield',
                            name: 'fecha',
                            id: 'fecha',
                            flex: 0.5,
                            fieldLabel: 'Fecha',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            listeners: {
                                afterrender: function (This) {
                                    var dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                                    var anno = App.selected_year;
                                    var min = new Date((App.selected_month) + '/' + 1 + '/' + anno);
                                    var max = new Date((App.selected_month) + '/' + dias + '/' + anno);
                                    This.setMinValue(min);
                                    This.setMaxValue(max);
                                }
                            }
                        },
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Para Mes:',
                            format: 'm/Y',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'periodo',
                            id: 'periodo',
                            listeners: {
                                afterrender: function (This) {
                                    var date = new Date();
                                    This.setValue(date);
                                }
                            }
                        },
                        // {
                        //     xtype: 'textfield',
                        //     name: 'cantidad',
                        //     id: 'cantidad',
                        //     editable: false,
                        //     decimalSeparator: '.',
                        //     decimalPrecision: 2,
                        //     fieldLabel: 'Cantidad',
                        //     afterLabelTextTpl: [
                        //         '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        //     ],
                        //     value: 0,
                        //     minValue: 0
                        // }
                    ]
                }
            ];

            this.callParent();
        }
    });

    Ext.define('Portadores.distribucion_combustible_desglose.Window', {
        extend: 'Ext.window.Window',
        width: 500,
        modal: true,
        plain: true,
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
                        labelAlign: 'top',
                        msgTarget: 'side',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'fieldcontainer',
                            flex: 1,
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [{
                                xtype: 'combobox',
                                margin: '0 5 0 0',
                                width: 70,
                                name: 'vehiculoid',
                                id: 'vehiculoid',
                                readOnly: true,
                                fieldLabel: 'Vehículo',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                store: store_vehiculo,
                                displayField: 'matricula',
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                triggerAction: 'all',
                                emptyText: 'Seleccione el vehículo...',
                                selectOnFocus: true,
                                editable: true
                            },
                                {
                                    xtype: 'combobox',
                                    margin: '0 5 0 5',
                                    flex: .8,
                                    name: 'personaid',
                                    id: 'personaid',
                                    fieldLabel: 'Persona ',
                                    allowBlank: true,
                                    store: store_persona,
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione la persona...',
                                    selectOnFocus: true,
                                    editable: true

                                },
                                {
                                    xtype: 'combobox',
                                    margin: '0 0 0 5',
                                    width: 160,
                                    name: 'tarjetaid',
                                    id: 'tarjetaid',
                                    fieldLabel: 'Tarjeta',
                                    allowBlank: true,
                                    store: store_tarjeta,
                                    displayField: 'nro_tarjeta',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione la tarjeta...',
                                    selectOnFocus: true,
                                    editable: true
                                }]
                        },
                        {
                            xtype: 'fieldcontainer',
                            flex: 1,
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [{
                                xtype: 'numberfield',
                                margin: '0 5 0 0',
                                flex: 1,
                                name: 'cantidad',
                                id: 'cantidad',
                                decimalSeparator: '.',
                                decimalPrecision: 2,
                                fieldLabel: 'Carga  ',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                value: 0,
                                minValue: 0
                            }, {
                                xtype: 'numberfield',
                                margin: '0 0 0 5',
                                flex: 1,
                                name: 'preciocombustible',
                                id: 'preciocombustible',
                                decimalSeparator: '.',
                                decimalPrecision: 2,
                                fieldLabel: 'Precio Combustible  ',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                value: 0,
                                minValue: 0
                            }]
                        }, {
                            xtype: 'textfield',
                            flex: 1,
                            name: 'nota',
                            id: 'nota',
                            fieldLabel: 'Observaciones',
                            allowBlank: true
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });

    Ext.define('Portadores.eventualidades.Window', {
        extend: 'Ext.window.Window',
        width: 400,
        modal: true,
        plain: true,
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
                            xtype: 'fieldcontainer',
                            flex: 1,
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            border: false,
                            bodyPadding: 10,
                            items: [
                                {
                                    xtype: 'datefield',
                                    name: 'fecha',
                                    id: 'fecha',
                                    margin: '0 5 0 0',
                                    flex: 1,
                                    editable: false,
                                    value: new Date(),
                                    listeners: {
                                        afterrender: function (This) {
                                            var dias = App.getDaysInMonth(App.current_year, App.current_month);
                                            var anno = App.current_year;
                                            var min = new Date(App.current_month + '/' + 1 + '/' + anno);
                                            var max = new Date(App.current_month + '/' + dias + '/' + anno);
                                            This.setMinValue(min);
                                            This.setMaxValue(max);
                                        }
                                    },
                                    format: 'd/m/Y',
                                    fieldLabel: 'Fecha',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ]
                                },
                                {
                                    xtype: 'combobox',
                                    margin: '0 0 0 5',
                                    flex: 1,
                                    name: 'personaid',
                                    id: 'personaid',
                                    fieldLabel: 'Persona ',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    store: store_persona,
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione la persona...',
                                    selectOnFocus: true,
                                    editable: true
                                }]
                        },
                        {
                            xtype: 'fieldcontainer',
                            flex: 1,
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            border: false,
                            bodyPadding: 10,
                            items: [
                                {
                                    xtype: 'combobox',
                                    margin: '0 5 0 0',
                                    flex: 1,
                                    name: 'tarjetaid',
                                    id: 'tarjetaid',
                                    fieldLabel: 'Tarjeta',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    store: store_tarjeta,
                                    displayField: 'nro_tarjeta',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione la tarjeta...',
                                    selectOnFocus: true,
                                    editable: true
                                },
                                {
                                    xtype: 'combobox',
                                    margin: '0 0 0 5',
                                    flex: 1,
                                    name: 'vehiculoid',
                                    id: 'vehiculoid',
                                    fieldLabel: 'Vehículo',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    store: store_vehiculo,
                                    displayField: 'matricula',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione el vehículo...',
                                    selectOnFocus: true,
                                    editable: true
                                }]
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Cantidad(L)',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            decimalSeparator: '.',
                            decimalPrecision: 4,
                            value: 0,
                            minValue: 0,
                            name: 'cantidad',
                            id: 'cantidadid'
                        },
                        {
                            xtype: 'textarea',
                            flex: 1,
                            name: 'motivo',
                            id: 'motivo',
                            fieldLabel: 'Observaciones',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });

    let _btnMenu = Ext.create('Ext.button.MyButton', {
        id: 'plan_btn_menu',
        text: 'Menu',
        disabled: false,
        iconCls: 'fa fa-bars text-primary',
        width: 100,
        menu: [
            {
                text: 'Gestionar',
                menu: [
                    {
                        id: 'distribucion_combustible_btn_add',
                        text: 'Adicionar',
                        // iconCls: 'fas fa-plus-square text-primary',
                        glyph: 0xf0fe,
                        // width: 100,
                        handler: function (This, e) {
                            Ext.create('Portadores.distribucion_combustible.Window', {
                                title: 'Adicionar distribucion de combustible',
                                id: 'window_distribucion_combustible_id',
                                buttons: [
                                    {
                                        text: 'Aceptar',
                                        width: 70,
                                        handler: function () {
                                            var window = Ext.getCmp('window_distribucion_combustible_id');
                                            var form = window.down('form').getForm();
                                            if (form.isValid()) {
                                                window.hide();
                                                var obj = form.getValues();
                                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                                App.request('POST', App.buildURL('/portadores/distribucion/add'), obj, null, null,
                                                    function (response) { // success_callback
                                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                            form.reset();
                                                            Ext.getCmp('id_grid_distribucion').getStore().load();
                                                        } else {
                                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                                window.down('form').getForm().markInvalid(response.errors);
                                                            }
                                                        }
                                                        window.close();
                                                    },
                                                    function (response) { // failure_callback
                                                        window.show();
                                                    }
                                                );

                                                // var _result = App.PerformSyncServerRequest(Routing.generate('asignacioo'), form.getValues());
                                                // App.HideWaitMsg();
                                                // if (_result.success) {
                                                //     form.reset();
                                                //     Ext.getCmp('id_grid_distribucion').getStore().load();
                                                //     Ext.getCmp('id_grid_desglose').getStore().load();
                                                // }
                                                // window.show();
                                                // App.InfoMessage('Información', _result.message, _result.cls);
                                            }
                                        }
                                    },
                                    {
                                        text: 'Cancelar',
                                        width: 70,
                                        handler: function () {
                                            Ext.getCmp('window_distribucion_combustible_id').close()
                                        }
                                    }
                                ]
                            }).show();
                        }
                    },
                    {
                        id: 'distribucion_combustible_btn_mod',
                        text: 'Modificar',
                        // iconCls: 'fas fa-edit text-primary',
                        glyph: 0xf044,
                        disabled: true,
                        // width: 100,
                        handler: function (This, e) {
                            var selection = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected();
                            var window = Ext.create('Portadores.distribucion_combustible.Window', {
                                title: 'Modificar distribución',
                                id: 'window_distribucion_id',
                                listeners: {
                                    afterrender: function () {
                                        Ext.getCmp('tipo_combustible_id').setReadOnly(true);
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
                                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                                App.request('POST', App.buildURL('/portadores/distribucion/mod'), obj, null, null,
                                                    function (response) { // success_callback
                                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                            window.close();
                                                            Ext.getCmp('id_grid_distribucion').getStore().load();

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
                                            Ext.getCmp('window_distribucion_id').close();
                                        }
                                    }
                                ]
                            });

                            window.show();
                            window.down('form').loadRecord(selection);
                        }
                    },
                    {
                        id: 'distribucion_combustible_btn_del',
                        text: 'Eliminar',
                        // iconCls: 'fas fa-trash-alt text-primary',
                        glyph: 0xf2ed,
                        disabled: true,
                        // width: 100,
                        handler: function (This, e) {
                            selection = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected();
                            Ext.Msg.show({
                                title: '¿Eliminar Asignación?',
                                message: Ext.String.format('¿Está seguro que desea eliminar la distribución <span class="font-italic font-weight-bold">{0}</span>?', selection.data.denominacion),
                                buttons: Ext.Msg.YESNO,
                                icon: Ext.Msg.QUESTION,
                                fn: function (btn) {
                                    if (btn === 'yes') {
                                        var obj = {};
                                        obj.id = selection.data.id;
                                        obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                        App.request('DELETE', App.buildURL('/portadores/distribucion/del'), obj, null, null, function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                Ext.getCmp('id_grid_distribucion').getStore().reload();
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    }
                ]
            },
            {
                id: 'distribucion_combustible_btn_aprobar',
                text: 'Aprobar',
                // iconCls: 'fas fa-check-circle text-primary',
                glyph: 0xf058,
                disabled: true,
                handler: function (This, e) {
                    if(!Ext.getCmp('distribucion_combustible_desglose_btn_act').isDisabled()){
                        App.showAlert('Actualize los cambios del Desglose antes de aprobar la distribución', 'warning');
                        return;
                    }

                    var datos = Ext.getCmp('id_grid_desglose').getStore().data.items;
                    var suma = 0;
                    for (var i = 0; i < datos.length; i++) {
                        suma += datos[i].data.cambustible_asignado;
                    }

                    if(suma ===0 ){
                        App.showAlert('No se ha realizado el desglose de combustible', 'warning');
                        return;
                    }
                    if (Ext.getCmp('disposicion').getValue() < 0) {
                        App.showAlert('El combustible total distribuido excede la disponibilidad', 'warning');
                        return;
                    }

                    selection = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected();
                    Ext.Msg.show({
                        title: '¿Aprobar Distribución?',
                        message: Ext.String.format('¿Está seguro que desea aprobar la distribución <span class="font-italic font-weight-bold">{0}</span>?', selection.data.denominacion),
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                var obj = {};
                                obj.id = selection.data.id;
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/distribucion/aprobar'), obj, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('id_grid_distribucion').getStore().load();
                                    }
                                });
                            }
                        }
                    });
                }
            },
            {
                id: 'distribucion_combustible_btn_desaprobar',
                text: 'Desaprobar',
                // iconCls: 'fas fa-times-circle text-primary',
                glyph: 0xf057,
                disabled: true,
                handler: function (This, e) {
                    selection = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected();
                    Ext.Msg.show({
                        title: '¿Desprobar Distribución?',
                        message: Ext.String.format('¿Está seguro que desea desaprobar la distribución <span class="font-italic font-weight-bold">{0}</span>?', selection.data.denominacion),
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                var obj = {};
                                obj.id = selection.data.id;
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/distribucion/desaprobar'), obj, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('id_grid_distribucion').getStore().reload();
                                    }
                                });
                            }
                        }
                    });
                }
            },
            {
                id: 'distribucion_combustible_btn_print',
                text: 'Imprimir',
                // iconCls: 'fas fa-print text-primary',
                glyph: 0xf02f,
                disabled: true,
                // width: 100,
                handler: function (This, e) {
                    var seleccion = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected();
                    App.request('GET', App.buildURL('/portadores/distribucion/print'), {distribucion: seleccion.data.id, disponible: Ext.getCmp('disposicion').getValue()}, null, null,
                        function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                var newWindow = window.open('', '', 'width=1200, height=700'),
                                    document = newWindow.document.open();
                                document.write(response.html);
                                setTimeout(() => {
                                    newWindow.print();
                                }, 500);
                                document.close();

                            }
                        }, null, null, true
                    );

                    // App.ShowWaitMsg();
                    // var _result = App.PerformSyncServerRequest(Routing.generate('printDistribucionCombustible'), {
                    //     distribucion: seleccion.data.id
                    // });
                    // App.HideWaitMsg();
                    //
                    // if (_result.success) {
                    //     var newWindow = window.open('', 'center', 'width=1024, height=600'),
                    //         document = newWindow.document.open();
                    //
                    //     document.write(_result.html);
                    //     document.close();
                    //     newWindow.print();
                    // } else {
                    //     App.InfoMessage('Información', _result.message, _result.cls);
                    // }
                }
            }


        ],

    });

    var _btnEventualidades = Ext.create('Ext.button.MyButton', {
        id: 'distribucion_combustible_btn_eventualidades',
        text: 'Autorizo',
        width: 100,
        iconCls: 'fa fa-file-o',
        handler: function (This, e) {
            Ext.create('widget.window', {
                title: 'Autorizo de cambio o entrega de tarjeta',
                id: 'id_windows_eventualidades',
                //maximizable: false,
                width: 800,
                height: 500,
                modal: true,
                plain: true,
                resizable: false,
                layout: 'border',
                items: [
                    Ext.create('Ext.grid.Panel', {
                        region: 'center',
                        id: 'id_grid_eventualidades',
                        store: Ext.create('Ext.data.JsonStore', {
                            storeId: 'store_eventualidades',
                            fields: [
                                {name: 'id'},
                                {name: 'fecha'},
                                {name: 'personaid'},
                                {name: 'persona'},
                                {name: 'vehiculoid'},
                                {name: 'vehiculo'},
                                {name: 'tarjetaid'},
                                {name: 'tarjeta'},
                                {name: 'motivo'}
                            ],
                            proxy: {
                                type: 'ajax',
                                url: Routing.generate('loadEventualidad'),
                                reader: {
                                    rootProperty: 'rows'
                                }
                            },
                            autoLoad: true,
                            listeners: {
                                beforeload: function (This, operation, eOpts) {
                                    Ext.getCmp('id_grid_eventualidades').getSelectionModel().deselectAll();
                                }
                            }
                        }),
                        columns: [
                            {
                                text: '<strong>Fecha</strong>',
                                dataIndex: 'fecha',
                                flex: 1
                            }, {
                                text: '<strong>Persona</strong>',
                                dataIndex: 'persona',
                                flex: 1
                            }, {
                                text: '<strong>Matrícula</strong>',
                                dataIndex: 'vehiculo',
                                flex: 1
                            }, {
                                text: '<strong>Tarjeta</strong>',
                                dataIndex: 'tarjeta',
                                flex: 1
                            }
                        ],
                        listeners: {
                            selectionchange: function (This, selected, eOpts) {
                                if (Ext.getCmp('eventualidad_btn_del') != undefined)
                                    Ext.getCmp('eventualidad_btn_del').setDisabled(selected.length == 0);
                                if (Ext.getCmp('eventualidad_btn_print') != undefined)
                                    Ext.getCmp('eventualidad_btn_print').setDisabled(selected.length == 0);
                                if (Ext.getCmp('eventualidad_btn_export') != undefined)
                                    Ext.getCmp('eventualidad_btn_export').setDisabled(selected.length == 0);
                            }
                        },
                        tbar: {
                            xtype: 'toolbar',
                            title: 'Limpiar',
                            height: 36,
                            items: [
                                Ext.create('Ext.button.MyButton', {
                                    id: 'eventualidad_btn_add',
                                    text: 'Adicionar',
                                    iconCls: 'fa fa-plus-square-o fa-1_4',
                                    width: 100,
                                    handler: function (This, e) {
                                        Ext.create('Portadores.eventualidades.Window', {
                                            title: 'Adicionar autorizo',
                                            id: 'window_eventualidadadd_id',
                                            buttons: [
                                                {
                                                    text: 'Aceptar',
                                                    width: 70,
                                                    handler: function () {
                                                        var window = Ext.getCmp('window_eventualidadadd_id');
                                                        var form = window.down('form').getForm();
                                                        if (form.isValid()) {
                                                            App.ShowWaitMsg();
                                                            window.hide();
                                                            var _result = App.PerformSyncServerRequest(Routing.generate('addEventualidad'), form.getValues());
                                                            App.HideWaitMsg();
                                                            if (_result.success) {
                                                                form.reset();
                                                                Ext.getCmp('id_grid_eventualidades').getStore().load();
                                                            }
                                                            else {
                                                                form.markInvalid(_result.message);
                                                            }
                                                            window.show();
                                                            App.InfoMessage('Información', _result.message, _result.cls);
                                                        }
                                                    }
                                                },
                                                {
                                                    text: 'Cancelar',
                                                    width: 70,
                                                    handler: function () {
                                                        Ext.getCmp('window_eventualidadadd_id').close()
                                                    }
                                                }
                                            ]
                                        }).show();
                                    }
                                }),
                                Ext.create('Ext.button.MyButton', {
                                    id: 'eventualidad_btn_del',
                                    text: 'Eliminar',
                                    iconCls: 'fa fa-minus-square-o fa-1_4',
                                    disabled: true,
                                    width: 100,
                                    handler: function (This, e) {
                                        App.ConfirmMessage(function () {
                                            var selection = Ext.getCmp('id_grid_eventualidades').getSelectionModel().getLastSelected();
                                            App.ShowWaitMsg();
                                            var _result = App.PerformSyncServerRequest(Routing.generate('delEventualidad'), {id: selection.data.id});
                                            App.HideWaitMsg();
                                            App.InfoMessage('Información', _result.message, _result.cls);
                                            Ext.getCmp('id_grid_eventualidades').getStore().load();

                                        }, "¿Está seguro que desea eliminar el autorizo seleccionado'?");
                                    }
                                }),
                                Ext.create('Ext.button.MyButton', {
                                    id: 'eventualidad_btn_print',
                                    text: 'Imprimir',
                                    iconCls: 'fas fa-print text-primary',
                                    disabled: true,
                                    handler: function (This, e) {
                                        App.ShowWaitMsg();
                                        var selection = Ext.getCmp('id_grid_eventualidades').getSelectionModel().getLastSelected();
                                        var _result = App.PerformSyncServerRequest(Routing.generate('printEventualidad'), {
                                            id: selection.data.id
                                        });
                                        App.HideWaitMsg();
                                        if (_result.success) {
                                            var newWindow = window.open('', 'center', 'width=800, height=600'),
                                                document = newWindow.document.open();

                                            document.write(_result.html);
                                            document.close();
                                            newWindow.print();
                                        }
                                    }
                                }),
                                Ext.create('Ext.button.MyButton', {
                                    id: 'eventualidad_btn_export',
                                    text: 'Exportar',
                                    iconCls: 'fa fa-file-word-o',
                                    disabled: true,
                                    handler: function (This, e) {
                                        App.ShowWaitMsg();
                                        var selection = Ext.getCmp('id_grid_eventualidades').getSelectionModel().getLastSelected();
                                        var _result = App.PerformSyncServerRequest(Routing.generate('printEventualidad'), {
                                            id: selection.data.id
                                        });
                                        App.HideWaitMsg();
                                        if (_result.success) {
                                            window.open('data:application/msword,' + encodeURIComponent(_result.html));
                                        } else {
                                            App.InfoMessage('Información', _result.message, _result.cls);
                                        }
                                    }
                                })
                            ]
                        },
                        bbar: {
                            xtype: 'pagingtoolbar',
                            pageSize: 25,
                            store: Ext.getStore('store_eventualidades'),
                            displayInfo: true,
                            plugins: new Ext.ux.ProgressBarPager()
                        }
                    })
                ]


            }).show();
        }
    });

    var _tbar = Ext.getCmp('distribucion_combustible_tbar');
    _tbar.add('-');
    _tbar.add(_btnMenu);
    // _tbar.add('-');
    // _tbar.add(_btnEventualidades);
    _tbar.setHeight(36);


    var _btnModDesglose = Ext.create('Ext.button.MyButton', {
        id: 'distribucion_combustible_desglose_btn_mod',
        text: 'Modificar',
        iconCls: 'fa fa-pencil-square-o fa-1_4',
        width: 100,
        disabled: true,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_desglose').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.distribucion_combustible_desglose.Window', {
                title: 'Modificar asignación',
                id: 'window_distribucion_desglose_id',
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
                                obj.dist_comb_desg = selection.data.dist_comb_desg;
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
                                // App.ShowWaitMsg();
                                // var obj = form.getValues();
                                //
                                // var _result = App.PerformSyncServerRequest(Routing.generate('modDistribucionCombustibleDesglose'), obj);
                                // App.HideWaitMsg();
                                // if (_result.success) {
                                //     window.close();
                                //     Ext.getCmp('id_grid_desglose').getStore().load();
                                // }
                                // App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_distribucion_desglose_id').close();
                        }
                    }
                ]

            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btnAct = Ext.create('Ext.button.MyButton', {
        id: 'distribucion_combustible_desglose_btn_act',
        iconCls: 'fas fas fa-check-square text-primary',
        text: 'Actualizar',
        width: 100,
        disabled: true,
        handler: function (This, e) {
            if (Ext.getCmp('disposicion').getValue() < 0) {
                App.showAlert('El total de combustible distribuido excede la Disponibilidad', 'warning');
                return;
            }

            if (Ext.getStore('store_distribucion_combustible_desglose').isFiltered())
                Ext.getStore('store_distribucion_combustible_desglose').clearFilter();

            var store = Ext.getCmp('id_grid_desglose').getStore();
            var send = [];
            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });
            var obj = {};
            obj.store = Ext.encode(send);
            obj.distribucion_combustible_id = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected().data.id;
            App.request('POST', App.buildURL('/portadores/distribucion/actualizarDesglose'), obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        This.setStyle('borderColor', '#d8d8d8');
                        This.disable();
                        _btnAtras.setDisabled(true);
                        Ext.getCmp('find_button_vehiculo').setValue(null);
                        Ext.getCmp('id_grid_desglose').getStore().load();
                    } else {
                        if (response && response.hasOwnProperty('errors') && response.errors) {
                            window.down('form').getForm().markInvalid(response.errors);
                        }
                    }
                }
            );
            // App.ShowWaitMsg();
            // var store = Ext.getCmp('id_grid_desglose').getStore();
            // var send = [];
            // Ext.Array.each(store.data.items, function (valor) {
            //     send.push(valor.data);
            // });
            // var store_send = Ext.encode(send);
            // var _result = App.PerformSyncServerRequest(Routing.generate('actDesgloseComb'), {store: store_send});
            // App.HideWaitMsg();
            // if (_result.success) {
            //     This.setStyle('borderColor', '#d8d8d8');
            //     This.disable();
            //     Ext.getCmp('id_grid_desglose').getStore().load();
            // }
            // App.InfoMessage('Información', _result.message, _result.cls);
        }
    });

    var _btnAtras = Ext.create('Ext.button.MyButton', {
        id: 'distribucion_combustible_desglose_btn_back',
        text: 'Deshacer',
        iconCls: 'fas fas fa-undo-alt text-primary',
        width: 100,
        disabled: true,
        handler: function (This, e) {
            This.setDisabled(true);
            _btnAct.setDisabled(true);
            _btnAct.setStyle('borderColor', '#d8d8d8');
            Ext.getCmp('id_grid_desglose').getStore().reload();
        }
    });

    var _btnDes = Ext.create('Ext.button.MyButton', {
        id: 'distribucion_combustible_desglose_btn_des',
        text: 'Reiniciar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Desglose?',
                message: 'Esta acci&oacute;n elimininará el desglose de combustible.<br>¿Está seguro que desea realizar esta acci&oacute;n?.',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/distribucion/delDesglose'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_distribucion').getStore().load();
                                Ext.getCmp('id_grid_desglose').getSelectionModel().deselectAll();
                                Ext.getCmp('id_grid_desglose').getStore().removeAll();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbarDesglose = Ext.getCmp('grid_desglose_tbar');
    // _tbarDesglose.add(_btnModDesglose);
    // _tbarDesglose.add('->');
    // _tbarDesglose.add('-');
    _tbarDesglose.add(_btnAct);
    _tbarDesglose.add('-');
    _tbarDesglose.add(_btnAtras);
    _tbarDesglose.add('-');
    _tbarDesglose.add(_btnDes);
    _tbarDesglose.add('->');
    _tbarDesglose.add({
        xtype: 'textfield',
        fieldLabel: '<b>Disponibilidad</b>',
        id: 'disposicion',
        editable: false,
        width: 137,
        labelWidth:80,
        fieldStyle: 'font-weight: bold; color: green',
        listeners: {
            change: function (This, newValue, oldValue, eOpts) {
                if (newValue == 0)
                    This.setFieldStyle('font-weight: bold; color: black',)
                else if (newValue < 0)
                    This.setFieldStyle('font-weight: bold; color: red',)
                else
                    This.setFieldStyle('font-weight: bold; color: green',)
            }
        }
    });
    _tbarDesglose.setHeight(36);

});


