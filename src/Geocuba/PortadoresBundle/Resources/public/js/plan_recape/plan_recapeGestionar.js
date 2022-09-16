/**
 * Created by kireny on 06/07/2017.
 */
Ext.onReady(function () {

    var store_vehiculo_plan = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'vehiculo_id'},
            {name: 'matricula'},
            {name: 'marca'},
            {name: 'medidas'},
            {name: 'fecha_rotacion'}
        ],

        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/plan_recape/loadVehiculos'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    id_recape: Ext.getCmp('id_grid_plan_recape').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    var store_vehiculo_unidad = Ext.create('Ext.data.JsonStore', {
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

    var _store_Mes = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_mes',
        fields: [
            {name: 'id_mes'},
            {name: 'mes'}
        ],
        data: [
            {id_mes: '1', mes: 'Enero'},
            {id_mes: '2', mes: 'Febrero'},
            {id_mes: '3', mes: 'Marzo'},
            {id_mes: '4', mes: 'Abril'},
            {id_mes: '5', mes: 'Mayo'},
            {id_mes: '6', mes: 'Junio'},
            {id_mes: '7', mes: 'Julio'},
            {id_mes: '8', mes: 'Agosto'},
            {id_mes: '9', mes: 'Septiembre'},
            {id_mes: '10', mes: 'Octubre'},
            {id_mes: '11', mes: 'Noviembre'},
            {id_mes: '12', mes: 'Diciembre'}
        ],
        proxy: {
            type: 'memory',
            reader: {
                type: 'json'
            }
        }
    });

    Ext.define('Portadores.plan_recape.Window', {
        extend: 'Ext.window.Window',
        width: 1000,
        height: 400,
        modal: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    bodyPadding: 15,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top'
                    },
                    items: [
                        {
                            xtype: 'container',
                            layout: {
                                type: 'hbox'
                            },
                            margin: '0 0 0 0',
                            items: [
                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'hbox'
                                    },
                                    margin: '0 0 0 0',
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            name: 'nombre',
                                            id: 'nombre',
                                            fieldLabel: 'Nombre del Plan:',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            allowBlank: false,
                                            width: 300,
                                            margin: '0 10 0 10'
                                        },
                                        {
                                            xtype: 'fieldset',
                                            title: 'Información',
                                            margin: '0 0 0 50',
                                            height: 100,
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'hbox'
                                                    },
                                                    margin: '0 0 0 0',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'cant_vehiculos',
                                                            id: 'cant_vehiculos',
                                                            fieldLabel: 'Cant Vehiculos',
                                                            editable: false,
                                                            width: 150,
                                                            margin: '-5 5 0 0',
                                                            value: 0,
                                                        },
                                                        // {
                                                        //     xtype: 'numberfield',
                                                        //     name: 'neumaticos_vehiculos',
                                                        //     id: 'neumaticos_vehiculos',
                                                        //     fieldLabel: 'Neumáticos x Vehículos',
                                                        //     minValue: 0,
                                                        //     value: 0,
                                                        //     width: 150,
                                                        //     margin: '-5 5 0 0',
                                                        //     listeners: {
                                                        //         change: function (This, newValue) {
                                                        //             var total_neumaticos = newValue * Ext.getCmp('cant_vehiculos').getValue();
                                                        //             Ext.getCmp('total_neumaticos').setValue(total_neumaticos);
                                                        //             var neumaticos_recapar = total_neumaticos * 32 / 100;
                                                        //             Ext.getCmp('neumaticos_recapar').setValue(neumaticos_recapar);
                                                        //         }
                                                        //     }
                                                        // },
                                                        // {
                                                        //     xtype: 'textfield',
                                                        //     name: 'total_neumaticos',
                                                        //     id: 'total_neumaticos',
                                                        //     fieldLabel: 'Total de Neumaticos',
                                                        //     editable: false,
                                                        //     width: 150,
                                                        //     margin: '-5 5 0 0'
                                                        // },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'neumaticos_recapar',
                                                            id: 'neumaticos_recapar',
                                                            fieldLabel: 'Neumáticos a Recapar',
                                                            editable: false,
                                                            width: 150,
                                                            margin: '-5 5 0 0'
                                                        }
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            xtype: 'gridpanel',
                            id: 'gridVehiculosRecaparId',
                            height: 350,
                            width: 800,
                            store: Ext.create('Ext.data.JsonStore', {
                                storeId: 'id_store_vehiculo_recapar',
                                fields: [
                                    {name: 'id'},
                                    {name: 'vehiculo_id'},
                                    {name: 'matricula'},
                                    {name: 'marca'},
                                    {name: 'medidas'},
                                    {name: 'fecha_rotacion'}
                                ],
                                proxy: {
                                    type: 'ajax',
                                    url: App.buildURL('/portadores/plan_recape/loadVehiculos'),
                                    reader: {
                                        rootProperty: 'rows'
                                    }
                                },
                                listeners: {
                                    beforeload: function (This, operation, eOpts) {
                                        operation.setParams({
                                            id_recape: Ext.getCmp('id_grid_plan_recape').getSelectionModel().getLastSelected().data.id
                                        });
                                    },
                                    load: function (This) {
                                        Ext.getCmp('id_btn_add').enable();
                                        calcCantidadVehiculos(This, Ext.getCmp('cant_vehiculos'));
                                        calcNeumaticosRecapar(This, Ext.getCmp('neumaticos_recapar'));
                                    }
                                }
                            }),
                            forceFit: true,
                            enableColumnHide: false,
                            plugins: {
                                ptype: 'cellediting',
                                clicksToEdit: 1,
                                listeners: {
                                    edit: function (This, e) {
                                        let store_vehiculos_rec = Ext.getCmp('gridVehiculosRecaparId').getStore();
                                        if(e.colIdx === 2){
                                            calcNeumaticosRecapar(store_vehiculos_rec, Ext.getCmp('neumaticos_recapar'));
                                        }
                                    }
                                }
                            },
                            columns: [
                                {
                                    text: '<strong>Mes</strong>',
                                    flex: 15,
                                    dataIndex: 'mes',
                                    editor: Ext.create('Ext.form.field.ComboBox', {
                                        typeAhead: true,
                                        triggerAction: 'all',
                                        store: _store_Mes,
                                        displayField: 'mes',
                                        forceSelection: true,
                                    })
                                },
                                {
                                    text: '<strong>Vehículo</strong>',
                                    flex: 15,
                                    dataIndex: 'matricula',
                                    editor: Ext.create('Ext.form.field.ComboBox', {
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        displayField: 'matricula',
                                        value: 'id',
                                        store: store_vehiculo_unidad
                                    })
                                },
                                {
                                    text: '<strong>Cant Neumáticos</strong>',
                                    flex: 15,
                                    dataIndex: 'cant_neumaticos',
                                    editor: Ext.create('Ext.form.field.Number', {
                                        typeAhead: true,
                                        triggerAction: 'all',
                                        minValue: 0,
                                        allowBlank: false
                                    })
                                },
                                {
                                    text: '<strong>Marca Neumáticos</strong>',
                                    flex: 15,
                                    dataIndex: 'marca',
                                    editor: Ext.create('Ext.form.field.Text', {
                                            typeAhead: true,
                                            triggerAction: 'all',
                                            allowBlank: false
                                        })
                                },
                                {
                                    text: '<strong>Medidas Neumáticos</strong>',
                                    flex: 15,
                                    dataIndex: 'medidas',
                                    editor: Ext.create('Ext.form.field.Number', {
                                        typeAhead: true,
                                        triggerAction: 'all',
                                        minValue: 0,
                                        allowBlank: false
                                    }),
                                },
                                {
                                    text: '<strong>Fecha de Rotación</strong>',
                                    flex: 15,
                                    dataIndex: 'fecha_rotacion',
                                    format: 'd/m/Y',
                                    editor: Ext.create('Ext.form.field.Date',{format: 'd/m/Y'
                                    }),
                                    renderer: function (value, eOpts) {
                                        let date = value;
                                        if (typeof(value) === 'string'){
                                            let arr = value.split('/');
                                            date = new Date(parseInt(arr[2]), parseInt(arr[1]), parseInt(arr[0]))
                                        }
                                        return Ext.Date.format(date, 'd/m/Y');
                                    }
                                }

                            ],
                            tbar: [
                                {
                                    text: 'Adicionar',
                                    id: 'id_btn_add',
                                    disabled:true,
                                    iconCls: 'fas fa-plus-square text-primary',
                                    handler: function () {
                                        Ext.getCmp('gridVehiculosRecaparId').getStore().add({
                                            id: '',
                                            mes: 'Seleccione el mes',
                                            matricula: 'Seleccione el vehículo',
                                            fecha_rotacion: Ext.Date.format(new Date(), 'd/m/Y')
                                        });
                                        calcCantidadVehiculos(Ext.getCmp('gridVehiculosRecaparId').getStore(), Ext.getCmp('cant_vehiculos'));
                                    }
                                },
                                '-',
                                {
                                    text: 'Eliminar',
                                    id: 'id_btn_del',
                                    disabled:true,
                                    iconCls: 'fas fa-trash-alt text-primary',
                                    handler: function () {
                                        var record = Ext.getCmp('gridVehiculosRecaparId').getSelectionModel().getLastSelected();
                                        Ext.getCmp('gridVehiculosRecaparId').getStore().remove(record);
                                        Ext.getCmp('cant_vehiculos').setValue(Ext.getCmp('gridVehiculosRecaparId').getStore().getCount());
                                    }
                                }
                            ],
                            listeners: {
                                selectionchange: function (This, selected) {
                                    Ext.getCmp('id_btn_del').setDisabled(selected.length == 0);
                                }
                            }
                        }
                    ]
                }];
            this.callParent();
        }
    });

    function calcNeumaticosRecapar(store, cmp) {
        let cant_neumaticos = store.getCount() > 1 ? store.data.items.reduce((total, item) => {
            return parseInt(total.data.cant_neumaticos) + parseInt(item.data.cant_neumaticos);
        }) : store.data.items[0].data.cant_neumaticos;
        cmp.setValue(cant_neumaticos);
    }
    function calcCantidadVehiculos(store, cmp) {
        cmp.setValue(store.getCount());
    }


    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'plan_recape_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        disabled: true,
        handler: function (This, e) {
            Ext.create('Portadores.plan_recape.Window', {
                title: 'Realizar Plan de Recape',
                id: 'window_plan_recape_id',
                listeners:{
                    afterrender: function () {
                        Ext.getCmp('id_btn_add').enable();
                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_plan_recape_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                var store = Ext.getCmp('gridVehiculosRecaparId').getStore();
                                var send_vehiculos = [];
                                Ext.Array.each(store.data.items, function (valor) {
                                    send_vehiculos.push(valor.data);
                                });
                                obj.send_vehiculos = Ext.encode(send_vehiculos);
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/plan_recape/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_plan_recape').getStore().load();
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
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_plan_recape_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'plan_recape_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_plan_recape').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.plan_recape.Window', {
                title: 'Modificar Plan de Recape',
                id: 'window_plan_recape_id',
                listeners: {
                    beforerender: function () {
                        Ext.getStore('id_store_vehiculo_recapar').load();

                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            var store = Ext.getCmp('gridVehiculosRecaparId').getStore();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                var send_vehiculos = [];
                                Ext.Array.each(store.data.items, function (valor) {
                                    send_vehiculos.push(valor.data);
                                });
                                obj.id = selection.data.id;
                                obj.send_vehiculos = Ext.encode(send_vehiculos);
                                App.request('POST', App.buildURL('/portadores/plan_recape/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_plan_recape').getStore().load();
                                            Ext.getCmp('id_grid_vehiculos_recape').getStore().load();
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
                            Ext.getCmp('window_plan_recape_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'plan_recape_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_plan_recape').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Plan de Recape?',
                message: Ext.String.format('¿Está seguro que desea eliminar el plan de recape <span class="font-italic font-weight-bold">{0}</span>?', selection.data.nombre),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/plan_recape/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_plan_recape').getStore().load();
                                Ext.getCmp('id_grid_vehiculos_recape').getSelectionModel().deselectAll();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('plan_recape_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});