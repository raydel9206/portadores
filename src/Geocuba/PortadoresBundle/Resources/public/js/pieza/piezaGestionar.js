/**
 * Created by javier on 20/05/2016.
 */
Ext.define('Portadores.pieza.Window', {
    extend: 'Ext.window.Window',
    width: 250,
    height: 110,
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
Ext.onReady(function () {
    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'pieza_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.pieza.Window', {
                title: 'Adicionar pieza',
                id: 'window_pieza_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_pieza_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/pieza/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_pieza').getStore().loadPage(1);
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
                            Ext.getCmp('window_pieza_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'pieza_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_pieza').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.pieza.Window', {
                title: 'Modificar pieza',
                id: 'window_pieza_id',
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
                                App.request('POST', App.buildURL('/portadores/pieza/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_pieza').getStore().loadPage(1);
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
                            Ext.getCmp('window_pieza_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'pieza_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_pieza').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar pieza?' : '¿Eliminar piezas?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar la pieza <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar las piezas seleccionadas?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/pieza/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_pieza').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('pieza_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
