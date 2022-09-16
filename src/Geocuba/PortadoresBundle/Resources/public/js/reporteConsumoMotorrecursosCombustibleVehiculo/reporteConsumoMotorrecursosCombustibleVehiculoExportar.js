Ext.onReady(function () {

var _btn_print = Ext.create('Ext.button.MyButton',{
    id: '_btn_print_motorrecurso_combustible_vehiculo',
    text: 'Imprimir',
    iconCls: 'fas fa-print text-primary',
    handler: function(This, e){
        var store = Ext.getCmp('id_grid_motorrecurso_combustible_vehiculo').getStore();
        var obj = {};
        var send = [];
        Ext.Array.each(store.data.items,function(valor){
            send.push(valor.data);
        });
        obj.mes = Ext.getCmp('mes_anno').getValue().getMonth()+1;
        obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
        obj.store = Ext.encode(send);

        App.request('POST', App.buildURL('/portadores/consumo_motorrecurso/print'), obj, null, null,
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

var _btn_export = Ext.create('Ext.button.MyButton',{
    id: '_btn_export_motorrecurso_combustible_vehiculo',
    text: 'Exportar',
    // disabled:true,
    iconCls: 'fas fa-file-excel text-primary',
    handler: function(This, e){
        var store = Ext.getCmp('id_grid_motorrecurso_combustible_vehiculo').getStore();
        var send = [];
        Ext.Array.each(store.data.items,function(valor){
            send.push(valor.data);
        });
        var obj = {};
        obj.mes = Ext.getCmp('mes_anno').getValue().getMonth()+1;
        obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
        obj.store = Ext.encode(send);
        App.request('POST', App.buildURL('/portadores/consumo_motorrecurso/print'), obj, null, null,
            function (response) { // success_callback
                window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
            }
        );
    }
});

var _tbar = Ext.getCmp('motorrecurso_combustible_vehiculo_tbar');
_tbar.add('->');
_tbar.add(_btn_print);
_tbar.add(_btn_export);
_tbar.setHeight(36);

});