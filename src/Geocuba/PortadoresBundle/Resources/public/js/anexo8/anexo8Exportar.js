/**
 * Created by rherrerag on 1/8/2018.
 */
Ext.onReady(function () {

    var _btn_Print = Ext.create('Ext.button.MyButton', {
        id: 'reporte_anexo_8_btn_print',
        text: 'Imprimir',
        iconCls: 'fa fa-print text-primary',
        disabled: true,
        handler: function () {
            var store = Ext.getCmp('grid_reporte_anexo_8').getStore();
            var tarjeta = Ext.getCmp('combo_search').getValue();
            if (store.getCount() != 0) {
                var action = true;
                var obj = {};
                obj.action = action;
                obj.id = tarjeta;
                obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
                obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

                App.request('POST', App.buildURL('/portadores/anexo8/print'), obj, null, null,
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
            else
                App.showAlert("No Existen Datos Para Imprimir", 'info');
        }
    });

    var _btn_Export = Ext.create('Ext.button.MyButton', {
        id: 'reporte_anexo_8_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        disabled: true,
        handler: function (This, e) {
            var store = Ext.getCmp('grid_reporte_anexo_8').getStore();
            var tarjeta = Ext.getCmp('combo_search').getValue();
            if (store.getCount() != 0) {
                var action = true;
                var obj = {};
                obj.action = action;
                obj.id = tarjeta;
                obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
                obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

                App.request('POST', App.buildURL('/portadores/anexo8/print'), obj, null, null,
                    function (response) { // success_callback
                        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                    }
                );
            }
            else
                App.showAlert("No Existen Datos Para Exportar", 'info');
        }
    });


    var _tbar = Ext.getCmp('tbar_reporte_anexo_8');
    _tbar.add('->');
    _tbar.add(_btn_Print);
    // _tbar.add('-');
    _tbar.add(_btn_Export);
    // _tbar.add('-');
    _tbar.setHeight(36);

});