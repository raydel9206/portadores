/**
 * Created by Yosley on 14/07/2017.
 */
Ext.onReady(function(){
    var _btnGuardar = Ext.create('Ext.button.MyButton', {
        id: 'btn_guardar',
        text: 'Guardar',
        iconCls: 'fa fa-floppy-o fa-1_4',
        width: 90,
        handler: function (This, e) {

            App.ConfirmMessage(function(){
                App.ShowWaitMsg();
                var store = Ext.getCmp('grid_info_primaria_5073').getStore();
                var store_1 = Ext.getCmp('grid_info_primaria_5073_1').getStore();
                var send = [];
                var send_1 = [];
                Ext.Array.each(store.data.items, function (valor) {
                    send.push(valor.data);
                });
                Ext.Array.each(store_1.data.items, function (valor) {
                    send_1.push(valor.data);
                });

                var obj={};
                obj.store=send;
                obj.store_1=send_1;
                obj.nunidadid=Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;

                App.ShowWaitMsg();
                var _result = App.PerformSyncServerRequest(Routing.generate('guardarInfoPrimaria5073'), obj);
                App.HideWaitMsg();
                App.InfoMessage('Información', _result.message, _result.cls);
                Ext.getCmp('grid_info_primaria_5073').getStore().load();
                Ext.getCmp('grid_info_primaria_5073_1').getStore().load();
            }, "Está usted seguro de guardar los cambios de la unidad "+' '+Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.nombre);




        }
    });

    var _tbar = Ext.getCmp('pane_id_5073_tbar');
    _tbar.add('->');
    _tbar.add(_btnGuardar);
    _tbar.setHeight(36);
})
