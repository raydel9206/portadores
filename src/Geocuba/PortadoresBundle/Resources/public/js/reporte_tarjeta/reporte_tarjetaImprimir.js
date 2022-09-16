/**
 * Created by javier on 17/05/2016.
 */

Ext.onReady(function(){
    var _btnPrint = Ext.create('Ext.button.MyButton',{
        id: 'reporte_tarjeta_btn_print',
        text: 'Imprimir',
        iconCls: 'fas fa-print text-primary',
        handler: function(This, e){
            App.ShowWaitMsg();
            var store = Ext.getCmp('id_grid_reporte_tarjeta').getStore();
            var send = [];
            Ext.Array.each(store.data.items,function(valor){
                send.push(valor.data);
            });

            var _result = App.PerformSyncServerRequest(Routing.generate('printReporteTarjeta'),{store:Ext.encode(send)});
            App.HideWaitMsg();
            if(_result.success){
                var newWindow = window.open('', '', 'width=800, height=500'),
                    document = newWindow.document.open();

                document.write(_result.html);
                document.close();
                newWindow.print();
            }
        }
    });

    var _btnExport = Ext.create('Ext.button.MyButton',{
        id: 'reporte_tarjeta_btn_export',
        text: 'Exportar',
        iconCls: 'fa fa-download fa-1_4',
        handler: function(This, e){
            App.ShowWaitMsg();
            var store = Ext.getCmp('id_grid_reporte_tarjeta').getStore();
            var send = [];
            Ext.Array.each(store.data.items,function(valor){
                send.push(valor.data);
            });

            var _result = App.PerformSyncServerRequest(Routing.generate('printReporteTarjeta'),{store:Ext.encode(send)});
            App.HideWaitMsg();
            if(_result.success){
                window.open('data:application/vnd.ms-excel,' + encodeURIComponent(_result.html));
//                var newWindow = window.open('', '', 'width=800, height=500'),
//                    document = newWindow.document.open();
//
//                document.write(_result.html);
//                document.close();
//                newWindow.print();
            }
        }
    });

    var _tbar = Ext.getCmp('reporte_tarjeta_tbar');
    _tbar.add('-');
    _tbar.add(_btnPrint);
    _tbar.add('-');
    _tbar.add(_btnExport);
});
