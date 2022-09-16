Ext.onReady(function () {
    let gridEntradasSalidas = Ext.getCmp('gridEntradasSalidas');
    let gridTanques = Ext.getCmp('gridTanques');

    Ext.define('Portadores.entradas_salidas.Window', {
        extend: 'Ext.window.Window',
        width: 560,
        bodyPadding: '10',
        modal: true,
        resizable: false,
        items: [{
            xtype: 'form',
            // frame: true,
            layout: {
                type: 'hbox',
                align: 'stretch'
            },
            items: [
                {
                    xtype: 'container',
                    layout: 'vbox',
                    defaults: {
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 100,
                        width: 250,
                        labelAlign: 'right',
                        allowBlank: false,
                        xtype: 'numberfield'
                    },
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'fecha',
                            fieldLabel: 'Fecha',
                            margin: '0 0 10 0',
                            listeners: {
                                afterrender: function (This) {
                                    let dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                                    let anno = App.selected_year;
                                    let min = new Date((App.selected_month) + '/' + 1 + '/' + anno);
                                    let max = new Date((App.selected_month) + '/' + dias + '/' + anno);
                                    This.setMinValue(min);
                                    This.setMaxValue(max);
                                }
                            }
                        },
                        {
                            fieldLabel: 'Medición Inicial',
                            id: 'medicion_antes',
                            name: 'medicion_antes',
                            decimalSeparator: '.',
                            decimalPrecision: 4,
                            minValue: 0
                        },
                        {
                            fieldLabel: 'Medición Final',
                            name: 'medicion_despues',
                            decimalSeparator: '.',
                            decimalPrecision: 4,
                            minValue: 0
                        },
                        {
                            fieldLabel: 'Cantidad',
                            afterLabelTextTpl: '',
                            name: 'cantidad',
                            decimalSeparator: '.',
                            decimalPrecision: 4,
                            renderer: function (value, metaData) {
                                // todo revisar
                                if (value < 0) metaData.style += 'color: red!important;'
                                if (value > 0) metaData.style += 'color: green!important;'
                            }
                        }
                    ]
                },
                {
                    xtype: 'container',
                    layout: 'vbox',
                    margin: '0 0 0 20',
                    defaults: {
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 110,
                        width: 260,
                        labelAlign: 'right',
                        allowBlank: false,
                        xtype: 'numberfield'
                    },
                    items: [
                        {
                            xtype: 'displayfield'
                        },
                        {
                            fieldLabel: 'Existencia Inicial',
                            afterLabelTextTpl: '',
                            name: 'existencia_antes',
                            decimalSeparator: '.',
                            decimalPrecision: 4,
                            minValue: 0,
                            editable: false,
                            hideTrigger: true
                        },
                        {
                            fieldLabel: 'Existencia Final',
                            afterLabelTextTpl: '',
                            name: 'existencia_despues',
                            decimalSeparator: '.',
                            decimalPrecision: 4,
                            minValue: 0,
                            editable: false,
                            hideTrigger: true
                        },
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-calculator window-button',
                            text: 'Calcular',
                            width: 100,
                            margin: '0 0 0 10',
                            handler: function (This) {
                                let form = This.up('form').getForm();
                                let params = form.getValues();
                                params.tanque_id = gridTanques.getSelection()[0].get('id');

                                App.request('POST', App.buildURL('/portadores/entradas_salidas/calcularNivel'), params, null, null, (response) => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        let record = {};
                                        record.getData = function () {
                                            return {
                                                existencia_antes: response.data['existencia_antes'],
                                                existencia_despues: response.data['existencia_despues'],
                                                cantidad: response.data['cantidad'],
                                            }
                                        };
                                        form.loadRecord(record);
                                    }
                                });
                            }
                        }
                    ]
                }
            ]
        }]
    });

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/entradas_salidas/${action}`),
            selection = (action !== 'add' && gridEntradasSalidas.getSelectionModel().hasSelection()) ? gridEntradasSalidas.getSelection()[0] : null;

        if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar entrada/salida?',
                message: `¿Está seguro que desea eliminar la entrada/salida?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        let params = {id: selection.get('id')};
                        App.request('DELETE', url, params, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridEntradasSalidas.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.entradas_salidas.Window', {
                title: !selection ? 'Adicionar entrada/salida' : `Modificar entrada/salida`,
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

                                params.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
                                params.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridEntradasSalidas.getStore().load();
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
                    boxready: function (This) {
                        if (action === 'add') {
                            App.mask();
                            let params = {};
                            params.tanque_id = gridTanques.getSelection()[0].data.id;
                            params.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
                            params.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

                            App.request('GET', App.buildURL(`/portadores/entradas_salidas/ultima_medida`), params, null, null, response => {
                                Ext.getCmp('medicion_antes').setValue(response.data);
                                App.unmask();
                            })
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
        bind: {disabled: '{!gridEntradasSalidas.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'upd')
    });

    let _btnDel = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridEntradasSalidas.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'delete')
    });

    let tbar = Ext.getCmp('gridEntradasSalidasTbar');
    tbar.add(_btnAdd);
    tbar.add('-');
    tbar.add(_btnMod);
    tbar.add('-');
    tbar.add(_btnDel);
});