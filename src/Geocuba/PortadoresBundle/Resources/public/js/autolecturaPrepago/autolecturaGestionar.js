/**
 * Created by yosley on 10/04/2017.
 */
Ext.define('bitacora', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'nromes', type: 'int'},
        {name: 'mes', type: 'string'},
        {name: 'cant_dias', type: 'int'},
        {name: 'id_l', type: 'string'},
        {name: 'lectura', type: 'string'},
    ]
});
let meses = Ext.create('Ext.data.Store', {
    model: 'bitacora',
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

Ext.define('Portadores.autolectura.Window', {
    extend: 'Ext.window.Window',
    modal: true,
    plain: true,
    resizable: false,

    initComponent: function () {
        this.items = [
            {
                xtype: 'form',
                frame: true,
                width: '100%',
                height: '100%',
                layout: 'anchor',
                bodyStyle: 'padding:5px 5px 0',
                fieldDefaults: {
                    labelAlign: 'top',
                    msgTarget: 'qtip'
                },
                items: [
                    {

                        xtype: 'fieldset',
                        flex: 1,
                        title: 'Lecturas',
                        layout: 'hbox',
                        border: true,
                        collapsible: true,
                        collapsed: false,
                        labelAlign: 'right',
                        labelWidth: 90,
                        items: [
                            {
                                xtype: 'datefield',
                                name: 'fecha_autolectura',
                                id: 'fecha_autolectura',
                                margin: '10 10 10 10',
                                fieldLabel: 'Fecha de Autolectura',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                // listeners: {
                                //     afterrender: function (This) {
                                //         This.setMaxValue(new Date());
                                //     }
                                // }
                            }, {

                                xtype: 'textfield',
                                name: 'lectura_dia',
                                margin: '10 10 10 10',
                                id: 'lectura_dia',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                fieldLabel: 'Lectura Día Actual 9 AM',
                                maskRe: /[0-9. ]/,
                                bodyPadding: 10


                            }
                        ]

                    }
                ]
            }
        ]
        ;

        this.callParent();
    }
});


Ext.onReady(function () {
    let _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'autolecturan_btn_add',
        text: 'Autolecturas',
        glyph: 0xf0e7,
        width: 120,
        handler: function (This, e) {
            let serviciosid = Ext.getCmp('id_grid_prepagoautolecturaservicios').getSelectionModel().getLastSelected();
            Ext.getCmp('id_gridprepagoacumulados').collapse();

            if (serviciosid) {
                let obj = {};
                obj.servicio = serviciosid.data.id;
                obj.mes = App.selected_month;
                App.request('GET', App.buildURL('/portadores/autolectura_prepago/existedesgloseAutolecturaPrepago'), obj, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) {
                            Ext.create('Portadores.autolectura.Window', {
                                title: 'Autolectura',
                                id: 'window_autolectura_id',
                                buttons: [
                                    {
                                        text: 'Aceptar',
                                        width: 70,
                                        handler: function () {
                                            let lectura_dia = Ext.getCmp('lectura_dia').getValue();
                                            let fecha = Ext.getCmp('fecha_autolectura').getValue();
                                            if (fecha === null || lectura_dia === null) {
                                                App.showAlert('Revise existen datos vacios', 'warning', 3500);
                                            } else {
                                                let servicioid = Ext.getCmp('id_grid_prepagoautolecturaservicios').getSelectionModel().getLastSelected();
                                                let window = Ext.getCmp('window_autolectura_id');
                                                let form = window.down('form').getForm();
                                                let datos = form.getValues();
                                                datos.serviciosid = servicioid.data.id;

                                                if (form.isValid()) {
                                                    App.request('POST', App.buildURL('/portadores/autolectura_prepago/addAutolecturaPrepago'), datos, null, null,
                                                        function (response) {
                                                            if (response && response.hasOwnProperty('success') && response.success) {
                                                                window.close();

                                                                Ext.getCmp('id_gridprepago').getStore().load({
                                                                    params: {
                                                                        idservicios: servicioid.data.id
                                                                    }
                                                                });
                                                                Ext.getCmp('id_gridprepago').getStore().sort('id', 'ASC');
                                                                Ext.getCmp('id_gridprepago').getView();
                                                            } else {
                                                                window.show();
                                                                form.markInvalid(response.message);
                                                            }
                                                        });
                                                }
                                            }
                                        }
                                    }, {
                                        text: 'Cancelar',
                                        width: 70,
                                        handler: function () {
                                            Ext.getCmp('window_autolectura_id').close()
                                        }
                                    }
                                ]
                            }).show();
                        } else {
                            App.showAlert('No Puede Realizar Autolectura de un servicio no desglosado', 'danger', 3500);
                        }
                    },
                    function (response) {
                    }, null, true);
            } else {
                App.showAlert('Información', 'Seleccione un Servicio', 'warning', 3500)
            }
        }
    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'autolecturan_btn_mod',
        text: 'Corregir Lecturas',
        glyph: 0xf303,
        disabled: true,
        width: 130,
        itemId: 'corregir',
        handler: function (This, e) {
            let selection = Ext.getCmp('id_gridprepago').getSelectionModel().getLastSelected();
            let servicioid = Ext.getCmp('id_grid_prepagoautolecturaservicios').getSelectionModel().getLastSelected();
            if (!selection) {
                App.showAlert('Por favor seleccione la autolectura del ultimo dia ', 'warning', 3500);
            }
            else {
                let obj = {};
                obj.fecha_autolectura = Ext.util.Format.date(selection.data.fecha_autolectura, 'Y-m-d');
                obj.servicio = servicioid.id;
                obj.anno = App.selected_year;
                App.request('GET', App.buildURL('/portadores/autolectura_prepago/getIsLastLect'), obj, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) {
                            let win = Ext.create('Portadores.autolectura.Window', {
                                title: 'Autolectura',
                                id: 'window_autolectura_id',
                                buttons: [
                                    {
                                        text: 'Aceptar',
                                        width: 70,
                                        handler: function () {
                                            let form = win.down('form').getForm();
                                            if (form.isValid()) {
                                                win.hide();
                                                let obj = form.getValues();
                                                obj.id = selection.data.id;
                                                obj.serviciosid = servicioid.data.id;
                                                App.request('POST', App.buildURL('/portadores/autolectura_prepago/modAutolecturaPrepago'), obj, null, null,
                                                    function (response) {
                                                        if (response && response.hasOwnProperty('success') && response.success) {
                                                            Ext.getCmp('id_gridprepago').getStore().load({
                                                                params: {
                                                                    idservicios: servicioid.data.id
                                                                }
                                                            });
                                                            win.close();
                                                            Ext.getCmp('id_gridprepago').getStore().sort('id', 'ASC');
                                                            Ext.getCmp('id_gridprepago').getView();
                                                        } else {
                                                            win.show();
                                                            form.markInvalid(response.message);
                                                        }
                                                    }, function (response) {
                                                    });
                                            }
                                        }
                                    },
                                    {
                                        text: 'Cancelar',
                                        width: 70,
                                        handler: function () {
                                            Ext.getCmp('window_autolectura_id').close()
                                        }
                                    }
                                ]
                            }).show();
                            win.down('form').loadRecord(selection);
                        } else {
                            App.showAlert('No puede entrar autolecturas de dias anteriores a la ultima autolectura tomada para este servicio.', 'danger', 3500);
                        }
                    },
                    function (response) {
                    }, null, true);

            }
        }
    });

    let _btn_Acumulados = Ext.create('Ext.button.MyButton', {
        id: 'autolecturaprepago_btn_acumulados',
        text: 'Acumulados',
        glyph: 0xf201,
        width: 120,
        handler: function (This, e) {
            if (Ext.getCmp('id_gridprepago').getStore().count() === 0) {
                App.showAlert('No Existen Autolecturas', 'warning', 3500);
            }
            else {
                let servicioid = Ext.getCmp('id_grid_prepagoautolecturaservicios').getSelectionModel().getLastSelected();
                if (servicioid) {
                    let idservicios = {};
                    idservicios.id = servicioid.data.id;
                    Ext.getCmp('id_gridprepagoacumulados').setHidden(false);
                    Ext.getCmp('id_gridprepagoacumulados').expand();

                    App.request('POST', App.buildURL('/portadores/autolectura_prepago/acumulados'), idservicios, null, null,
                        function (response) {
                            if (response && response.hasOwnProperty('success') && response.success) {
                                console.log(response);
                                Ext.getCmp('id_plan').setValue(response.array.acumulado_plan);
                                Ext.getCmp('id_real').setValue(response.array.acumulado_real);
                                Ext.getCmp('id_diferencia').setValue(response.array.diferencia);
                                Ext.getCmp('id_plan_real').setValue(response.array._plan_real);
                            }
                        }, function (response) {
                        });
                } else {
                    App.showAlert('Seleccione un servicio ', 'warning', 3500);
                }
            }

        }
    });

    Ext.define('bitacora_mes', {
        extend: 'Ext.data.Model'

    });

    let _serviciosBitacora = Ext.create('Ext.data.JsonStore', {
        storeId: '_serviciosBitacora',
        fields: [
            {name: 'id'},
            {name: 'nombre_servicio'},
            {name: 'codigo_cliente'},
            {name: 'factor_metrocontador'},
            {name: 'MaximaDemandaContratada'},
            {name: 'control'},
            {name: 'ruta'},
            {name: 'folio'},
            {name: 'direccion'},
            {name: 'factor_combustible'},
            {name: 'indice_consumo'},
            {name: 'consumo_prom_anno'},
            {name: 'consumo_prom_plan'},
            {name: 'consumo_prom_real'},
            {name: 'capac_banco_transf'},
            {name: 'tipo_servicio'},
            {name: 'turno_trabajo'},
            {name: 'nunidadid'},
            {name: 'nombreunidadid'},
            {name: 'provicianid'},
            {name: 'nombreprovicianid'},
            {name: 'tarifaid'},
            {name: 'nombretarifaid'},
            {name: 'nactividadid'},
            {name: 'nombrenactividadid'},
            {name: 'num_nilvel_actividadid'},
            {name: 'nombreum_nilvel_actividadid'},
            {name: 'servicio_mayor'},
            {name: 'servicio_prepago'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/servicio/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        filters: [{
            property: 'servicio_prepago',
            value: /true/
        }],
        listeners: {
            beforeload: function (This, operation) {
                operation.setParams({
                    unidadid: (Ext.getCmp('paneltree').getSelectionModel().getLastSelected() !== undefined) ? Ext.getCmp('paneltree').getSelectionModel().getLastSelected().data.id : null,
                });
            }
        }
    });

    let _btn_BorrarAutolecturas = Ext.create('Ext.button.MyButton', {
        id: 'autolecturan_btn_delete',
        text: 'Limpiar Lecturas',
        glyph: 0xf51a,
        disabled: true,
        width: 135,
        handler: function (This, e) {
            let grid_datos = Ext.getCmp('id_gridprepago').getSelectionModel().getLastSelected();
            let serviciosid = Ext.getCmp('id_grid_prepagoautolecturaservicios').getSelectionModel().getLastSelected();

            let datos = {};
            datos.servicio_id = serviciosid.data.id;
            datos.fecha_lectura = grid_datos.data.fecha;

            if (grid_datos) {
                Ext.Msg.show({
                    title: '¿Limpiar lecturas?',
                    message: Ext.String.format('Está seguro que desea limpiar las autolecturas posteriores a la fecha seleccionada. Esta Acción no se podrá deshacer?'),
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.QUESTION,
                    fn: function (btn) {
                        if (btn === 'yes') {
                            App.request('POST', App.buildURL('/portadores/autolectura_prepago/cleanautolecturas'), datos, null, null,
                                function (response) {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        App.showAlert('Los datos han sido Eliminados.Introdúscalos nuevamente', 'warning', 3500);
                                        Ext.getCmp('id_gridprepago').getStore().removeAll();
                                        Ext.getCmp('id_gridprepago').getStore().load({
                                            params: {
                                                idservicios: serviciosid.data.id
                                            }
                                        });
                                        Ext.getCmp('id_gridprepago').getStore().sort('id', 'ASC');
                                        Ext.getCmp('id_gridprepago').getView().refresh;
                                    } else {
                                        App.showAlert('Error eliminando los datos. Contacte con el administrador', 'danger', 3500);
                                    }
                                }, function (response) {
                                });
                        }
                    }
                });
            } else
                App.showAlert('Información', 'Por favor selecccione a partir de que fecha desea limpiar las autolecturas', 'warning');
        }
    });

    let bitacora_mes = Ext.create('Ext.data.JsonStore', {
        model: 'bitacora_mes',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'nromes', type: 'int'},
            {name: 'mes', type: 'string'},
            {name: 'cant_dias', type: 'int'},
            {name: 'id_l', type: 'string'},
            {name: 'lectura', type: 'string'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/autolectura_prepago/bitacoraPrepago'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
    });

    let _btn_bitacoraa = Ext.create('Ext.button.MyButton', {
        id: 'autolectura_btn_bitacora',
        text: 'Bitácora',
        tooltip: 'Se muestra la bitacora',
        glyph: 0xf037,
        width: 100,
        disabled: true,
        handler: function () {
            if (Ext.getCmp('id_gridprepago').getStore().count() === 0) {
                App.showAlert('Información', 'No existen datos para mostrar en la Bitácora', 'warning');
            }
            else {
                let servicioid = Ext.getCmp('id_grid_prepagoautolecturaservicios').getSelectionModel().getLastSelected();
                Ext.create('Ext.window.Window', {
                    title: 'Bitácora Electricidad Horario Normal',
                    id: 'window_bitacora_id',
                    height: 600,
                    width: 1024,
                    modal: true,
                    layout: 'fit',
                    items: [
                        {
                            xtype: 'gridpanel',
                            frame: true,
                            id: 'grid_bitacora',
                            columns: [
                                {
                                    text: '<strong>Día</strong>',
                                    dataIndex: 'dia',
                                    flex: 2,
                                    width: 140, align: 'center',

                                },
                                {
                                    text: '<strong>Lectura del Metro</strong>',
                                    columns: [
                                        {
                                            text: '<strong>Anterior 9 AM</strong>',
                                            dataIndex: 'lect_anterior',
                                            width: 100, align: 'center',
                                            filter: 'string',
                                            sortable: true,
                                            flex: 2,
                                            groupable: false
                                        }, {
                                            text: '<strong>Actual 9 AM </strong>',
                                            dataIndex: 'lect_actual',
                                            width: 100, align: 'center',
                                            filter: 'string',
                                            sortable: true,
                                            flex: 2,
                                            groupable: false
                                        }
                                    ]
                                },
                                {
                                    text: '<strong>Consumo Diario Kwh</strong>',
                                    columns: [
                                        {
                                            text: '<strong>Real</strong>',
                                            dataIndex: 'consumo_diario_real',
                                            width: 100, align: 'center',
                                            formatter: "number('0.00')",
                                            sortable: true,
                                            flex: 2,
                                            groupable: false,
                                            renderer: function (value, metaData, record) {
                                                if (record.get('consumo_diario_real') === null) {
                                                    return '-';
                                                }
                                                return Ext.util.Format.round(record.get('consumo_diario_real'), 2);
                                            }
                                        },
                                        {
                                            text: '<strong>Plan</strong>',
                                            dataIndex: 'plan_diario',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            flex: 2,
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Real-Plan</strong>',
                                            dataIndex: 'real_plan',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            flex: 2,
                                            groupable: false,
                                            renderer: function (value, metaData, record) {
                                                if (record.get('real_plan') === null) {
                                                    return '-';
                                                }
                                                return Ext.util.Format.round(record.get('real_plan'), 2);
                                            }
                                        }
                                    ]
                                }, {
                                    text: '<strong>Consumo Acumulado Kwh</strong>',
                                    columns: [
                                        {
                                            text: '<strong>Real</strong>',
                                            dataIndex: 'consumo_acum_real',
                                            width: 100, align: 'center',
                                            formatter: "number('0.00')",
                                            sortable: true,
                                            flex: 2,
                                            groupable: false,
                                            renderer: function (value, metaData, record) {
                                                if (record.get('consumo_acum_real') === null) {
                                                    return '-';
                                                }
                                                return Ext.util.Format.round(record.get('consumo_acum_real'), 2);
                                            }
                                        },
                                        {
                                            text: '<strong>Plan</strong>',
                                            dataIndex: 'consumo_acum_plan',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            flex: 2,
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Real-Plan</strong>',
                                            dataIndex: 'acum_real_plan',
                                            align: 'center',
                                            formatter: "number('0.00')",
                                            sortable: true,
                                            flex: 2,
                                            groupable: false,
                                            renderer: function (value, metaData, record) {
                                                if (record.get('acum_real_plan') === null) {
                                                    return '-';
                                                }
                                                return Ext.util.Format.round(record.get('acum_real_plan'), 2);
                                            }
                                        }
                                    ]
                                }, {
                                    text: '<strong>Perdidas diarias</strong>',
                                    dataIndex: 'perdidaT_dia',
                                    align: 'center',
                                    filter: 'string',
                                    sortable: true,
                                    flex: 2,
                                    groupable: false,
                                    renderer: function (value, metaData, record) {
                                        if (record.get('perdidaT_dia') === null) {
                                            return '-';
                                        }
                                        return Ext.util.Format.round(record.get('perdidaT_dia'), 2);
                                    }
                                }
                            ],
                            store: bitacora_mes
                        }
                    ],
                    dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [
                            {
                                xtype: 'combobox',
                                fieldLabel: 'Servicios',
                                id: 'serviciosid',
                                labelWidth: 60,
                                store: _serviciosBitacora,
                                displayField: 'nombre_servicio',
                                valueField: 'id',
                                emptyText: 'Seleccione el servicio...',
                                value: servicioid.data.id,
                                queryMode: 'local'
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: 'Mes a visualizar',
                                id: 'id_combo_prapgo',
                                labelWidth: 100,
                                store: meses,
                                queryMode: 'local',
                                displayField: 'mes',
                                valueField: 'nromes',
                                emptyText: 'Seleccione el mes...',
                                value: App.selected_month
                            },

                            {
                                xtype: 'button',
                                width: 30,
                                height: 28,
                                tooltip: 'Buscar',
                                iconCls: 'fa fa-search fa-1_4',
                                handler: function () {
                                    if (Ext.getCmp('id_combo_prapgo').getValue() === null || Ext.getCmp('serviciosid').getValue() === null) {
                                        App.showAlert('Información', "Selecione el servicio.", 'danger');
                                    } else {
                                        bitacora_mes.load({
                                            params: {
                                                servicio: Ext.getCmp('serviciosid').getValue(),
                                                mes: Ext.getCmp('id_combo_prapgo').getValue(),
                                                horario: 'normal',
                                                anno: App.selected_year,
                                            }
                                        });
                                    }
                                }
                            }, {
                                xtype: 'button',
                                height: 28,
                                width: 100,
                                id: 'bitacoraprepago_print',
                                text: 'Imprimir',
                                iconCls: 'fa fa-print',
                                handler: function (This, e) {
                                    if (Ext.getCmp('grid_bitacora').getStore().count() !== 0) {
                                        let mes = {};
                                        mes.valor = Ext.getCmp('id_combo_prapgo').getValue();
                                        let store = Ext.getCmp('grid_bitacora').getStore();
                                        let obj = {};
                                        let send = [];
                                        Ext.Array.each(store.data.items, function (valor) {
                                            send.push(valor.data);
                                        });
                                        obj.store = Ext.encode(send);
                                        obj.mes = Ext.getCmp('id_combo_prapgo').getValue();
                                        obj.horario = 'pico';
                                        App.request('POST', App.buildURL('/portadores/autolectura_prepago/printBitacoraPrepago'), obj, null, null,
                                            function (response) {
                                                if (response && response.hasOwnProperty('success') && response.success) {
                                                    let newWindow = window.open('', '', 'width=1020, height=800'),
                                                        document = newWindow.document.open();
                                                    document.write(response.html);
                                                    document.close();
                                                    newWindow.print();
                                                }
                                            }, function (response) {
                                            });
                                    } else {
                                        App.showAlert('No existen valores para Imprimir.', 'warning', 3500);
                                    }
                                }
                            },
                        ]
                    }],
                    buttons: [
                        {
                            xtype: 'button',
                            height: 25,
                            id: 'bitacora_ver',
                            text: 'Consultar',
                            iconCls: 'fa fa-file-text-o',
                            width: 100,
                            handler: function (This, e) {
                                var html = '<p><h4>Introducción:</h4> </<br/><br/> ' +
                                    'La bitácora se implementará en los centros estatales con consumos mayores de 3 mil KWh mensuales,' +
                                    ' con el objetivo de que el centro se autoinspeccione y controle el uso racional y eficiente de la' +
                                    ' energía eléctrica. <br/><br/>' +
                                    'En la bitácora se reflejarán los resultados de la autoinspección diaria que realizará el energético ' +
                                    'del centro o la persona que se designe. Al cierre del mes se evaluará el cumplimiento de los' +
                                    'indicadores establecidos y se firmará por el energético y el director del centro en las tablas al final de la bitácora. <br/><br/>' +
                                    ' En la bitácora se incluirá el resultado de las inspecciones que recibirá el centro de su Empresa u Organismo superior y de las' +
                                    'inspecciones de la Oficina Nacional de Uso Racional de la Energía (ONURE). En estas inspecciones se verificará que el centro se esté' +
                                    'autoinspeccionando y se comprobará el llenado correcto de la bitácora por parte del centro. <br/><br/>' +

                                    '<h4>Lista de chequeo para la autoinspección: </h4><br/><br/> ' +
                                    'El incumplimiento por parte del centro de las medidas que se indican en la lista de chequeo siguiente,' +
                                    'serán las de deficiencias o violaciones que se reflejarán en esta bitácora. <br/><br/>' +
                                    'En el horario pico de 5 a 9 pm:<br/><br/>' +
                                    '1. Deberán paralizarse los hornos de fundición por arco eléctrico.<br/><br/>' +
                                    '2. Desconexión de los sistemas de clima no tecnológicos.<br/><br/>' +
                                    '3. Paralizar los bombeos de fluido (agua, combustibles, etc.)<br/><br/>' +
                                    'excepto en fuentes de abasto a la población y los expendios de combustible automotor<br/><br/>' +
                                    '4. Paralizar durante este horario los frigoríficos, equipos de refrigeración y cámaras frías.<br/><br/>' +
                                    '5. Reducir la iluminación al mínimo, solo para seguridad.<br/><br/>' +
                                    '+ 6. En los centros principales consumidores, los planes de producción deberán garantizar la utilización<br/><br/>' +
                                    'al máximo de las capacidades productivas evitando el trabajo durante este horario.<br/><br/>' +

                                    '<h4>Fuera del horario:</h4><br/><br/>' +
                                    '1. El aire acondicionado deberá estar ajustado para garantizar la temperatura de confort en el local de (24 ºC).<br/><br/>' +
                                    ' 2. El local deberá estar debidamente sellado y no deben existir fugas de aire.<br/><br/>' +
                                    ' 3. Los filtros, evaporadores y condensadores de los equipos de refrigeración y climatización, deben mantenerse siempre limpios.<br/><br/>' +
                                    '4. Los locales que tengan ventanas de cristal y que incida el sol directamente deberán tener quiebrasoles o papeles reflectores.<br/><br/>' +
                                    '5. El aislamiento térmico y estado de las cámaras frías y recintos refrigerados y de sus puertas, debe poseer la máxima' +
                                    'hermeticidad, para impedir la entrada de aire caliente a dichos locales.<br/><br/>' +
                                    '6. En el caso de los frigoríficos se cumplirán estrictamente los programas de apertura y cierre de las cámaras.<br/><br/>' +
                                    '7. Evitar la operación en vacío de los motores<br/><br/>' +
                                    '8. Evitar la explotación de motores con capacidad sobredimensionada respecto a las cargas que mueven los mismos.<br/><br/>' +
                                    '9. Evitar operar transformadores a baja carga (menor al 20%) respecto a su capacidad nominal, si es posible redistribuir las <br/><br/>' +
                                    '10. Revisar los filtros de las bombas. Limpiarlos con frecuencia para evitar que las obstrucciones ocasionen sobre' +
                                    'cargas que aumenten innecesariamente sus consumos de energía.<br/><br/>' +
                                    '11. Revisar toda la instalación de los conductos y tuberías para verificar que no existan fugas en especial en las uniones' +
                                    'de los tramos de tubería, tanques elevados, tanques de servicios sanitarios, cisternas y otros acumuladores de fluidos.<br/><br/>' +
                                    '12. Revisar la temperatura de operación de los conductores eléctricos. El calentamiento puede ser causado, entre otras' +
                                    'cosas por el calibre inadecuado de los conductores o por empalmes y conexiones mal efectuados.<br/><br/>' +
                                    ' 13. Limpiar periódicamente las luminarias, porque la suciedad disminuye el nivel de iluminación de una lámpara hasta en un 20%.<br/><br/>' +
                                    '14. Usar colores claros en las paredes, muros y techos,porque los colores oscuros absorben gran cantidad de luz y' +
                                    'obligan a utilizar más lámparas.<br/><br/>' +
                                    '15. Independizar y sectorizar los circuitos de iluminación, esto ayudará iluminar sólo los lugares que se necesitan.<br/><br/>' +
                                    '</p>';

                                var panell = Ext.create('Ext.panel.Panel', {
                                    title: 'Bitácora autoinspección consumo de energía eléctrica',
                                    width: 600,
                                    height: 500,
                                    scrollable: true,
                                    id: 'id_panel_bitacoraEx',
                                    html: html
                                });
                                Ext.create('Ext.window.Window', {
                                    id: 'window_com_id',
                                    plain: true,
                                    resizable: false,
                                    modal: true,
                                    items: [panell],
                                    buttons: [
                                        {
                                            text: 'Cerrar',
                                            width: 70,
                                            handler: function () {
                                                Ext.getCmp('window_com_id').close();
                                            }
                                        }
                                    ]
                                }).show();
                            }
                        },
                        {
                            text: 'Cerrar',
                            width: 70,
                            height: 25,
                            handler: function () {
                                Ext.getCmp('window_bitacora_id').close();
                            }
                        }
                    ],
                    listeners:{
                        afterrender: function () {
                            _serviciosBitacora.load();
                        }
                    }
                }).show();
            }
        }
    });

    let scrollMenu = Ext.create('Ext.menu.Menu');
    for (let i = 0; i < 50; ++i) {
        scrollMenu.add({
            text: 'Item ' + (i + 1),
        });
    }

    var _tbar = Ext.getCmp('autolectura_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Acumulados);
    _tbar.add('-');
    _tbar.add(_btn_BorrarAutolecturas);
    _tbar.add('-');
    _tbar.add(_btn_bitacoraa);
    _tbar.add(scrollMenu);
    _tbar.setHeight(36);
});