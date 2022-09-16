Ext.onReady(function(){
    var grid_clasificador = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_clasificador',
        store: Ext.create('Ext.data.JsonStore',{
            storeId: 'id_grid_clasificador',
            fields: [
                { name: 'id'},
                { name: 'nombre'},
                { name: 'codigo'},

            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/clasificador/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: true
        }),
        columns: [
            { text: '<strong>Nombre</strong>',
                dataIndex: 'nombre',
                filter: 'string',
                flex: 1
            },{ text: '<strong>Codigo</strong>',
                dataIndex: 'codigo',
                filter: 'string',
                flex: 1
            }
        ]
    });
    var _panel_clasificador = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_clasificador',
        title: 'Clasificador de cuenta',
        frame : true,
        closable:true,
        layout: 'fit',
        items:[grid_clasificador]
    });
    App.render(_panel_clasificador);
});