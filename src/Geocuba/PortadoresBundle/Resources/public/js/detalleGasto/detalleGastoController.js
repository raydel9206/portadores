/**
 * Created by orlando on 06/10/2015.
 */

Ext.onReady(function() {

    var store_detallegasto= Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_detallegasto',
        fields: [
            { name: 'id'},
            { name: 'codigo'},
            { name: 'moneda'},
            { name: 'monedaid'},
            { name: 'descripcion'},
            {name: 'tipo_combustible_id'},
            {name: 'tipo_combustible'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/detallegasto/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_detallegasto').getSelectionModel().deselectAll();
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
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

            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_detallegasto').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    codigo: value,
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
                        Ext.getCmp('id_grid_detallegasto').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_detallegasto').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_detallegasto').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_detallegasto').getStore().loadPage(1, {params: {codigo: value}});
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
                        Ext.getCmp('id_grid_detallegasto').getStore().loadPage(1);
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

    let panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        id: 'arbolunidades',
        width: 280,
        rootVisible: false,
        hideHeaders: true,
        border: true,
        collapsible: true,
        collapsed: false,
        region: 'west',
        collapseDirection: 'left',
        header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'},},
        layout: 'fit',

        columns: [
            {xtype: 'treecolumn', iconCls: Ext.emptyString, width: 400, dataIndex: 'nombre'}
        ],
        root: {
            text: 'root',
            expanded: true
        },
        listeners: {
            select: function (This, record, index, eOpts) {
                if (record) {
                    grid_detallegasto.getStore().load();
                    grid_detallegasto.setDisabled(false);
                }
            }
        }
    });

    var grid_detallegasto = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_detallegasto',
        store: store_detallegasto,
        region:'center',
        width:'75%',
        selModel: {
            mode: 'MULTI'
        },
        disabled:true,
        split:true,
        columns: [
            { text: '<strong>Código</strong>', dataIndex: 'codigo', filter: 'string', flex: 0.2},
            { text: '<strong>Moneda</strong>', dataIndex: 'moneda', filter: 'string', flex: 0.2},
            // { text: '<strong>Combustible</strong>', dataIndex: 'tipo_combustible', filter: 'string', flex: 0.3},
            { text: '<strong>Descripci&oacute;n</strong>', dataIndex: 'descripcion', filter: 'string', flex: 1}

        ],
        tbar: {
            id: 'detallegasto_tbar',
            height: 36,
            items: [ /*textSearch, btnSearch, btnClearSearch, '-'*/]
        },
        // bbar: {
        //     xtype: 'pagingtoolbar',
        //     pageSize: 25,
        //     store: store_detallegasto,
        //     displayInfo: true,
        // },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('detallegasto_btn_mod'))
                Ext.getCmp('detallegasto_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('detallegasto_btn_del'))
                Ext.getCmp('detallegasto_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var panel_detallegasto = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_detallegasto',
        title: 'Detalles del Gasto',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_detallegasto],
    });

    App.render(panel_detallegasto);
});