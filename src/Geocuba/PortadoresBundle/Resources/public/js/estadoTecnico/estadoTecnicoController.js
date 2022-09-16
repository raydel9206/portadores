/**
 * Created by yosley on 05/10/2015.
 */

Ext.onReady(function () {
    var grid_estado_Tecnico = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_estado_Tecnico',
        selModel: {
            mode: 'MULTI'
        },
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'id_store_estado_Tecnico',
            fields: [
                {name: 'id'},
                {name: 'nombre'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/estadotecnico/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: true,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    Ext.getCmp('id_grid_estado_Tecnico').getSelectionModel().deselectAll();
                }
            }
        }),
        columns: [
            {text: '<strong>Nombre</strong>', dataIndex: 'nombre', filter: 'string', flex: 1}
        ],
        tbar: {
            id: 'estado_Tecnico_tbar',
            height: 36,
            items: []
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_estado_Tecnico'),
            displayInfo: true,
            //     plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('estadoTecnico_btn_mod'))
                Ext.getCmp('estadoTecnico_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('estadoTecnico_btn_del'))
                Ext.getCmp('estadoTecnico_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var panel_estado_Tecnico = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_estado_Tecnico',
        title: 'Estados Técnicos de los Vehículos',
        frame: true,
        closable:true,
        layout: 'fit',
        items: [grid_estado_Tecnico]
    });

    App.render(panel_estado_Tecnico);
});