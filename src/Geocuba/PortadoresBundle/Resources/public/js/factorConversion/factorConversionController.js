Ext.onReady(function () {
    let store_factor = Ext.create('Ext.data.JsonStore', {
        frame: true,
        storeId: 'id_store_factor',
        fields: [
            {name: 'id'},
            {name: 'portador_id'},
            {name: 'portador_nombre'},
            {name: 'de_um_id'},
            {name: 'de_um_nombre'},
            {name: 'a_um_id'},
            {name: 'a_um_nombre'},
            {name: 'factor'}
        ],
        viewConfig: {forceFit: true},
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/factor/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });

    let grid_factor = Ext.create('Ext.grid.Panel', {
        id: 'grid_factor',
        reference: 'grid_factor',
        store: store_factor,
        viewModel: {},
        columns: [
            {
                text: '<strong>PORTADOR</strong>',
                dataIndex: 'portador_nombre',
                align: 'left',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>De UM</strong>',
                dataIndex: 'de_um_nombre',
                align: 'center',
                flex: 1
            },
            {
                text: '<strong>A UM</strong>',
                dataIndex: 'a_um_nombre',
                align: 'center',
                flex: 1
            },
            {
                text: '<strong>Factor</strong>',
                dataIndex: 'factor',
                align: 'center',
                flex: 1
            }
        ],
        tbar: {
            id: 'factor_tbar',
            height: 36,
            items: []
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_factor'),
            displayInfo: true,
        },
    });

    let _panel_factor = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_factor',
        title: 'Factores de Conversión',
        frame: true,
        closable: true,
        layout: 'fit',
        items: [grid_factor]
    });
    App.render(_panel_factor);
});