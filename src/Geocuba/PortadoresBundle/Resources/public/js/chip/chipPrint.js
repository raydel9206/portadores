/**
 * Created by pfcadenas on 16/05/16.
 */

Ext.onReady(function () {


    let store_tarjeta_imprimir = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta_imprimir',
        fields: [
            {name: 'id'},
            {name: 'nro_tarjeta'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadTarjetaCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                    anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                });
            }
        }
    });

    var _btnPrintEntregas = Ext.create('Ext.button.MyButton', {
        id: 'anticipo_print_btn',
        // disabled: true,
        text: 'Imprimir',
        width: 80,
        iconCls: 'fas fa-print text-primary',
        handler: function (This, e) {
            Ext.create('Ext.window.Window', {
                title: 'Tarjeta',
                id: 'window_tarjeta_id',
                width: 225,
                modal: true,
                plain: true,
                resizable: false,
                items: [{
                    xtype: 'form',
                    frame: true,
                    bodyPadding: 5,
                    items: [{
                        xtype: 'combobox',
                        width: 210,
                        allowBlank: false,
                        name: 'nro_tarjeta',
                        id: 'tarjetaid',
                        fieldLabel: 'Tarjeta',
                        labelWidth: 50,
                        store: store_tarjeta_imprimir,
                        displayField: 'ntarjetaidnro',
                        valueField: 'ntarjetaid',
                        typeAhead: true,
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione...',
                        selectOnFocus: true,
                        editable: true
                    }]
                }],
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_tarjeta_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                var params = {
                                    id: Ext.getCmp('tarjetaid').getValue(),
                                    formato: CONSTANTS.FORMATO_WORD,
                                    mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                                    anno: Ext.getCmp('mes_anno').getValue().getFullYear()
                                };
                                window.close();
                                App.request('GET', App.buildURL('/portadores/chip/print'), params, null, null, function (response) {
                                        App.showDownloadWindow(response.getResponseHeader('Content-Type'), response.getResponseHeader('Content-Disposition'), response.responseBytes);
                                    }
                                    , null, {binary: true});
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_tarjeta_id').close()
                        }
                    }
                ]
            }).show();

            // if (!gridpanel.getSelectionModel().hasSelection()) {
            //     button.disable();
            //     return;
            // }


        }
    });

    var _tbar1 = Ext.getCmp('chip_tbar');
    _tbar1.add('->');
    _tbar1.add(_btnPrintEntregas);

});


