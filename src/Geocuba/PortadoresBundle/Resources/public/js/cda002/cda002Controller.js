/**
 * Created by yosley on 09/03/2016.
 */

Ext.onReady(function () {

    let mes_anno = Ext.create('Ext.form.field.Month', {
        format: 'm, Y',
        id: 'mes_anno',
        // fieldLabel: 'Date',
        width: 90,
        value: new Date(App.selected_month + '/1/' + App.selected_year),
        renderTo: Ext.getBody(),
        listeners: {
            boxready: function () {
                let me = this;
                me.selectMonth = new Date(App.selected_month + '/1/' + App.selected_year);

                let assignGridPromise = new Promise((resolve, reject) => {
                    let i = 0;
                    while(!Ext.getCmp('id_grid_cda002') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_cda002'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let monedaStore = Ext.create('Ext.data.JsonStore', {
        storeId: 'monedaStore',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/moneda/loadMoneda'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true,
        listeners: {
            load: function (This, records, successful, eOpts) {
                Ext.getCmp('moneda_combo').setValue(monedaStore.getData().items[0]);
            }
        }
    });

    let monedaCombo = Ext.create('Ext.form.ComboBox', {
        id: 'moneda_combo',
        width: 100,
        store: monedaStore,
        displayField: 'nombre',
        valueField: 'id',
        queryMode: 'local',
        emptyText: 'Moneda...',
        listeners: {
            change: function (This, newValue, oldValue, eOpts) {
                if (Ext.getCmp('combo_portador').getValue() !== null){
                    grid_cda002.getStore().load();
                }
            }
        }
    });

    var tree_store = Ext.create('Ext.data.TreeStore', {
        id: 'store_unidades',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'nombre', type: 'string'},
            {name: 'siglas', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'municipio', type: 'string'},
            {name: 'municipio_nombre', type: 'string'},
            {name: 'provincia', type: 'string'},
            {name: 'provincia_nombre', type: 'string'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad/loadTree'),
            reader: {
                type: 'json',
                rootProperty: 'children'
            }
        },
        sorters: 'nombre',
        listeners: {
            beforeload: function () {
                if (Ext.getCmp('arbolunidades') !== undefined)
                    Ext.getCmp('arbolunidades').getSelectionModel().deselectAll();
            }
        }
    });

    var panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        border: true,
        id: 'arbolunidades',
        hideHeaders: true,
        rootVisible: false,
        collapsible: true,
        collapsed: false,

        collapseDirection: 'left',
        header: {             style: {                 backgroundColor: 'white',                 borderBottom: '1px solid #c1c1c1 !important'             },         },
        layout: 'fit',

        columns: [
            {xtype: 'treecolumn', iconCls: Ext.emptyString, width: 450, dataIndex: 'nombre'}
        ],
        root: {
            text: 'root',
            expanded: true
        },
        listeners: {
            select: function (This, record, tr, rowIndex, e, eOpts) {
                if (record) {
                    if (record) {
                        // Ext.getCmp('select_portadorid').setReadOnly(false);
                        // Ext.getCmp('id_grid_cda002').setTitle('CDA002: ' + record.data.nombre);
                        Ext.getCmp('id_grid_cda002').setDisabled(false);

                        if (Ext.getCmp('combo_portador').getValue()) {
                            Ext.getCmp('id_grid_cda002').getStore().load();
                        }

                    }

                }
            }
        }


    });

    var _storeportadores = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_portadores',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/portador/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    var selec_portador = Ext.create('Ext.form.ComboBox', {
        fieldLabel: 'Portador',
        labelAlign: 'left',
        width: 190,
        labelWidth: 50,
        store: _storeportadores,
        queryMode: 'local',
        displayField: 'nombre',
        valueField: 'id',
        id: 'combo_portador',
        listeners: {
            select: function (combo, record, eOpts) {
                // if (__user_unidad != record.data.id) {
                //     if (Ext.getCmp('generar_cda002').isDisabled()) {
                //         Ext.getCmp('generar_cda002').setDisabled(true);
                //     }
                // } else {
                //     if (Ext.getCmp('generar_cda002').isDisabled()) {
                //         Ext.getCmp('generar_cda002').setDisabled(false);
                //     }
                // }
                Ext.getCmp('id_grid_cda002').getStore().load();
            }
        }
    });

    Ext.define('Cda002', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id'},
            {name: 'codigo'},
            {name: 'actividad'},
            {name: 'nivel_activ', type: 'number'},
            {name: 'consumo', type: 'number'},
            {name: 'indice', type: 'number'},
            {name: 'nivel_activ_acum', type: 'number'},
            {name: 'consumo_acum', type: 'number'},
            {name: 'indice_acum', type: 'number'},

            {name: 'nivel_actividad_plan', type: 'number'},
            {name: 'consumo_plan', type: 'number'},
            {name: 'indice_plan', type: 'number'},
            {name: 'indice_anual', type: 'number'},

            {name: 'mes'},
            {name: 'codigo_gae'},
            {name: 'unidad'},
            {name: 'portador'},
            {name: 'actividad_nombre'},
            {name: 'um_actividad_nombre'},

            {name: 'desc_det_id'},
            {name: 'desc_det_cant'},
            {name: 'desc_det_mes'},
            {name: 'desc_det_acumulado'},

            {name: 'desc_bajo_nivel_id'},
            {name: 'desc_bajo_nivel_cant'},
            {name: 'desc_bajo_nivel_mes'},
            {name: 'desc_bajo_nivel_acumulado'},

            {name: 'desc_sobreconsumo_id'},
            {name: 'desc_sobreconsumo_cant'},
            {name: 'desc_sobreconsumo_mes'},
            {name: 'desc_sobreconsumo_acumulado'},

            {name: 'relacion_real_plan'},
            {name: 'relacion_acumulado_aprobado'},
        ]
    });

    var grid_cda002 = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_cda002',
        width: '75%',
        disabled: true,
        columnWidth: '50',
        // flex: 1,
        region: 'center',
        // frame: true,
        columnLines: true,
        viewConfig: {
            stripeRows: true
        },
        store: Ext.create('Ext.data.Store', {
            model: 'Cda002',
            storeId: 'id_store_cda002',
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/cda002/load'),
                reader: {
                    type: 'json',
                    rootProperty: 'rows'
                }
            },
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    if(!selec_portador.getValue())
                        return false;
                    operation.setParams({
                        unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                        portadorid: Ext.getCmp('combo_portador').getValue(),
                        portadorName: Ext.getCmp('combo_portador').getRawValue(),
                        mes: mes_anno.getValue().getMonth()+1,
                        anno: mes_anno.getValue().getFullYear(),
                        moneda: monedaCombo.getValue()
                    });
                },
                load: function (This, records, successful, eOpts) {
                    if (successful) {
                        if (records.length === 0) {
                            App.showAlert('No existen datos para el CDA 002 en el mes de ' +
                                App.getMonthName(mes_anno.getValue().getMonth()+1) + ' ' + mes_anno.getValue().getFullYear() + ' para el portador ' + Ext.getStore('id_store_portadores').findRecord('id', Ext.getCmp('combo_portador').getValue()).data.nombre + (Ext.getCmp('arbolunidades').getSelectionModel() ? '' : ' en la unidad ' + Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.nombre), 'warning');
                        } else {
                            Ext.getCmp('real').setText('<strong>  Real de ' + App.getMonthName(mes_anno.getValue().getMonth()+1) + ' </strong> ');
                            Ext.getCmp('plan').setText('<strong> Plan de ' + App.getMonthName(mes_anno.getValue().getMonth()+1) + ' </strong> ');
                        }
                    }

                }
            }

        }),

        columns: [
            {
                xtype: 'gridcolumn',
                text: '<strong>CODIGO</strong>',
                dataIndex: 'codigo',
                width: 100, align: 'center',
                filter: 'string',
                locked: true
            },
            {
                text: '<strong>Actividad</strong>',
                dataIndex: 'actividad_nombre',
                width: 270, align: 'center',
                locked: true,
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('administrativa') === true) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #40FF00;';
                        return val2;
                    } else
                        return val2;
                }
            },
            {
                text: '<strong>UM. N.ACT</strong>',
                dataIndex: 'um_actividad_nombre',
                width: 150, align: 'center',
                filter: 'string'
            },
            {
                text: '<strong>ACUMULADO DEL AÑO </strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'nivel_activ_acum',
                        width: 100, align: 'right',
                        formatter: "number('0.000')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'consumo_acum',
                        width: 100, align: 'right',
                        formatter: "number('0.000')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        dataIndex: 'indice_acum',
                        width: 100, align: 'right',
                        formatter: "number('0.00000')"
                    }
                ]
            },
            {
                text: '<strong>PLAN</strong>',
                id: 'plan',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'nivel_actividad_plan',
                        width: 100, align: 'right',
                        formatter: "number('0.000')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        }
                    }, {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'consumo_plan',
                        width: 100, align: 'right',
                        formatter: "number('0.000')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        }
                    }, {
                        text: '<strong>Índice</strong>',
                        dataIndex: 'indice_plan',
                        width: 100, align: 'right',
                        formatter: "number('0.00000')"
                    }
                ]
            },
            {
                text: '<strong>REAL</strong>',
                id: 'real',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'nivel_activ',
                        width: 100, align: 'right',
                        formatter: "number('0.000')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision: 3,
                            allowBlank: false
                        }
                    }, {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'consumo',
                        width: 100, align: 'right',
                        formatter: "number('0.000')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision: 3,
                            disable: true,
                            allowBlank: false
                        }
                    }, {
                        text: '<strong>Índice</strong>',
                        dataIndex: 'indice',
                        width: 100, align: 'right',
                        formatter: "number('0.00000')"
                    }
                ]
            },
            {
                text: '<strong>RELACIÓN</strong>',
                columns: [
                    {
                        text: '<strong>REAL vs PLAN</strong>',
                        dataIndex: 'relacion_real_plan',
                        width: 100, align: 'center',
                        filter: 'string',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var indicereal = record.get('indice');
                            var indice_plan = record.get('indice_plan');
                            var relacion = indice_plan === 0 ? 0 : indicereal / indice_plan;
                            if (relacion > 1) {
                                met.style = 'font-style:italic !important;font-weight: bold;background: #F22222 ;';
                            }
                            return Ext.util.Format.number(relacion, '0.00');
                        }
                    }, {
                        text: '<strong>ACUM vs APROB</strong>',
                        dataIndex: 'relacion_acumulado_aprobado',
                        width: 100, align: 'center',
                        filter: 'string',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var indice_acum = record.get('indice_acum');
                            var indice_anual = record.get('indice_anual');
                            var relacion = indice_anual === 0 ? 0 : indice_acum / indice_anual;
                            if (relacion > 1) {
                                met.style = 'font-style:italic !important;font-weight: bold;background: #F22222 ;';
                            }
                            return Ext.util.Format.number(relacion, '0.00');
                        }
                    }
                ]
            },
            {
                text: '<strong>DESCUENTOS</strong>',
                columns: [
                    {
                        text: '<strong>BAJO NIVEL ACTIVIDAD</strong>',
                        columns: [
                            {
                                text: '<strong>Cant</strong>',
                                dataIndex: 'desc_bajo_nivel_cant',
                                width: 90, align: 'center',
                                formatter: "number('0.000')",
                                filter: 'string'
                            }, {
                                text: '<strong>Mes</strong>',
                                dataIndex: 'desc_bajo_nivel_mes',
                                width: 90, align: 'center',
                                formatter: "number('0.000')",
                                filter: 'string'
                            }, {
                                text: '<strong>Acumulado</strong>',
                                dataIndex: 'desc_bajo_nivel_acumulado',
                                width: 90, align: 'center',
                                formatter: "number('0.000')",
                                filter: 'string'
                            }
                        ]
                    }, {
                        text: '<strong>DETERIORO</strong>',
                        columns: [
                            {
                                text: '<strong>Cant</strong>',
                                dataIndex: 'desc_det_cant',
                                width: 90, align: 'center',
                                formatter: "number('0.000')",
                                filter: 'string'
                            }, {
                                text: '<strong>Mes</strong>',
                                dataIndex: 'desc_det_mes',
                                width: 90, align: 'center',
                                formatter: "number('0.000')",
                                filter: 'string'
                            }, {
                                text: '<strong>Acumulado</strong>',
                                dataIndex: 'desc_det_acumulado',
                                width: 90, align: 'center',
                                formatter: "number('0.000')",
                                filter: 'string'
                            }
                        ]
                    }, {
                        text: '<strong>SOBRECONSUMO</strong>',
                        columns: [
                            {
                                text: '<strong>Cant</strong>',
                                dataIndex: 'desc_sobreconsumo_cant',
                                width: 90, align: 'center',
                                formatter: "number('0.000')",
                                filter: 'string'
                            }, {
                                text: '<strong>Mes</strong>',
                                dataIndex: 'desc_sobreconsumo_mes',
                                width: 90, align: 'center',
                                formatter: "number('0.000')",
                                filter: 'string'
                            }, {
                                text: '<strong>Acumulado</strong>',
                                dataIndex: 'desc_sobreconsumo_acumulado',
                                width: 90, align: 'center',
                                formatter: "number('0.000')",
                                filter: 'string'
                            }
                        ]
                    }
                ]
            },
            {
                text: '<strong>CODIGO GAE</strong>',
                dataIndex: 'codigo_gae',
                width: 130, align: 'center',
                filter: 'string',
                hidden: true
            },
            {
                text: '<strong>INDICE ANUAL</strong>',
                dataIndex: 'indice_anual',
                width: 130, align: 'center',
                formatter: "number('0.0000')",
                filter: 'string',

            }
        ],
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        }],
        plugins: {
            ptype: 'cellediting',
            clicksToEdit: 1,
            listeners: {
                edit: function (editor, e, eOpts) {
                    if (Ext.getCmp('cda002_btn_guardar').isDisabled()) {
                        Ext.getCmp('cda002_btn_guardar').setDisabled(false);
                    }

                    if (e.colIdx === 7 || e.colIdx === 8) {
                        var indice = e.record.data.consumo / e.record.data.nivel_activ;
                        e.record.data.indice = indice;

                        Ext.getCmp('id_grid_cda002').getView().refresh();
                        var grid = Ext.getCmp('id_grid_cda002');
                        // console.log(grid.getView().all.view.features[0].summaryRecord.data)
                        // var summary_diesel = grid.getView().features[0].summaryRecord.data['diesel'];
                        //desc_bajo_nivel_cant
                        if (grid.getView().all.view.features[0].summaryRecord.data.consumo && e.record.get('nivel_actividad_plan') > 0 &&
                            e.record.get('consumo_plan') > 0 && e.record.get('indice_plan') > 0 && e.record.get('nivel_actividad_plan') > e.record.get('nivel_activ')) {
                            e.record.data.desc_bajo_nivel_cant = 1;
                        } else {
                            e.record.data.desc_bajo_nivel_cant = 0;
                        }

                        if (e.record.get('desc_det_cant') === 1 && e.record.get('consumo_plan') > e.record.get('consumo')) {
                            e.record.data.desc_bajo_nivel_mes = 1;
                        } else {
                            e.record.data.desc_bajo_nivel_mes = 0;
                        }
                        e.record.data.desc_bajo_nivel_acumulado = e.record.get('desc_det_mes');

                        if (e.record.get('relacion_real_plan') < 1.03) {
                            e.record.data.desc_det_cant = 0;
                        } else {
                            e.record.data.desc_det_cant = 1;
                        }

                        if (e.record.get('desc_det_cant') === 1) {
                            e.record.data.desc_det_mes = (e.record.get('indice') - e.record.get('indice_plan')) * e.record.get('nivel_activ');
                        } else {
                            e.record.data.desc_det_mes = 0;
                        }
                        e.record.data.desc_det_acumulado = e.record.data.desc_det_mes;

                        if (e.record.get('consumo_plan') > 0 && e.record.get('consumo') > e.record.get('consumo_plan')) {
                            e.record.data.desc_sobreconsumo_cant = 1;
                        } else {
                            e.record.data.desc_sobreconsumo_cant = 0;
                        }

                        if (e.record.data.desc_sobreconsumo_cant === 0) {
                            e.record.data.desc_sobreconsumo_mes = e.record.get('consumo') - e.record.get('consumo_plan')
                        } else {
                            e.record.data.desc_sobreconsumo_mes = 0;
                        }
                        e.record.data.desc_sobreconsumo_acumulado = e.record.get('desc_sobreconsumo_mes');
                    }
                    Ext.getCmp('id_grid_cda002').getView().refresh();

                }
            }
        },
        tbar: {
            id: 'cda002_tbar',
            height: 36,
            items: [mes_anno, selec_portador, monedaCombo]
        },
    });


    var _panel_cda002 = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_cda002',
        title: 'CDA002',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid_cda002]
    });

    App.render(_panel_cda002);
});

