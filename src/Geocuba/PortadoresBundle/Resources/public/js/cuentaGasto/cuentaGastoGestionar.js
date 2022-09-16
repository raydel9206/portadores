/**
 * Created by orlando on 06/10/2015.
 */

Ext.onReady(function () {

    let store_centrocosto = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_centro_costo',
        fields: [
            {name: 'id'},
            {name: 'codigo'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/centrocosto/loadCombo'),
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

    let store_elementogasto = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_elemento_gasto',
        fields: [
            {name: 'id'},
            {name: 'codigo'},
            {name: 'descripcion'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/elementogasto/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners:{
            beforeload: function (This, operation, eOpts) {
                //TODO NO funciona el before load
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }

    });

    let store_detallegasto = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_detalle_gasto',
        fields: [
            {name: 'id'},
            {name: 'codigo'},
            {name: 'descripcion'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/detallegasto/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });


    Ext.define('Portadores.cuentagasto.Window', {
        extend: 'Ext.window.Window',
        width: 330,
        modal: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
                    bodyPadding: 5,
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
                            xtype: 'fieldcontainer',
                            columnWidth: 0.5,
                            //margin: '0 0 0 10',
                            // margin: '10 10 10 10',
                            title: ' ',
                            collapsible: false,

                            defaults: {anchor: '100%'},
                            layout: 'hbox',
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'No. Cuenta',
                                    id: 'nocuenta1_id',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    maxLength: 3,
                                    enforceMaxLength: true,
                                    maskRe: /[0-9]/,
                                    regex: /^[0-9]{3}$/,
                                    labelWidth: 125,
                                    labelAlign: 'right',
                                    // width: 160,
                                    flex: .6,
                                    name: 'no1_cuenta'
                                },
                                {
                                    xtype: 'textfield',
                                    margin: '0 0 0 2',
                                    id: 'nocuenta2_id',
                                    editable:false,
                                    disabled:true,
                                    allowBlank:true,
                                    // width: 200,
                                    flex: .4,
                                    name: 'no2_cuenta'
                                },
                            ]
                        },

                        {
                            fieldLabel: 'Descripción',
                            xtype: 'textarea',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 125,
                            labelAlign: 'right',
                            name: 'descripcion'
                        },
                        {
                            xtype: 'combobox',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'centrocostoid',
                            id: 'centrocosto_id',
                            fieldLabel: 'Centro de Costo',
                            labelWidth: 125,
                            labelAlign: 'right',
                            store: store_centrocosto,
                            displayField: 'codigo',
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione...',
                            selectOnFocus: true,
                            editable: true,
                            listeners: {
                                select: function (This, record, eOpts) {
                                    console.log(This.getSelection().data.codigo);
                                    Ext.getCmp('elementogasto_id').setDisabled(record == undefined);
                                    let valor = Ext.getCmp('nocuenta2_id').getValue();
                                    valor = This.getSelection().data.codigo+valor.substr(3,13);
                                    Ext.getCmp('nocuenta2_id').setDisabled(false);
                                    Ext.getCmp('nocuenta2_id').setValue(valor);

                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'elementogastoid',
                            id: 'elementogasto_id',
                            fieldLabel: 'Elemento de Gasto',
                            labelWidth: 125,
                            labelAlign: 'right',
                            store: store_elementogasto,
                            displayField: 'codigo',
                            valueField: 'id',
                            disabled: true,
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione...',
                            selectOnFocus: true,
                            editable: true,
                            listeners: {
                                select: function (This, record, eOpts) {
                                    let valor = Ext.getCmp('nocuenta2_id').getValue();
                                    valor = valor.substr(0,3)+This.getSelection().data.codigo+valor.substr(8,13);
                                    Ext.getCmp('nocuenta2_id').setValue(valor);

                                    Ext.getCmp('detallegasto_id').setDisabled(record == undefined);
                                    Ext.getCmp('detallegasto_id').clearValue();
                                    Ext.getCmp('detallegasto_id').getStore().removeAll();
                                    if (record != undefined) {
                                        Ext.getCmp('detallegasto_id').getStore().load(
                                            {
                                                params: {elementogastoid: record.data.id, unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id}
                                            }
                                        );
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'detallegastoid',
                            id: 'detallegasto_id',
                            fieldLabel: 'Detalle de Gasto',
                            labelWidth: 125,
                            labelAlign: 'right',
                            store: store_detallegasto,
                            displayField: 'codigo',
                            valueField: 'id',
                            disabled: true,
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione...',
                            selectOnFocus: true,
                            editable: true,
                            listeners: {
                                select: function (This, record, eOpts) {
                                    let valor = Ext.getCmp('nocuenta2_id').getValue();
                                    valor = valor.substr(0,8)+This.getSelection().data.codigo;
                                    Ext.getCmp('nocuenta2_id').setValue(valor);

                                }
                            }
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'cuentagasto_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.cuentagasto.Window', {
                title: 'Adicionar Cuenta de Gasto',
                id: 'window_cuentagasto_id',
                listeners: {
                    // afterrender: function () {
                    //     var selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                    //     // var _record_ini = Ext.getCmp('unidadid').store;
                    //     var _record_i = _record_ini.findRecord('id', selected.id);
                    //     Ext.getCmp('unidadid').select(_record_i);
                    //     Ext.getCmp('unidadid').setReadOnly(true);
                    // }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_cuentagasto_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                let obj = form.getValues();
                                obj.no_cuenta = form.getValues().no1_cuenta+form.getValues().no2_cuenta;
                                console.log(form.getValues());
                                console.log(obj.no_cuenta);
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/cuentagasto/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            // var selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                                            // var _record_ini = Ext.getCmp('unidadid').store;
                                            // var _record_i = _record_ini.findRecord('id', selected.id);
                                            // Ext.getCmp('unidadid').select(_record_i);
                                            // Ext.getCmp('unidadid').setReadOnly(true);
                                            Ext.getCmp('id_grid_cuentagasto').getStore().loadPage(1);
                                            window.show();
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
                            Ext.getCmp('window_cuentagasto_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'cuentagasto_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_cuentagasto').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.cuentagasto.Window', {
                title: 'Modificar Cuenta de Gasto',
                id: 'window_cuentagasto_id',
                listeners: {
                    afterrender: function () {
                        Ext.getCmp('nocuenta2_id').setDisabled(false);
                        Ext.getCmp('nocuenta1_id').setValue(selection.data.no_cuenta.substr(0,3));
                        Ext.getCmp('nocuenta2_id').setValue(selection.data.no_cuenta.substr(3,13));
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
                                obj.id = Ext.getCmp('id_grid_cuentagasto').getSelectionModel().getLastSelected().data.id;
                                obj.no_cuenta = form.getValues().no1_cuenta+form.getValues().no2_cuenta;
                                App.request('POST', App.buildURL('/portadores/cuentagasto/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_cuentagasto').getStore().loadPage(1);
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
                            Ext.getCmp('window_cuentagasto_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'cuentagasto_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_cuentagasto').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar Cuenta de Gasto?',
                message: Ext.String.format('¿Está seguro que desea eliminar la cuenta de gasto <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('no_cuenta')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/cuentagasto/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_cuentagasto').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('cuentagasto_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);


});