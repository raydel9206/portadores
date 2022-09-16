/**
 * Created by pfcadenas on 27/09/2016.
 */

Ext.onReady(function () {
    var tree_store = Ext.create('Ext.data.TreeStore', {
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

    var panetree = Ext.create('Ext.tree.Panel', {
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
        header: {             style: {                 backgroundColor: 'white',                 borderBottom: '1px solid #c1c1c1 !important'             },         },
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
                grid.enable();
                Ext.getStore('storePieFirmaId').load();
                if (Ext.getStore('id_store_aprobado'))
                    Ext.getStore('id_store_aprobado').load();
                if (Ext.getStore('id_store_revisado'))
                    Ext.getStore('id_store_revisado').load();
                if (Ext.getStore('id_store_confeccionado'))
                    Ext.getStore('id_store_confeccionado').load();
                if (Ext.getStore('id_store_cajera'))
                    Ext.getStore('id_store_cajera').load();
            }
        }


    });

    var store = Ext.create('Ext.data.JsonStore', {
        storeId: 'storePieFirmaId',
        fields: [
            {name: 'id'},
            {name: 'documento'},
            {name: 'documentonombre'},
            {name: 'confecciona'},
            {name: 'revisa'},
            {name: 'autoriza'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/pie_firma/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners:{
            beforeload: function (store, operation, eOpts) {
                Ext.getCmp('gridPieFirmaId').getSelectionModel().deselectAll();
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            },
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'gridPieFirmaId',
        region: 'center',
        width: '75%',
        store: store,
        disabled: true,
        columns: [
            {text: '<strong>Documento</strong>', dataIndex: 'documentonombre', filter: 'string', flex: 1},
            {text: '<strong>Confeccionado Por</strong>', dataIndex: 'confeccionanombre', filter: 'string', flex: 1},
            {text: '<strong>Revisado Por</strong>', dataIndex: 'revisanombre', filter: 'string', flex: 1},
            {text: '<strong>Aprobado Por</strong>', dataIndex: 'autorizanombre', filter: 'string', flex: 1},
            {text: '<strong>Cajera</strong>', dataIndex: 'cajeranombre', filter: 'string', flex: 1}
        ],
        tbar: {
            id: 'PieFirma_tbar',
            height: 36,
            items: []
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storePieFirmaId'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('PieFirma_btn_mod'))
                Ext.getCmp('PieFirma_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('PieFirma_btn_del'))
                Ext.getCmp('PieFirma_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'manage_PieFirma_panel_id',
        title: 'Pie de Firmas de Documentos',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });
    App.render(_panel);
});