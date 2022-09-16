Ext.onReady(function () {

    var btnPrint = Ext.create('Ext.button.MyButton', {
        id: 'btn_prin_resumen_eficiencia',
        text: 'Imprimir',
        disabled: true,
        iconCls: 'fas fa-print text-primary',
        handler: function (This, e) {
            var store = Ext.getCmp('id_grid_resumen_eficiencia').getStore();
            var obj = {};
            var send = [];
            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });
            obj.store = Ext.encode(send);
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            obj.acumulado = Ext.getCmp('chech_acumulado_resumen_eficiencia').getValue();

            App.request('POST', App.buildURL('/portadores/resumen_eficiencia/print'), obj, null, null,
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

    var btnExport = Ext.create('Ext.button.MyButton', {
        id: '_btn_resumen_eficiencia_export',
        text: 'Exportar',
        disabled: true,
        iconCls: 'fas fa-file-excel text-primary',
        handler: function (This, e) {
            var store = Ext.getCmp('id_grid_resumen_eficiencia').getStore();
            var obj = {};
            var send = [];
            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });
            obj.store = Ext.encode(send);
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            obj.acumulado = Ext.getCmp('chech_acumulado_resumen_eficiencia').getValue();

            App.request('POST', App.buildURL('/portadores/resumen_eficiencia/print'), obj, null, null,
                function (response) {
                    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                }
            );
        }
    });

    var _tbar = Ext.getCmp('resumen_eficiencia_tbar');
    _tbar.add('->');
    _tbar.add(btnPrint);
    _tbar.add(btnExport);
    _tbar.setHeight(36);

});