/**
 * Created by yosley on 09/03/2016.
 */



Ext.onReady(function () {

    Ext.define('cda001_meses', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'nromes', type: 'int'},
            {name: 'mes', type: 'string'},
            {name: 'cant_dias', type: 'int'}
        ]
    });

    var meses_cda001 = Ext.create('Ext.data.Store', {
        model: 'cda001_meses',
        id: 'id_store_meses_cda001',
        data: [
            {id: '1', nromes: 1, mes: 'Enero', cant_dias: 31},
            {id: '2', nromes: 2, mes: 'Febrero', cant_dias: 28},
            {id: '3', nromes: 3, mes: 'Marzo', cant_dias: 31},
            {id: '4', nromes: 4, mes: 'Abril', cant_dias: 31},
            {id: '5', nromes: 5, mes: 'Mayo', cant_dias: 31},
            {id: '6', nromes: 6, mes: 'Junio', cant_dias: 31},
            {id: '7', nromes: 7, mes: 'Julio', cant_dias: 31},
            {id: '8', nromes: 8, mes: 'Agosto', cant_dias: 31},
            {id: '9', nromes: 9, mes: 'Septiembre', cant_dias: 31},
            {id: '10', nromes: 10, mes: 'Octubre', cant_dias: 31},
            {id: '11', nromes: 11, mes: 'Noviembre', cant_dias: 30},
            {id: '12', nromes: 12, mes: 'Diciembre', cant_dias: 31}
        ]
    });


    var Limpiar = Ext.create('Ext.Button', {
        // text: 'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function () {
            Ext.getCmp('combo_portador').reset();
        }
    });

    var _btn_generar = Ext.create('Ext.Button', {
        text: 'Generar CDA002',
        disabled: false,
        iconCls: 'fas fa-retweet text-primary',
        id: 'generar_cda002',
        handler: function () {
            Ext.getCmp('cda002_btn_guardar').setDisabled(false);
            var cantidad_elementos_store = Ext.getCmp('id_grid_cda002').getStore().getTotalCount();
            if (cantidad_elementos_store > 0) {
                Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Esta seguro que desea Generar el CDA002 ? Una ves realizada la acción, todo cambio en el mes actual se perderá', function (btn) {
                    if (btn === 'yes') {
                        if (Ext.getCmp('combo_portador').getValue() === null) {
                            App.showAlert('Debe seleccionar un portador para generar el CDA002', 'warning');
                            return;
                        }

                        App.request('POST', App.buildURL('/portadores/cda002/generar'), {
                                portadorid: Ext.getCmp('combo_portador').getValue(),
                                portadorName: Ext.getCmp('combo_portador').getRawValue(),
                                unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                                mes :  Ext.getCmp('mes_anno').getValue().getMonth()+1,
                                anno :  Ext.getCmp('mes_anno').getValue().getFullYear(),
                                moneda :  Ext.getCmp('moneda_combo').getValue(),
                            }, null, null,
                            function (response) { // success_callback
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    Ext.getCmp('cda002_btn_guardar').setDisabled(false);
                                    Ext.getCmp('id_grid_cda002').getStore().load();
                                }
                            }
                        );
                    }

                });

            } else {

                if (Ext.getCmp('combo_portador').getValue() === null) {
                    App.showAlert('Debe seleccionar un portador', 'warning');
                    return;
                }

                App.request('POST', App.buildURL('/portadores/cda002/generar'), {
                        portadorid: Ext.getCmp('combo_portador').getValue(),
                        portadorName: Ext.getCmp('combo_portador').getRawValue(),
                        unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                        mes :  Ext.getCmp('mes_anno').getValue().getMonth()+1,
                        anno :  Ext.getCmp('mes_anno').getValue().getFullYear(),
                        moneda :  Ext.getCmp('moneda_combo').getValue(),
                    }, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                            Ext.getCmp('cda002_btn_guardar').setDisabled(false);
                            Ext.getCmp('id_grid_cda002').getStore().load();
                        }
                    }
                );
            }
        }
    });

    var _btnGuardar = Ext.create('Ext.button.MyButton', {
        id: 'cda002_btn_guardar',
        text: 'Guardar CDA002 ',
        disabled: true,
        iconCls: 'fas fa-save text-primary',
        width: 130,
        handler: function (This, e) {
            if (Ext.getCmp('combo_portador').getValue() === null) {
                App.showAlert('Debe seleccionar un portador', 'warning');
                return;
            } else {
                Ext.MessageBox.confirm('Confirmaci&oacute;n', 'Desea guardar los cambios Realizados', function (btn) {
                    if (btn === 'yes') {
                        var store = Ext.getCmp('id_grid_cda002').getStore();
                        var obj = {};
                        var send = [];
                        Ext.Array.each(store.data.items, function (valor) {
                            send.push(valor.data);
                        });
                        obj.store = Ext.encode(send);

                        App.request('POST', App.buildURL('/portadores/cda002/guardar'), obj, null, null,
                            function (response) { // success_callback
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    Ext.getCmp('id_grid_cda002').getStore().load();
                                    Ext.getCmp('cda002_btn_guardar').setDisabled(false);
                                }
                            }
                        );
                    }
                });
            }

        }
    });

    var _tbar = Ext.getCmp('cda002_tbar');

    _tbar.add(_btn_generar);
    _tbar.add('-');
    _tbar.add(_btnGuardar);

    _tbar.setHeight(45);
});