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

    let tipo_combustible = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeTipoCombustible',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/tipocombustible/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
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
        width: 155,
        store: monedaStore,
        displayField: 'nombre',
        valueField: 'id',
        queryMode: 'local',
        emptyText: 'Moneda...',
        listeners: {
            select: function (This, newValue, oldValue, eOpts) {
                grid.getStore().load();
            }
        }
    });

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
                grid.enable();
                grid.getStore().load();
            }
        }
    });

    var store_demanda_combustible = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_demanda_combustible',
        fields: [
            {name: 'demanda_id'},
            {name: 'mes'},
            {name: 'anno'},
            {name: 'unidad_id'},
            {name: 'tipo_combustible'},
            {name: 'tipo_combustible_id'},
            {name: 'cant_litros'},
            {name: 'propuesta'},
            {name: 'disponible_fincimex'},
            {name: 'comb_planificado'},
            {name: 'saldo_fincimex'},
            {name: 'saldo_caja'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/demanda_combustible/load'),
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
                Ext.getCmp('id_grid_demanda_combustible').getSelectionModel().deselectAll();
                operation.setParams({
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    moneda: Ext.getCmp('moneda_combo').getValue()
                });
            },
        }
    });
    var edit = new Ext.grid.plugin.CellEditing({
        clicksToEdit: 1,
        listeners: {
            edit: function (This, e, eOpts) {
                if (Ext.getCmp('demanda_combustible_btn_act'))
                    Ext.getCmp('demanda_combustible_btn_act').setDisabled(false);
                if (Ext.getCmp('demanda_combustible_btn_back'))
                    Ext.getCmp('demanda_combustible_btn_back').setDisabled(false);

                grid.getView().refresh();
            }
        }
    });


    var grid = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_demanda_combustible',
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        // layout: 'border',
        store: store_demanda_combustible,

        plugins: [edit],
        columnLines: true,
        columns: [
            {
                text: '<b>Tipo Combustible</b>',
                tooltip: 'Tipo Combustible',
                dataIndex: 'tipo_combustible',
                flex: .2,
                align: 'center',
                name: 'tipo_combustible',
            },
            {
                xtype: 'gridcolumn',
                text: '<b>Demanda(U/F)</b>',
                tooltip: 'Cantidad de Litros a Demandar',
                dataIndex: 'cant_litros',
                flex: .1,
                formatter: "number('0.00')",
                align: 'center',
                name: 'cant_litros',
                editor: {
                    xtype: 'numberfield',
                    decimalSeparator: '.',
                    hideTrigger: true
                },
            },
            // {
            //     xtype: 'gridcolumn',
            //     text: '<b>Propuesta(U/F)</b>',
            //     tooltip: 'Cantidad de Litros Propuesta a Demandar',
            //     dataIndex: 'propuesta',
            //     formatter: "number('0.00')",
            //     flex: .1,
            //     align: 'center',
            //     name: 'propuesta'
            // },
            {
                xtype: 'gridcolumn',
                text: '<b>Planificado(U/F)</b>',
                tooltip: 'Cantidad de Litros Combustible Planificado',
                dataIndex: 'comb_planificado',
                formatter: "number('0.00')",
                flex: .1,
                align: 'center',
                name: 'comb_planificado'
            },
            {
                xtype: 'gridcolumn',
                text: '<b>Caja(U/F)</b>',
                tooltip: 'Cantidad de Litros Combustible en Caja',
                dataIndex: 'saldo_caja',
                formatter: "number('0.00')",
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
                        formatter: "number('0.00')",
                        align: 'center',
                        name: 'disponible_fincimex'
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<b>Saldo(U/F)</b>',
                        tooltip: 'Cantidad de Litros Combustible en Fincimex',
                        dataIndex: 'saldo_fincimex',
                        formatter: "number('0.00')",
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
                id: 'demanda_combustible_tbar',
                dock: 'top',
                items: [mes_anno, monedaCombo]
            }
        ],

        listeners: {
            selectionchange: function (This, selected, eOpts) {
                if (Ext.getCmp('demanda_combustible_btn_reinciar')) {
                    if (selected.length > 0)
                        Ext.getCmp('demanda_combustible_btn_reinciar').setDisabled(selected[0].data.demanda_id === '');
                    else
                        Ext.getCmp('demanda_combustible_btn_reinciar').setDisabled(true);
                }
            }
        }
    });
    var panelContainer = Ext.create('Ext.panel.Panel', {
        title: 'Demanda de Combustible',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panelTree, grid]
    });
    App.render(panelContainer);
});
