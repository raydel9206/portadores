/**
 * Created by Yosley on 11/07/2017.
 */

Ext.onReady(function () {

    var _btn_Print = Ext.create('Ext.button.MyButton', {
        id: 'equipo_equipo_btn_print',
        text: 'Imprimir',
        iconCls: 'fas fa-print text-primary',
        handler: function (This, e) {
            var store = Ext.getCmp('grid_equipo_equipo').getStore();
            if (store.getTotalCount() == 0) {
                App.showAlert('No existen datos para imprimir', 'warning');
            } else {
                var obj = {};

                obj.unidad_nombre = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.siglas;
                obj.unidad = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                obj.export = true;
                obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
                obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
                obj.tipoCombustible= Ext.getCmp('id_tipos_combustible').getValue();
                obj.nombre_combustible= Ext.getCmp('id_tipos_combustible').getRawValue();

                App.request('GET', App.buildURL('/portadores/analisis_equipo_equipo/print'), obj, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                            var newWindow = window.open('align:center', '', 'width=1600, height=2000'),
                                document = newWindow.document.open();

                            document.write(response.html);
                            document.close();
                            newWindow.print();
                        } else {
                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                window.down('form').getForm().markInvalid(response.errors);
                            }
                        }

                    },
                    function (response) { // failure_callback
                    }, null, true
                );
            }
        }
    });

    var _btn_Export = Ext.create('Ext.button.MyButton', {
        id: 'equipo_equipo_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        menu: [
            {
                text: 'Mensual',
                width: 150,
                handler: function (This, e) {
                    var store = Ext.getCmp('grid_equipo_equipo').getStore();
                    if (store.getTotalCount() == 0) {
                        App.showAlert('No existen datos para exportar', 'warning');
                    }
                    else {
                        var obj = {};

                        obj.unidad_nombre = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.siglas;
                        obj.unidad = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                        obj.export = true;
                        obj.exporttype = "mensual";
                        obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
                        obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
                        obj.tipoCombustible= Ext.getCmp('id_tipos_combustible').getValue();
                        obj.nombre_combustible= Ext.getCmp('id_tipos_combustible').getRawValue();

                        App.request('GET', App.buildURL('/portadores/analisis_equipo_equipo/print'), obj, null, null,
                            function (response) { // success_callback
                                window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                            }
                        );
                    }
                }
            }, {
                text: 'Acumulado',
                width: 150,
                handler: function (This, e) {
                    var store = Ext.getCmp('grid_equipo_equipo_acumulado').getStore();
                    if (store.getTotalCount() === 0) {
                        App.showAlert('No existen datos para exportar', 'warning');
                    }
                    else {
                        var obj = {};
                        obj.unidad_nombre = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.siglas;
                        obj.unidad = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                        obj.export = true;
                        obj.exporttype = "acumulado";
                        obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
                        obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
                        obj.tipoCombustible= Ext.getCmp('id_tipos_combustible').getValue();
                        obj.nombre_combustible= Ext.getCmp('id_tipos_combustible').getRawValue();

                        App.request('GET', App.buildURL('/portadores/analisis_equipo_equipo/print'), obj, null, null,
                            function (response) { // success_callback
                                window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                            }
                        );
                    }
                }
            }

        ],
    });

    var _tbar = Ext.getCmp('equipo_equipo_tbar');

    _tbar.add('->');
    // _tbar.add(_btn_Print);
    _tbar.add(_btn_Export);
// _tbar.add(btnClearSearch);
    _tbar.setHeight(36);
})