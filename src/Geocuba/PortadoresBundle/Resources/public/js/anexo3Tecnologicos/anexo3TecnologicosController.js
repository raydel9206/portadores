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

    let storeEquiposTecnologicos = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeEquiposTecnologicos',
        fields: ['id', 'descripcion', 'nro_inventario', 'unidad_id', 'unidad_nombre'],
        groupField: 'unidad_nombre',
        sorters: 'unidad_nombre',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/equipos_tecn/load'),
            extraParams: {tipo: 'otro'},
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation) {
                operation.setParams({
                    unidad: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                })
            }

        }

    });

    let storeAnexos3 = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeAnexos3',
        fields: ['id', 'medicion', 'litros', 'consumo_litros', 'fecha'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anexo3/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation) {
                operation.setParams({
                    equipo_id: gridEquiposTecnologicos.getSelection()[0].data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear()
                })
            },
            load: function (This, records) {
                if (Ext.getCmp('btn_extra_data')) Ext.getCmp('btn_extra_data').setDisabled(records.length === 0);
                if (Ext.getCmp('btn_print')) Ext.getCmp('btn_print').setDisabled(records.length === 0);
            }
        }

    });

    let groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Unidad: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
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
            select: function () {
                gridEquiposTecnologicos.enable();
                gridEquiposTecnologicos.getStore().load();
                gridEquiposTecnologicos.focus();
                panetree.collapse();
            },
            collapse: function () {
                gridAnexos3.setWidth('78%');
            },
            expand: function () {
                gridAnexos3.setWidth('58%');
            }
        }
    });

    let gridEquiposTecnologicos = Ext.create('Ext.grid.Panel', {
        id: 'gridEquiposTecnologicos',
        reference: 'gridEquiposTecnologicos',
        title: 'Equipos Tecnológicos',
        features: [groupingFeature],
        store: storeEquiposTecnologicos,
        region: 'center',
        disabled: true,
        width: '22%',
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen equipos tecnológicos registrados</div>'},
        columns: [
            {text: '<strong>Nro. Inv.</strong>', dataIndex: 'numero_inventario', align: 'center', flex: 0.4},
            {text: '<strong>Descripción</strong>', dataIndex: 'descripcion', filter: 'string', flex: 1},
        ],
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeEquiposTecnologicos'),
            displayInfo: true,
        },
        tbar: {
            xtype: 'toolbar',
            items: [{
                xtype: 'combo',
                id: 'combo_denominacion',
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'nombre'],
                    data: [
                        {value: 'otro', name: 'Otros'},
                        {value: 'static_tec_denomination_1', name: 'Calderas'},
                        {value: 'static_tec_denomination_3', name: 'Montacargas'}
                    ]
                }),
                value: 'otro',
                valueField: 'value',
                displayField: 'name',
                editable: false,
                listeners: {
                    select: function (This, record) {
                        storeEquiposTecnologicos.getProxy().setExtraParam('tipo', record.data.value);
                        gridEquiposTecnologicos.getStore().load();
                    }
                }
            }]
        },
        listeners: {
            selectionchange: function (This, selected) {
                if (selected.length) {
                    gridAnexos3.getStore().load();
                    gridAnexos3.enable();
                } else {
                    gridAnexos3.getStore().removeAll();
                    gridAnexos3.disable();
                }
            }
        }
    });

    let gridAnexos3 = Ext.create('Ext.grid.Panel', {
        title: 'Listado de Operaciones',
        id: 'gridAnexos3',
        reference: 'gridAnexos3',
        region: 'east',
        width: '58%',
        disabled: true,
        viewModel: {},
        // viewConfig: {emptyText: '<div class="text-center">No se han realizado operaciones.</div>'},
        store: storeAnexos3,
        plugins: [new Ext.grid.plugin.CellEditing({
            clicksToEdit: 2,
            listeners: {
                beforeedit: function () {
                    return Ext.getCmp('btn_save') !== undefined;
                },
                edit: function (This, e) {
                    let otherValue = e.originalValue;

                    if (e.value !== parseFloat(otherValue)) {
                        Ext.getCmp('btn_save').enable();
                        otherValue = Ext.util.Format.number(e.value, '0.0000');
                    }

                    e.record.data[e.field] = otherValue;
                    e.grid.getView().refresh();
                }
            }
        })],
        columns: [
            {text: 'Día', dataIndex: 'dia', align: 'center', locked: true, width: 70},
            {text: 'Actividad', dataIndex: 'actividad', align: 'center', width: 180},
            {
                text: 'Indice de<br>Consumo<br>Normado',
                dataIndex: 'indice_normado',
                align: 'center',
                width: 100,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Hora Inicio',
                dataIndex: 'hora_inicio',
                align: 'center',
                width: 100,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Hora Final',
                dataIndex: 'hora_parada',
                align: 'center',
                width: 100,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Tiempo<br>Empleado<br>(Horas)',
                dataIndex: 'tiempo_empleado',
                align: 'center',
                width: 100,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Nivel de<br>Actividad<br>Real<br>Ejecutado',
                dataIndex: 'nivel_act_real',
                align: 'center',
                width: 100,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Comb. que<br>debió<br>Consumir<br>(L)',
                dataIndex: 'combustible_debio_consumir',
                align: 'center',
                width: 140,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Comb. en<br>Tanque al<br>Concluir las<br>Operaciones<br>(L)',
                dataIndex: 'combustible_restante',
                align: 'center',
                width: 140,
                editor: {
                    xtype: 'numberfield',
                    decimalSeparator: '.',
                    decimalPrecision: 4,
                    minValue: 0
                },
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Comb. real<br>Consumido<br>(L)',
                dataIndex: 'combustible_real',
                align: 'center',
                width: 140,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Comb.<br>Abastecido<br>(L)',
                dataIndex: 'combustible_abastecido',
                align: 'center',
                width: 140,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Indice de<br>Consumo<br>real',
                dataIndex: 'indice_real',
                align: 'center',
                width: 100,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: 'Diferencia<br>Consumo<br>Real - Plan<br>(L)',
                dataIndex: 'diferencia_real_plan',
                width: 100,
                align: 'center',
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
            {
                text: '% de<br>Desviación',
                dataIndex: 'porciento_desviacion',
                align: 'center',
                width: 100,
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }
            },
        ],
        tbar: {
            id: 'gridAnexo3Tbar',
            items: [mes_anno, '-']
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
        title: 'Análisis Equipo a Equipo (Anexo 3)',
        frame: true,
        closable: true,
        layout: 'border',
        items: [panetree, gridEquiposTecnologicos, gridAnexos3]
    });

    App.render(panel);
});
