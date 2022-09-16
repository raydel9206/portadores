/**
 * Created by yosley on 07/10/2015.
 */

Ext.onReady(function () {

    let storeEquiposTecn = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeEquiposTecn',
        fields: ['id', 'numero_inventario', 'descripcion', 'capacidad', 'existencia', 'unidad_id', 'unidad_nombre', 'tipo_combustible_id', 'tipo_combustible_nombre'],
        groupField: 'unidad_nombre',
        sorters: 'unidad_nombre',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/equipos_tecn/load'),
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
            select: function () {
                gridEquiposTecn.enable();
                gridEquiposTecn.getStore().load();
            }
        }
    });

    let gridEquiposTecn = Ext.create('Ext.grid.Panel', {
        id: 'gridEquiposTecn',
        reference: 'gridEquiposTecn',
        features: [groupingFeature],
        store: storeEquiposTecn,
        region: 'center',
        disabled: true,
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen tanques registrados</div>'},
        columns: [
            {text: '<strong>Nro. Inventario</strong>', dataIndex: 'numero_inventario', filter: 'string', flex: 0.4},
            {text: '<strong>Descripción</strong>', dataIndex: 'descripcion', filter: 'string', flex: 0.6},
            {text: '<strong>Denominación</strong>', dataIndex: 'denominacion_nombre', align: 'center', flex: 0.6},
            {text: '<strong>Actividad</strong>', dataIndex: 'actividad_nombre', align: 'center', flex: 0.6},
            {text: '<strong>Tipo de<br/>Combustible</strong>', dataIndex: 'tipo_combustible_nombre', align: 'center', flex: 0.5},
            {text: '<strong>Indice de <br/> Consumo</strong>', dataIndex: 'norma', align: 'center', flex: 0.4, renderer: function (value, meta) {
                    meta.style += 'text-align: right';
                    return value;
                }}
        ],
        tbar: {
            id: 'gridEquiposTecnTbar',
            height: 36,
            items: []
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeEquiposTecn'),
            displayInfo: true,
        },
        plugins: 'gridfilters'
    });

    let panel = Ext.create('Ext.panel.Panel', {
        title: 'Equipos tecnológicos',
        frame: true,
        closable: true,
        layout: 'border',
        items: [panetree, gridEquiposTecn]
    });

    App.render(panel);
});