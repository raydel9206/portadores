/**
 * Created by pfcadenas on 11/11/2016.
 */

Ext.onReady(function () {

    var _btnAct = Ext.create('Ext.button.MyButton', {
        id: 'demanda_combustible_btn_act',
        iconCls: 'fas fas fa-check-square text-primary',
        text: 'Guardar',
        width: 100,
        disabled: true,
        handler: function (This, e) {
            _btnAtras.setDisabled(true);

            var store = Ext.getCmp('id_grid_demanda_combustible').getStore();
            var send = [];

            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });

            var obj = {};
            obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            obj.demandas = Ext.encode(send);
            App.request('POST', App.buildURL('/portadores/demanda_combustible/guardar'), obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        _btnAct.setDisabled(true);
                        Ext.getCmp('id_grid_demanda_combustible').getStore().load();
                    } else {
                        if (response && response.hasOwnProperty('errors') && response.errors) {
                            window.down('form').getForm().markInvalid(response.errors);
                        }
                    }
                }
            );
        }
    });

    var _btnAtras = Ext.create('Ext.button.MyButton', {
        id: 'demanda_combustible_btn_back',
        text: 'Deshacer',
        iconCls: 'fas fas fa-undo-alt text-primary',
        width: 100,
        disabled: true,
        handler: function (This, e) {
            This.setDisabled(true);
            _btnAct.setDisabled(true);
            Ext.getCmp('id_grid_demanda_combustible').getStore().reload();
        }
    });

    var _btnReinicar = Ext.create('Ext.button.MyButton', {
        id: 'demanda_combustible_btn_reinciar',
        text: 'Recalcular',
        iconCls: 'fas fa-calculator text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_demanda_combustible').getSelectionModel().getLastSelected();
            Ext.MessageBox.confirm('Confirmaci&oacute;n', '¡Esta acción perderá los datos de la demanda seleccionada! ¿Desea continuar?', function (btn) {
                if (btn == 'yes') {
                    var obj = {};
                    obj.id = selection.data.demanda_id;
                    App.request('GET', App.buildURL('/portadores/demanda_combustible/del'), obj, null, null,
                        function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_demanda_combustible').getStore().reload();
                            }
                        }
                    );
                }

            });


        }
    });

    var _tbar = Ext.getCmp('demanda_combustible_tbar');
    _tbar.add('-');
    _tbar.add(_btnAct);
    _tbar.add('-');
    _tbar.add(_btnAtras);
    _tbar.add('-');
    _tbar.add(_btnReinicar);


});


