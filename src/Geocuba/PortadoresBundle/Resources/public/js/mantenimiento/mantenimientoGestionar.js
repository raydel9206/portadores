Ext.onReady(function () {

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipomantenimiento_mantenimiento',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/mantenimiento/loadTipoMant'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('grid_vehiculos').getSelectionModel().getLastSelected();
                operation.setParams({
                    vehiculoid: Ext.getCmp('grid_vehiculos').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo_mantenimiento',
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

    Ext.define('Portadores.mantenimiento.Window', {
        extend: 'Ext.window.Window',
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
                            flex: 1,
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            border: false,
                            items: [
                                {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    border: false,
                                    bodyPadding: 5,
                                    margin: '10 10 0 10',
                                    items: [
                                        {
                                            xtype: 'datefield',
                                            name: 'fecha',
                                            id: 'fecha',
                                            fieldLabel: 'Fecha',
                                            value: new Date(),
                                            // minValue: Ext.getStore('ultimo').getData().fecha,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ]
                                        },
                                        {
                                            xtype: 'numberfield',
                                            name: 'kilometraje',
                                            id: 'kilometraje',
                                            fieldLabel: 'Kilometraje',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            value: 0,
                                            minValue: 0
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
                                    border: false,
                                    bodyPadding: 5,
                                    margin: '10 10 0 10',
                                    items: [
                                        {
                                            xtype: 'hidden',
                                            id: 'nvehiculoid',
                                            name: 'nvehiculoid',
                                        }, {
                                            xtype: 'combobox',
                                            name: 'tipo_mantenimientoid',
                                            id: 'tipo_mantenimientoid',
                                            fieldLabel: 'Tipo de mantenimiento',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            store: Ext.getStore('id_store_tipomantenimiento_mantenimiento'),
                                            // value: Ext.getStore('id_store_tipomantenimiento_mantenimiento').getData().items[0].id,
                                            displayField: 'nombre',
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione el Tipo de Mantenimiento',
                                            selectOnFocus: true,
                                            editable: true
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            xtype: 'textareafield',
                            width: 500,
                            bodyPadding: 5,
                            margin: '0 10 10 10',
                            name: 'observaciones',
                            id: 'observaciones',
                            fieldLabel: 'Observaciones',
                            allowBlank: true
                        }
                    ],
                    listeners: {
                        afterrender: function (This) {
                            var url = App.buildURL('/portadores/mantenimiento/loadUltimo');
                            var fecha = null;
                            var obj = {};
                            obj.nvehiculoid = Ext.getCmp('grid_vehiculos').getSelectionModel().getLastSelected().data.id;
                            App.request('GET', url, obj, null, null,
                                function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        fecha = response.fecha;
                                    }
                                },
                                function (response) {
                                }, null, true);

                            setTimeout(() => {
                                Ext.getCmp('fecha').setMinValue(fecha);
                            }, 1500);

                            let km = Ext.getCmp('grid_vehiculos').getSelectionModel().getLastSelected().data.odometro;
                            Ext.getCmp('kilometraje').setValue((typeof km === 'string') ? 0 : km);
                            Ext.getCmp('nvehiculoid').setValue(Ext.getCmp('grid_vehiculos').getSelectionModel().getLastSelected().data.id);
                        }
                    }
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'mantenimiento_btn_add',
        text: 'Adicionar',
        disabled: true,
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.mantenimiento.Window', {
                title: 'Adicionar mantenimiento',
                id: 'window_mantenimiento_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_mantenimiento_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/mantenimiento/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_mantenimiento').getStore().loadPage(1);
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
                            Ext.getCmp('window_mantenimiento_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'mantenimiento_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_mantenimiento').getSelectionModel().getLastSelected();
            var selectionVehiculo = Ext.getCmp('grid_vehiculos').getSelectionModel();
            var idVehiculo = selectionVehiculo.getLastSelected();
            var storeVehiculo = Ext.getCmp('grid_vehiculos').getStore();
            var rowExpander = Ext.getCmp('grid_vehiculos').plugins[0];

            var window = Ext.create('Portadores.mantenimiento.Window', {
                title: 'Modificar mantenimiento',
                id: 'window_mantenimiento_id',
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
                                App.request('POST', App.buildURL('/portadores/mantenimiento/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_mantenimiento').getStore().loadPage(1);
                                            Ext.getCmp('grid_vehiculos').getStore().load(
                                                {
                                                    /*Todo Mantener el ultimo elemento seleccionado despues de recargar el store actualizando los valores*/
                                                    callback: function (records, operation, success) {
                                                        if (success) {
                                                            let newrecord = '';
                                                            let newIndex = 0;
                                                            Ext.Array.each(records, function (record, index) {
                                                                if (idVehiculo.data.id === record.data.id) {
                                                                    newrecord = record;
                                                                    newIndex = index;
                                                                }
                                                            });
                                                            selectionVehiculo.select(newrecord);
                                                            rowExpander.toggleRow(newIndex, storeVehiculo.getAt(newIndex));
                                                        }
                                                    }
                                                }
                                            );
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
                            Ext.getCmp('window_mantenimiento_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'mantenimiento_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_mantenimiento').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Mantenimiento?',
                message: Ext.String.format('¿Está seguro que desea eliminar el mantenimiento para el vehículo <span class="font-italic font-weight-bold">{0}</span>?', selection.data.nvehiculomatricula),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/mantenimiento/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_mantenimiento').getStore().loadPage(1);
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('mantenimiento_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);

});