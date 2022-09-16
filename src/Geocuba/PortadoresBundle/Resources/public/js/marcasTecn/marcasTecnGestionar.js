Ext.onReady(function () {

    let gridMarcas = Ext.getCmp('gridMarcas');

    Ext.define('Portadores.marca_technologica.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        width: 350,
        items: [
            {
                xtype: 'form',
                frame: true,
                defaultType: 'textfield',
                bodyPadding: 10,
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
                        labelWidth: 60,
                        afterLabelTextTpl: ['<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'],
                        name: 'nombre'
                    },

                ]
            }
        ]
    });

    let action_handler_marca = function (action) {
        let url = App.buildURL(`/portadores/marcas_tecn/${action}`),
            selection = (action !== 'add' && gridMarcas.getSelectionModel().hasSelection()) ? gridMarcas.getSelection()[0] : null;

        if (action === 'delete') {
            let selection = gridMarcas.getSelection();
            let params = {};
            selection.forEach(function (record, index) {
                params['ids[' + index + ']'] = record.getId();
            });
            Ext.Msg.show({
                title: `¿Eliminar marca${selection.length > 1 ? 's' : ''}?`,
                message: `¿Está seguro que desea eliminar 
                                ${selection.length > 1 
                                    ? 'las marcas seleccionadas'
                                    : `<span class="font-italic font-weight-bold">${selection[0].get('nombre')}</span>?`}?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', url, params, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridMarcas.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.marca_technologica.Window', {
                title: !selection ? 'Adicionar marca' : `Modificar <span class="font-italic font-weight-bold">${selection.data.nombre}</span>`,
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = winform.down('form').getForm();
                            if (form.isValid()) {
                                let params = form.getValues();
                                params.id = action === 'upd' ? selection.data.id : null;

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridMarcas.getStore().load();
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

    let _btnAddMarca = Ext.create('Ext.button.MyButton', {
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: action_handler_marca.bind(this, 'add')
    });

    let _btnUpdMarca = Ext.create('Ext.button.MyButton', {
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        width: 100,
        // bind: {disabled: '{gridMarcas.selection}'},
        id: 'btnUpdMarca',
        disabled: true,
        handler: action_handler_marca.bind(this, 'upd')
    });

    let _btnDelMarca = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        width: 100,
        bind: {disabled: '{!gridMarcas.selection}'},
        handler: action_handler_marca.bind(this, 'delete')
    });

    let _tbarMarcas = Ext.getCmp('marcas_tbar');
    _tbarMarcas.add(_btnAddMarca);
    _tbarMarcas.add(_btnUpdMarca);
    _tbarMarcas.add(_btnDelMarca);


    let gridModelos = Ext.getCmp('gridModelos');

    Ext.define('Portadores.modelo_technologico.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        plain: true,
        resizable: false,
        width: 300,
        items: [
            {
                xtype: 'form',
                frame: true,
                defaultType: 'textfield',
                bodyPadding: 10,
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
                        name: 'nombre',
                        fieldLabel: 'Nombre',
                        labelWidth: 60,
                        afterLabelTextTpl: ['<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'],
                    }
                ]
            }
        ]
    });

    let action_handler_modelo = function (action) {
        let url = App.buildURL(`/portadores/modelos_tecn/${action}`),
            selection = (action !== 'add' && gridModelos.getSelectionModel().hasSelection()) ? gridModelos.getSelection()[0] : null;

        if (action === 'delete') {
            let selection = gridModelos.getSelection();
            let params = {};
            selection.forEach(function (record, index) {
                params['ids[' + index + ']'] = record.getId();
            });
            Ext.Msg.show({
                title: `¿Eliminar modelo${selection.length > 1 ? 's' : ''}?`,
                message: `¿Está seguro que desea eliminar 
                                ${selection.length > 1
                    ? 'los modelos seleccionados'
                    : `<span class="font-italic font-weight-bold">${selection[0].get('nombre')}</span>?`}?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', url, params, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridModelos.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.modelo_technologico.Window', {
                title: !selection ? 'Adicionar modelo' : `Modificar <span class="font-italic font-weight-bold">${selection.data.nombre}</span>`,
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = winform.down('form').getForm();
                            if (form.isValid()) {
                                let params = form.getValues();
                                params.id = action === 'upd' ? selection.data.id : null;
                                params.marca_id = action === 'add' ? gridMarcas.getSelection()[0].data.id : null;

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridModelos.getStore().load();
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

    let _btnAddModelo = Ext.create('Ext.button.MyButton', {
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: action_handler_modelo.bind(this, 'add')
    });

    let _btnUpdModelo = Ext.create('Ext.button.MyButton', {
        id: 'btnUpdModelo',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: action_handler_modelo.bind(this, 'upd')
    });

    let _btnDelModelo = Ext.create('Ext.button.MyButton', {
        id: 'modelo_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridModelos.selection}'},
        width: 100,
        handler: action_handler_modelo.bind(this, 'delete')
    });

    let _tbarModelo = Ext.getCmp('modelos_tbar');
    _tbarModelo.add(_btnAddModelo);
    _tbarModelo.add(_btnUpdModelo);
    _tbarModelo.add(_btnDelModelo);
});
