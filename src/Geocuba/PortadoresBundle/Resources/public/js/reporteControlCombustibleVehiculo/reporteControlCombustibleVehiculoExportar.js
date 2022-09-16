Ext.onReady(function () {

    var _btn_Print = Ext.create('Ext.button.MyButton', {
        id: 'reporte_control_combustible_Vehiculo_btn_print',
        text: 'Imprimir',
        iconCls: 'fa fa-print text-primary',
        disabled: true,
        handler: function (This, e) {

            let obj = {};
            obj.chapa = Ext.getCmp('id_comboSearch').getRawValue();
            obj.id = Ext.getCmp('id_comboSearch').getValue();
            obj.unidadid = Ext.getCmp('arbolunidades').getSelection()[0].data.id;
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            obj.export = true;

            App.request('POST', App.buildURL('/portadores/controlCombustibleVehiculo/print'), obj, null, null,
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
        id: 'reporte_control_combustible_Vehiculo_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        disabled: true,
        handler: function (This, e) {
            let obj = {};
            obj.chapa = Ext.getCmp('id_comboSearch').getRawValue();
            obj.id = Ext.getCmp('id_comboSearch').getValue();
            obj.unidadid = Ext.getCmp('arbolunidades').getSelection()[0].data.id;
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            obj.export = true;

            App.request('POST', App.buildURL('/portadores/controlCombustibleVehiculo/print'), obj, null, null,
                function (response) { // success_callback
                    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                }
            );

        }
    });

    let _tbar = Ext.getCmp('tbar_reporte_control_combustible_Vehiculo');
    _tbar.add(_btn_Print);
    _tbar.add(_btn_Export);

});