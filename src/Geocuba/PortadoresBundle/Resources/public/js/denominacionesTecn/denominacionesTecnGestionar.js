Ext.onReady(function () {

    let grid = Ext.getCmp('gridDenominaciones');

    Ext.define('Portadores.denominacion_tecn.Window', {
        extend: 'Ext.window.Window',
        width: 250,
        // height: 135,
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
                        }]
                }
            ];

            this.callParent();
        }
    });

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/denominaciones_tecn/${action}`),
            selection = (action !== 'add' && grid.getSelectionModel().hasSelection()) ? grid.getSelection()[0] : null;

        if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar denominación?',
                message: `¿Está seguro que desea eliminar la denominación?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        let params = {
                            id: selection.get('id'),
                        };
                        App.request('DELETE', url, params, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                grid.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.denominacion_tecn.Window', {
                title: !selection ? 'Adicionar denominación' : `Modificar <span class="font-italic font-weight-bold">${selection.get('nombre')}</span>`,
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = winform.down('form').getForm();
                            if (form.isValid()) {
                                let params = form.getValues();
                                params.id = action === 'upd' ? selection.get('id') : null;

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        grid.getStore().load();
                                        winform.close();
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            winform.close()
                        }
                    }
                ]
            }).show();
            if (action === 'upd') winform.down('form').getForm().loadRecord(selection);
        }
    };

    let _btnAdd = Ext.create('Ext.button.MyButton', {
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: action_handler.bind(this, 'add')
    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        bind: {disabled: '{!gridDenominaciones.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'upd')
    });

    let _btn_Del = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridDenominaciones.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'delete')
    });

    let _tbar = Ext.getCmp('denominaciones_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
});
