/**
 * Created by orlando on 11/01/2017.
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
                    while (!Ext.getCmp('grid_conciliacion_motorrecurso') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('grid_conciliacion_motorrecurso'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let tree_store = Ext.create('Ext.data.TreeStore', {
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

    let groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Actividad: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });

    let store_conciliacion_motorrecurso = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_conciliacion_motorrecurso',
        fields: [
            {name: 'matricula'},
            {name: 'nro_orden'},
            {name: 'plan_motorrecurso_mes', type: 'float'},
            {name: 'real_motorrecurso_mes', type: 'float'},
            {name: 'porciento_motorrecurso_mes', type: 'float'},
            {name: 'plan_combustible_mes', type: 'float'},
            {name: 'real_combustible_mes', type: 'float'},
            {name: 'porciento_combustible_mes', type: 'float'},
            {name: 'plan_motorrecurso_acumulado', type: 'float'},
            {name: 'real_motorrecurso_acumulado', type: 'float'},
            {name: 'porciento_motorrecurso_acumulado', type: 'float'},
            {name: 'plan_combustible_acumulado', type: 'float'},
            {name: 'real_combustible_acumulado', type: 'float'},
            {name: 'porciento_combustible_acumulado', type: 'float'},
            {name: 'plan_motorrecurso_anno', type: 'float'},
            {name: 'porciento_motorrecurso_anno', type: 'float'},
            {name: 'faltan_motorrecurso_anno', type: 'float'},
            {name: 'plan_combustible_anno', type: 'float'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/conciliacion_motorrecursos/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        groupField: 'actividad',
        sorters: [{
            property: 'nro_orden',
            direction: 'ASC'
        }],
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear()
                });
            },
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
        header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'},},
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
                grid_conciliacion_motorrecurso.enable();
                grid_conciliacion_motorrecurso.getStore().load();
            }
        }
    });

    var grid_conciliacion_motorrecurso = Ext.create('Ext.grid.Panel', {
        id: 'grid_conciliacion_motorrecurso',
        store: store_conciliacion_motorrecurso,
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        scrollable: true,
        features: [groupingFeature,{
            ftype: 'summary',
            dock: 'bottom'
        }],
        columns: [
            {
                text: '<strong style="align:left;">Nro.</strong>',
                xtype: 'rownumberer',
                align: 'center',
                width: 50,
                summaryType: 'count',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return 'Total'
                }
            },
            {
                text: '<strong>DENOMINACION DE LA TECNICA<br>POR ESPECIALIDADES<br>Y NIVELES DE ACTIVIDAD</strong>',
                dataIndex: 'denominacion',
                width: 150,
                locked: false,
                align: 'center',

            }, {
                text: '<strong>Matrícula</strong>', dataIndex: 'matricula', width: 150, locked: false, align: 'center',

            },
            {
                text: '<strong>MOTORRECURSOS</strong>',
                columns: [
                    {
                        header: '<strong>Plan Anual</strong>', dataIndex: 'plan_motorrecurso_anno',
                        width: 97,
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    },
                    {
                        header: '<strong>Ejecutado<br>en el Mes</strong>',
                        dataIndex: 'real_motorrecurso_mes',
                        width: 97,
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    }, {
                        header: '<strong>Acumulado</strong>',
                        dataIndex: 'real_motorrecurso_acumulado',
                        width: 97,
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    },
                    {
                        header: '<strong>% de Ejecución</strong>',
                        dataIndex: 'porciento_motorrecurso_anno',
                        width: 97,
                        align: 'center',
                        summaryType: 'average',
                        id: 'porciento_motorrecurso_mes',
                        renderer: function (value) {
                            return '<strong>' + value + '%</strong>';
                        },
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            var plan_motorrecurso_mes = summaryData.plan_motorrecurso_mes;
                            var real_motorrecurso_mes = summaryData.real_motorrecurso_mes;

                            var porciento = (real_motorrecurso_mes / plan_motorrecurso_mes) * 100;
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(porciento, '0.00'));
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    }
                ]
            },

            {
                text: '<strong>CANTIDAD DE COMBUSTIBLE POR TIPO</strong>',
                columns: [
                    {
                        text: '<strong>B-83</strong>',
                        columns: [
                            {
                                header: '<strong>Consumo s/norma</strong>',
                                dataIndex: 'consumo_norma_b83',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Consumo real</strong>',
                                dataIndex: 'consumo_real_b83',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Diferencia</strong>',
                                dataIndex: 'diferencia_b83',
                                width: 97,
                                align: 'center',
                                field: {
                                    xtype: 'numberfield'
                                }
                            }
                        ]
                    }, {
                        text: '<strong>B-90</strong>',
                        columns: [
                            {
                                header: '<strong>Consumo s/norma</strong>',
                                dataIndex: 'consumo_norma_b90',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Consumo real</strong>',
                                dataIndex: 'consumo_real_b90',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Diferencia</strong>',
                                dataIndex: 'diferencia_b90',
                                width: 97,
                                align: 'center',
                                field: {
                                    xtype: 'numberfield'
                                }
                            }
                        ]
                    }, {
                        text: '<strong>B-94</strong>',
                        columns: [
                            {
                                header: '<strong>Consumo s/norma</strong>',
                                dataIndex: 'consumo_norma_b94',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Consumo real</strong>',
                                dataIndex: 'consumo_real_b94',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Diferencia</strong>',
                                dataIndex: 'diferencia_b94',
                                width: 97,
                                align: 'center',
                                field: {
                                    xtype: 'numberfield'
                                }
                            }
                        ]
                    }, {
                        text: '<strong>Diesel</strong>',
                        columns: [
                            {
                                header: '<strong>Consumo s/norma</strong>',
                                dataIndex: 'consumo_norma_diesel',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Consumo real</strong>',
                                dataIndex: 'consumo_real_diesel',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Diferencia</strong>',
                                dataIndex: 'diferencia_diesel',
                                width: 97,
                                align: 'center',
                                summaryType: 'average',
                                field: {
                                    xtype: 'numberfield'
                                }
                            }
                        ]
                    }, {
                        text: '<strong>GLP</strong>',
                        columns: [
                            {
                                header: '<strong>Consumo s/norma</strong>',
                                dataIndex: 'consumo_norma_glp',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Consumo real</strong>',
                                dataIndex: 'consumo_real_glp',
                                width: 97,
                                align: 'center',
                                summaryType: 'sum',
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Diferencia</strong>',
                                dataIndex: 'diferencia_glp',
                                width: 97,
                                align: 'center',
                                field: {
                                    xtype: 'numberfield'
                                }
                            }
                        ]
                    }
                ]
            }
        ],
        tbar: {
            id: 'motorrecurso_combustible_vehiculo_tbar',
            height: 36,
            items: [mes_anno]
        },
        plugins: 'gridfilters'
    });

    var panel_motorrecurso_combustible_vehiculo = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_motorrecurso_combustible_vehiculo',
        title: 'Conciliación de  Motorrecursos',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid_conciliacion_motorrecurso]
    });

    App.render(panel_motorrecurso_combustible_vehiculo);
});