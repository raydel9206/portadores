Ext.onReady(function () {

    let mes_anno = Ext.create('Ext.form.field.Month', {
        format: 'm, Y',
        id: 'mes_anno',
        width: 90,
        value: new Date(App.selected_month + '/1/' + App.selected_year),
        renderTo: Ext.getBody(),
        listeners: {
            boxready: function () {
                let me = this;
                me.selectMonth = new Date(App.selected_month + '/1/' + App.selected_year);

                let assignGridPromise = new Promise((resolve) => {
                    let i = 0;
                    while (!Ext.getCmp('gridAnexos3') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('gridAnexos3'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let storeAnexos3 = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeAnexos3',
        fields: [],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anexo3gee/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        limit: 9999,
        listeners: {
            beforeload: function (store, operation) {
                operation.setParams({
                    unidad_id: panetree.getSelection()[0].data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                    quincena: Ext.getCmp('quincena_combo').getValue()
                })
            },
            load: function (This, records) {
                if (Ext.getCmp('btn_export')) Ext.getCmp('btn_export').setDisabled(records.length === 0);
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

    let panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: '20%',
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
            select: function (This) {
                gridAnexos3.enable();
                gridAnexos3.getStore().load();
                gridAnexos3.focus();
                Ext.getCmp('arbolunidades').collapse();
            }
        }
    });

    let gridAnexos3 = Ext.create('Ext.grid.Panel', {
        title: '',
        id: 'gridAnexos3',
        reference: 'gridAnexos3',
        region: 'center',
        width: '80%',
        disabled: true,
        viewModel: {},
        // viewConfig: {emptyText: '<div class="text-center">No se han realizado operaciones.</div>'},
        store: storeAnexos3,
        columns: [
            {text: 'GEE', dataIndex: 'equipo_descripcion', align: 'center', minWidth: 150},
            // {text: 'Centro', dataIndex: 'unidad_nombre', align: 'center', width: 150},
            {text: 'Municipio', dataIndex: 'municipio_nombre', align: 'center', flex: 1, minWidth: 150},
            {
                text: 'kVA',
                dataIndex: 'kva',
                align: 'center',
                minWidth: 100,
                flex: 1,
                renderer: function (value, meta) {
                    meta.style += 'text-align:right!important;';
                    return Ext.util.Format.number(value, '0,0.00');
                }
            },
            {
                text: 'Operaciones',
                columns: [
                    {
                        text: 'Sin Carga',
                        dataIndex: 'cant_oper_sin_carga',
                        align: 'center',
                        minWidth: 100,
                        flex: 1,
                        renderer: function (value, meta) {
                            meta.style += 'text-align: right;';
                            return value;
                        }
                    },
                    {
                        text: 'Con Carga',
                        dataIndex: 'cant_oper_con_carga',
                        align: 'center',
                        minWidth: 100,
                        flex: 1,
                        renderer: function (value, meta) {
                            meta.style += 'text-align: right;';
                            return value;
                        }
                    }
                ]
            },
            {
                text: 'Horas Trabajadas',
                columns: [
                    {
                        text: 'Sin Carga',
                        dataIndex: 'horas_sin_carga',
                        align: 'center',
                        minWidth: 100,
                        flex: 1,
                        renderer: function (value, meta) {
                            meta.style += 'text-align: right;';
                            return Ext.util.Format.number(value, '0,0.00');
                        }
                    },
                    {
                        text: 'Con Carga',
                        dataIndex: 'horas_con_carga',
                        align: 'center',
                        minWidth: 100,
                        flex: 1,
                        renderer: function (value, meta) {
                            meta.style += 'text-align: right;';
                            return Ext.util.Format.number(value, '0,0.00');
                        }
                    }
                ]
            },
            {
                text: 'Energía<br>Generada',
                dataIndex: 'energia_generada',
                align: 'center',
                minWidth: 100,
                flex: 1,
                renderer: function (value, meta) {
                    meta.style += 'text-align: right;';
                    return Ext.util.Format.number(value, '0,0.00');
                }
            },
            {
                text: 'Combustible Consumido',
                columns: [
                    {
                        text: 'Sin Carga',
                        dataIndex: 'comb_consumido_sin_carga',
                        align: 'center',
                        minWidth: 100,
                        flex: 1,
                        renderer: function (value, meta) {
                            meta.style += 'text-align: right;';
                            return Ext.util.Format.number(value, '0,0.00');
                        }
                    },
                    {
                        text: 'Con Carga',
                        dataIndex: 'comb_consumido_con_carga',
                        align: 'center',
                        minWidth: 100,
                        flex: 1,
                        renderer: function (value, meta) {
                            meta.style += 'text-align: right;';
                            return Ext.util.Format.number(value, '0,0.00');
                        }
                    },
                    {
                        text: 'Total',
                        dataIndex: 'comb_consumido_total',
                        align: 'center',
                        minWidth: 100,
                        flex: 1,
                        renderer: function (value, meta) {
                            meta.style += 'text-align: right;';
                            return Ext.util.Format.number(value, '0,0.00');
                        }
                    }
                ]
            },
            {
                text: 'Lts/H',
                dataIndex: 'indice_consumo',
                align: 'center',
                minWidth: 100,
                flex: 1,
                renderer: function (value, meta) {
                    meta.style += 'text-align: right;';
                    return Ext.util.Format.number(value, '0,0.00');
                }
            },
            {
                text: 'g/kWh',
                dataIndex: 'indice_cargabilidad',
                align: 'center',
                minWidth: 100,
                flex: 1,
                renderer: function (value, meta) {
                    meta.style += 'text-align: right;';
                    return Ext.util.Format.number(value, '0,0.00');
                }
            },
            {
                text: '(%)<br>Cargabilidad',
                dataIndex: 'porciento_cargabilidad',
                align: 'center',
                minWidth: 100,
                flex: 1,
                renderer: function (value, meta) {
                    meta.style += 'text-align: right;';
                    return Ext.util.Format.number(value, '0,0.00') + '%';
                }
            }
        ],
        tbar: {
            id: 'gridAnexo3Tbar',
            items: [mes_anno, '-',
                {
                    xtype: 'combobox',
                    id: 'quincena_combo',
                    fieldLabel: 'Quincena',
                    labelWidth: 60,
                    queryMode: 'local',
                    editable: false,
                    value: 'ambas',
                    displayField: 'display',
                    valueField: 'value',
                    store: Ext.create('Ext.data.JsonStore', {
                        fields: ['value', 'display'],
                        data: [
                            { value: 'primera', display: 'Primera'},
                            { value: 'segunda', display: 'Segunda'},
                            { value: 'ambas', display: 'Ambas'}
                        ]
                    }),
                    listeners: {
                        change: function (This, value) {
                            gridAnexos3.getStore().reload();
                        }
                    }
                }]
        },
        tools: [{
            type: 'refresh',
            tooltip: 'Actualiza el listado de mediciones',
            callback: function (gridpanel) {
                gridpanel.getStore().reload();
            }
        }]
    });

    let panel = Ext.create('Ext.panel.Panel', {
        id: 'panel',
        title: 'Resumen de Operaciones y Combustible Consumido (Anexo 3)',
        frame: true,
        closable: true,
        layout: 'border',
        items: [panetree, gridAnexos3]
    });

    App.render(panel);
});
