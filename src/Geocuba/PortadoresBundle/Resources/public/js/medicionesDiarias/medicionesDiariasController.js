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

                let assignGridPromise = new Promise((resolve, reject) => {
                    let i = 0;
                    while(!Ext.getCmp('gridMediciones') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('gridMediciones'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let storeTanques = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeTanques',
        fields: ['id', 'numero_inventario', 'descripcion', 'capacidad', 'existencia', 'unidad_id', 'unidad_nombre', 'tipo_combustible_id', 'tipo_combustible_nombre'],
        groupField: 'unidad_nombre',
        sorters: 'unidad_nombre',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tanques/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                operation.setParams({
                    unidad: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                })
            }

        }

    });

    let storeMediciones = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeMediciones',
        fields: ['id', 'medicion', 'litros', 'consumo_litros', 'fecha'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/mediciones_diarias/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                operation.setParams({
                    tanque_id: gridTanques.getSelection()[0].data.id,
                    mes: mes_anno.getValue().getMonth()+1,
                    anno: mes_anno.getValue().getFullYear()
                })
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
            select: function (This, record, tr, rowIndex, e, eOpts) {
                gridTanques.enable();
                gridTanques.getStore().load();
            }
        }
    });

    let gridTanques = Ext.create('Ext.grid.Panel', {
        id: 'gridTanques',
        reference: 'gridTanques',
        title: 'Tanques',
        features: [groupingFeature],
        store: storeTanques,
        region: 'center',
        disabled: true,
        width: '20%',
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen tanques registrados</div>'},
        columns: [
            {text: '<strong>Descripción</strong>', dataIndex: 'descripcion', filter: 'string', flex: 1},
            {text: '<strong>U.M.</strong>', dataIndex: 'unidad_medida_nombre', align: 'center', flex: 0.3},
        ],
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeTanques'),
            displayInfo: true,
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                if (selected) {
                    gridMediciones.getStore().load();
                    gridMediciones.enable();
                }
                else {
                    gridMediciones.getStore().removeAll();
                }
            }
        }
    });

    let gridMediciones = Ext.create('Ext.grid.Panel', {
        title: 'Listado de Mediciones',
        id: 'gridMediciones',
        reference: 'gridMediciones',
        region: 'east',
        width: '60%',
        disabled: true,
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No se han realizado mediciones.</div>'},
        store: storeMediciones,
        columns: [
            {text: 'Fecha', dataIndex: 'fecha', flex: 1, align: 'center'},
            {text: 'Nivel', dataIndex: 'medicion', flex: 1, align: 'center', renderer: function (value, meta) {
                    meta.style += 'text-align: right';
                    return value;
                }},
            {text: 'Existencia', dataIndex: 'existencia', flex: 1, align: 'center', renderer: function (value, meta) {
                    meta.style += 'text-align: right';
                    return value;
                }},
            {text: 'Consumo', dataIndex: 'consumo', flex: 1, align: 'center', renderer: function (value, meta) {
                    meta.style += 'text-align: right';
                    return value;
                }}
        ],
        tbar: {
            id: 'gridMedicionesTbar',
            height: 30,
            padding: '3 0 6 8',
            items: [mes_anno, '-']
        },
        tools: [{
            type: 'refresh',
            tooltip: 'Actualiza el listado de mediciones',
            callback: function (gridpanel, tool, event) {
                gridpanel.getStore().reload();
            }
        }]
    });

    let panel = Ext.create('Ext.panel.Panel', {
        id: 'panel',
        title: 'Mediciones Diarias',
        frame: true,
        closable: true,
        layout: 'border',
        items: [panetree, gridTanques, gridMediciones]
    });

    App.render(panel);
});
