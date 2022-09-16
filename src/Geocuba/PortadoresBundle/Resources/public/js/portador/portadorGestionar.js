/**
 * Created by kireny on 17/02/16.
 */
Ext.onReady(function () {

var _store_unidad_medida = Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_unidad_actividad',
    fields: [
        {name: 'id'},
        {name: 'nombre'}
    ],
    proxy: {
        type: 'ajax',
        url: App.buildURL('/portadores/unidad_medida/load'),
        reader: {
            rootProperty: 'rows'
        }
    },
    autoLoad: true
});

Ext.define('Portadores.portador.Window', {
    extend: 'Ext.window.Window',
    width: 350,
    modal: true,
    plain: true,
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
                        fieldLabel: 'Nombre',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 95,
                        name: 'nombre'
                        //maskRe: /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ -]/,
                        //regex: /^[A-Za-z0-9áéíóúñÁÉÍÓÚÑ-]*\s?([A-Za-z0-9áéíóúñÁÉÍÓÚÑ-]+\s?)+[A-Za-z0-9áéíóúñÁÉÍÓÚÑ-]$/,
                        //regexText: 'El nombre no es válido'
                    },
                    {
                        xtype: 'combobox',
                        name: 'unidad_medidaid',
                        id: 'unidad_medidaid',
                        margin: '10 0 0 0',
                        fieldLabel: 'Unidad Medida',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 95,
                        store: _store_unidad_medida,
                        displayField: 'nombre',
                        valueField: 'id',
                        typeAhead: true,
                        editable: true,
                        forceSelection: true,
                        queryMode: 'local',
                        triggerAction: 'all',
                        emptyText: 'Seleccione ...'
                    }
                ]
            }
        ];

        this.callParent();
    }
});

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'portador_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.portador.Window', {
                title: 'Adicionar portador',
                id: 'window_portador_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_portador_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/portador/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('gridportadorId').getStore().loadPage(1);
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
                            Ext.getCmp('window_portador_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'portador_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('gridportadorId').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.portador.Window', {
                title: 'Modificar portador',
                id: 'window_portador_id',
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
                                App.request('POST', App.buildURL('/portadores/portador/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('gridportadorId').getStore().loadPage(1);

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
                            Ext.getCmp('window_portador_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'portador_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('gridportadorId').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar Portador?',
                message: Ext.String.format('¿Está seguro que desea eliminar el portador <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/portador/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('gridportadorId').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('portador_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});