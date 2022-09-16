
Ext.onReady(function () {

    let store = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeServicentroId',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/saldos_combustible/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                if (Ext.getCmp('gridReporte'))
                    Ext.getCmp('gridReporte').getSelectionModel().deselectAll();
                operation.setParams({
                    nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    combustible:  Ext.getCmp('id_tipos_combustible').getValue()
                });
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
        width: 280,
        id: 'arbolunidades',
        hideHeaders: true,
        frame: true,
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
                gridReporte.enable();
                gridReporte.getStore().loadPage(1);
                gridReporte.focus();
            }
        }
    });


    let _storec = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_Combustible',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tipocombustible/load'),
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
                }]);
                Ext.getCmp('id_tipos_combustible').setValue(_storec.getData().items[0]);
            }
        }
    });

    let tipos_combustible = Ext.create('Ext.form.ComboBox', {
        id: 'id_tipos_combustible',
        store: _storec,
        width: 150,
        queryMode: 'local',
        displayField: 'nombre',
        valueField: 'id',
        emptyText: 'Combustible...',
        listeners: {
            select: function (This, record, eOpts) {
                gridReporte.getStore().load();
            }
        }
    });


    let gridReporte = Ext.create('Ext.grid.Panel', {
        id: 'gridReporte',
        store: store,
        region: 'center',
        disabled: true,
        columns: [
            {text: '<strong>Tipo de combustible</strong>', dataIndex: 'combustible', flex: 1},
            {text: '<strong>Saldo total en tarjeta</strong>', dataIndex: 'importe', flex: 1},
        ],
        tbar: {
            id: 'servicentro_tbar',
            height: 36,
            items:[tipos_combustible]
        }
    });

    let _panel = Ext.create('Ext.panel.Panel', {
        id: 'manage_servicentro_panel_id',
        title: 'Servicentros',
        frame: true,
        closable:true,
        layout: 'border',
        items: [gridReporte,panetree]
    });
    App.render(_panel);
});