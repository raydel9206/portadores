/**
 * Created by orlando on 06/01/2017.
 */


Ext.onReady(function () {
    var _btn_print_tarjetas_post = Ext.create('Ext.button.MyButton', {
        id: '_btn_print_tarjetas_post',
        text: 'Imprimir',
        //disabled:true,
        iconCls: 'fa fa-print text-primary',
        handler: function (This, e) {
            var store = Ext.getCmp('id_grid_tarjetas_post').getStore();
            var obj = {};
            var send = [];
            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });
            obj.store = Ext.encode(send);
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

            App.request('POST', App.buildURL('/portadores/tarjeta_post/print'), obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        var newWindow = window.open('', '', 'width=1200, height=700'),
                            document = newWindow.document.open();
                        document.write(response.html);
                        setTimeout(() => {
                            newWindow.print();
                        }, 500);
                        document.close();
                    }

                }, null, null, true
            );

        }
    });

    var _btn_export_tarjetas_post = Ext.create('Ext.button.MyButton', {
        id: '_btn_export_tarjetas_post',
        text: 'Exportar',
        // disabled:true,
        iconCls: 'fas fa-file-excel text-primary',
        handler: function (This, e) {
            var store = Ext.getCmp('id_grid_tarjetas_post').getStore();
            var obj = {};
            var send = [];
            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });
            obj.store = Ext.encode(send);
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

            App.request('POST', App.buildURL('/portadores/tarjeta_post/print'), obj, null, null,
                function (response) { // success_callback
                    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                }
            );
        }
    });

    var _tbar = Ext.getCmp('tarjetas_post_tbar');
    _tbar.add('->');
    _tbar.add(_btn_print_tarjetas_post);
    _tbar.add(_btn_export_tarjetas_post);
    _tbar.setHeight(36);
});

