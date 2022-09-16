Ext.onReady(function () {

    let storeAsignaciones = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeAsignaciones',
        fields: ['id', 'fecha', 'cantidad', 'unidad_id', 'unidad_nombre', 'tipo_combustible_id', 'tipo_combustible_nombre'],
        groupField: 'unidad_nombre',
        sorters: 'unidad_nombre',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/asignacion_tecnologicos/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation) {
                operation.setParams({
                    unidad_id: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear()
                })
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

    let groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Unidad: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });

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
                    while (!Ext.getCmp('gridAsignaciones') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('gridAsignaciones'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
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
                gridAsignaciones.enable();
                gridAsignaciones.getStore().load();
            },
            beforeexpand: function () {
                gridAsignaciones.focus();
            }
        }
    });

    let gridAsignaciones = Ext.create('Ext.grid.Panel', {
        id: 'gridAsignaciones',
        reference: 'gridAsignaciones',
        features: [groupingFeature],
        store: storeAsignaciones,
        region: 'center',
        disabled: true,
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen asignaciones registradas</div>'},
        columns: [
            {text: '<strong>Fecha</strong>', dataIndex: 'fecha', filter: 'string', flex: 0.5},
            {text: '<strong>Tipo de Combustible</strong>', dataIndex: 'tipo_combustible_nombre', align: 'center', flex: 0.5},
            {text: '<strong>Cantidad</strong>', dataIndex: 'cantidad', flex: 0.5, align: 'center', renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                }}
        ],
        tbar: {
            id: 'gridAsignacionesTbar',
            height: 36,
            items: [mes_anno, '-']
        },
        plugins: 'gridfilters'
    });

    let panelAsignaciones = Ext.create('Ext.panel.Panel', {
        id: 'panelAsignaciones',
        title: 'Asignación de Combustible para Equipos Tecnológicos',
        frame: true,
        closable: true,
        layout: 'border',
        items: [panetree, gridAsignaciones]
    });

    App.render(panelAsignaciones);
});