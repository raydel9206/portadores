/**
 * Created by yosley on 23/05/2016.
 */

Ext.onReady(function () {

    var grid_turno = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_turno',
        columns: [
            {
                text: '<strong>Turno</strong>',
                dataIndex: 'turno',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Horas</strong>',
                dataIndex: 'horas',
                filter: 'string',
                flex: 1
            }

        ],
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'id_store_turnos',
            fields: [
                {name: 'id'},
                {name: 'turno'},
                {name: 'horas'}

            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/turno/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: true
        }),
        tbar: {
            id: 'turnos_tbar',
            height: 36
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_turnos'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('turnos_btn_mod'))
                Ext.getCmp('turnos_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('turno_btn_del'))
                Ext.getCmp('turno_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var panel_turno = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_turno',
        title: 'Turnos',
        frame: true,
        closable:true,
        layout: 'fit',
        items: [grid_turno]
    });

    App.render(panel_turno);
});

