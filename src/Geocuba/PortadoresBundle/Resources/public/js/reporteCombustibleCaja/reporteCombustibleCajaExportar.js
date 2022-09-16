/**
 * Created by orlando on 09/01/2017.
 */

Ext.onReady(function () {
    var _btn_Print = Ext.create('Ext.button.MyButton', {
        id: 'reporte_control_combustible_Deposito_btn_print',
        text: 'Imprimir',
        iconCls: 'fas fa-print text-primary',
        disabled: true,
        handler: function (This, e) {

            App.request('POST', App.buildURL('/portadores/libro_caja/print'), {
                    nro_tarjeta: Ext.getCmp('nro_tarjeta').getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                    anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                    start: (Ext.getCmp('nro_tarjeta').store.currentPage - 1) * 25,
                    limit: (Ext.getCmp('nro_tarjeta').store.currentPage - 1) * 25 + 25
                }, null, null,
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

    var _btn_Export = Ext.create('Ext.button.MyButton', {
        id: 'reporte_control_combustible_Deposito_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        disabled: true,
        handler: function (This, e) {

            App.request('POST', App.buildURL('/portadores/libro_caja/print'), {
                    nro_tarjeta: Ext.getCmp('nro_tarjeta').getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                    anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                    start: (Ext.getCmp('nro_tarjeta').store.currentPage - 1) * 25,
                    limit: (Ext.getCmp('nro_tarjeta').store.currentPage - 1) * 25 + 25
                }, null, null,
                function (response) { // success_callback
                        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));

                }
            );
        }
    });

    var _tbar = Ext.getCmp('combustible_caja_tbar');
    _tbar.add('->');
    _tbar.add(_btn_Print);
    _tbar.add(_btn_Export);
    _tbar.setHeight(36);
});
