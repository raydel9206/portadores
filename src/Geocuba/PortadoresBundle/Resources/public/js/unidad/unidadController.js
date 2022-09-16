Ext.onReady(function () {
    let tree_store = Ext.create('Ext.data.TreeStore', {
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

    var panel_tree = Ext.create('Ext.tree.Panel', {
        store: tree_store,
        id: 'arbolunidades',
        title: 'Unidades',
        closable: true,
        frame: true,
        columnLines:true,
        rowLines: true,
        rootVisible: false,
        columns: [
            {xtype: 'treecolumn', iconCls: Ext.emptyString, text: "<b>Unidades</b>", flex: 1, dataIndex: 'nombre'},
            {text: "<b>Siglas</b>", flex: 0.5, dataIndex: 'siglas'},
            {text: "<b>C&oacute;digo</b>", flex: 0.5, dataIndex: 'codigo'},
            {text: "<b>C&oacute;digo Fincimex</b>", flex: 0.5, dataIndex: 'codfincimex'},
            {text: "<b>Municipio</b>", align: 'center', flex: 0.8, dataIndex: 'municipio_nombre'},
            {text: "<b>Provincia</b>", align: 'center', flex: 0.8, dataIndex: 'provincia_nombre'},
            {
                text: '<strong>Tipo</strong>',
                dataIndex: 'nivel',
                align: 'center',
                filter: 'string',
                flex: .3,
                renderer: function (value) {
                    if (value === 'osde') {
                        return '<div class="badge badge-pill badge-info">OSDE</div>';
                    }
                    else if(value === 'empresa'){
                        return '<div class="badge badge-pill badge-secondary">EMPRESA</div>';
                    }
                    else{
                        return '<div class="badge badge-pill badge-success">UEB</div>';
                    }
                }
            }
        ],
        tbar: {
            id: 'unidad_tbar',
            height: 36,
            items: [{
                text: 'Expandir',
                tooltip: 'Expande todos los recursos',
                glyph: 0xf065,
                cls: 'border-secondary',
                iconCls: 'text-dark',
                width: 95,
                handler: function (button, event) {
                    panel_tree.expandAll();
                }
            }, {
                text: 'Contraer',
                tooltip: 'Contrae todos los recursos',
                glyph: 0xf066,
                cls: 'border-secondary',
                iconCls: 'text-dark',
                width: 95,
                handler: function (button, event) {
                    panel_tree.collapseAll();
                }
            }]
        },
        listeners: {
            selectionchange: function (This, selected) {
                if (Ext.getCmp('unidad_btn_add') !== undefined)
                    Ext.getCmp('unidad_btn_add').setDisabled(selected.length === 0);
                if (Ext.getCmp('unidad_btn_upd') !== undefined)
                    Ext.getCmp('unidad_btn_upd').setDisabled(selected.length === 0);
                if (Ext.getCmp('unidad_btn_del') !== undefined)
                    Ext.getCmp('unidad_btn_del').setDisabled(selected.length === 0);
            }
        }
    });

    App.render(panel_tree);
});