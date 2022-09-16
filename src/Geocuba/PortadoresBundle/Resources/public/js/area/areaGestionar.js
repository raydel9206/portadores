Ext.onReady(function () {

    Ext.define('Portadores.Area.Window', {
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
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'textfield',
                            name: 'nombre',
                            id: 'nombre',
                            labelWidth: 60,
                            fieldLabel: 'Nombre',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
//                        maskRe: /[a-z,.A-ZáéíóúñÁÉÍÓÚÑ ]/,
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'Area_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.Area.Window', {
                title: 'Adicionar área',
                id: 'window_Area_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_Area_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/area/addArea'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('gridAreaId').getStore().load();
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
                            Ext.getCmp('window_Area_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'Area_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('gridAreaId').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.Area.Window', {
                title: 'Modificar área',
                id: 'window_Area_id',
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
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/area/modArea'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('gridAreaId').getStore().load();
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
                            Ext.getCmp('window_Area_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'Area_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('gridAreaId').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar área?' : '¿Eliminar áreas?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar la área <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar las áreas seleccionadas?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/area/delArea'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('gridAreaId').getStore().load();
                            }
                        });
                    }
                }
            });

        }
    });

    var _tbar = Ext.getCmp('Area_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});