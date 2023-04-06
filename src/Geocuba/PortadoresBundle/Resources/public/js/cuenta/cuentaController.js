
Ext.onReady(function () {

    var store = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_cuenta',
        fields: [
            {name: 'id'},
            {name: 'no_cuenta'},
            {name: 'descripcion'},
            {name: 'clasificador_nombre'},
            {name: 'clasificador'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/cuenta/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_cuenta').getSelectionModel().deselectAll();
                operation.setParams({
                    no_cuenta: find_button.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    var storeSub = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_subcuenta',
        fields: [
            {name: 'id'},
            {name: 'no_cuenta'},
            {name: 'moneda_id'},
            {name: 'moneda'},
            {name: 'cuenta_id'},
            {name: 'cuenta'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/cuenta/loadSub'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_subcuenta').getSelectionModel().deselectAll();
                operation.setParams({
                    cuenta: Ext.getCmp('id_grid_cuenta').getSelectionModel().getLastSelected().data.id
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
                Ext.getCmp('id_grid_cuenta').getStore().on({
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
                        Ext.getCmp('id_grid_cuenta').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_cuenta').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_cuenta').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_cuenta').getStore().loadPage(1, {params: {no_cuenta: value}});
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
                        Ext.getCmp('id_grid_cuenta').getStore().loadPage(1);
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
                Ext.getCmp('id_grid_cuenta').enable();
                Ext.getCmp('id_grid_cuenta').getStore().loadPage(1);
            }
        }


    });

    var grid_subcuenta = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_subcuenta',
        title: 'Subcuentas',
        width: '25%',
        region: 'east',
        collapsible: true,
        collapsed: true,
        border: true,
        disabled:true,
        columnLines: true,
        store: storeSub,
        columns: [
            {
                dataIndex: 'no_cuenta',
                flex: 1,
                text: '<b>Nro. Subcuenta</b>'
            },
            {
                dataIndex: 'moneda',
                flex: 1,
                name: 'moneda',
                text: '<b>Moneda</b>',
            }
        ],
        tbar: {
            id: 'grid_subcuenta_tbar',
            height: 36,
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('subcuenta_btn_mod'))
                    Ext.getCmp('subcuenta_btn_mod').setDisabled(selected.length === 0);
                if (Ext.getCmp('subcuenta_btn_del'))
                    Ext.getCmp('subcuenta_btn_del').setDisabled(selected.length === 0);
            }
        }
    });
    
    var grid_cuenta = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_cuenta',
        store: store,
        region: 'center',
        width: '75%',
        disabled: true,
        columns: [
            {text: '<strong>No. Cuenta</strong>', dataIndex: 'no_cuenta', filter: 'string', flex: 0.4},
            {text: '<strong>Denominación</strong>', dataIndex: 'clasificador_nombre', filter: 'string', flex: 1},
            {text: '<strong>Descripci&oacute;n</strong>', dataIndex: 'descripcion', filter: 'string', flex: 1},
        ],
        tbar: {
            id: 'cuenta_tbar',
            height: 36,
            items: [find_button,/*textSearch, btnSearch, btnClearSearch, '-'*/]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_cuenta'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('cuenta_btn_mod'))
                    Ext.getCmp('cuenta_btn_mod').setDisabled(selected.length === 0);
                if (Ext.getCmp('cuenta_btn_del'))
                    Ext.getCmp('cuenta_btn_del').setDisabled(selected.length === 0);

                Ext.getCmp('id_grid_subcuenta').getStore().removeAll();
                if (selected.length > 0) {
                    Ext.getCmp('id_grid_subcuenta').enable();
                    Ext.getCmp('id_grid_subcuenta').expand();
                    Ext.getCmp('id_grid_subcuenta').getStore().load();
                }
                else {
                    Ext.getCmp('id_grid_subcuenta').disable();
                    Ext.getCmp('id_grid_subcuenta').collapse();
                }
            }
        }
    });

    var panel_cuenta = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_cuenta',
        title: 'Cuentas',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_cuenta,grid_subcuenta]
    });

    App.render(panel_cuenta);
});