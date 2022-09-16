Ext.onReady(function () {
    let gridMediciones = Ext.getCmp('gridMediciones');
    let gridTanques = Ext.getCmp('gridTanques');

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
                    name: 'medicion',
                    decimalSeparator: '.',
                    decimalPrecision: 4,
                    minValue: 0
                }
            ]
        }]
    });

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/mediciones_diarias/${action}`),
            selection = (action !== 'add' && gridMediciones.getSelectionModel().hasSelection()) ? gridMediciones.getSelection()[0] : null;

        if (action === 'recalcularConsumo'){
            App.request('POST', url, { medicion_id: selection.get('id') }, null, null, (response) => {
                if (response && response.hasOwnProperty('success') && response.success) {
                    gridMediciones.getStore().reload();
                }
            })
        }
        else if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar medición?',
                message: `¿Está seguro que desea eliminar la medición?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        let params = {
                            id: selection.get('id'),
                            tanque_id: selection.get('tanque_id'),
                            mes: Ext.getCmp('mes_anno').getValue().getMonth()+1,
                            anno: Ext.getCmp('mes_anno').getValue().getFullYear()
                        };
                        App.request('DELETE', url, params, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridMediciones.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.mediciones.Window', {
                title: !selection ? 'Adicionar medición' : `Modificar ${selection.data.fecha}`,
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

                                params.mes = Ext.getCmp('mes_anno').getValue().getMonth()+1;
                                params.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridMediciones.getStore().load();
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

    // let _btnMod = Ext.create('Ext.button.MyButton', {
    //     text: 'Modificar',
    //     iconCls: 'fas fa-edit text-primary',
    //     bind: {disabled: '{!gridMediciones.selection}'},
    //     width: 100,
    //     handler: action_handler.bind(this, 'upd')
    // });

    let _btnDel = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridMediciones.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'delete')
    });

    let _btnCalcular = Ext.create('Ext.button.MyButton', {
        text: 'Calcular',
        iconCls: 'fas fa-calculator text-primary',
        bind: {disabled: '{!gridMediciones.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'recalcularConsumo')
    });

    let _tbarMediciones = Ext.getCmp('gridMedicionesTbar');
    _tbarMediciones.add(_btnAdd);
    _tbarMediciones.add('-');
    // _tbarMediciones.add(_btnMod);
    // _tbarMediciones.add('-');
    _tbarMediciones.add(_btnDel);
    _tbarMediciones.add('-');
    _tbarMediciones.add(_btnCalcular);
});
