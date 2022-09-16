Ext.onReady(function () {
    var _btnGuardar = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_combustible_btn_mod',
        text: 'Guardar',
        iconCls: 'fas fa-save text-primary',
        disabled: true,
        handler: function (This, e) {
            if (Ext.getStore('store_planificacion_combustible').isFiltered())
                Ext.getStore('store_planificacion_combustible').clearFilter();
            var store = Ext.getStore('store_planificacion_combustible');

            var vehiculo = '';
            var send = [];
            var flag1 = false;
            var flag2 = false;
            var flag3 = false;
            var flag4 = false;

            Ext.Array.each(store.data.items, function (valor) {
                if (Math.abs(parseFloat(valor.data['combustible_litros_total_anno']) - parseFloat(valor.data['combustible_litros_total'])) > 0.1) {
                    vehiculo = valor.data['vehiculo'];
                    flag1 = true;
                }

                if (Math.abs(parseFloat(valor.data['nivel_act_kms_total_anno']) - parseFloat(valor.data['nivel_act_kms_total'])) > 0.1) {
                    vehiculo = valor.data['vehiculo'];
                    flag2 = true;
                }

                if (Math.abs(parseFloat(valor.data['lubricante_total_anno']) - parseFloat(valor.data['lubricante_total'])) > 0.1) {
                    vehiculo = valor.data['vehiculo'];
                    flag3 = true;
                }

                if (Math.abs(parseFloat(valor.data['liquido_freno_total_anno']) - parseFloat(valor.data['liquido_freno_total'])) > 0.1) {
                    vehiculo = valor.data['vehiculo'];
                    flag4 = true;
                }
                send.push(valor.data);
            });

            // if (flag1) {
            //     App.showAlert('La distribución mensual de combustible del vehículo '+vehiculo+' no puede superar el monto planificado para el año', 'danger');
            //     return;
            // }
            // if (flag2) {
            //     App.showAlert('La distribución mensual de actividad del vehículo '+vehiculo+' no puede superar el monto planificado para el año', 'danger');
            //     return;
            // }
            // if (flag3) {
            //     App.showAlert('La distribución mensual de lubricante del vehículo '+vehiculo+' no puede superar el monto planificado para el año', 'danger');
            //     return;
            // }
            // if (flag4) {
            //     App.showAlert('La distribución mensual de liquido de freno del vehículo '+vehiculo+' no puede superar el monto planificado para el año', 'danger');
            //     return;
            // }
            var store_send = Ext.encode(send);
            App.request('POST', App.buildURL('/portadores/plan_combustible/mod'), {store: store_send}, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        This.setStyle('borderColor', '#d8d8d8');
                        This.disable();
                        Ext.getCmp('find_button_vehiculo').setValue(null);
                        Ext.getCmp('id_grid_planificacion_combustible').getStore().load();
                    } else {
                        if (response && response.hasOwnProperty('errors') && response.errors) {
                        }
                    }
                },
                function (response) { // failure_callback
                }
            );
        }
    });

    let _btnMenu = Ext.create('Ext.button.MyButton', {
        id: 'plan_btn_menu',
        text: 'Menu',
        disabled: true,
        iconCls: 'fa fa-bars text-primary',
        // cls: 'fa fa-pencil-square-o fa-1_4',
        width: 100,
        menu: [
            {
                id: 'planificacion_combustible_btn_aprobar',
                text: 'Aprobar',
                // iconCls: 'fas fa-check-circle text-primary',
                glyph: 0xf058,
                disabled: true,
                handler: function (This, e) {
                    Ext.Msg.show({
                        title: '¿Aprobar Planificación?',
                        message: '¿Está seguro que desea aprobar la planificación de combustible?',
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                if (Ext.getStore('store_planificacion_combustible').isFiltered())
                                    Ext.getStore('store_planificacion_combustible').clearFilter();
                                var store = Ext.getCmp('id_grid_planificacion_combustible').getSelection();
                                var send = [];

                                Ext.Array.each(store, function (valor) {
                                    send.push(valor.data);
                                });

                                var store_send = Ext.encode(send);
                                App.request('POST', App.buildURL('/portadores/plan_combustible/aprobar'), {store: store_send}, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('find_button_vehiculo').setValue(null);
                                        Ext.getCmp('id_grid_planificacion_combustible').getStore().load();
                                        Disable();
                                        Ext.getCmp('planificacion_combustible_btn_mod').disable();
                                    }
                                });
                            }
                        }
                    });
                }
            },
            {
                id: 'planificacion_combustible_btn_desaprobar',
                text: 'Desaprobar',
                // iconCls: 'fas fa-times-circle text-primary',
                glyph: 0xf057,
                disabled: true,
                handler: function (This, e) {
                    Ext.Msg.show({
                        title: '¿Desaprobar Planificación?',
                        message: '¿Está seguro que desea desaprobar la planificación de combustible?',
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                if (Ext.getStore('store_planificacion_combustible').isFiltered())
                                    Ext.getStore('store_planificacion_combustible').clearFilter();
                                var store = Ext.getCmp('id_grid_planificacion_combustible').getSelection();
                                var send = [];
                                Ext.Array.each(store, function (valor) {
                                    send.push(valor.data);
                                });
                                var store_send = Ext.encode(send);
                                App.request('POST', App.buildURL('/portadores/plan_combustible/desaprobar'), {store: store_send}, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('find_button_vehiculo').setValue(null);
                                        Ext.getCmp('id_grid_planificacion_combustible').getStore().load();
                                        Disable();
                                        Ext.getCmp('planificacion_combustible_btn_mod').disable();
                                    }
                                });
                            }
                        }
                    });
                }
            },
        ],

    });

    var _tbar = Ext.getCmp('planificacion_combustible_tbar');
    _tbar.add(_btnGuardar);
    _tbar.add('-');
    _tbar.add(_btnMenu);
});

Enable = function () {
    if (Ext.getCmp('planificacion_combustible_btn_del'))
        Ext.getCmp('planificacion_combustible_btn_del').enable();
    if (Ext.getCmp('planificacion_combustible_btn_aprobar') !== undefined)
        Ext.getCmp('planificacion_combustible_btn_aprobar').enable();
    if (Ext.getCmp('planificacion_combustible_btn_desaprobar') !== undefined)
        Ext.getCmp('planificacion_combustible_btn_desaprobar').enable();
};

Disable = function () {
    if (Ext.getCmp('planificacion_combustible_btn_del'))
        Ext.getCmp('planificacion_combustible_btn_del').disable();
    if (Ext.getCmp('planificacion_combustible_btn_aprobar') !== undefined)
        Ext.getCmp('planificacion_combustible_btn_aprobar').disable();
    if (Ext.getCmp('planificacion_combustible_btn_desaprobar') !== undefined)
        Ext.getCmp('planificacion_combustible_btn_desaprobar').disable();
};

