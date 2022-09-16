Ext.onReady(function () {

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
        fields: ['id', 'nivel_cm', 'existencia_m3', 'tanque_id'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tanques/loadMediciones'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                operation.setParams({
                    tanque_id: gridTanques.getSelection()[0].data.id
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
                gridTanques.enable();
                gridTanques.getStore().load();
            },
            beforeexpand: function () {
                gridTanques.focus();
                gridAfore.collapse('right', true);
            }
        }
    });

    let gridTanques = Ext.create('Ext.grid.Panel', {
        id: 'gridTanques',
        reference: 'gridTanques',
        features: [groupingFeature],
        store: storeTanques,
        region: 'center',
        disabled: true,
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen tanques registrados</div>'},
        columns: [
            {text: '<strong>Nro. Inventario</strong>', dataIndex: 'numero_inventario', filter: 'string', flex: 0.5},
            {text: '<strong>Descripción</strong>', dataIndex: 'descripcion', filter: 'string', flex: 0.8},
            {text: '<strong>Tipo Comb.</strong>', dataIndex: 'tipo_combustible_nombre', align: 'center', flex: 0.5},
            {text: '<strong>U.M.</strong>', dataIndex: 'unidad_medida_nombre', align: 'center', flex: 0.3},
            {text: '<strong>Capacidad</strong>', dataIndex: 'capacidad', flex: 0.5, align: 'right'},
            {text: '<strong>Existencia</strong>', dataIndex: 'existencia', flex: 0.5, align: 'right'},
            {
                text: '<strong>Cilindro</strong>', dataIndex: 'cilindro', align: 'center', flex: 0.3, renderer: function (value) {
                    if (value) return '<i class="fa fa-check"></i>'
                }
            },
        ],
        selModel: {
            mode: 'MULTI'
        },
        tbar: {
            id: 'gridTanquesTbar',
            height: 36,
            items: []
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeTanques'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (selected && !selected[0].data.cilindro) {
                    gridAfore.getStore().load();
                    gridAfore.expand(true);
                    gridAfore.enable();
                } else {
                    gridAfore.getStore().removeAll();
                    gridAfore.collapse();
                    gridAfore.disable();
                }
            }
        }
    });

    let gridAfore = Ext.create('Ext.grid.Panel', {
        title: 'Tabla Afore',
        id: 'gridAfore',
        reference: 'gridAfore',
        region: 'east',
        width: '25%',
        collapsed: true,
        collapsible: true,
        disabled: true,
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen tanques registrados</div>'},
        store: storeMediciones,
        columns: [
            {text: 'Nivel', dataIndex: 'nivel', flex: 1, align: 'right'},
            {text: 'Representa', dataIndex: 'existencia', flex: 1, align: 'right'}
        ],
        tbar: {
            id: 'gridMedicionesTbar',
            height: 36,
            items: []
        },
        tools: [{
            type: 'refresh',
            tooltip: 'Actualiza el listado de mediciones',
            callback: function (gridpanel, tool, event) {
                gridpanel.getStore().reload();
            }
        }],
        listeners: {
            beforeexpand: function () {
                let tanque = gridTanques.getSelection()[0];
                if (!tanque || tanque.data.cilindro) return false;

                gridTanques.focus();
                panetree.collapse('left', true);
            }
        }
    });

    let panelTanques = Ext.create('Ext.panel.Panel', {
        id: 'panelTanques',
        title: 'Tanques',
        frame: true,
        closable: true,
        layout: 'border',
        items: [panetree, gridTanques, gridAfore]
    });

    App.render(panelTanques);
});