/**
 * Created by kireny on 5/11/15.
 */

Ext.onReady(function () {
    var store = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeAreaId',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/area/loadArea'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('gridAreaId').getSelectionModel().deselectAll();
                operation.setParams({
                    nombre: find_button.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }

    });

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
        border:true,
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
                Ext.getCmp('gridAreaId').getStore().loadPage(1);
            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('gridAreaId').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value,
                                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                                });
                            }
                        }
                    },
                    load: function () {
                        field.enable();
                    }
                });
            },
            change: function (field, newValue, oldValue, eOpt) {
                field.getTrigger('clear').setVisible(newValue);
                if (Ext.isEmpty(Ext.String.trim(field.getValue()))) {
                    var marked = field.marked;
                    field.setMarked(false);

                    if (marked) {
                        Ext.getCmp('gridAreaId').getStore().loadPage(1);
                    }

                    field.getTrigger('search').hide();
                } else {
                    field.getTrigger('search').show();

                    if (field.marked) {
                        field.setMarked(true);
                    }
                }
            },
            specialkey: function (field, e) {
                var value = field.getValue();

                if (!Ext.isEmpty(Ext.String.trim(value)) && e.getKey() === e.ENTER) {
                    field.setMarked(true);
                    Ext.getCmp('gridAreaId').getStore().loadPage(1);
                } else if (e.getKey() === e.BACKSPACE && e.getKey() === e.DELETE && (e.ctrlKey && e.getKey() === e.V)) {
                    field.setMarked(false);
                }
            }
        },
        triggers: {
            search: {
                cls: Ext.baseCSSPrefix + 'form-search-trigger',
                hidden: true,
                handler: function () {
                    var value = this.getValue();
                    if (!Ext.isEmpty(Ext.String.trim(value))) {
                        this.setMarked(true);
                        if (Ext.getCmp('gridAreaId').getStore().getCount() > 0)
                            Ext.getCmp('gridAreaId').getStore().loadPage(1, {params: {nombre: value}});
                    }
                }
            },
            clear: {
                cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                hidden: true,
                handler: function () {
                    this.setValue(null);
                    this.updateLayout();

                    if (this.marked) {
                        Ext.getCmp('gridAreaId').getStore().loadPage(1);
                    }
                    // Ext.getCmp('id_grid_tiporam').setTitle('tiporam');
                    this.setMarked(false);
                }
            }
        },

        setMarked: function (marked) {
            var el = this.getEl(),
                id = '#' + this.getId();

            this.marked = marked;

            if (marked) {
                el.down(id + '-inputEl').addCls('x-form-invalid-field x-form-invalid-field-default');
                el.down(id + '-inputWrap').addCls('form-text-wrap-invalid');
                el.down(id + '-triggerWrap').addCls('x-form-trigger-wrap-invalid');
            } else {
                el.down(id + '-inputEl').removeCls('x-form-invalid-field x-form-invalid-field-default');
                el.down(id + '-inputWrap').removeCls('form-text-wrap-invalid');
                el.down(id + '-triggerWrap').removeCls('x-form-trigger-wrap-invalid');
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'gridAreaId',
        region:'center',
        width:'75%',
        disabled: true,
        store: store,
        selModel: {
            mode: 'MULTI'
        },
        columns: [
            {text: '<strong>Nombre</strong>', dataIndex: 'nombre', filter: 'string', flex: 1}
        ],
        tbar: {
            id: 'Area_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeAreaId'),
            displayInfo: true,
            // plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('Area_btn_mod'))
                Ext.getCmp('Area_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('Area_btn_del'))
                Ext.getCmp('Area_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'manage_Area_panel_id',
        title: 'Áreas',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid]
    });
    App.render(_panel);
});