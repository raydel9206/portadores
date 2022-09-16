Ext.onReady(function () {
    var _btn_Print = Ext.create('Ext.button.MyButton', {
        id: 'reembolso_semanal_combustible_btn_print',
        text: 'Imprimir',
        iconCls: 'fa fa-print text-primary',
        disabled: true,
        handler: function (This, e) {

            if (Ext.getCmp('id_comboSearch').getValue() === null)
                Ext.getCmp('id_comboSearch').validate();
            if (Ext.getCmp('fecha_desde').getRawValue() === '')
                Ext.getCmp('fecha_desde').validate();
            if (Ext.getCmp('fecha_hasta').getRawValue() === '')
                Ext.getCmp('fecha_hasta').validate();

            if (Ext.getCmp('id_comboSearch').getValue() !== null &&
                (Ext.getCmp('fecha_desde').getRawValue() !== '') &&
                (Ext.getCmp('fecha_hasta').getRawValue() !== '')) {

                let obj = {};
                obj.monedaid = Ext.getCmp('id_comboSearch').getValue();
                obj.fechaDesde = Ext.getCmp('fecha_desde').getRawValue();
                obj.fechaHasta = Ext.getCmp('fecha_hasta').getRawValue();
                obj.unidadid = Ext.getCmp('arbolunidades').getSelection()[0].data.id;
                obj.export = true;


                App.request('POST', App.buildURL('/portadores/reembolso/print'), obj, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                            var newWindow = window.open('', '', 'width=1200, height=700'),
                                document = newWindow.document.open();
                            document.write(response.html);
                            document.close();
                            newWindow.print();
                        }

                    }, null, null, true
                );

            }
        }
    });

    var _btn_Export = Ext.create('Ext.button.MyButton', {
        id: 'reembolso_semanal_combustible_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        disabled: true,
        handler: function (This, e) {

            if (Ext.getCmp('id_comboSearch').getValue() === null)
                Ext.getCmp('id_comboSearch').validate();
            if (Ext.getCmp('fecha_desde').getRawValue() === '')
                Ext.getCmp('fecha_desde').validate();
            if (Ext.getCmp('fecha_hasta').getRawValue() === '')
                Ext.getCmp('fecha_hasta').validate();

            if (Ext.getCmp('id_comboSearch').getValue() !== null &&
                (Ext.getCmp('fecha_desde').getRawValue() !== '') &&
                (Ext.getCmp('fecha_hasta').getRawValue() !== '')) {

                let obj = {};
                obj.monedaid = Ext.getCmp('id_comboSearch').getValue();
                obj.fechaDesde = Ext.getCmp('fecha_desde').getRawValue();
                obj.fechaHasta = Ext.getCmp('fecha_hasta').getRawValue();
                obj.unidadid = Ext.getCmp('arbolunidades').getSelection()[0].data.id;
                obj.unidad_nombre = Ext.getCmp('arbolunidades').getSelection()[0].data.nombre;
                obj.export = true;

                App.request('POST', App.buildURL('/portadores/reembolso/print'), obj, null, null,
                    function (response) { // success_callback
                        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                    }
                );


            }
        }
    });

    let _tbar = Ext.getCmp('tbar_reembolso_semanal_combustible');
    _tbar.add(_btn_Print);
    _tbar.add(_btn_Export);


});