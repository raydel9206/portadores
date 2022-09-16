/**
 * Created by orlando on 06/10/2015.
 */

Ext.onReady(function () {

    var store = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_cuentagasto',
        fields: [
            {name: 'id'},
            {name: 'no_cuenta'},
            {name: 'descripcion'},
            {name: 'centro_costo_id'},
            {name: 'centro_costo_codigo'},
            {name: 'centro_costo_descripcion'},
            {name: 'elemento_gasto_id'},
            {name: 'elemento_gasto_codigo'},
            {name: 'elemento_gasto_descripcion'},
            {name: 'detalle_gasto_id'},
            {name: 'detalle_gasto_codigo'},
            {name: 'detalle_gasto_descripcion'},
        ],
        groupField: 'centro_costo_id',
        sorters: 'centro_costo_id',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/cuentagasto/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_cuentagasto').getSelectionModel().deselectAll();
                operation.setParams({
                    no_cuenta: find_button.getValue(),
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

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_cuentagasto').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    no_cuenta: value,
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
                        Ext.getCmp('id_grid_cuentagasto').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_cuentagasto').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_cuentagasto').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_cuentagasto').getStore().loadPage(1, {params: {no_cuenta: value}});
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
                        Ext.getCmp('id_grid_cuentagasto').getStore().loadPage(1);
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

    //TODO barra de scroll para las unidades
    var panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        hideHeaders: true,
        id: 'arbolunidades',
        hideHeaders: true,
        border: true,
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
                Ext.getCmp('id_grid_cuentagasto').enable();
                Ext.getCmp('id_grid_cuentagasto').getStore().loadPage(1);
                if (Ext.getStore('id_store_elemento_gasto'))
                    Ext.getStore('id_store_elemento_gasto').load();
                if (Ext.getStore('id_store_centro_costo'))
                    Ext.getStore('id_store_centro_costo').load();
            }
        }


    });

    var grid_cuentagasto = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_cuentagasto',
        store: store,
        region: 'center',
        width: '75%',
        disabled: true,
        columns: [
            {text: '<strong>No. Cuenta</strong>', dataIndex: 'no_cuenta', filter: 'string', flex: 0.4},
            {text: '<strong>Descripci&oacute;n</strong>', dataIndex: 'descripcion', filter: 'string', flex: 1},
            {text: '<strong>Centro Costo</strong>', dataIndex: 'centro_costo_descripcion', filter: 'string', flex: 0.5},
            {
                text: '<strong>Elemento de Gasto</strong>',
                dataIndex: 'elemento_gasto_descripcion',
                filter: 'string',
                flex: 0.5
            },
            {
                text: '<strong>Detalle del Gasto</strong>',
                dataIndex: 'detalle_gasto_descripcion',
                filter: 'string',
                flex: 0.5
            }


        ],
        tbar: {
            id: 'cuentagasto_tbar',
            height: 36,
            items: [/*textSearch, btnSearch, btnClearSearch, '-'*/find_button]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_cuentagasto'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('cuentagasto_btn_mod'))
                    Ext.getCmp('cuentagasto_btn_mod').setDisabled(selected.length == 0);
                if (Ext.getCmp('cuentagasto_btn_del'))
                    Ext.getCmp('cuentagasto_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var panel_cuentagasto = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_cuentagasto',
        title: 'Cuentas de Gasto',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_cuentagasto]
    });

    App.render(panel_cuentagasto);
});