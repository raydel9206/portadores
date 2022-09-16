/**
 * Created by kireny on 11/07/2017.
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
                    while (!Ext.getCmp('grid_cierre_mensual') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('grid_cierre_mensual'));
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
            load: function (store, records) {
                store.insert(0, [{
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
                grid.getStore().load();
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
        id: 'arbolunidades',
        hideHeaders: true,
        border: true,
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
                Ext.getCmp('grid_cierre_mensual').enable();
            }
        }


    });

    var store_cierre_mensual = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_cierre_mensualId',
        fields: [
            {name: 'actv'},
            {name: 'unidad'},
            {name: 'responsable'},
            {name: 'cargo'},
            {name: 'modelo'},
            {name: 'matricula'},
            {name: 'km_inicial', type: 'number'},
            {name: 'km_final', type: 'number'},
            {name: 'km_rec', type: 'number'},
            {name: 'comb_inicial', type: 'number'},
            {name: 'comb_abast', type: 'number'},
            {name: 'comb_final', type: 'number'},
            {name: 'comb_real_cons', type: 'number'},
            {name: 'indice_cons_real', type: 'number'},
            {name: 'indice_cons_norm', type: 'number'},
            {name: 'porciento_desv', type: 'number'},
            {name: 'desv_abs', type: 'number'},
            {name: 'tipo_combustible_id'},
            {name: 'tipo_combustible'},
            {name: 'unidad_padre'},
            {name: 'actividad'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/cierre_mensual/load'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },

        autoLoad: false,
        pageSize: 1000,
        // groupField: 'actividad',
        listeners: {
            beforeload: function (This, operation, eOpts) {
                if (Ext.getCmp('cierre_mensual_btn_print'))
                    Ext.getCmp('cierre_mensual_btn_print').setDisabled(true);
                if (Ext.getCmp('cierre_mensual_btn_export'))
                    Ext.getCmp('cierre_mensual_btn_export').setDisabled(true);
                operation.setParams({
                    unidad: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                    tipoCombustible: tipos_combustible.getValue(),
                });
            },
            load: function (This, records, successful, eOpts) {
                if (Ext.getCmp('cierre_mensual_btn_print'))
                    Ext.getCmp('cierre_mensual_btn_print').setDisabled(!successful);
                if (Ext.getCmp('cierre_mensual_btn_export'))
                    Ext.getCmp('cierre_mensual_btn_export').setDisabled(!successful);
            }
        }
    });

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '{name} ' + ' ({rows.length} ' + 'Vehiculos)',
        hideGroupedHeader: false,
        startCollapsed: false,
        align: 'center',
        ftype: 'groupingsummary'
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_cierre_mensual',
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        store: store_cierre_mensual,
        features: [{
            ftype: 'summary',
            dock: 'bottom',
        },
            {

                ftype: 'groupingsummary',
                hideGroupedHeader: false,
                enableGroupingMenu: true,
                startCollapsed: false,
                groupHeaderTpl: '<b>Actividad: {name} ' + ' ({rows.length} ' + 'Vehículos)</b>',
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
            {text: '<strong>Unidad</strong>', dataIndex: 'unidad', filter: 'string', width: 150, align: 'center'},
            // {text: '<strong>Responsable</strong>', dataIndex: 'responsable', filter: 'string', flex: 0.5, align: 'center' },
            // {text: '<strong>Cargo</strong>',dataIndex: 'cargo',filter: 'string',flex: 0.5,align: 'center',
            //     summaryRenderer: function (value, summaryData, dataIndex) {
            //         return '<strong>SUB TOTAL</strong>';
            //     }},

            {
                text: '<strong>Equipos</strong>',
                // flex: .3,
                columns: [
                    {
                        text: '<strong>Modelo</strong>',
                        dataIndex: 'modelo',
                        width: 130, align: 'center'
                    },
                    {
                        text: '<strong>Matrícula</strong>',
                        dataIndex: 'matricula',
                        width: 110, align: 'center',
                        filter: 'string',
                        // renderer: function (val2, met, record, a, b, c, d) {
                        //     if (record.get('tipo_combustible') == 'Diesel') {
                        //         met.style = 'font-style:italic !important;font-weight: bold;background: #2ECCFA;';
                        //     } else if (record.get('tipo_combustible') == 'Gasolina Especial') {
                        //         met.style = 'font-style:italic !important;font-weight: bold;background: #04B431;';
                        //     }
                        //     return val2;
                        // }
                    },
                ]
            },
            {
                text: '<strong>Km Incial</strong>',
                // width: 100    ,
                columns: [
                    {
                        text: '<strong>Km</strong>',
                        width: 100,
                        dataIndex: 'km_inicial',
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        }
                    }
                ]
            },
            {
                text: '<strong>Km Final</strong>',
                // width: 100,
                columns: [
                    {
                        text: '<strong>Km</strong>',
                        width: 100,
                        dataIndex: 'km_final',
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        }
                    }
                ]
            },
            {
                text: '<strong>Km Rec.</strong>',
                // width: 100,
                columns: [
                    {
                        text: '<strong>Km</strong>',
                        width: 100,
                        dataIndex: 'km_rec',
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        }
                    }
                ]
            },
            {
                text: '<strong>Comb. </br> Incial TQ</strong>',
                // width: 100,
                columns: [
                    {
                        text: '<strong>L</strong>',
                        width: 100,
                        dataIndex: 'comb_inicial',
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        }
                    }
                ]
            },
            {
                text: '<strong>Comb. </br> Abast.</strong>',
                // width: 100,
                columns: [
                    {
                        text: '<strong>L</strong>',
                        width: 100,
                        dataIndex: 'comb_abast',
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        }
                    }
                ]
            },
            {
                text: '<strong>Comb. </br> Final TQ</strong>',
                width: 100,
                columns: [
                    {
                        text: '<strong>L</strong>',
                        width: 100,
                        dataIndex: 'comb_final',
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        }
                    }
                ]
            },
            {
                text: '<strong>Comb. </br>Real Cons.</strong>',
                // width: 100,
                columns: [
                    {
                        text: '<strong>L</strong>',
                        width: 100,
                        dataIndex: 'comb_real_cons',
                        align: 'center',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        }
                    }
                ]
            },
            {
                text: '<strong>Indice de </br> cons. real</strong>',
                // width: 100,
                columns: [
                    {
                        text: '<strong>Km/L</strong>',
                        width: 100,
                        dataIndex: 'indice_cons_real',
                        align: 'center',
                        renderer: function (val, met, record, a, b, c, d) {
                            if (val) {
                                return Ext.util.Format.number(val, '0.00');
                            }
                            else
                                return 0;
                        }
                    }
                ]
            },
            {
                text: '<strong>Indice de </br>cons. Norm.</strong>',
                // width: 100,
                columns: [
                    {
                        text: '<strong>Km/L</strong>',
                        width: 100,
                        dataIndex: 'indice_cons_norm',
                        align: 'center'
                    }
                ]
            },
            {
                text: '<strong>% de Desv.</strong>',
                dataIndex: 'porciento_desv',
                filter: 'string',
                width: 90,
                align: 'center',
                renderer: function (val, met, record, a, b, c, d) {
                    if (val) {
                        return Ext.util.Format.number(val, '0.00');
                    }
                    else
                        return 0;
                }
            },
            {
                text: '<strong>Desv.ABS</strong>', dataIndex: 'desv_abs', filter: 'string', width: 90, align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    return Ext.util.Format.number(val2, '0.00');
                }
            }
        ],
        tbar: {
            id: 'cierre_mensual_tbar',
            height: 36,
            items: [mes_anno, tipos_combustible]
        },

        // bbar: {
        //     id: 'cierre_bbar',
        //     height: 36,
        //     items: [
        //         {
        //             xtype: 'container',
        //             layout: 'hbox',
        //             ui: 'footer',
        //             // margin: '5 5 5 5',
        //             items: [
        //                 {
        //                     xtype: 'textfield',
        //                     id: 'Cant_Vehiculos_diesel',
        //                     width: 160,
        //                     // margin: '5 5 5 5',
        //                     // ui: 'bottom',
        //                     fieldLabel: 'Cant Vehículos',
        //                     readOnly: true,
        //                     value: 0
        //
        //                 },
        //                 {
        //                     xtype: 'textfield',
        //                     id: 'diesel',
        //                     width: 160,
        //                     style: 'font-style:italic !important;font-weight: bold;background: #2ECCFA; margin-left: 20px',
        //                     fieldLabel: 'Total Diesel',
        //                     readOnly: true,
        //                     value: 0
        //
        //                 },
        //             ]
        //
        //         },
        //         {
        //             xtype: 'container',
        //             layout: 'hbox',
        //             dock: 'bottom',
        //             // margin: '5 5 5 5 ',
        //             items: [
        //                 {
        //                     xtype: 'textfield',
        //                     id: 'Cant_Vehiculos_gasollina',
        //                     width: 160,
        //                     fieldLabel: 'Cant Vehículos',
        //                     readOnly: true,
        //                     // margin: '5 5 5 5',
        //                     value: 0
        //
        //                 },
        //                 {
        //                     xtype: 'textfield',
        //                     id: 'gasolina',
        //                     width: 160,
        //                     style: 'font-style:italic !important;font-weight: bold;background: #04B431; margin-left: 20px',
        //                     fieldLabel: 'Total Gasolina',
        //                     readOnly: true,
        //                     value: 0
        //                 }
        //             ]
        //         }
        //     ]
        // },
        // enableLocking: true,

        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('cierre_mensual_tbar').items.each(
                    function (item, index, length) {
                        if (index != 0)
                            item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            }
        }
    });

    // function CierreMes(pmes, btn) {
    //     var _bbar = Ext.getCmp('cierre_bbar');
    //     _bbar.items.each(function (element) {
    //         element.setStyle({
    //             background: '#F5F5F5'
    //         });
    //     });
    //     btn.setStyle({
    //         background: '#C1DDF1'
    //     });
    //     var store = Ext.getCmp('grid_cierre_mensual').getStore().load({
    //         params: {
    //             mes: pmes
    //         }
    //     });
    //
    //
    //     // var store_my=Ext.getCmp('grid_cierre_mensual').getStore().getData();
    //     //   console.log(store_my)
    //
    // }

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'cierre_mensual_panel_id',
        title: 'Conciliación Combustible Cierre Mes',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid],
    });
    App.render(_panel);
})
