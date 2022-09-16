Ext.onReady(function () {

    var store_marca_vehiculo = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeMarcaVehiculoId',
        fields: [
            {name: 'id'},
            {name: 'nombre'},

        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/marcavehiculo/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true,

    });

    var find_button_marca = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('gridMarcaVehiculoId').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value
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
                        Ext.getCmp('gridMarcaVehiculoId').getStore().loadPage(1);
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
                    Ext.getCmp('gridMarcaVehiculoId').getStore().loadPage(1);
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
                        if (Ext.getCmp('gridMarcaVehiculoId').getStore().getCount() > 0)
                            Ext.getCmp('gridMarcaVehiculoId').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('gridMarcaVehiculoId').getStore().loadPage(1);
                    }
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
        id: 'gridMarcaVehiculoId',
        store: store_marca_vehiculo,
        selModel: {
            mode: 'MULTI'
        },
        region: 'center',
        width: '98%',
        columns: [
            {text: '<strong>Nombre</strong>', dataIndex: 'nombre', filter: 'string', flex: 1},

        ],
        tbar: {
            id: 'marcaVehiculo_tbar',
            height: 36,
            items: [find_button_marca, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeMarcaVehiculoId'),
            displayInfo: true,
            // plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                find_button_modelo.setValue(null);
                grid_modelo.store.removeAll();
                if (selected.length > 0) {
                    grid_modelo.store.loadPage(1);
                    if (Ext.getCmp('modelo_btn_add'))
                        Ext.getCmp('modelo_btn_add').enable();
                    grid_modelo.expand();
                }
                else {
                    if (Ext.getCmp('modelo_btn_add'))
                        Ext.getCmp('modelo_btn_add').disable();
                    grid_modelo.collapse();
                }

                if (Ext.getCmp('marcaVehiculo_btn_mod'))
                    Ext.getCmp('marcaVehiculo_btn_mod').setDisabled(selected.length === 0);
                if (Ext.getCmp('marcaVehiculo_btn_del'))
                    Ext.getCmp('marcaVehiculo_btn_del').setDisabled(selected.length === 0);
            }
        }
    });


    var store_modelo = Ext.create('Ext.data.JsonStore', {
        frame: true,
        storeId: 'id_store_modelo',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'nmarca_vehiculoid'},
            {name: 'nnombremarca'}
        ],
        viewConfig: {forceFit: true},
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/modelovehiculo/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_modelo').getSelectionModel().deselectAll();
                var selected = Ext.getCmp('gridMarcaVehiculoId').getSelectionModel().getLastSelected();
                operation.setParams({
                    marca: selected.data.id,
                    // nombre: textSearchModelo.getValue()
                });
            }
        }
    });
    var find_button_modelo = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_modelo').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            var selected = Ext.getCmp('gridMarcaVehiculoId').getSelectionModel().getLastSelected();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value,
                                    marca: selected.data.id,
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
                        Ext.getCmp('id_grid_modelo').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_modelo').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_modelo').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_modelo').getStore().loadPage(1, {
                                params: {
                                    nombre: value,
                                    marca: Ext.getCmp('gridMarcaVehiculoId').getSelectionModel().getLastSelected().data.id,
                                }
                            });
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
                        Ext.getCmp('id_grid_modelo').getStore().loadPage(1);
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
    var grid_modelo = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_modelo',
        store: store_modelo,
        selModel: {
            mode: 'MULTI'
        },
        width: '50%',
        border: true,
        title: 'Listado de Modelos',
        region: 'east',
        collapsible: true,
        collapsed: true,
        columns: [
            {
                text: '<strong>Modelo</strong>',
                dataIndex: 'nombre',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'modelo_tbar',
            height: 36,
            items: [find_button_modelo, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_modelo'),
            displayInfo: true,
            // plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('modelo_btn_mod').setDisabled(selected.length === 0);
                Ext.getCmp('modelo_btn_del').setDisabled(selected.length === 0);
            }
        }
    });


    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'manage_marcaVehiculo_panel_id',
        frame: true,
        title: 'Listado de Marcas',
        closable: true,
        layout: 'border',
        items: [grid, grid_modelo]
    });
    App.render(_panel);
});