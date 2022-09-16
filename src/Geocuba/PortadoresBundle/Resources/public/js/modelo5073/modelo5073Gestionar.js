Ext.onReady(function(){
    var _btn_generar = Ext.create('Ext.Button', {
        text: 'Generar Modelo',
        disabled: false,
        iconCls: 'fas fa-retweet text-primary',
        id: 'generar_modelo5073',
        handler: function () {
            var cantidad_elementos_store = Ext.getCmp('grid_modelo5073').getStore().getTotalCount();
            if (cantidad_elementos_store > 0) {
                Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Esta seguro que desea Generar el modelo 5073 ? Una ves realizada la acción, todo cambio en el mes actual se perderá', function (btn) {
                    if (btn == 'yes') {
                        App.request('GET', App.buildURL('/portadores/modelo5073/generar'), {
                                mes: Ext.getCmp('mes_anno').getValue().getMonth()+1,
                                anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                                unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                            }, null, null,
                            function (response) { // success_callback
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    Ext.getCmp('grid_modelo5073').getStore().load({
                                        params: {
                                            unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                                        }
                                    });
                                }
                            }
                        );
                    }

                });

            } else {
                App.request('GET', App.buildURL('/portadores/modelo5073/generar'), {
                        mes: Ext.getCmp('mes_anno').getValue().getMonth()+1,
                        anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                        unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                    }, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors

                            Ext.getCmp('grid_modelo5073').getStore().load({
                                params: {
                                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                                }
                            });
                        }
                    }
                );
            }
        }
    });

    var _btnGuardar = Ext.create('Ext.button.MyButton', {
        id: 'modelo5073_btn_guardar',
        text: 'Guardar Cambios ',
        disabled: true,
        iconCls: 'fas fa-save text-primary',
        width: 130,
        handler: function (This, e) {
            Ext.MessageBox.confirm('Confirmaci&oacute;n', 'Desea guardar los cambios Realizados', function (btn) {
                if (btn == 'yes') {
                    Ext.getCmp('modelo5073_btn_guardar').setDisabled(true);
                    var store = Ext.getCmp('grid_modelo5073').getStore();
                    var obj = {};
                    var send = [];
                    Ext.Array.each(store.data.items, function (valor) {
                        send.push(valor.data);
                    });
                    obj.store = Ext.encode(send);
                    App.request('POST', App.buildURL('/portadores/modelo5073/guardar'), obj, null, null,
                        function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('grid_modelo5073').getStore().load({
                                    params: {
                                        unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                                    }
                                });
                                Ext.getCmp('modelo50723_btn_export').setDisabled(false);
                            }
                        }
                    );
                }
            });

        }
    });

    var _tbar = Ext.getCmp('modelo5073_tbar');
    _tbar.add('-');
    _tbar.add(_btn_generar);
    _tbar.add('-');
    _tbar.add(_btnGuardar);
    // _tbar.add(_btn_Print);
    // _tbar.add(_btn_Export);
    // _tbar.setHeight(36);
})