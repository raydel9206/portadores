/**
 * Created by adonis on 24/09/2015.
 */
Ext.onReady(function () {

    Ext.define('Portadores.denominacionVehiculo.Window', {
        extend: 'Ext.window.Window',
        width: 250,
        height: 135,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: '100%',
                    height: '100%',
                    defaultType: 'textfield',
                    bodyPadding: 5,
                    items: [
                        {
                            fieldLabel: 'Nombre',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 55,
                            name: 'nombre',
                            allowBlank: false
                            //maskRe: /^[a-zA-Z-áéíóúñÁÉÍÓÚÑ ]/,
                            //regex: /^[A-Za-z-áéíóúñÁÉÍÓÚÑ]*\s?([A-Za-z-áéíóúñÁÉÍÓÚÑ]+\s?)+[A-Za-z-áéíóúñÁÉÍÓÚÑ]$/,
                            //regexText: 'El nombre no es válido'
                        }, {
                            xtype: 'numberfield',
                            name: 'orden',
                            fieldLabel: 'Orden',
                            labelWidth: 55,
                            value: 0,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            allowBlank: false
                        }]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'denominacionVehiculo_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        // iconCls: 'fa fa-plus-square fa-1_4',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.denominacionVehiculo.Window', {
                title: 'Adicionar denominación',
                id: 'window_denominacionVehiculo_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_denominacionVehiculo_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/denominacionvehiculo/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('storeDenominacionVehiculoId').loadPage(1);
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
                            Ext.getCmp('window_denominacionVehiculo_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'denominacionVehiculo_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        // iconCls: 'fa fa-edit fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('gridDenominacionVehiculoId').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.denominacionVehiculo.Window', {
                title: 'Modificar denominación',
                id: 'window_denominacionVehiculo_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('gridDenominacionVehiculoId').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/denominacionvehiculo/upd'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getStore('storeDenominacionVehiculoId').loadPage(1);
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
                            Ext.getCmp('window_denominacionVehiculo_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'denominacionVehiculo_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        // iconCls: 'fa fa-minus-square fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('gridDenominacionVehiculoId').getSelection()[0];
            Ext.Msg.show({
                title: '¿Eliminar denominación de vehículo?',
                message: Ext.String.format('¿Está seguro que desea eliminar la denominación <span class="font-italic font-weight-bold">{0}</span>?', selection.get('nombre')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit, id: selection.get('id')};

                        App.request('DELETE', App.buildURL('/portadores/denominacionvehiculo/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('gridDenominacionVehiculoId').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    var _tbar = Ext.getCmp('denominacionVehiculo_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
