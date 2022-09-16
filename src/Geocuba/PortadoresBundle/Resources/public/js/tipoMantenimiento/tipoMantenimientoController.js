Ext.onReady(function () {

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_tipomantenimiento').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    clasificacion: value
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
                        Ext.getCmp('id_grid_tipomantenimiento').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_tipomantenimiento').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_tipomantenimiento').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_tipomantenimiento').getStore().loadPage(1, {params: {clasificacion: value}});
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
                        Ext.getCmp('id_grid_tipomantenimiento').getStore().loadPage(1);
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

    var grid_tipomantenimiento = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_tipomantenimiento',
        width: '96%',
        height: '100%',
        region: 'center',
        selModel: {
            mode: 'MULTI'
        },
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'id_store_tipomantenimiento',
            fields: [
                {name: 'id'},
                {name: 'nombre'},
                {name: 'clasificacionid'},
                {name: 'clasificacion'}

            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/tipoMantenimiento/loadTipoMantenimiento'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: true,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    Ext.getCmp('id_grid_tipomantenimiento').getSelectionModel().deselectAll();
                }
            }
        }),
        columns: [
            {
                text: '<strong>Nombre</strong>',
                dataIndex: 'nombre',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Clasificación</strong>',
                dataIndex: 'clasificacion',
                filter: 'string',
                flex: 1
            }

        ],
        tbar: {
            id: 'tipomantenimiento_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_tipomantenimiento'),
            displayInfo: true
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('tipomantenimiento_btn_mod'))
                    Ext.getCmp('tipomantenimiento_btn_mod').setDisabled(selected.length === 0);
                if (Ext.getCmp('tipomantenimiento_btn_del'))
                    Ext.getCmp('tipomantenimiento_btn_del').setDisabled(selected.length === 0);
                Ext.getCmp('id_grid_marca_norma').getStore().load();
                Ext.getCmp('id_grid_marca_norma').setCollapsed(false);
            }
        }
    });

    var grid_marca_norma = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_marca_norma',
        title: 'Norma por Marca',
        region: 'east',
        collapsible: true,
        collapsed: true,
        width: '30%',
        height: '96%',
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'id_store_marca_norma',
            fields: [
                {name: 'idmarca'},
                {name: 'marca'},
                {name: 'cant_horas'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/tipoMantenimiento/loadNormaMarca'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: false,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    var selected = Ext.getCmp('id_grid_tipomantenimiento').getSelectionModel().getLastSelected();
                    operation.setParams({
                        tipo_mantenimiento: selected.data.id
                    });
                }
            }
        }),
        plugins: [Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
              edit: function (editor, e, eOpts) {
                  if (Ext.getCmp('saveEdit') !== undefined) {
                      Ext.getCmp('saveEdit').enable();
                      Ext.getCmp('saveEdit').setStyle('borderColor', 'red');
                  }
                }
            }
        })],
        columns: [
            {
                text: '<strong>Tipo Vehículo</strong>',
                dataIndex: 'marca',
                flex: 1
            },
            {
                header: '<strong>Kms/Horas</strong>',
                dataIndex: 'cant_horas',
                flex: 0.5,
                editor: {
                    allowBlank: false
                },
                field: {
                    xtype: 'numberfield'
                }
            }
        ],
        tbar:[{
            xtype: 'button',
            text: 'Guardar',
            id: 'saveEdit',
            iconCls: 'fas fa-save text-primary',
            disabled: true,
            handler: function () {
                let selectiongrid = Ext.getCmp('id_grid_marca_norma');
                let selectiongridmod = selectiongrid.getStore();
                var selected = Ext.getCmp('id_grid_tipomantenimiento').getSelectionModel().getLastSelected();
                let values1 = [];
                selectiongridmod.each(function (r) {
                    values1 = values1.concat({
                        tipoMantenimiento: selected.data.id,
                        marca: r.get('idmarca'),
                        cantHoras: r.get('cant_horas'),
                    });
                }, this);

                App.request('POST', App.buildURL('portadores/tipoMantenimiento/modMarcaNorma'), {datosmod: JSON.stringify(values1)}, null, null,
                    function (response) {
                        if (response && response.hasOwnProperty('success') && response.success) {
                            selectiongridmod.load();
                            Ext.getCmp('saveEdit').setStyle('borderColor', 'grey');
                        }
                    });

            }
        }]
    });


    var panel_tipomantenimiento = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_tipomantenimiento',
        title: 'Tipos de Mantenimiento',
        frame: true,
        closable: true,
        layout: 'border',
        items: [grid_tipomantenimiento, grid_marca_norma]
    });


    App.render(panel_tipomantenimiento);


});