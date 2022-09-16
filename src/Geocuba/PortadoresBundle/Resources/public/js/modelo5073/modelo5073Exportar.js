Ext.onReady(function () {
    var _btn_Print = Ext.create('Ext.button.MyButton', {
        id: 'modelo50723_btn_print',
        text: 'Imprimir',
        iconCls: 'fas fa-print text-primary',
        handler: function (This, e) {
            var obj ={};
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth()+1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            obj.unidadid =Ext.getCmp('arbolunidades').getSelection()[0].data.id;
            obj.accion = true;

            App.request('POST', App.buildURL('/portadores/modelo5073/export'), obj, null, null,
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

            // window.open(App.buildURL("/portadores/modelo5073/print") + "?view_id=" + App.route + "&unidadid=" + Ext.getCmp('arbolunidades').getSelection()[0].data.id + "&accion=true");
        }
    });

    var _btn_Export = Ext.create('Ext.button.MyButton', {
        id: 'modelo50723_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        handler: function (This, e) {
            var summary = Ext.getCmp('grid_modelo5073').getView().normalView.features[0].summaryRecord.data;
            var send = [];
            Ext.Array.each(summary, function (valor) {
                send.push(valor);
            });
            var obj ={};
            obj.summary = Ext.encode(send);
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth()+1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            obj.unidadid =Ext.getCmp('arbolunidades').getSelection()[0].data.id;
            obj.siglas_unidad =Ext.getCmp('arbolunidades').getSelection()[0].data.siglas;
            obj.accion = true;

            App.request('POST', App.buildURL('/portadores/modelo5073/export'), obj, null, null,
                function (response) { // success_callback
                    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                }
            );

            // var mes = Ext.getCmp('mes_anno').getValue().getMonth()+1;
            // var anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            // window.open(App.buildURL("/portadores/modelo5073/export") + "?view_id=" + App.route + "&unidadid=" + Ext.getCmp('arbolunidades').getSelection()[0].data.id + "&anno=" + anno + "&mes=" + mes + "&accion=true");
        }
    });

    var _btn_Serie = Ext.create('Ext.button.MyButton', {
        id: 'serie_btn_export',
        text: 'Serie',
        iconCls: 'fa fa-share-square-o',
        handler: function (This, e) {
            Ext.create('Ext.window.Window', {
                title: 'Serie Cronologica',
                id: 'serie_id',
                height: 200,
                // width: 250,
                // layout: 'fit',
                items: {  // Let's put an empty grid in just to illustrate fit layout
                    xtype: 'grid',
                    flex: 1,
                    columns: [
                        {
                            text: '<strong>Producto</strong>',
                            dataIndex: 'producto',
                            width: 160, align: 'right',
                        },
                        {
                            text: '<strong>Enero</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        },
                        {
                            text: '<strong>Febrero</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        },
                        {
                            text: '<strong>Marzo</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        }, {
                            text: '<strong>Abril</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        }, {
                            text: '<strong>Mayo</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        }, {
                            text: '<strong>Junio</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        }, {
                            text: '<strong>Julio</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        }, {
                            text: '<strong>Agosto</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        }, {
                            text: '<strong>Septiembre</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        }, {
                            text: '<strong>Octubre</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        }, {
                            text: '<strong>Diciembre</strong>',
                            dataIndex: 'producto',
                            width: 100, align: 'right',
                        },


                    ],


                },
                buttons: [
                    {
                        text: 'Cerrar',
                        handler: function () {
                            Ext.getCmp('serie_id').close();
                        }
                    }

                ]
            }).show();
        }
    });

    var _tbar = Ext.getCmp('modelo5073_tbar');
    _tbar.add('->');
    // _tbar.add(_btn_Print);
    _tbar.add(_btn_Export);
    // _tbar.add(_btn_Serie);
    _tbar.setHeight(36);
})