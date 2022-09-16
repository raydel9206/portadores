/**
 * Created by pfcadenas on 11/11/2016.
 */

Ext.onReady(function () {
    var edit = false;

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
                    while (!Ext.getCmp('id_grid_demanda_combustible') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_demanda_combustible'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    // let tipo_combustible = Ext.create('Ext.data.JsonStore', {
    //     storeId: 'storeTipoCombustible',
    //     fields: [
    //         {name: 'id'},
    //         {name: 'nombre'},
    //         {name: 'codigo'}
    //     ],
    //     proxy: {
    //         type: 'ajax',
    //         url: App.buildURL('portadores/tipocombustible/loadCombo'),
    //         reader: {
    //             rootProperty: 'rows'
    //         }
    //     },
    //     autoLoad: true
    // });

    const treeStore = Ext.create('Ext.data.TreeStore', {
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
    const panelTree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: treeStore,
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
                grid_solicitud.enable();
                store_solicitud_compra.load();
            }
        }
    });

    var store_solicitud_compra = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_solicitud_compra',
        fields: [
            {name: 'id'},
            {name: 'fecha'},
            {name: 'unidadid'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/solicitud_compra/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 10,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                grid_solicitud.getSelectionModel().deselectAll();
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                });
            }
        }
    });

    var store_solicitud_compra_desglose = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_solicitud_compra_desglose',
        fields: [
            {name: 'solicitud_id'},
            {name: 'unidad_id'},
            {name: 'tipo_combustible'},
            {name: 'tipo_combustible_precio', type: 'float'},
            {name: 'tipo_combustible_id'},
            {name: 'litros_cup', type: 'float'},
            {name: 'litros_cuc', type: 'float'},
            {name: 'monto_cup', type: 'float'},
            {name: 'monto_cuc', type: 'float'},
            {name: 'propuesta'},
            {name: 'disponible_fincimex', type: 'float'},
            {name: 'comb_distribuido', type: 'float'},
            {name: 'saldo_fincimex', type: 'float'},
            {name: 'saldo_caja', type: 'float'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/solicitud_compra/desglose/load'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                if (Ext.getCmp('demanda_combustible_btn_act'))
                    Ext.getCmp('demanda_combustible_btn_act').setDisabled(true);
                if (Ext.getCmp('demanda_combustible_btn_back'))
                    Ext.getCmp('demanda_combustible_btn_back').setDisabled(true);
                Ext.getCmp('id_grid_solicitud_compra_desglose').getSelectionModel().deselectAll();
                operation.setParams({
                    solicitud_id: grid_solicitud.getSelectionModel().getLastSelected().data.id,
                    unidadid: panelTree.getSelectionModel().getLastSelected().data.id,
                });
            },
            load: function (This, records, successful, eOpts) {
                if (Ext.getCmp('demanda_combustible_btn_reinciar'))
                    Ext.getCmp('demanda_combustible_btn_reinciar').setDisabled(Aprobado(grid_solicitud));
            }
        }
    });

    var edit = new Ext.grid.plugin.CellEditing({
        clicksToEdit: 2,
        listeners: {
            beforeedit: function (This, e, eOpts) {
                return !Aprobado(grid_solicitud);
            },
            edit: function (This, e, eOpts) {
                if (Ext.getCmp('demanda_combustible_btn_act'))
                    Ext.getCmp('demanda_combustible_btn_act').setDisabled();
                if (Ext.getCmp('demanda_combustible_btn_back'))
                    Ext.getCmp('demanda_combustible_btn_back').setDisabled(false);

                if (e.record.data['litros_cup'] + e.record.data['litros_cuc'] > e.record.data['disponible_fincimex']) {
                    e.colIdx === 1 ? e.record.data['litros_cup'] = e.record.modified['litros_cup'] : e.record.data['litros_cuc'] = e.record.modified['litros_cuc'];
                    App.showAlert('La cantidad de ' + e.record.data['tipo_combustible_nombre'] + ' excede el plan disponible en Fincimex', 'warning');
                } else {
                    if (e.colIdx === 1 && edit) {
                        e.record.data['monto_cup'] = e.value * e.record.data['tipo_combustible_precio'];
                    }
                    // if (e.colIdx === 2 && edit) {
                    //     e.record.data['litros_cup'] = e.value / e.record.data['tipo_combustible_precio'];
                    //     console.log(e.record.data['litros_cup'])
                    // }
                    if (e.colIdx === 3 && edit) {
                        e.record.data['monto_cuc'] = e.value * e.record.data['tipo_combustible_precio'];
                    }
                    // if (e.colIdx === 4 && edit) {
                    //     e.record.data['litros_cuc'] = e.value / e.record.data['tipo_combustible_precio'];
                    // }
                }

                grid_desglose.getView().refresh();
            }
        }
    });

    var grid_solicitud = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_solicitud',
        region: 'north',
        width: '100%',
        height: '50%',
        disabled: true,
        store: store_solicitud_compra,
        features: [{
            ftype: 'grouping',
            groupHeaderTpl: '<strong>{name}</strong>',
            hideGroupedHeader: true,
            startCollapsed: false
        }],
        columns: [
            {
                text: '<strong>Fecha</strong>',
                dataIndex: 'fecha',
                flex: .2,
                align: 'center'
            },
            {
                text: '<strong>Monto</strong>',
                columns: [
                    {
                        text: '<strong>CUP</strong>',
                        dataIndex: 'monto_cup',
                        width: 200,
                        align: 'center',
                        renderer: function (value) {
                            return Ext.util.Format.number(value, '0.00');
                        }
                    },
                    {
                        text: '<strong>CUC</strong>',
                        dataIndex: 'monto_cuc',
                        width: 200,
                        align: 'center',
                        renderer: function (value) {
                            return Ext.util.Format.number(value, '0.00');
                        }
                    }
                ]
            },
            {
                text: '<strong>Aprobada</strong>',
                dataIndex: 'aprobado',
                flex: .2,
                align: 'center',
                renderer: function (value) {
                    if (value)
                        return "<div class='badge-true'>Si</div>";
                    else
                        return "<div class='badge-false'>No</div>";
                }
            }

        ],
        tbar: {
            id: 'solicitud_compra_tbar',
            height: 36,
            items: []
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 1,
            store: Ext.getStore('store_solicitud_compra'),
            displayInfo: true,
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                if(selected.length>0){
                    if (Ext.getCmp('solicitud_compra_btn_mod'))
                        Ext.getCmp('solicitud_compra_btn_mod').setDisabled(Aprobado(grid_solicitud));
                    if (Ext.getCmp('solicitud_compra_btn_del'))
                        Ext.getCmp('solicitud_compra_btn_del').setDisabled(Aprobado(grid_solicitud));
                    if (Ext.getCmp('solicitud_compra_btn_aprob'))
                        Ext.getCmp('solicitud_compra_btn_aprob').setDisabled(Aprobado(grid_solicitud));
                    if (Ext.getCmp('solicitud_compra_btn_desaprob'))
                        Ext.getCmp('solicitud_compra_btn_desaprob').setDisabled(!Aprobado(grid_solicitud));
                }
                else{
                    if (Ext.getCmp('solicitud_compra_btn_mod'))
                        Ext.getCmp('solicitud_compra_btn_mod').setDisabled(true);
                    if (Ext.getCmp('solicitud_compra_btn_del'))
                        Ext.getCmp('solicitud_compra_btn_del').setDisabled(true);
                    if (Ext.getCmp('solicitud_compra_btn_aprob'))
                        Ext.getCmp('solicitud_compra_btn_aprob').setDisabled(true);
                    if (Ext.getCmp('solicitud_compra_btn_desaprob'))
                        Ext.getCmp('solicitud_compra_btn_desaprob').setDisabled(true);
                }



                grid_desglose.setDisabled(selected.length == 0);
                if (selected.length > 0) {
                    grid_desglose.getStore().load();
                }
            }
        }
    });

    var grid_desglose = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_solicitud_compra_desglose',
        region: 'south',
        width: '100%',
        height: '50%',
        disabled: true,
        store: store_solicitud_compra_desglose,

        plugins: [edit],
        columnLines: true,
        columns: [
            {
                // xtype: 'gridcolumn',
                text: '<b>Tipo Combustible</b>',
                tooltip: 'Tipo Combustible',
                dataIndex: 'tipo_combustible',
                flex: .2,
                align: 'center',
                name: 'tipo_combustible',
            },
            {
                text: '<b>CUP</b>',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<b>U/F</b>',
                        tooltip: 'Cantidad de Litros a Comprar CUP',
                        dataIndex: 'litros_cup',
                        flex: .1,
                        align: 'center',
                        name: 'litros_cup',
                        editor: {
                            xtype: 'numberfield',
                            // decimalSeparator: '.',
                            hideTrigger: true
                        },
                        renderer: function (value) {
                            return Ext.util.Format.number(value, '0');
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<b>U/M</b>',
                        tooltip: 'Monto de Combustible a Comprar CUP',
                        dataIndex: 'monto_cup',
                        flex: .1,
                        align: 'center',
                        name: 'monto_cup',
                        // editor: {
                        //     xtype: 'numberfield',
                        //     // decimalSeparator: '.',
                        //     hideTrigger: true
                        // },
                        renderer: function (value) {
                            return Ext.util.Format.number(value, '0.00');
                        }
                    },
                ]
            },
            {
                text: '<b>CUC</b>',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<b>U/F</b>',
                        tooltip: 'Cantidad de Litros a Comprar CUC',
                        dataIndex: 'litros_cuc',
                        flex: .1,
                        align: 'center',
                        name: 'litros_cuc',
                        editor: {
                            xtype: 'numberfield',
                            // decimalSeparator: '.',
                            hideTrigger: true
                        },
                        renderer: function (value) {
                            return Ext.util.Format.number(value, '0');
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<b>U/M</b>',
                        tooltip: 'Monto de Combustible a Comprar CUC',
                        dataIndex: 'monto_cuc',
                        flex: .1,
                        align: 'center',
                        name: 'monto_cuc',
                        // editor: {
                        //     xtype: 'numberfield',
                        //     // decimalSeparator: '.',
                        //     hideTrigger: true
                        // },
                        renderer: function (value) {
                            return Ext.util.Format.number(value, '0.00');
                        }
                    },
                ]
            },
            {
                xtype: 'gridcolumn',
                text: '<b>Propuesta(U/F)</b>',
                tooltip: 'Cantidad de Litros Necesarios Comprar',
                dataIndex: 'propuesta',
                flex: .1,
                align: 'center',
                name: 'propuesta'
            },
            {
                xtype: 'gridcolumn',
                text: '<b>Distribuido(U/F)</b>',
                tooltip: 'Cantidad de Litros Combustible Distribuido',
                dataIndex: 'comb_distribuido',
                flex: .1,
                align: 'center',
                name: 'comb_distribuido'
            },
            {
                xtype: 'gridcolumn',
                text: '<b>Caja(U/F)</b>',
                tooltip: 'Cantidad de Litros Combustible en Caja',
                dataIndex: 'saldo_caja',
                flex: .1,
                align: 'center',
                name: 'saldo_caja'
            },
            {
                text: '<strong>Fincimex</strong> ',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<b>Plan Disponible(U/F)</b>',
                        tooltip: 'Cantidad de Litros Plan Disponible en Fincimex',
                        dataIndex: 'disponible_fincimex',
                        flex: .1,
                        align: 'center',
                        name: 'disponible_fincimex'
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<b>Saldo(U/F)</b>',
                        tooltip: 'Cantidad de Litros Combustible en Fincimex',
                        dataIndex: 'saldo_fincimex',
                        flex: .1,
                        align: 'center',
                        name: 'saldo_fincimex'
                    }
                ]
            }


        ],
        dockedItems: [
            {
                xtype: 'toolbar',
                id: 'solicitud_compra_desglose_tbar',
                dock: 'top',
                items: []
            }
        ],
        // tbar: {
        //     id: 'planificacion_combustible_tbar',
        //     height: 36,
        //     items: [     textSearch, textAnnoSearch, btnSearch, btnClearSearch, '-']
        // },

        listeners: {
            selectionchange: function (This, selected, eOpts) {


            }

        }
    });

    var panelCenter = Ext.create('Ext.panel.Panel', {
        region: 'center',
        width: '75%',
        padding: '0 1',
        layout: 'border',
        items: [grid_solicitud, grid_desglose]
    });

    var panelContainer = Ext.create('Ext.panel.Panel', {
        title: 'Solicitud de Compra de Combustible',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panelTree, panelCenter]
    });
    App.render(panelContainer);



});
function Aprobado(grid){
    return grid.getSelectionModel().getLastSelected().data.aprobado;
}

