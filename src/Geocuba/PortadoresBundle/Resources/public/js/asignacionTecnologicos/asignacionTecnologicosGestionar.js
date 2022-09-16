Ext.onReady(function () {

    let gridAsignaciones = Ext.getCmp('gridAsignaciones');

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

    Ext.define('Portadores.asignacionTecnologicos.Window', {
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
                    xtype: 'datefield',
                    fieldLabel: 'Fecha',
                    name: 'fecha'
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
                    xtype: 'numberfield',
                    fieldLabel: 'Cantidad',
                    name: 'cantidad',
                    decimalSeparator: '.',
                    decimalPrecision: 4,
                    minValue: 0
                }
            ]
        }]
    });

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/asignacion_tecnologicos/${action}`),
            selection = (action !== 'add' && gridAsignaciones.getSelectionModel().hasSelection()) ? gridAsignaciones.getSelection()[0] : null;

        if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar asignación?',
                message: `¿Está seguro que desea eliminar la asignación?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', url, { id: selection.get('id') }, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridAsignaciones.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.asignacionTecnologicos.Window', {
                title: !selection ? 'Adicionar asignación' : `Modificar asignación`,
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
                                params.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
                                params.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridAsignaciones.getStore().load();
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
                            // Ext.getCmp('existencia').disable();
                            // Ext.getCmp('existencia').hide();
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
        handler: action_handler.bind(this, 'add')
    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        bind: {disabled: '{!gridAsignaciones.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'upd')
    });

    let _btnDel = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridAsignaciones.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'delete')
    });

    let _tbar = Ext.getCmp('gridAsignacionesTbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btnDel);
    _tbar.setHeight(36);
});
