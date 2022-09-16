
/*Ext.define('Portadores.cargo.Window', {
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
        id: 'cargo_btn_add',
        text: 'Adicionar',
        iconCls: 'fa fa-plus-square-o fa-1_4',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.cargo.Window', {
                title: 'Adicionar cargo',
                id: 'window_cargo_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_cargo_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                App.ShowWaitMsg();
                                window.hide();
                                var _result = App.PerformSyncServerRequest(Routing.generate('addCargo'), form.getValues());
                                App.HideWaitMsg();
                                if (_result.success) {
                                    form.reset();
                                    Ext.getCmp('id_grid_cargo').getStore().load();
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
                            Ext.getCmp('window_cargo_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'cargo_btn_mod',
        text: 'Modificar',
        iconCls: 'fa fa-pencil-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_cargo').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.cargo.Window', {
                title: 'Modificar cargo',
                id: 'window_cargo_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                App.ShowWaitMsg();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                var _result = App.PerformSyncServerRequest(Routing.generate('modCargo'), obj);
                                App.HideWaitMsg();
                                if (_result.success) {
                                    window.close();
                                    Ext.getCmp('id_grid_cargo').getStore().load();
                                }
                                App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_cargo_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'cargo_btn_del',
        text: 'Eliminar',
        iconCls: 'fa fa-minus-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            App.ConfirmMessage(function () {
                var selection = Ext.getCmp('id_grid_cargo').getSelectionModel().getLastSelected();
                App.ShowWaitMsg();
                var _result = App.PerformSyncServerRequest(Routing.generate('delCargo'), {id: selection.data.id});
                App.HideWaitMsg();
                App.InfoMessage('Información', _result.message, _result.cls);
                Ext.getCmp('id_grid_cargo').getStore().load();
            }, "Está seguro que desea eliminar el cargo seleccionado?");

        }
    });

    var _tbar = Ext.getCmp('cargo_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});*/
