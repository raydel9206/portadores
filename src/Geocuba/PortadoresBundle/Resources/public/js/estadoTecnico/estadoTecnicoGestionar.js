/**
 * Created by yosley on 05/10/2015.
 */
Ext.onReady(function () {
    Ext.define('Portadores.estadoTecnico.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        plain: true,
        resizable: false,
        width: 250,
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
                            //maskRe: /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]/,
                            //regex: /^[A-Za-z0-9áéíóúñÁÉÍÓÚÑ]*\s?([A-Za-z0-9áéíóúñÁÉÍÓÚÑ]+\s?)+[A-Za-z0-9áéíóúñÁÉÍÓÚÑ]$/,
                            //regexText: 'El nombre no es válido'
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'estadoTecnico_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        // iconCls: 'fa fa-plus-square fa-1_4',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.estadoTecnico.Window', {
                title: 'Adicionar estado técnico',
                id: 'window_estadotecnico_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_estadotecnico_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/estadotecnico/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('id_store_estado_Tecnico').loadPage(1);
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
                                // window.hide();
                                // var _result = App.PerformSyncServerRequest(Routing.generate('addEstadoTecnico'), form.getValues());
                                // App.HideWaitMsg();
                                // if (_result.success) {
                                //     form.reset();
                                //     Ext.getCmp('id_grid_estado_Tecnico').getStore().load();
                                // }
                                // else {
                                //     form.markInvalid(_result.message);
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
                            Ext.getCmp('window_estadotecnico_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'estadoTecnico_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        // iconCls: 'fa fa-edit fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_estado_Tecnico').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.estadoTecnico.Window', {
                title: 'Modificar estado técnico',
                id: 'window_estadotecnico_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_estado_Tecnico').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/estadotecnico/upd'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getStore('id_store_estado_Tecnico').loadPage(1);
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
                                // App.ShowWaitMsg();
                                // window.hide();
                                // var obj = form.getValues();
                                // obj.id = selection.data.id;
                                // var _result = App.PerformSyncServerRequest(Routing.generate('modEstadoTecnico'), obj);
                                // App.HideWaitMsg();
                                // if (_result.success) {
                                //     window.close();
                                //     Ext.getCmp('id_grid_estado_Tecnico').getStore().load();
                                // }
                                // else {
                                //     window.show();
                                //     form.markInvalid(_result.message);
                                // }
                                // App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_estadotecnico_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'estadoTecnico_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        // iconCls: 'fa fa-minus-square fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_estado_Tecnico').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar estado técnico?' : '¿Eliminar estados técnicos?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el estado técnico <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar los estados técnicos?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/estadotecnico/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_estado_Tecnico').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('estado_Tecnico_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
