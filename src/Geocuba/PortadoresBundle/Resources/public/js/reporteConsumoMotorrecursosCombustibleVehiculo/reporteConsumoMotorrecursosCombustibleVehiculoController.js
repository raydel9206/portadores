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
                    while(!Ext.getCmp('id_grid_motorrecurso_combustible_vehiculo') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_motorrecurso_combustible_vehiculo'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let _storec = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_Combustible_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tipocombustible/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true,
        listeners: {
            load: function (store,records) {
                store.insert(0,[{
                    id: 'null',
                    nombre: 'Todos'
                }])
            }
        }
    });

    let tipos_combustible = Ext.create('Ext.form.ComboBox', {
        store: _storec,
        width: 150,
        queryMode: 'local',
        displayField: 'nombre',
        valueField: 'id',
        id: 'id_tipos_combustible',
        emptyText: 'Combustible...',
        listeners: {
            select: function (This, record, eOpts) {
                grid_motorrecurso_combustible_vehiculo.getStore().load();
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

    var store_motorrecurso_combustible_vehiculo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_motorrecurso_combustible_vehiculo',
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
            url: App.buildURL('/portadores/consumo_motorrecurso/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        sorters: [{
            property: 'nro_orden',
            direction: 'ASC'
        }],
        listeners: {
            beforeload: function (This, operation, eOpts)
            {
                operation.setParams({
                    unidadid:Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: mes_anno.getValue().getMonth()+1,
                    anno: mes_anno.getValue().getFullYear(),
                    tipoCombustible: tipos_combustible.getValue()
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
                grid_motorrecurso_combustible_vehiculo.enable();
                // grid_motorrecurso_combustible_vehiculo.getStore().load();
            }
        }
    });

    var grid_motorrecurso_combustible_vehiculo = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_motorrecurso_combustible_vehiculo',
        store: store_motorrecurso_combustible_vehiculo,
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        scrollable: true,
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        }],
        columns: [
            {
                text: '<strong style="align:left;">Nro.</strong>',
                xtype: 'rownumberer',
                align:'center',
                width: 50,
                summaryType: 'count',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return 'Total'
                }
            },
            {
                text: '<strong><h5>Matr&iacute;cula</h5></strong>', dataIndex: 'matricula', width: 150 , locked: false, align: 'center',

            },
            {
                text: '<strong>MES</strong>',
                columns:[
                    {
                        text: '<strong>Motorrecurso</strong>',
                        columns:[
                            {
                                header: '<strong>Plan</strong>', dataIndex: 'plan_motorrecurso_mes', width: 97, align: 'center',
                                summaryType: 'sum',
                                id:'plan_motorrecurso_mes',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Real</strong>', dataIndex: 'real_motorrecurso_mes', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'real_motorrecurso_mes',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>%</strong>', dataIndex: 'porciento_motorrecurso_mes', width: 97, align: 'center',
                                summaryType: 'average',
                                id: 'porciento_motorrecurso_mes',
                                renderer: function(value){
                                    return '<strong>'+value + '%</strong>';
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
                        text: '<strong>Combustible</strong>',
                        columns:[
                            {
                                header: '<strong>Plan</strong>', dataIndex: 'plan_combustible_mes', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'plan_combustible_mes',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Real</strong>', dataIndex: 'real_combustible_mes',  width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'real_combustible_mes',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>%</strong>', dataIndex: 'porciento_combustible_mes',  width: 97, align: 'center',
                                summaryType: 'average',
                                id: 'porciento_combustible_mes',
                                renderer: function(value){
                                    return '<strong>'+value + '%</strong>';
                                },
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    var plan_combustible_mes = summaryData.plan_combustible_mes;
                                    var real_combustible_mes = summaryData.real_combustible_mes;

                                    var porciento = (real_combustible_mes / plan_combustible_mes) * 100;
                                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(porciento, '0.00'));
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            }
                        ]
                    }
                ]
            },
            {
                text: '<strong>ACUMULADO HASTA EL MES</strong>',
                columns:[
                    {
                        text: '<strong>Motorrecurso</strong>',
                        columns:[
                            {
                                header: '<strong>Plan</strong>', dataIndex: 'plan_motorrecurso_acumulado', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'plan_motorrecurso_acumulado',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Real</strong>', dataIndex: 'real_motorrecurso_acumulado', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'real_motorrecurso_acumulado',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>%</strong>', dataIndex: 'porciento_motorrecurso_acumulado', width: 97, align: 'center',
                                summaryType: 'average',
                                id: 'porciento_motorrecurso_acumulado',
                                renderer: function(value){
                                    return '<strong>'+value + '%</strong>';
                                },
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    var plan_motorrecurso_acumulado = summaryData.plan_motorrecurso_acumulado;
                                    var real_motorrecurso_acumulado = summaryData.real_motorrecurso_acumulado;

                                    var porciento = (real_motorrecurso_acumulado / plan_motorrecurso_acumulado) * 100;
                                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(porciento, '0.00'));
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            }
                        ]
                    },
                    {
                        text: '<strong>Combustible</strong>',
                        columns:[
                            {
                                header: '<strong>Plan</strong>', dataIndex: 'plan_combustible_acumulado', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'plan_combustible_acumulado',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Real</strong>', dataIndex: 'real_combustible_acumulado', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'real_combustible_acumulado',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>%</strong>', dataIndex: 'porciento_combustible_acumulado', width: 97, align: 'center',
                                summaryType: 'average',
                                id: 'porciento_combustible_acumulado',
                                renderer: function(value){
                                    return '<strong>'+value + '%</strong>';
                                },
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    var plan_combustible_acumulado = summaryData.plan_combustible_acumulado;
                                    var real_combustible_acumulado = summaryData.real_combustible_acumulado;

                                    var porciento = (real_combustible_acumulado / plan_combustible_acumulado) * 100;
                                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(porciento, '0.00'));
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            }
                        ]
                    }
                ]
            },
            {
                text: '<strong>AÑO</strong>',
                columns:[
                    {
                        text: '<strong>Motorrecurso</strong>',
                        columns:[
                            {
                                header: '<strong>Plan</strong>', dataIndex: 'plan_motorrecurso_anno', width: 97, align: 'center',
                                summaryType:'sum',
                                id: 'plan_motorrecurso_anno',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Real Acumul.</strong>', dataIndex: 'real_motorrecurso_acumulado', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'real_motorrecurso_anno',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>%</strong>', dataIndex: 'porciento_motorrecurso_anno', width: 97, align: 'center',
                                summaryType: 'average',
                                id: 'porciento_motorrecurso',
                                renderer: function(value){
                                    return '<strong>'+value + '%</strong>';
                                },
                                summaryRenderer: function (value, summaryData, dataIndex) {
                                    var plan_motorrecurso_anno = summaryData.plan_motorrecurso_anno;
                                    var real_motorrecurso_anno = summaryData.real_motorrecurso_acumulado;
                                    var porciento = (real_motorrecurso_anno / plan_motorrecurso_anno) * 100;
                                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(porciento, '0.00'));
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Faltan Rec.</strong>', dataIndex: 'faltan_motorrecurso_anno', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'faltan_motorrecurso_anno',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            }
                        ]
                    },
                    {
                        text: '<strong>Combustible</strong>',
                        columns:[
                            {
                                header: '<strong>Plan</strong>', dataIndex: 'plan_combustible_anno', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'plan_combustible_anno',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
                                field: {
                                    xtype: 'numberfield'
                                }
                            },
                            {
                                header: '<strong>Real</strong>', dataIndex: 'real_combustible_acumulado', width: 97, align: 'center',
                                summaryType: 'sum',
                                id: 'real_combustible_anno',
                                summaryRenderer: function(value, summaryData, dataIndex) {
                                    return "<strong>"+Ext.util.Format.round(value,2)+"</strong>";
                                },
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
            items: [mes_anno, tipos_combustible]
        },
        plugins: 'gridfilters'
    });

    var panel_motorrecurso_combustible_vehiculo = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_motorrecurso_combustible_vehiculo',
        title: 'Consumo Motorrecursos y Combustible x Vehículos',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid_motorrecurso_combustible_vehiculo]
    });

    App.render(panel_motorrecurso_combustible_vehiculo);
});