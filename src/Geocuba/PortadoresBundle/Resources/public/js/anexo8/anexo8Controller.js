/**
 * Created by rherrerag on 1/8/2018.
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
                    while (!Ext.getCmp('grid_reporte_anexo_8') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('grid_reporte_anexo_8'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
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

    var anexo_8 = Ext.create('Ext.data.JsonStore', {
        storeId: 'anexo_8',
        fields: [
            {name: 'matricula'},
            {name: 'nombre'},
            {name: 'nro_vale'},
            {name: 'd'},
            {name: 'm'},
            {name: 'y'},
            {name: 'existencia_inicial_imp'},
            {name: 'existencia_imp'},
            {name: 'existencia_inicial_cant'},
            {name: 'existencia_cant'},
            {name: 'entrada_importe_imp'},
            {name: 'entrada_cantidad_cant'},
            {name: 'abastecido_importe'},
            {name: 'abastecido_cantidad'}
        ],

        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anexo8/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                    id: Ext.getCmp('combo_search').getValue()
                });
            }
        }
    });

    var store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta',
        fields: [
            {name: 'nro_tarjeta'},
            {name: 'id'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        sorters: [{property: 'nro_tarjeta', direction: 'ASC'}],
        listeners: {
            beforeload: function (store, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
        }
    });

    var cmbSearch = Ext.create('Ext.form.ComboBox', {
        labelWidth: 140,
        id: 'combo_search',
        store: store_tarjeta,
        displayField: 'nro_tarjeta',
        valueField: 'id',
        queryMode: 'local',
        forceSelection: true,
        emptyText: 'No. Tarjeta...',
        selectOnFocus: true,
        editable: true,
        allowBlank: false,
        listeners: {
            change: function (This, newValue) {
                Ext.getCmp('grid_reporte_anexo_8').getStore().currentPage = 1;
                Ext.getCmp('grid_reporte_anexo_8').getStore().load();

                if (Ext.getCmp('reporte_anexo_8_btn_print'))
                    Ext.getCmp('reporte_anexo_8_btn_print').setDisabled(!newValue)
                if (Ext.getCmp('reporte_anexo_8_btn_export'))
                    Ext.getCmp('reporte_anexo_8_btn_export').setDisabled(!newValue);

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
                grid.enable();
                store_tarjeta.load();
            }
        }


    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_reporte_anexo_8',
        region: 'center',
        width: '75%',
        height: '100%',
        store: anexo_8,
        disabled: true,
        features: [{
            ftype: 'summary'
        }],
        columns: [
            {header: '<strong>No.</strong>', align: 'center', xtype: 'rownumberer', id: 'numero', width: 45},
            {
                header: 'Fecha',
                columns: [
                    {
                        header: '<strong>D</strong>',
                        align: 'center',
                        dataIndex: 'd',
                        width: 50
                    }, {
                        header: '<strong>M</strong>',
                        align: 'center',
                        dataIndex: 'm',
                        width: 50

                    }, {
                        header: '<strong>A</strong>',
                        align: 'center',
                        dataIndex: 'y',
                        width: 50
                    }
                ]
            },
            {
                header: '<strong>MATRICULA</strong>',
                width: 100,
                align: 'center',
                dataIndex: 'matricula',
            },
            {
                header: '<strong>Nro. COMPROBANTE</strong>',
                width: 100,
                align: 'center',
                dataIndex: 'nro_vale'
            },
            {
                header: 'EXISTENCIA INICIAL',

                columns: [
                    {
                        header: '<strong>CANT</strong>',
                        width: 100,
                        dataIndex: 'existencia_inicial_cant'

                    },
                    {
                        header: '<strong>IMP</strong>',
                        width: 100,
                        dataIndex: 'existencia_inicial_imp'
                    }
                ]
            }, {
                header: 'ENTRADA',
                columns: [
                    {
                        header: '<strong>CANT</strong>',
                        width: 100,
                        dataIndex: 'entrada_cant'

                    },
                    {
                        header: '<strong>IMP</strong>',
                        width: 100,
                        dataIndex: 'entrada_imp'
                    }
                ]
            }, {
                header: 'ABASTECIDO',
                columns: [
                    {
                        header: '<strong>CANT</strong>',
                        width: 100,
                        dataIndex: 'abastecido_cantidad'

                    },
                    {
                        header: '<strong>IMP</strong>',
                        width: 100,
                        dataIndex: 'abastecido_importe'
                    }, {
                        header: '<strong>NOMBREY APELLIDOS</strong>',
                        width: 200,
                        dataIndex: 'nombre'
                    }
                ]
            }, {
                header: 'EXISTENCIA FINAL',
                columns: [
                    {
                        header: '<strong>CANT</strong>',
                        width: 100,
                        dataIndex: 'existencia_cant'

                    }, {
                        header: '<strong>IMP</strong>',
                        width: 100,
                        dataIndex: 'existencia_imp'
                    }
                ]
            }
        ],

        tbar: {
            id: 'tbar_reporte_anexo_8',
            // height: 36,
            items: [mes_anno, cmbSearch]
        }
    });

    var panelContainer = Ext.create('Ext.panel.Panel', {
        title: 'Anexo 8',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });


    App.render(panelContainer);
});


