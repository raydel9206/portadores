Ext.onReady(function () {
    const _btnGuardar = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_combustible_btn_mod',
        text: 'Guardar',
        iconCls: 'fas fa-save text-primary',
        disabled: true,
        handler: function (This, e) {
            if (Ext.getStore('store_planificacion_combustible').isFiltered())
                Ext.getStore('store_planificacion_combustible').clearFilter();
            let store = Ext.getStore('store_planificacion_combustible');

            let equipo = '';
            let send = [];
            let flag1 = false;
            let flag2 = false;

            Ext.Array.each(store.data.items, function (valor) {
                if (parseFloat(valor.data['combustible_total']) < parseFloat(valor.data['combustible_consumido'])) {
                    equipo = valor.data['equipo_tecnologico_descripcion'];
                    flag1 = true;
                }
                if (parseFloat(valor.data['nivel_actividad_total']) < parseFloat(valor.data['nivel_actividad_consumido'])) {
                    // vehiculo = valor.data['vehiculo'];
                    flag2 = true;
                }
                send.push(valor.data);
            });
            if (flag1) {
                App.showAlert('La distribución mensual de combustible del equipo '+equipo+' no puede superar el monto planificado para el año', 'danger');
                return;
            }
            if (flag2) {
                App.showAlert('La distribución mensual de actividad del equipo '+equipo+' no puede superar el monto planificado para el año', 'danger');
                return;
            }
            let store_send = Ext.encode(send);
            App.request('PUT', App.buildURL('/portadores/planificacion_combustible_tecn/save'), {store: store_send}, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        This.setStyle('borderColor', '#d8d8d8');
                        This.disable();
                        Ext.getCmp('find_button_vehiculo').setValue(null);
                        Ext.getCmp('grid_planificacion_combustible').getStore().load();
                    } else {
                        if (response && response.hasOwnProperty('errors') && response.errors) {
                        }
                    }
                }
            );
        }
    });

    let _btnMenu = Ext.create('Ext.button.MyButton', {
        id: 'plan_btn_menu',
        text: 'Menu',
        iconCls: 'fa fa-bars text-primary',
        width: 100,
        menu: [
            {
                text: 'Generar',
                glyph: 0xf021,
                handler: function () {
                    let params = {
                        unidad_id: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                        tipo_combustible_id: Ext.getCmp('nTipoCombustibleId').getValue(),
                        anno: Ext.getCmp('fieldAnnoId').getValue()
                    };
                    if (!params.tipo_combustible_id || !params.anno){
                        App.showAlert('Seleccione año y tipo de combustible antes de continuar.', 'warning');
                        return;
                    }
                    Ext.Msg.show({
                        title: '¿Generar Planificación?',
                        message: '¿Está seguro que desea continuar con la acción? <span style="color: darkred">Esto eliminará toda la planificación actual</span>',
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                let params = {
                                    unidad_id: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                                    tipo_combustible_id: Ext.getCmp('nTipoCombustibleId').getValue(),
                                    anno: Ext.getCmp('fieldAnnoId').getValue()
                                };
                                App.request('POST', App.buildURL('/portadores/planificacion_combustible_tecn/generate'), params, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('find_button_vehiculo').setValue(null);
                                        Ext.getCmp('grid_planificacion_combustible').getStore().load();
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
                id: 'planificacion_combustible_btn_aprobar',
                text: 'Aprobar',
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
                                let store = Ext.getCmp('grid_planificacion_combustible').getSelection();
                                let send = [];
                                Ext.Array.each(store, function (valor) {
                                    send.push(valor.data);
                                });
                                let store_send = Ext.encode(send);
                                App.request('PUT', App.buildURL('/portadores/planificacion_combustible_tecn/aprobar'), {store: store_send}, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('find_button_vehiculo').setValue(null);
                                        Ext.getCmp('grid_planificacion_combustible').getStore().load();
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
                                let store = Ext.getCmp('grid_planificacion_combustible').getSelection();
                                let send = [];
                                Ext.Array.each(store, function (valor) {
                                    send.push(valor.data);
                                });
                                let store_send = Ext.encode(send);
                                App.request('PUT', App.buildURL('/portadores/planificacion_combustible_tecn/desaprobar'), {store: store_send}, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('find_button_vehiculo').setValue(null);
                                        Ext.getCmp('grid_planificacion_combustible').getStore().load();
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

    let _tbar = Ext.getCmp('planificacion_combustible_tbar');
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
