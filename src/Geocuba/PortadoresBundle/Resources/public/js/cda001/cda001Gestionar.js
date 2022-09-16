/**
 * Created by yosley on 06/01/2016.
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

    function damemes(Nromes) {
        if (Nromes === 1) {
            return 'Enero';
        } else if (Nromes === 2) {
            return 'Febrero';
        } else if (Nromes === 3) {
            return 'Marzo';
        } else if (Nromes === 4) {
            return 'Abril';
        } else if (Nromes === 5) {
            return 'Mayo';
        } else if (Nromes === 6) {
            return 'Junio';
        } else if (Nromes === 7) {
            return 'Julio';
        } else if (Nromes === 8) {
            return 'Agosto';
        } else if (Nromes === 9) {
            return 'Septiembre';
        } else if (Nromes === 10) {
            return 'Octubre';
        } else if (Nromes === 11) {
            return 'Noviembre';
        } else if (Nromes === 12) {
            return 'Diciembre';
        }
    }

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

    Ext.Date.patterns = {
        ISO8601Long: "Y-m-d H:i:s",
        ISO8601Short: "Y-m-d",
        ShortDate: "n/j/Y",
        LongDate: "l, F d, Y",
        FullDateTime: "l, F d, Y g:i:s A",
        MonthDay: "F d",
        ShortTime: "g:i A",
        LongTime: "g:i:s A",
        SortableDateTime: "Y-m-d\\TH:i:s",
        UniversalSortableDateTime: "Y-m-d H:i:sO",
        YearMonth: "F, Y"
    };

    Ext.define('cda_meses', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'nromes', type: 'int'},
            {name: 'mes', type: 'string'},
            {name: 'cant_dias', type: 'int'}
        ]
    });

    var meses = Ext.create('Ext.data.Store', {
        model: 'cda_meses',
        id: 'id_store_meses',
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

    //DEFINE LA CLASE FILEFIELD
    Ext.define('Ext.enhance.form.field.File', {
        override: 'Ext.form.field.File',
        onFileChange: function (button, e, value) {
            this.duringFileSelect = true;
            Ext.form.field.File.superclass.setValue.call(this, value.replace(/^.*(\\|\/|\:)/, ''));
            delete this.duringFileSelect;
        }
    });

    var _Exportar = Ext.create('Ext.button.MyButton', {
        id: 'cda001_Electricidad_btn_expor',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        handler: function () {
            var store_cda = Ext.getCmp('id_grid_cda001').getStore();
            if (store_cda.getCount() !== 0) {
                var flag = false;
                Ext.Array.each(store_cda.data.items, function (valor) {
                        if (valor.data.actividad_nombre === "") {
                            flag = true
                        }
                    }
                );

                if (flag === true) {
                    App.showAlert('Existe actividad nueva sin definir nombre', 'warning');
                } else {
                    var obj = {};
                    var send = [];
                    Ext.Array.each(store_cda.data.items, function (valor) {
                        send.push(valor.data);
                    });
                    obj.portadorid = Ext.getCmp('select_portadorid').getValue();
                    obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                    obj.store = Ext.encode(send);

                    App.request('POST', App.buildURL('/portadores/cda001/export'), obj, null, null,
                        function (response) { // success_callback
                            window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                        }
                    );
                }
            } else
                App.showAlert('No existen datos para exportar.', 'warning');
        }
    });

    var menuButton = Ext.create('Ext.Button', {
        id: 'id_menu_button',
        text: 'Acciones',
        iconCls: 'fa fa-bars text-primary',
        width: 120,
        // disabled: true,
        // arrowAlign: 'bottom',
        menu: [
            {
                width: 150,
                text: 'Generar CDA001',
                id: 'generar_cda001',
                tooltip: 'Generar Datos del CDA001',
                // iconCls: 'fa fa-retweet',
                glyph: 0xf079,
                disabled: false,
                handler: function () {
                    if (Ext.getCmp('select_portadorid').getValue() === null) {
                        App.showAlert('Debe seleccionar un portador para generar el CDA 001', 'warning');
                    } else {
                        var cantidad_elementos_store = Ext.getCmp('id_grid_cda001').getStore().getTotalCount();


                        Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Esta seguro que desea Generar el CDA001 del año ' + Ext.getCmp('fieldAnnoId').getValue() + ' ?', function (btn) {
                            if (btn === 'yes') {
                                var obj = {};
                                obj.portadorid = Ext.getCmp('select_portadorid').getValue();
                                obj.portadorName= Ext.getCmp('select_portadorid').getRawValue();
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                obj.anno = Ext.getCmp('fieldAnnoId').getValue();
                                obj.moneda = Ext.getCmp('moneda_combo').getValue();
                                App.request('GET', App.buildURL('/portadores/cda001/generar'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_cda001').getStore().reload(/*{
                                                params: {
                                                    portadorid: Ext.getCmp('select_portadorid').getValue(),
                                                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                                                }
                                            }*/);
                                        }
                                    }
                                );
                            }

                        });
                    }
                }
            },
            {
                width: 150,
                text: 'Guardar CDA001',
                id: 'guardar_cda001',
                tooltip: 'Guardar Datos del CDA001',
                // iconCls: 'fa fa-save',
                glyph: 0xf0c7,
                disabled: false,
                handler: function () {
                    var flag = false;
                    var store_cda = Ext.getCmp('id_grid_cda001').getStore();
                    Ext.Array.each(store_cda.data.items, function (valor) {
                            if (valor.data.actividad_nombre === "") {
                                flag = true
                            }
                        }
                    );
                    if (Ext.getCmp('select_portadorid').getValue() === null) {
                        App.showAlert('Debe seleccionar un portador', 'warning');
                        return;
                    }
                    else if (Ext.getCmp('id_grid_cda001').getStore().getTotalCount() === 0) {
                        App.showAlert('No hay datos para guardar', 'warning');
                        return;
                    }
                    else if (flag === true) {
                        App.showAlert('No se puede guardar los datos, existe actividad sin definir nombre', 'warning');
                    }
                    else {
                        Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Desea guardar los cambios Realizados?', function (btn) {
                            if (btn === 'yes') {
                                var store = Ext.getCmp('id_grid_cda001').getStore();

                                var obj = {};
                                var send = [];
                                Ext.Array.each(store.data.items, function (valor) {
                                    send.push(valor.data);
                                });
                                obj.store = Ext.encode(send);
                                obj.portadorid = store.data.items[0].data.portadorid;
                                obj.unidadid = store.data.items[0].data.unidadid;
                                obj.anno = Ext.getCmp('fieldAnnoId').getValue();
                                obj.moneda = Ext.getCmp('moneda_combo').getValue();

                                App.request('POST', App.buildURL('/portadores/cda001/guardarCambios'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_cda001').getStore().reload();
                                        }
                                    }
                                );
                            }
                        });
                    }

                }
            },
            {
                width: 150,
                text: 'Adicionar Actividad',
                id: 'adicionar_actividad',
                tooltip: 'Adicionar Actividad',
                // iconCls: 'fa fa-plus-square',
                glyph: 0xf0fe,
                disabled: true,
                handler: function () {
                    if (Ext.getCmp('select_portadorid').getValue() === null) {
                        App.showAlert('Debe seleccionar un portador para adicionar una Actividad', 'warning');
                        return;
                    }
                    Ext.getCmp('id_grid_cda001').getStore().add({
                        id: '',
                        actividadid: '',
                        actividad_nombre: '',
                        portadorid: Ext.getCmp('select_portadorid').getValue(),
                        unidadid: Ext.getCmp('arbolunidades').getSelectionModel().selected.length === 0 ? null : Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                        real_nivel_act: 0.00,
                        real_consumo: 0.00,
                        real_indice: 0.000,
                        acumulado_nivel_act: 0.00,
                        acumulado_consumo: 0.00,
                        acumulado_indice: 0.00,
                        estimado_nivel_act: 0.00,
                        estimado_consumo: 0.00,
                        estimado_indice: 0.00,
                        propuesta_plan_nivel_act: 0.00,
                        propuesta_plan_consumo: 0.00,
                        propuesta_plan_indice: 0.00,
                        plan_final_nivel_act: 0.00,
                        plan_final_consumo: 0.00,
                        plan_final_indice: 0.00,
                        total_desglose_nivel_act: 0.00,
                        total_desglose_consumo: 0.00,
                        total_desglose_indice: 0.00,
                        enero_nivel_act: 0.00,
                        enero_consumo: 0.00,
                        enero_indice: 0.00,
                        febrero_nivel_act: 0.00,
                        febrero_consumo: 0.00,
                        febrero_indice: 0.00,
                        marzo_nivel_act: 0.00,
                        marzo_consumo: 0.00,
                        marzo_indice: 0.00,
                        abril_nivel_act: 0.00,
                        abril_consumo: 0.00,
                        abril_indice: 0.00,
                        mayo_nivel_act: 0.00,
                        mayo_consumo: 0.00,
                        mayo_indice: 0.00,
                        junio_nivel_act: 0.00,
                        junio_consumo: 0.00,
                        junio_indice: 0.00,
                        julio_nivel_act: 0.00,
                        julio_consumo: 0.00,
                        julio_indice: 0.00,
                        agosto_nivel_act: 0.00,
                        agosto_consumo: 0.00,
                        agosto_indice: 0.00,
                        septiembre_nivel_act: 0.00,
                        septiembre_consumo: 0.00,
                        septiembre_indice: 0.00,
                        octubre_nivel_act: 0.00,
                        octubre_consumo: 0.00,
                        octubre_indice: 0.00,
                        noviembre_nivel_act: 0.00,
                        noviembre_consumo: 0.00,
                        noviembre_indice: 0.00,
                        diciembre_nivel_act: 0.00,
                        diciembre_consumo: 0.00,
                        diciembre_indice: 0.00,
                    });
                }
            },
            {
                width: 150,
                text: 'Eliminar Actividad',
                id: 'eliminar_actividad',
                disabled: true,
                tooltip: 'Eliminar Actividad',
                // iconCls: 'fa fa-times',
                glyph: 0xf00d,

                handler: function () {
                    var record = Ext.getCmp('id_grid_cda001').getSelectionModel().getLastSelected();
                    Ext.getCmp('id_grid_cda001').getStore().remove(record);
                    if (Ext.getCmp('guardar_cda001').isDisabled()) {
                        Ext.getCmp('guardar_cda001').setDisabled(false);
                    }
                }
            }
        ]
    });

    var _tbar = Ext.getCmp('cda001_Electricidad_tbar');
    _tbar.add('-');
    _tbar.add(menuButton);
    _tbar.add('->');
    _tbar.add(_Exportar);
    _tbar.setHeight(36);
});
