/**
 * Created by yosley on 14/07/2017.
 */
Ext.onReady(function () {

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

    let panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        id: 'arbolunidades',
        hideHeaders: true,
        width: 280,
        rootVisible: false,
        border: true,
        collapsible: true,
        collapsed: false,
        region: 'west',
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
                console.log(record)
                if (record) {
                    var obj = {};
                    obj.unidad = record.id;

                    Ext.getCmp('gruidplanpe').expand();
                    // Ext.getCmp('id_grid_totales').expand();
                    Ext.getCmp('gruidplanpe').setTitle(record.data.nombre);
                    App.request('GET', App.buildURL('/portadores/plan_pe/getVehiculosPlanPE'), null, null, null,
                        function (response) { // success_callback
                            var _result = response;
                        }
                    );
                    // var _result = App.PerformSyncServerRequest(Routing.generate('getVehiculosbyunidadPlanAnualPE'), obj);
                    console.log(_result.rows.plan_pe)
                    if (_result) {

                        Ext.getCmp('gruidplanpe').getStore().loadData(_result.rows.plan_pe);

                        // Ext.getCmp('resumen_panel').setHidden(false);
                        // Ext.getCmp('total_inventario').setValue(_result_total.rows.SUB_TOTAL.total_inventario);
                        // Ext.getCmp('total_gasolina').setValue(_result_total.rows.SUB_TOTAL.total_gasolina);
                        // Ext.getCmp('total_diesel').setValue(_result_total.rows.SUB_TOTAL.total_diesel);

                        Ext.getCmp('total_anual_fisico').setValue(_result.rows.TOTAL_ANUAL_FISICO_L.total_anual_fisico);
                        Ext.getCmp('total_diesel_anual_fisico').setValue(_result.rows.TOTAL_ANUAL_FISICO_L.total_diesel_anual_fisico);
                        Ext.getCmp('total_gasolina_anual_fisico').setValue(_result.rows.TOTAL_ANUAL_FISICO_L.total_gasolina_anual_fisico);
                        Ext.getCmp('total_plan_elect_anual_fisico').setValue(_result.rows.TOTAL_ANUAL_FISICO_L.total_plan_elect_anual_fisico);
                        Ext.getCmp('total_plan_glp_anual_fisico').setValue(_result.rows.TOTAL_ANUAL_FISICO_L.total_plan_glp_anual_fisico);
                        Ext.getCmp('total_plan_lubric_anual_fisico').setValue(_result.rows.TOTAL_ANUAL_FISICO_L.total_plan_lubric_anual_fisico);

                        Ext.getCmp('total_CUC').setValue(_result.rows.Valor_CUC.total_cuc);
                        Ext.getCmp('diesel_CUC').setValue(_result.rows.Valor_CUC.diesel_cuc);
                        Ext.getCmp('gasolina_CUC').setValue(_result.rows.Valor_CUC.gasolina_cuc);
                        Ext.getCmp('lubricante_CUC').setValue(_result.rows.Valor_CUC.lubricante_cuc);
                        Ext.getCmp('id_grid_resumen').expand();


                    }

                }

            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                panel_asignado.collapse();
            }
            // select: function (This, record, index, eOpts) {
            //     // Ext.getCmp('id_unidad').setValue(null);
            //     if (record) {
            //         if(Ext.getCmp('btn_listado_candeladas'))
            //         Ext.getCmp('btn_listado_candeladas').enable();
            //     }
            // }
        }


    });

    Ext.define('planpe', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'id_plan', type: 'string'},
            {name: 'modelo', type: 'string'},
            {name: 'inventario', type: 'string'},
            {name: 'diesel', type: 'number'},
            {name: 'gasolina', type: 'number'},
            {name: 'plan_lubric', type: 'number'},
            {name: 'plan_elect', type: 'number'},
            {name: 'plan_glp', type: 'number'},
            {name: 'ind_consumo', type: 'number'},
            {name: 'km_diesel', type: 'number'},
            {name: 'km_gasolina', type: 'number'},
            {name: 'diesel_anual', type: 'number'},
            {name: 'gasolina_anual', type: 'number'},
            {name: 'Actividad', type: 'string'},
            {name: 'actividadid', type: 'string'},
            {name: 'id_vehiculo', type: 'string'}

        ]
    });
    Ext.define('resumen_plan', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'total_diesel_RESUMEN', type: 'number'},
            {name: 'total_gasolina_RESUMEN', type: 'number'},
            {name: 'total_diesel_t', type: 'number'},
            {name: 'total_gasolina_t', type: 'number'},
            {name: 'total_plan_elect_resumen', type: 'number'},
            {name: 'total_plan_glp_resumen', type: 'number'},
            {name: 'total_plan_lubric_resumen', type: 'number'},
            {name: 'total_diesel_importe_resumen', type: 'number'},
            {name: 'total_gasolina_importe_resumen', type: 'number'},
            {name: 'total_cuc_resumen', type: 'number'},
            {name: 'anno', type: 'number'},
            {name: 'id', type: 'string'},


        ]
    });

    // GET  Precio del Combustibles
    App.request('GET', App.buildURL('/portadores/tipocombustible/load'), null, null, null,
        function (response) { // success_callback
            var _result = response;
        }
    );
    // var _result = App.PerformSyncServerRequest(Routing.generate('loadTipoCombustible'));

    var rowEditingPlan = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToMoveEditor: 2,
        autoCancel: false,
        listeners: {
            edit: function (editor, e, eOpts) {

                // var tipoComb = App.PerformSyncServerRequest(Routing.generate('loadTipoCombustible'));
                console.log(e.colIdx)
                console.log(e.record)
                // var new_value=0;
                if (e.colIdx == 2) {
                    // Ext.getCmp('gruidplanpe').getStore().getAt(e.rowIdx).set('gasolina', 0);
                    // e.record.data.km_gasolina=0;
                    // e.record.data.gasolina_anual=0;

                    /* calcular los KLM plan para cada Vehiculo*/
                    var norma = Ext.getCmp('gruidplanpe').getStore().getAt(e.rowIdx).get('ind_consumo');
                    e.record.data.km_diesel = Ext.util.Format.round(e.value * norma, 2);
                    console.log(Ext.util.Format.round(e.value * norma, 2));
                    e.record.data.diesel_anual = e.value * 12;
                    Ext.getCmp('gruidplanpe').getView().refresh();
                    /* datos de los totales */
                    var grid = Ext.getCmp('gruidplanpe');
                    console.log(grid.getView().features[0].summaryRecord.data);

                    var summary_diesel = grid.getView().features[0].summaryRecord.data['diesel'];

                    console.log(summary_diesel)
                    var valor_diesel = summary_diesel * 12;

                    var var_gasolina = Ext.getCmp('total_gasolina_anual_fisico').getValue();

                    Ext.getCmp('total_diesel_anual_fisico').setValue(valor_diesel);
                    Ext.getCmp('total_anual_fisico').setValue(var_gasolina + valor_diesel);
                    Ext.getCmp('total_plan_lubric_anual_fisico').setValue((var_gasolina + valor_diesel) * 0.028);
                    Ext.getCmp('gruidplanpe').getView().refresh();
                    /* valores  en CUC*/

                    console.log(_result)

                    var anual_diesel = Ext.getCmp('total_diesel_anual_fisico').getValue();
                    var cuc_diesel = anual_diesel * _result.rows[0]['precio'];

                    var varcuc_gasolina = Ext.getCmp('gasolina_CUC').getValue();

                    var varcuc_lubric = Ext.getCmp('total_plan_lubric_anual_fisico').getValue();

                    Ext.getCmp('diesel_CUC').setValue(cuc_diesel);

                    Ext.getCmp('total_CUC').setValue(cuc_diesel + varcuc_gasolina);

                    Ext.getCmp('lubricante_CUC').setValue(varcuc_lubric * 1.15);

                    Ext.getCmp('gruidplanpe').getView().refresh();
                    // Ext.getCmp('resumen_panel').getView().refresh();


                } else if (e.colIdx == 3) {
                    // Ext.getCmp('gruidplanpe').getStore().getAt(e.rowIdx).set('diesel', 0);
                    // e.record.data.km_diesel=0;
                    // e.record.data.diesel_anual=0;
                    var norma = Ext.getCmp('gruidplanpe').getStore().getAt(e.rowIdx).get('ind_consumo');
                    e.record.data.km_gasolina = Ext.util.Format.round(e.value * norma, 2);
                    e.record.data.gasolina_anual = e.value * 12;

                    var grid = Ext.getCmp('gruidplanpe');

                    var summary_gasolina = grid.getView().features[0].summaryRecord.data['gasolina'];
                    var valor_gasolina = summary_gasolina * 12;
                    var var_diesel = Ext.getCmp('total_diesel_anual_fisico').getValue();

                    Ext.getCmp('total_gasolina_anual_fisico').setValue(valor_gasolina);
                    Ext.getCmp('total_anual_fisico').setValue(valor_gasolina + var_diesel);
                    Ext.getCmp('total_plan_lubric_anual_fisico').setValue((valor_gasolina + var_diesel) * 0.028);


                    /* valores  en CUC*/

                    console.log(_result)

                    var anual_gasolina = Ext.getCmp('total_gasolina_anual_fisico').getValue();
                    var cuc_gasolina = anual_gasolina * _result.rows[1]['precio'];

                    var varcuc_diesel = Ext.getCmp('diesel_CUC').getValue();

                    var varcuc_lubric = Ext.getCmp('total_plan_lubric_anual_fisico').getValue();

                    Ext.getCmp('gasolina_CUC').setValue(cuc_gasolina);

                    Ext.getCmp('total_CUC').setValue(cuc_gasolina + varcuc_diesel);

                    Ext.getCmp('lubricante_CUC').setValue(varcuc_lubric * 1.15);


                    Ext.getCmp('gruidplanpe').getView().refresh();


                } else if (e.colIdx == 5) {

                    var diesel = Ext.getCmp('gruidplanpe').getStore().getAt(e.rowIdx).get('diesel');
                    e.record.data.km_diesel = e.value * diesel;

                    var gasolina = Ext.getCmp('gruidplanpe').getStore().getAt(e.rowIdx).get('gasolina');
                    console.log(e.value * gasolina)
                    e.record.data.km_gasolina = e.value * gasolina;


                    Ext.getCmp('gruidplanpe').getView().refresh();

                }

                // Ext.getCmp('griddemanda_mensualIdGC').getView().refresh();


            }


        }


    });

    var grid_plan = Ext.create('Ext.grid.Panel', {
        id: 'gruidplanpe',
        region: 'center',
        // height:'100%',
        flex: 1,
        // collapsible: true,
        // collapsed: true,
        // collapseDirection : 'top',
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'id_store_planpe',
            model: 'planpe',
            groupField: 'Actividad',
        }),
        plugins: [rowEditingPlan],

        features: [{
            ftype: 'summary',
            dock: 'bottom',
        },
            {
                ftype: 'grouping',
                groupHeaderTpl: '<b>Actividad: {name} ' + ' ({rows.length} ' + 'Vehículos)</b>',
                hideGroupedHeader: true,
                startCollapsed: true,

            }],

        columns: [
            {
                text: '<strong> </strong>',
                dataIndex: 'id_vehiculo',
                filter: 'string',
                width: 120,
                align: 'center',
                hidden: true

            },
            {
                text: '<strong> </strong>', dataIndex: 'modelo', filter: 'string', width: 120, align: 'center',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>TOTAL</strong>');
                }
            },
            {
                text: '<strong>INVENTARIO FISICO</strong>',
                dataIndex: 'inventario',
                filter: 'string',
                width: 150,
                align: 'center',
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },
            {
                text: '<strong>PLAN COMBUSTIBLE</strong>',
                columns: [
                    {
                        text: '<strong>DT</strong>', dataIndex: 'diesel', filter: 'string', width: 100, align: 'center',
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false,
                            decimalSeparator: ','
                        },

                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        },
                    },
                    {
                        text: '<strong>GM</strong>',
                        dataIndex: 'gasolina',
                        filter: 'string',
                        width: 100,
                        align: 'center',
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false,
                            decimalSeparator: ','
                        },

                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        },
                    },
                ]
            },
            {
                text: '<strong>PLAN LUBRIC.</strong>',
                dataIndex: 'plan_lubric',
                filter: 'string',
                width: 120,
                align: 'center',
                editor: {
                    xtype: 'numberfield',
                    allowBlank: false,
                    decimalSeparator: ','
                },
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                },
            },
            {
                text: '<strong>IND <br> COMSUMO</strong>',
                dataIndex: 'ind_consumo',
                filter: 'string',
                width: 110,
                align: 'center',
                // editor: {
                //     xtype: 'numberfield',
                //     allowBlank: false,
                //     decimalSeparator: ','
                // },

                // summaryType: 'sum',
                // summaryRenderer: function (value) {
                //     return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                // },
            },
            {
                text: '<strong>KILOMETROS <br>DT</strong>',
                dataIndex: 'km_diesel',
                // filter: 'string',
                width: 110,
                align: 'center',

                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                },
            },
            {
                text: '<strong>KILOMETROS <br>GM</strong>',
                dataIndex: 'km_gasolina',
                // filter: 'string',
                width: 110,
                align: 'center',


                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                },
            },
            {
                text: '<strong>DIESEL <br> ANUAL </strong>',
                dataIndex: 'diesel_anual',
                filter: 'string',
                width: 110,
                align: 'center',


                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                },
            },
            {
                text: '<strong>GASOLINA <br> ANUAL </strong>',
                dataIndex: 'gasolina_anual',
                filter: 'string',
                width: 110,
                align: 'center',

                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                },
            }
        ],
        dockedItems: [{
            xtype: 'toolbar',
            // dock: 'bottom',
            ui: 'footer',
            // defaults: {minWidth: minButtonWidth},
            items: [
                // { xtype: 'component', flex: 1 },
                {
                    xtype: 'button', text: 'Guardar',
                    id: 'bottom_id',
                    disabled: true,

                    handler: function () {
                        Ext.create('Ext.window.Window', {
                            title: 'Fecha del Plan',
                            height: 140,
                            width: 300,
                            id: 'id_windofecha',
                            // id:'id_fechaplan',
                            // layout: 'border',
                            items:  // Let's put an empty grid in just to illustrate fit layout
                                [
                                    {
                                        xtype: 'datefield',
                                        fieldLabel: 'Seleccione Fecha',
                                        // labelAlign:'top',
                                        bodyPadding: 10,
                                        id: 'id_fechaplan',
                                        name: 'fecha_plan'
                                    }
                                ],
                            buttons: [
                                {
                                    text: 'Aceptar',
                                    width: 70,
                                    handler: function () {
                                        var fecha = Ext.Date.format(Ext.getCmp('id_fechaplan').getValue(), 'Y-m-d');

                                        var selectiongrid = Ext.getCmp('gruidplanpe').getStore();
                                        console.log(selectiongrid)
                                        var unidad = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected();
                                        console.log(unidad)
                                        var values = [];
                                        var values_plan = [];

                                        selectiongrid.each(function (r) {
                                            values = values.concat({
                                                inventario: r.get('inventario'),
                                                id_vehiculo: r.get('id_vehiculo'),
                                                diesel: r.get('diesel'),
                                                gasolina: r.get('gasolina'),
                                                plan_lubric: r.get('plan_lubric'),
                                                ind_consumo: r.get('ind_consumo'),
                                                km_diesel: r.get('km_diesel'),
                                                km_gasolina: r.get('km_gasolina'),
                                                gasolina_anual: r.get('gasolina_anual'),
                                                diesel_anual: r.get('diesel_anual'),
                                                unidad: unidad.data.id,
                                                actividad: r.get('actividadid'),

                                            });
                                        }, this);

                                        var obj = {};
                                        obj.datos = values;
                                        obj.pla_elect = Ext.getCmp('total_plan_elect_anual_fisico').getValue();
                                        obj.plan_glp = Ext.getCmp('total_plan_glp_anual_fisico').getValue();
                                        obj.lubric = Ext.getCmp('total_plan_lubric_anual_fisico').getValue();
                                        obj.diesel = Ext.getCmp('total_diesel_anual_fisico').getValue();
                                        obj.gasolina = Ext.getCmp('total_gasolina_anual_fisico').getValue();
                                        obj.dieselcuc = Ext.getCmp('diesel_CUC').getValue();
                                        obj.gasolina_cuc = Ext.getCmp('gasolina_CUC').getValue();
                                        obj.lubricante_cuc = Ext.getCmp('lubricante_CUC').getValue();
                                        obj.unidad = unidad.data.id;
                                        obj.fecha = fecha;


                                        var _result1 = App.PerformSyncServerRequest(Routing.generate('addPlanPlanAnualPE'), obj);

                                        if (_result1.success) {
                                            Ext.getCmp('id_windofecha').close();
                                            App.InfoMessage('Información', _result1.message, _result1.cls);
                                            var _result = App.PerformSyncServerRequest(Routing.generate('getVehiculosbyunidadPlanAnualPE'), {unidad: unidad.data.id});
                                            console.log(_result)
                                            if (_result) {

                                                Ext.getCmp('gruidplanpe').getStore().loadData(_result.rows);
                                            }

                                        }


                                    }
                                }
                            ]
                        }).show();


                    }
                }
            ]
        }],
        listeners: {
            selectionchange: function (This, selected, e) {

                Ext.getCmp('bottom_id').enable();
            }
        }
    });

    var grid_resumen = Ext.create('Ext.panel.Panel', {
        title: 'Resumen Total',
        region: 'south',
        id: 'id_grid_resumen',
        // collapsible: true,
        collapsed: true,
        collapseDirection: 'right',
        height: 220,
        minHeight: 85,
        maxHeight: 150,
        scrollable: true,
        items: [
            {
                xtype: 'container',
                // frame: true,
                id: 'resumen_panel',
                // hidden: false,
                margin: '5 0 0 280',
                layout: 'hbox',
                bodyPadding: 5,
                // plugins: new Ext.ux.dd.PanelFieldDragZone({
                //     ddGroup: group
                // }),
                defaults: {
                    labelWidth: 150
                },
                // html:'sdsd',
                items: [
                    {
                        xtype: 'container',
                        margin: '10 10 10 10',
                        layout: 'vbox',
                        items: [
                            {
                                xtype: 'container',
                                layout: 'hbox',
                                defaults: {
                                    // labelWidth: 80,
                                    width: 85,
                                    margin: '10 10 10 10',
                                    // bodyPadding: 3,
                                },
                                items: [
                                    {
                                        xtype: 'displayfield',
                                        fieldLabel: '<strong>TOTAL ANUAL FISICO (L): </strong>',
                                        margin: '10 10 10 10',
                                    }, {
                                        xtype: 'numberfield',
                                        margin: '10 10 10 65',
                                        id: 'total_anual_fisico'
                                    }, {
                                        xtype: 'numberfield',
                                        margin: '10 5 5 20',
                                        id: 'total_diesel_anual_fisico'
                                    }, {
                                        xtype: 'numberfield',
                                        margin: '10 5 5 20',
                                        id: 'total_gasolina_anual_fisico'
                                    }, {
                                        xtype: 'numberfield',
                                        margin: '10 5 5 20',
                                        id: 'total_plan_lubric_anual_fisico'
                                    }]
                            },
                            {
                                xtype: 'container',
                                layout: 'hbox',
                                defaults: {
                                    // labelWidth: 80,
                                    width: 85,
                                    margin: '10 10 10 10',
                                    // bodyPadding: 3,
                                },
                                items: [
                                    {
                                        xtype: 'displayfield',
                                        fieldLabel: '<strong>Valor (CUC)</strong>',
                                        margin: '10 10 10 10',
                                    },
                                    // {
                                    //     xtype: 'splitter',   // A splitter between the two child items
                                    //     width: 20,
                                    // },
                                    {
                                        xtype: 'numberfield',
                                        margin: '10 10 10 65',
                                        // fieldLabel: 'Drag this text',
                                        id: 'total_CUC'

                                    }, {
                                        xtype: 'numberfield',
                                        margin: '10 5 5 20',

                                        // fieldLabel: 'Drag this text',
                                        id: 'diesel_CUC'

                                    }, {
                                        xtype: 'numberfield',
                                        margin: '10 5 5 20',

                                        // fieldLabel: 'Drag this text',
                                        id: 'gasolina_CUC'

                                    }, {
                                        xtype: 'numberfield',
                                        margin: '10 5 5 20',

                                        // fieldLabel: 'Drag this text',
                                        id: 'lubricante_CUC'

                                    },]
                            },
                        ]
                    }
                    ,

                    {
                        xtype: 'container',
                        margin: '10 5 5 30',
                        layout: 'vbox',
                        items: [
                            {
                                xtype: 'container',
                                layout: 'hbox',
                                defaults: {
                                    labelWidth: 60,
                                    // width: 95,
                                    // bodyPadding: 3,
                                },
                                items: [
                                    {
                                        xtype: 'displayfield',
                                        fieldLabel: '<strong>PLAN ELECT</strong>',
                                        margin: '10 10 10 10',
                                    },

                                    {
                                        xtype: 'numberfield',
                                        margin: '10 10 10 10',
                                        // fieldLabel: 'Drag this text',
                                        id: 'total_plan_elect_anual_fisico'

                                    }]
                            },
                            {
                                xtype: 'container',
                                layout: 'hbox',
                                defaults: {
                                    labelWidth: 60,
                                    //width: 95,
                                    // margin: '10 10 10 260',
                                    // bodyPadding: 3,
                                },
                                items: [
                                    {
                                        xtype: 'displayfield',
                                        fieldLabel: '<strong>PLAN GPL</strong>',
                                        margin: '10 10 10 10'
                                    },
                                    {
                                        xtype: 'numberfield',
                                        margin: '10 10 10 10',
                                        // fieldLabel: 'Drag this text',
                                        id: 'total_plan_glp_anual_fisico'

                                    }]
                            },
                        ]
                    },


                ]
            }
        ]


    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'plan_PEid',
        title: 'PLAN ANUAL PORTADORES ENERGETICOS',
        border: true,
        frame: true,
        bodyBorder: false,

        defaults: {
            collapsible: true,
            split: true,
            bodyPadding: 10
        },
        layout: {
            type: 'border',       // Arrange child items vertically
            // align: 'stretch',    // Each takes up full width
            padding: 2
        },
        items: [panetree,
            grid_plan, grid_resumen


        ],
    });
    App.render(_panel);

});
