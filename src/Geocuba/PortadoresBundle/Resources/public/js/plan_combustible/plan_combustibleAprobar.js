/**
 * Created by pfcadenas on 11/11/2016.
 */

Ext.onReady(function () {

    var _btn_Aprobar = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_combustible_btn_aprobar',
        text: 'Aprobar',
        iconCls: 'fa fa-check-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            Ext.Msg.show({
                title: 'Aprobar Planificación',
                message: '¿Está seguro que desea aprobar la planificación?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var store = Ext.getCmp('id_grid_planificacion_combustible').getSelection();
                        var send = [];
                        Ext.Array.each(store, function (valor) {
                            send.push(valor.data);
                        });
                        var store_send = Ext.encode(send);

                        App.request('POST', App.buildURL('/portadores/plan_combustible/aprobar'), store_send, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_planificacion_combustible').getStore().load();
                                Ext.getCmp('id_grid_planificacion_combustible').getSelectionModel().deselectAll();
                                Disable();
                                Ext.getCmp('planificacion_combustible_btn_mod').disable();
                            }
                        });
                    }
                }
            });

            App.ConfirmMessage(function () {
                var store = Ext.getCmp('id_grid_planificacion_combustible').getSelection();
                var send = [];
                Ext.Array.each(store, function (valor) {
                    send.push(valor.data);
                });
                var store_send = Ext.encode(send);
                var _result = App.PerformSyncServerRequest(Routing.generate('aprobarPlanCombustible'), {store: store_send});
                App.HideWaitMsg();
                App.InfoMessage('Información', _result.message, _result.cls);
                Ext.getCmp('id_grid_planificacion_combustible').getStore().load();
                Ext.getCmp('id_grid_planificacion_combustible').getSelectionModel().deselectAll();
                Disable();
                Ext.getCmp('planificacion_combustible_btn_mod').disable();
            }, "Está seguro que desea aprobar la planificación seleccionada?");
        }
    });

    var _btn_desaprobar = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_combustible_btn_desaprobar',
        text: 'Desaprobar',
        iconCls: 'fa fa-times fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            App.ConfirmMessage(function () {
                App.ShowWaitMsg();
                var store = Ext.getCmp('id_grid_planificacion_combustible').getSelection();
                var send = [];
                Ext.Array.each(store, function (valor) {
                    send.push(valor.data);
                });
                var store_send = Ext.encode(send);
                var _result = App.PerformSyncServerRequest(Routing.generate('desaprobarPlanCombustible'), {store: store_send});
                App.HideWaitMsg();
                App.InfoMessage('Información', _result.message, _result.cls);
                Ext.getCmp('id_grid_planificacion_combustible').getStore().load();
                Ext.getCmp('id_grid_planificacion_combustible').getSelectionModel().deselectAll();
            }, "Está seguro que desea desaprobar la demanda seleccionada?");
        }
    });

    var _tbar = Ext.getCmp('plan_btn_menu');
    _tbar.add(_btn_Aprobar);
    // _tbar.add('-');
    _tbar.add(_btn_desaprobar);
    // _tbar.add('-');
    // _tbar.setHeight(36);

});