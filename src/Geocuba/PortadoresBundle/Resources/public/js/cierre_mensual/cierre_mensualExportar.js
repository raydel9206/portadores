/**
 * Created by kireny on 11/07/2017.
 */

Ext.onReady(function () {
    var _btn_Print = Ext.create('Ext.button.MyButton', {
        id: 'cierre_mensual_btn_print',
        text: 'Imprimir',
        iconCls: 'fas fa-print text-primary',
        handler: function (This, e) {
            // App.ShowWaitMsg();
            var store = Ext.getCmp('grid_cierre_mensual').getStore();
            var obj = {};
            var send = [];
            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });
            obj.store = Ext.encode(send);
            obj.mes =  Ext.getCmp('mes_anno').getValue().getMonth()+1;
            obj.anno =  Ext.getCmp('mes_anno').getValue().getFullYear();
            obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;

            if (store.getTotalCount() == 0) {
                App.showAlert('No existen datos para imprimir', 'warning');
            }
            else {
                App.request('POST', App.buildURL('/portadores/cierre_mensual/print'), obj, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                            var newWindow = window.open('', '', 'width=1200, height=700'),
                                document = newWindow.document.open();
                            document.write(response.html);
                            setTimeout(() => {
                                newWindow.print();
                            }, 500);
                            document.close();
                        } else {
                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                window.down('form').getForm().markInvalid(response.errors);
                            }
                        }

                    },null,null,null,null,false
                );
            }
        }
    });

    var _btn_Export = Ext.create('Ext.button.MyButton', {
        id: 'cierre_mensual_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        handler: function (This, e) {
            let store = Ext.getCmp('grid_cierre_mensual').getStore();
            if (store.getData().length != 0) {
                var obj = {};
                var send = [];
                Ext.Array.each(store.data.items, function (valor) {
                    send.push(valor.data);
                });
                obj.store = Ext.encode(send);
                obj.mes =  Ext.getCmp('mes_anno').getValue().getMonth()+1;
                obj.anno =  Ext.getCmp('mes_anno').getValue().getFullYear();
                obj.unidad = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                obj.tipoCombustible = Ext.getCmp('id_tipos_combustible').getValue();
                // obj.export = true;

                App.request('POST', App.buildURL('/portadores/cierre_mensual/print'), obj, null, null,
                    function (response) { // success_callback
                        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                    }
                );
                // App.request('GET', App.buildURL('/portadores/cierre_mensual/export'), obj, null, null,
                //     function (response) { // success_callback
                //         window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                //     }
                // );
            }
            else
                App.showAlert('No existen datos para Exportar', 'warning');
        }
    });

    var _tbar = Ext.getCmp('cierre_mensual_tbar');
    _tbar.add('->');
    _tbar.add(_btn_Print);
    _tbar.add(_btn_Export);
    _tbar.setHeight(36);
})