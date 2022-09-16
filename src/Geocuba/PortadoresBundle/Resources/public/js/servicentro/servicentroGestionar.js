/**
 * Created by adonis on 23/09/2015.
 */
Ext.define('Energia.servicentro.Window', {
    extend: 'Ext.window.Window',
    width: 300,
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
                    labelWidth: 60,
                    allowBlank: false
                },
                items: [
                    {
                        fieldLabel: 'Nombre',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name: 'nombre'
                    }
                ]
            }
        ];

        this.callParent();
    }
});
Ext.onReady(function () {
    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'servicentro_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        // iconCls: 'fa fa-plus-square fa-1_4',
        width: 100,
        handler: function (This, e) {
            Ext.create('Energia.servicentro.Window', {
                title: 'Adicionar servicentro',
                id: 'window_servicentro_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_servicentro_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/servicentro/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('storeServicentroId').loadPage(1);
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
                            Ext.getCmp('window_servicentro_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'servicentro_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        // iconCls: 'fa fa-edit fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('gridservicentroId').getSelectionModel().getLastSelected();
            var window = Ext.create('Energia.servicentro.Window', {
                title: 'Modificar servicentro',
                id: 'window_servicentro_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('gridservicentroId').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/servicentro/upd'),obj , null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getStore('storeServicentroId').loadPage(1);
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
                            Ext.getCmp('window_servicentro_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'servicentro_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        // iconCls: 'fa fa-minus-square fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('gridservicentroId').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar servicentro?' : '¿Eliminar servicentros?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el servicentro <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    'Está seguro que desea eliminar los servicentros seleccionados?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });

                        App.request('DELETE', App.buildURL('/portadores/servicentro/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('gridservicentroId').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    var _tbar = Ext.getCmp('servicentro_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});