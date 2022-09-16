Ext.onReady(function () {

    let gridFactores = Ext.getCmp('grid_factor');

    let store_portador = Ext.create('Ext.data.JsonStore', {
        storeId: 'storePortadorId',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/portador/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true,
    });

    let _storeum = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_unidadmedida_factor',
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

    Ext.define('Portadores.factor.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        plain: true,
        resizable: false,
        // width: 350,
        items: [
            {
                xtype: 'form',
                layout: 'anchor',
                padding: 20,
                defaults: {
                    xtype: 'combobox',
                    labelWidth: 60,
                    width: 285,
                    labelAlign: 'right',
                    afterLabelTextTpl: [
                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                    ],
                    allowBlank: false,
                    editable: false
                },
                items: [
                    {
                        name: 'portador_id',
                        fieldLabel: 'Portador',
                        store: store_portador,
                        displayField: 'nombre',
                        valueField: 'id',
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione el portador',
                    },
                    {
                        name: 'de_um_id',
                        fieldLabel: 'De',
                        store: _storeum,
                        displayField: 'nombre',
                        valueField: 'id',
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione la unidad de medida',
                    },
                    {
                        name: 'a_um_id',
                        fieldLabel: 'A',
                        store: _storeum,
                        displayField: 'nombre',
                        valueField: 'id',
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione la unidad de medida',
                    },
                    {
                        xtype: 'numberfield',
                        name: 'factor',
                        fieldLabel: 'Factor',
                        decimalPrecision: 6,
                        decimalSeparator: '.',
                        editable: true
                    }
                ]
            }
        ]
    });

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/factor/${action}`),
            selection = (action !== 'add' && gridFactores.getSelectionModel().hasSelection()) ? gridFactores.getSelection()[0] : null;

        if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar factor de conversión?',
                message: `¿Está seguro que desea eliminar el factor de conversión?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', url, { id: selection.get('id') }, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridFactores.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.factor.Window', {
                title: !selection ? 'Adicionar factor de conversión' : `Modificar factor de conversión`,
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
                                        gridFactores.getStore().load();
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
        bind: {disabled: '{!grid_factor.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'upd')
    });

    let _btn_Del = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!grid_factor.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'delete')
    });

    let _tbar = Ext.getCmp('factor_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
});
