Ext.onReady(function () {

    let storeMarcas = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeMarcas',
        fields: [
            {name: 'id'},
            {name: 'nombre'},

        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/marcas_tecn/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true,
        listeners: {
            beforeload: function (This) {
                gridMarcas.getSelectionModel().deselectAll();
            }
        }
    });

    let find_button_marca = Ext.create('Ext.form.field.SearchText');

    let gridMarcas = Ext.create('Ext.grid.Panel', {
        id: 'gridMarcas',
        reference: 'gridMarcas',
        title: 'Listado de Marcas Tecnológicas',
        store: storeMarcas,
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen marcas registradas</div>'},
        selModel: {mode: 'MULTI'},
        region: 'center',
        width: '98%',
        columns: [{text: '<strong>Nombre</strong>', dataIndex: 'nombre', filter: 'string', flex: 1}],
        tbar: {
            id: 'marcas_tbar',
            height: 36,
            items: [find_button_marca, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: storeMarcas,
            displayInfo: true
        },
        plugins: 'gridfilters',
        listeners: {
            boxready: function (This) {
                find_button_marca.grid = This;
            },
            selectionchange: function (This, selected) {
                if (selected.length !== 1) gridModelos.collapse();
                else {
                    gridModelos.expand();
                    gridModelos.getStore().load();
                }

                if (Ext.getCmp('btnUpdMarca')) Ext.getCmp('btnUpdMarca').setDisabled(selected.length !== 1);
            }
        }
    });


    let storeModelos = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeModelos',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'marca_tecnologica_id'},
            {name: 'marca_tecnologica_nombre'}
        ],
        viewConfig: {forceFit: true},
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/modelos_tecn/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation) {
                Ext.getCmp('gridModelos').getSelectionModel().deselectAll();
                let selected = Ext.getCmp('gridMarcas').getSelectionModel().getLastSelected();
                // operation.setExtraParam('marca_id', selected.data.id);
                This.getProxy().setExtraParam('marca_id', selected.data.id);
            }
        }
    });

    let find_button_modelo = Ext.create('Ext.form.field.SearchText');

    let gridModelos = Ext.create('Ext.grid.Panel', {
        id: 'gridModelos',
        reference: 'gridModelos',
        store: storeModelos,
        selModel: {mode: 'MULTI'},
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen modelos registrados</div>'},
        width: '50%',
        border: true,
        title: 'Listado de Modelos',
        region: 'east',
        collapsible: true,
        collapsed: true,
        columns: [{text: '<strong>Modelo</strong>', dataIndex: 'nombre', filter: 'string', flex: 1}],
        tbar: {
            id: 'modelos_tbar',
            height: 36,
            items: [find_button_modelo, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_modelo'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            boxready: function (This) {
                find_button_modelo.grid = This;
            },
            beforeexpand: function () {
                if (gridMarcas.getSelection().length !== 1) return false;
            },
            collapse: function (This) {
                This.getStore().removeAll();
            },
            selectionchange: function (This, selected) {
                if (Ext.getCmp('btnUpdModelo')) Ext.getCmp('btnUpdModelo').setDisabled(selected.length !== 1);
            }
        }
    });

    let _panel = Ext.create('Ext.panel.Panel', {
        id: 'manage_marcaVehiculo_panel_id',
        frame: true,
        closable: true,
        layout: 'border',
        items: [gridMarcas, gridModelos]
    });

    App.render(_panel);
});