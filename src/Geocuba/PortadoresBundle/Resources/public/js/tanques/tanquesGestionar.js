Ext.onReady(function () {

    /** TANQUES */

    let gridTanques = Ext.getCmp('gridTanques');

    Ext.create('Ext.data.JsonStore', {
        storeId: 'storeTipoCombustible',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/tipocombustible/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'storeUnidadMedida',
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

    Ext.define('Portadores.tanques.Window', {
        extend: 'Ext.window.Window',
        width: 400,
        bodyPadding: '10',
        modal: true,
        resizable: false,
        items: [{
            xtype: 'form',
            frame: true,
            defaults: {
                afterLabelTextTpl: [
                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                ],
                labelWidth: 110,
                labelAlign: 'right',
                allowBlank: false
            },
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            items: [
                {
                    xtype: 'textfield',
                    fieldLabel: 'Nro. Inventario',
                    name: 'numero_inventario'
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Descripción',
                    name: 'descripcion'
                },
                {
                    xtype: 'numberfield',
                    fieldLabel: 'Capacidad',
                    name: 'capacidad',
                    decimalSeparator: '.',
                    decimalPrecision: 4,
                    minValue: 0
                },
                {
                    xtype: 'numberfield',
                    id: 'existencia',
                    fieldLabel: 'Existencia',
                    name: 'existencia',
                    decimalSeparator: '.',
                    decimalPrecision: 4,
                    minValue: 0
                },
                {
                    xtype: 'combobox',
                    name: 'tipo_combustible_id',
                    fieldLabel: 'Tipo Combustible',
                    store: Ext.getStore('storeTipoCombustible'),
                    displayField: 'nombre',
                    valueField: 'id',
                    typeAhead: true,
                    queryMode: 'local',
                    forceSelection: true,
                    triggerAction: 'all',
                    emptyText: 'Seleccione tipo de combustible...',
                    selectOnFocus: true,
                },
                {
                    xtype: 'combobox',
                    name: 'unidad_medida_id',
                    fieldLabel: 'Unidad Medida',
                    store: Ext.getStore('storeUnidadMedida'),
                    displayField: 'nombre',
                    valueField: 'id',
                    typeAhead: true,
                    queryMode: 'local',
                    forceSelection: true,
                    triggerAction: 'all',
                    emptyText: 'Seleccione unidad de medida...',
                    selectOnFocus: true,
                },
                {
                    xtype: 'checkboxfield',
                    boxLabel: 'Cilindro?',
                    name: 'cilindro',
                    margin: '0 0 0 20'
                }
            ]
        }]
    });

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/tanques/${action}`),
            selection = (action !== 'add' && gridTanques.getSelectionModel().hasSelection()) ? gridTanques.getSelection()[0] : null;

        if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar tanque?',
                message: `¿Está seguro que desea eliminar el tanque <span class="font-italic font-weight-bold">${selection.get('descripcion')}</span>?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', url, { id: selection.get('id') }, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridTanques.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.tanques.Window', {
                title: !selection ? 'Adicionar tanque' : `Modificar <span class="font-italic font-weight-bold">${selection.get('descripcion')}</span>`,
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = winform.down('form').getForm();
                            if (form.isValid()) {
                                let params = form.getValues();
                                params.id = action === 'upd' ? selection.data.id : null;
                                params.unidad_id = Ext.getCmp('arbolunidades').getSelection()[0].data.id;

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridTanques.getStore().load();
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
                ],
                listeners: {
                    afterrender: function () {
                        if(action === 'upd') {
                            Ext.getCmp('existencia').disable();
                            Ext.getCmp('existencia').hide();
                        }
                    }
                }
            }).show();
            if (action === 'upd') winform.down('form').getForm().loadRecord(selection);
        }
    };

    let _btnAdd = Ext.create('Ext.button.MyButton', {
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        // bind: {disabled: '!gridTanques.selection'},
        handler: action_handler.bind(this, 'add')
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        bind: {disabled: '{!gridTanques.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'upd')
    });

    var _btnDel = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridTanques.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'delete')
    });

    var _tbar = Ext.getCmp('gridTanquesTbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btnDel);
    _tbar.setHeight(36);

    /** MEDICIONES */

    let gridAfore = Ext.getCmp('gridAfore');

    Ext.define('Portadores.mediciones.Window', {
        extend: 'Ext.window.Window',
        width: 300,
        bodyPadding: '10',
        modal: true,
        resizable: false,
        items: [{
            xtype: 'form',
            frame: true,
            defaults: {
                afterLabelTextTpl: [
                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                ],
                labelWidth: 110,
                labelAlign: 'right',
                allowBlank: false,
                xtype: 'numberfield'
            },
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            items: [
                {
                    fieldLabel: 'Nivel',
                    name: 'nivel',
                    decimalSeparator: '.',
                    decimalPrecision: 4,
                    minValue: 0
                },
                {
                    fieldLabel: 'Existencia',
                    name: 'existencia',
                    decimalSeparator: '.',
                    decimalPrecision: 4,
                    minValue: 0
                },
            ]
        }]
    });

    let action_handler_mediciones = function (action) {
        let url = App.buildURL(`/portadores/tanques/${action}Medicion`),
            selection = (action !== 'add' && gridAfore.getSelectionModel().hasSelection()) ? gridAfore.getSelection()[0] : null;

        if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar medición?',
                message: `¿Está seguro que desea eliminar la medición?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', url, { id: selection.get('id'), tanque_id: selection.get('tanque_id') }, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridAfore.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.mediciones.Window', {
                title: !selection ? 'Adicionar medición' : `Modificar <span class="font-italic font-weight-bold">${selection.data.nivel_cm}</span>`,
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = winform.down('form').getForm();
                            if (form.isValid()) {
                                let params = form.getValues();
                                params.id = action === 'upd' ? selection.data.id : null;
                                params.tanque_id = action === 'add' ? gridTanques.getSelection()[0].data.id : selection.data.tanque_id;

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridAfore.getStore().load();
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

    let _btnAddMedicion = Ext.create('Ext.button.MyButton', {
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: action_handler_mediciones.bind(this, 'add')
    });

    var _btnModMedicion = Ext.create('Ext.button.MyButton', {
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        bind: {disabled: '{!gridAfore.selection}'},
        width: 100,
        handler: action_handler_mediciones.bind(this, 'upd')
    });

    var _btnDelMedicion = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridAfore.selection}'},
        width: 100,
        handler: action_handler_mediciones.bind(this, 'delete')
    });

    var _tbarMediciones = Ext.getCmp('gridMedicionesTbar');
    _tbarMediciones.add(_btnAddMedicion);
    _tbarMediciones.add('-');
    _tbarMediciones.add(_btnModMedicion);
    _tbarMediciones.add('-');
    _tbarMediciones.add(_btnDelMedicion);
    _tbarMediciones.setHeight(36);
});
