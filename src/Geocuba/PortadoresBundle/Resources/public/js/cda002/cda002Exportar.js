/**
 * Created by yosley on 09/03/2016.
 */



Ext.onReady(function () {
    var _btn_Export = Ext.create('Ext.button.MyButton', {
        id: 'cda002_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        handler: function (This, e) {
            var store = Ext.getCmp('id_grid_cda002').getStore();
            var flag = false;
            Ext.Array.each(store.data.items, function (valor) {
                    if (valor.data.actividad_nombre === "") {
                        flag = true
                    }
                }
            );

            if (flag === true) {
                App.showAlert('No se puede exportar, existe una actividad nueva sin definir el nombre', 'warning');
                return;
            }
            if (store.getCount() !== 0) {
                var obj = {};
                var send = [];
                Ext.Array.each(store.data.items, function (valor) {
                    send.push(valor.data);
                });

                var summary = Ext.getCmp('id_grid_cda002').getView().normalView.features[0].summaryRecord.data;
                var send_summary = [];
                Ext.Array.each(summary, function (valor) {
                    send_summary.push(valor);
                });
                obj.summary = Ext.encode(send_summary);
                obj.store = Ext.encode(send);
                obj.unidad_nombre = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.siglas;
                obj.unidad_id = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                var portador = Ext.getCmp('combo_portador').getStore().findRecord('id', Ext.getCmp('combo_portador').getValue());

                obj.portador_nombre = portador.data.nombre;
                obj.portador_um = portador.data.unidad_medida;
                var grid = Ext.getCmp('id_grid_cda002');
                obj.consumo_total = grid.getView().all.view.features[0].summaryRecord.data.consumo;
                obj.consumo_total_acum = grid.getView().all.view.features[0].summaryRecord.data.consumo_acum;
                obj.consumo_total_plan = grid.getView().all.view.features[0].summaryRecord.data.consumo_plan;
                obj.mes =  App.getMonthName(Ext.getCmp('mes_anno').getValue().getMonth()+1);
                obj.anno =  Ext.getCmp('mes_anno').getValue().getFullYear();

                App.request('POST', App.buildURL('/portadores/cda002/print'), obj, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                            window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                        }
                    }
                );
            }
            else
                App.showAlert('No Existen datos para exportar', 'warning');
        }
    });

    var _tbar = Ext.getCmp('cda002_tbar');

    _tbar.add('->');
    _tbar.add(_btn_Export);

    _tbar.setHeight(45);
});