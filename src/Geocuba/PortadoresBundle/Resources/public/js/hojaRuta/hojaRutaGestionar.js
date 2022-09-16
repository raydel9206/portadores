/**
 * Created by pfcadenas on 19/05/2017.
 */
Ext.onReady(function () {
    var store_vehiculos_unidad = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo_unidad',
        fields: [
            {name: 'id'},
            {name: 'matricula'},
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

    var store_persona_unidad = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_persona_unidad',
        fields: [{name: 'id'}, {name: 'nombre'}],
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
            },
        }
    });

    Ext.define('CombustibleKilometros', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'fecha', type: 'date'},
            {name: 'nro_tarjeta', type: 'string'},
            {name: 'kilometraje', type: 'number'},
            {name: 'comb_abast', type: 'number'},
            {name: 'comb_est_tanke', type: 'number'}
        ]
    });

    var tipo_manteniento = null;

    Ext.define('Portadores.hoja_ruta.Window', {
        extend: 'Ext.window.Window',
        width: 700,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    bodyPadding: 5,
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
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    bodyPadding: 5,
                                    margin: '10 10 0 10',
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'No. Hoja de Ruta',
                                            name: 'numerohoja',
                                            id: 'numerohoja',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ]
                                        }, {
                                            xtype: 'combobox',
                                            name: 'vehiculoid',
                                            id: 'vehiculoid',
                                            fieldLabel: 'Vehículo',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            store: store_vehiculos_unidad,
                                            displayField: 'matricula',
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione el vehículo...',
                                            selectOnFocus: true,
                                            editable: true
                                        }, {
                                            xtype: 'textfield',
                                            fieldLabel: 'Entidad',
                                            name: 'entidad',
                                            id: 'entidad',
                                            editable: false,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ]
                                        }, {
                                            xtype: 'textfield',
                                            fieldLabel: 'Servicio Autorizado',
                                            name: 'servicioautorizado',
                                            id: 'servicioautorizado',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ]
                                        }
                                    ]
                                }, {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    bodyPadding: 5,
                                    margin: '10 10 0 10',
                                    items: [
                                        {
                                            xtype: 'datefield',
                                            name: 'fecha',
                                            id: 'fecha',
                                            flex: 0.5,
                                            margin: '0 5 0 0',
                                            editable: false,
                                            value: new Date(),
                                            listeners: {
                                                afterrender: function (This) {
                                                    var dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                                                    var anno = App.current_year;
                                                    var min = new Date(App.selected_month + '/' + 1 + '/' + anno);
                                                    var max = new Date(App.selected_month + '/' + dias + '/' + anno);
                                                    This.setMinValue(min);
                                                    This.setMaxValue(max);
                                                }
                                            },
                                            format: 'd/m/Y',
                                            fieldLabel: 'Fecha',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ]
                                        }, {
                                            xtype: 'numberfield',
                                            fieldLabel: 'Capacidad',
                                            minValue: 0,
                                            value: 0,
                                            name: 'capacidad',
                                            id: 'capacidad',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ]
                                        }, {
                                            xtype: 'textfield',
                                            fieldLabel: 'Organismo',
                                            name: 'organismo',
                                            id: 'organismo',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ]
                                        }, {
                                            xtype: 'textfield',
                                            fieldLabel: 'Kms Disponibles',
                                            name: 'kmsdisponible',
                                            id: 'kmsdisponible',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ]
                                        }
                                    ]
                                }, {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    bodyPadding: 5,
                                    margin: '10 10 0 10',
                                    items: [{
                                        xtype: 'combobox',
                                        name: 'habilitadaporid',
                                        id: 'habilitadaporid',
                                        fieldLabel: 'Habilitada por',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        store: store_persona_unidad,
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione la persona...',
                                        selectOnFocus: true,
                                        editable: true
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: 'Número',
                                        minValue: 0,
                                        value: 0,
                                        name: 'numero',
                                        id: 'numero',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ]
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: 'Lugar de Parqueo',
                                        name: 'lugarparqueo',
                                        id: 'lugarparqueo',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ]
                                    }
                                    ]
                                }
                            ]
                        }, {
                            xtype: 'textarea',
                            fieldLabel: 'Observaciones',
                            name: 'observaciones',
                            id: 'observaciones',
                            margin: '0 10 10 10',
                            allowBlank: true
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'hoja_ruta_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        handler: function (This, e) {
            Ext.create('Portadores.hoja_ruta.Window', {
                title: 'Adicionar Hoja de Ruta',
                id: 'window_hoja_ruta_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_hoja_ruta_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/hoja_ruta/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_hoja_ruta').getStore().loadPage(1);
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
                            Ext.getCmp('window_hoja_ruta_id').close()
                        }
                    }
                ],
                listeners: {
                    boxready: function (This) {
                        let selected = Ext.getCmp('arbolunidades').getSelection()[0];
                        Ext.getCmp('entidad').setValue(selected.data.nombre);
                    }
                }
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'hoja_ruta_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_hoja_ruta').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.hoja_ruta.Window', {
                title: 'Modificar el hoja_ruta',
                id: 'window_hoja_ruta_id',
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
                                App.request('POST', App.buildURL('/portadores/hoja_ruta/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_hoja_ruta').getStore().loadPage(1);
                                            window.close();
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
                            Ext.getCmp('window_hoja_ruta_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'hoja_ruta_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_hoja_ruta').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Hoja de Ruta?',
                message: Ext.String.format('¿Está seguro que desea eliminar la hoja de ruta <span class="font-italic font-weight-bold">{0}</span>?', selection.data.numerohoja),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/hoja_ruta/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_hoja_ruta').getStore().reload();
                            }
                        });
                    }
                }
            });


        }
    });

    var _tbar = Ext.getCmp('hoja_ruta_tbar');
    _tbar.add(_btnAdd);
    _tbar.add(_btnMod);
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);


    Ext.define('Portadores.hoja_ruta_desglose.Window', {
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
                    bodyPadding: 5,
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
                            xtype: 'datefield',
                            name: 'fecha',
                            id: 'fecha',
                            flex: 1,
                            margin: '0 10 0 10',
                            editable: false,
                            value: new Date(),
                            listeners: {
                                afterrender: function (This) {
                                    var selected = Ext.getCmp('id_grid_hoja_ruta').getSelectionModel().getLastSelected();
                                    var dias = App.getDaysInMonth(App.current_year, App.current_month);
                                    var anno = App.current_year;
                                    // var min = new Date(selected.data.fecha);
                                    // var max = new Date(App.current_month + '/' + dias + '/' + anno);
                                    This.setMinValue(selected.data.fecha);
                                    // This.setMaxValue(max);
                                }
                            },
                            format: 'd/m/Y',
                            fieldLabel: 'Fecha',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        },
                        {
                            xtype: 'fieldcontainer',
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    bodyPadding: 5,
                                    margin: '10 10 0 10',
                                    items: [
                                        {
                                            xtype: 'timefield',
                                            increment: 5,
                                            flex: 1,
                                            name: 'horasalida',
                                            id: 'horasalida',
                                            fieldLabel: 'Hora de Salida',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            listeners: {
                                                select: function (This) {
                                                    Ext.getCmp('horallegada').setMinValue(This.value);
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'numberfield',
                                            fieldLabel: 'Kms Salida',
                                            name: 'kmssalida',
                                            id: 'kmssalida',
                                            value: 0,
                                            minValue: 0,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            listeners: {
                                                change: function (This) {
                                                    Ext.getCmp('kmsllegada').setMinValue(This.value);
                                                }
                                            }
                                        }
                                    ]
                                },
                                {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    bodyPadding: 5,
                                    margin: '10 10 0 10',
                                    items: [
                                        {
                                            xtype: 'timefield',
                                            increment: 5,
                                            flex: 1,
                                            name: 'horallegada',
                                            id: 'horallegada',
                                            fieldLabel: 'Hora de Llegada',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            listeners: {
                                                select: function (This) {
                                                    Ext.getCmp('horasalida').setMaxValue(This.value);
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'numberfield',
                                            fieldLabel: 'Kms Llegada',
                                            name: 'kmsllegada',
                                            id: 'kmsllegada',
                                            value: 0,
                                            minValue: 0,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            listeners: {
                                                change: function (This) {
                                                    Ext.getCmp('kmssalida').setMaxValue(This.value);
                                                }
                                            }
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAddDesglose = Ext.create('Ext.button.MyButton', {
        id: 'hoja_ruta_desglose_btn_add',
        text: 'Adicionar',
        disabled: true,
        iconCls: 'fas fa-plus-square text-primary',
        handler: function (This, e) {
            Ext.create('Portadores.hoja_ruta_desglose.Window', {
                title: 'Adicionar Rutas',
                id: 'window_hoja_ruta_desglose_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var selection = Ext.getCmp('id_grid_hoja_ruta').getSelectionModel().getLastSelected();
                            var window = Ext.getCmp('window_hoja_ruta_desglose_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.idhojaruta = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/hoja_ruta/desglose/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_desglose').getStore().reload();
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
                            Ext.getCmp('window_hoja_ruta_desglose_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btn_DelDesglose = Ext.create('Ext.button.MyButton', {
        id: 'hoja_ruta_desglose_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_desglose').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Hoja de Ruta?',
                message: '¿Está seguro que desea eliminar la Ruta?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/hoja_ruta/desglose/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_desglose').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbarDesglose = Ext.getCmp('hoja_ruta_desglose_tbar');
    _tbarDesglose.add(_btnAddDesglose);
    _tbarDesglose.add(_btn_DelDesglose);
    _tbarDesglose.setHeight(36);

    Ext.define('Portadores.conductor.Window', {
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
                            store: store_persona_unidad,
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
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });

    var _btnAddConductor = Ext.create('Ext.button.MyButton', {
        id: 'conductor_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        disabled: true,
        handler: function () {
            Ext.create('Portadores.conductor.Window', {
                title: 'Asignar conductor',
                id: 'window_conductor_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_conductor_id');
                            var form = window.down('form').getForm();
                            var selection = Ext.getCmp('id_grid_hoja_ruta').getSelectionModel().getLastSelected();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.idhojaruta = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/hoja_ruta/conductor/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_conductor').getStore().load();
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
                            Ext.getCmp('window_conductor_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btn_DelConductor = Ext.create('Ext.button.MyButton', {
        id: 'conductor_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_conductor').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Conductor?',
                message: '¿Está seguro que desea eliminar el conductor?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/hoja_ruta/conductor/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_conductor').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbarConductor = Ext.getCmp('grid_conductor_tbar');
    _tbarConductor.add(_btnAddConductor);
    _tbarConductor.add(_btn_DelConductor);
    _tbarConductor.setHeight(36);
});


