/**
 * Created by yosley on 23/05/2016.
 */
Ext.define('Portadores.turnos.Window', {
    extend: 'Ext.window.Window',
    width: 340,
    height: 140,
    modal: true,
    plain: true,
    resizable: false,
    initComponent: function () {
        this.items = [
            {
                xtype: 'form',
                frame: true,
                width: '200%',
                height: '200%',
                defaultType: 'textfield',
                bodyPadding: 5,
                fieldDefaults: {
                    msgTarget: 'side',
                    allowBlank: false
                },
                items: [
                    {
                        fieldLabel: 'Turno',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 60,
                        width: 300,
                        name: 'turno'
                    },
                    {
                        fieldLabel: 'Horas',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 60,
                        width: 300,
                        name: 'horas',
                        maskRe: /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]/,
                        regex: /^[A-Za-z0-9áéíóúñÁÉÍÓÚÑ]*\s?([A-Za-z0-9áéíóúñÁÉÍÓÚÑ]+\s?)+[A-Za-z0-9áéíóúñÁÉÍÓÚÑ]$/,
                        regexText: 'La hora no es válido'
                    }
                ]
            }
        ];

        this.callParent();
    }
});
Ext.onReady(function () {
    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'turnos_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.turnos.Window', {
                title: 'Adicionar',
                id: 'window_turnos_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_turnos_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/turno/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_turno').getStore().loadPage(1);
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
                            Ext.getCmp('window_turnos_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'turnos_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_turno').getSelectionModel().getLastSelected();
            console.log(selection);
            var window = Ext.create('Portadores.turnos.Window', {
                title: 'Modificar ',
                id: 'window_turnos_id',
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
                                App.request('POST', App.buildURL('/portadores/turno/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_turno').getStore().loadPage(1);

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
                            Ext.getCmp('window_turnos_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'turno_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_turno').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar Turno de Trabajo?',
                message: '¿Está seguro que desea eliminar el turno seleccionado?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/turno/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_turno').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });
    var tbar1 = Ext.getCmp('turnos_tbar');
    tbar1.add(_btnAdd);
    tbar1.add('-');
    tbar1.add(_btnMod);
    tbar1.add('-');
    tbar1.add(_btn_Del);
    tbar1.setHeight(36);

});