Ext.onReady(function () {
    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Vehículo a buscar...',
        width: 200,
        listeners: {
            render: function (field) {
                Ext.getCmp('grid_vehiculos').getStore().on({
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
                        Ext.getCmp('grid_vehiculos').getStore().loadPage(1);
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
                    Ext.getCmp('grid_vehiculos').getStore().loadPage(1);
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
                        if (Ext.getCmp('grid_vehiculos').getStore().getCount() > 0)
                            Ext.getCmp('grid_vehiculos').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_mantenimiento').getStore().loadPage(1);
                    }
                    this.setMarked(false);
                }
            }
        },

        setMarked: function (marked) {
            var el = this.getEl(),
                id = '#' + this.getId();

            this.marked = marked;
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
        rootVisible: false,
        collapsible: true,
        collapsed: false,
        border: true,
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
                storeVehiculos.load();
                storeMant.removeAll();
                grid_vehiculos.setDisabled(!record);
                panetree.collapse();
                panel_center.focus();
            }
        }


    });

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: 'Vehículo: {name} ' + ' ({rows.length})',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });


    var storeVehiculos = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculos',
        fields: [
            {name: 'id'},
            {name: 'kilometraje'},
            {name: 'proximo_mant'},

        ],
        viewConfig: {forceFit: true},
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/mantenimiento/loadVehiculos'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('grid_vehiculos').getSelectionModel().deselectAll();
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });
    var grid_vehiculos = Ext.create('Ext.grid.Panel', {
        id: 'grid_vehiculos',
        title: 'Vehiculos',
        flex: .5,
        border: true,
        disabled: true,
        store: storeVehiculos,
        columns: [
            {
                text: '<strong>Medio Técnico</strong>',
                dataIndex: 'matricula',
                filter: 'string',
                flex: 1
            }, {
                text: '<strong>Odómetro</strong>',
                dataIndex: 'odometro',
                filter: 'string',
                flex: 1,
                renderer: function (value) {
                    if (typeof value === 'string')
                        return '<div class="badge badge-pill badge-danger">' + value + '</div>';
                    return value;
                }
            }
        ],
        plugins: [{
            ptype: 'rowexpander',
            rowBodyTpl: new Ext.XTemplate(
                '<div class="card p-1">',
                '   <div class="card">',
                '       <tpl if="!Ext.isEmpty(proximo_mant)">',
                '           <div class="card-header text-center">',
                '               <strong>Próximos Mantenimientos</strong> <em class="text-muted">({proximo_mant.length})</em>',
                '           </div>',
                '       </tpl>',
                '       <table class="table table-bordered table-hover table-responsive-md mb-0">',
                '           <tpl if="Ext.isEmpty(proximo_mant)">',
                '               <tr class="text-center">',
                '                   <td colspan="4"><span class="badge badge-secondary">No tiene mantenimientos asociados</span></td>',
                '                </tr>',
                '            <tpl else>',
                '            <thead class="text-center">',
                '               <tr>',
                '                   <th scope="col">Tipo</th>',
                '                   <th scope="col">Kms/Hrs</th>',
                '                   <th scope="col">Faltan (Kms/Hrs)</th>',
                '               </tr>',
                '             </thead>',
                '             <tbody>',
                '               <tpl for="proximo_mant">',
                '                   <tr class="text-center">',
                '                       <td>{mantenimiento}</td>',
                '                       <td>{proximo}</td>',
                '                       <td>{dif}</td>',
                '                    </tr>',
                '                </tpl>',
                '              </tbody>',
                '           </tpl>',
                '       </table>',
                '   </div>',
                '</div>'
            )
        }],
        listeners: {
            selectionchange: function (This, selected, e) {
                if (selected.length !== 0) {
                    grid_mantenimiento.setDisabled(false);
                    Ext.getCmp('id_grid_mantenimiento').getStore().loadPage(1);
                    Ext.getStore('id_store_tipomantenimiento_mantenimiento').load();
                    if (Ext.getStore('id_store_vehiculo_mantenimiento'))
                        Ext.getStore('id_store_vehiculo_mantenimiento').load();

                    let odometro = selected[0].data.odometro;
                    if (selected.length > 0) {
                        if (Ext.getCmp('mantenimiento_btn_add') && typeof odometro !== 'string') {
                            Ext.getCmp('mantenimiento_btn_add').setDisabled(false);
                        } else {
                            Ext.getCmp('mantenimiento_btn_add').setDisabled(true);
                        }
                    } else {
                        Ext.getCmp('mantenimiento_btn_add').setDisabled(true);
                    }
                }
            }
        },
        tbar: [find_button],
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_vehiculos'),
        },
    });

    var storeMant = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_mantenimiento',
        fields: [
            {name: 'id'},
            {name: 'kilometraje'},
            {name: 'fecha'},
            {name: 'tipo_mantenimientoid'},
            {name: 'tipo_mantenimientonombre'},
            {name: 'nvehiculoid'},
            {name: 'nvehiculomatricula'},
            {name: 'observaciones'}
        ],
        viewConfig: {forceFit: true},
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/mantenimiento/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        groupField: 'nvehiculomatricula',
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_mantenimiento').getSelectionModel().deselectAll();
                operation.setParams({
                    nvehiculoid: Ext.getCmp('grid_vehiculos').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });
    var grid_mantenimiento = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_mantenimiento',
        disabled: true,
        title: 'Mantenimientos',
        flex: 1,
        border: true,
        width: '100%',
        store: storeMant,
        columns: [
            {
                text: '<strong>Vehículo</strong>',
                dataIndex: 'nvehiculomatricula',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Fecha</strong>',
                dataIndex: 'fecha',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Tipo de mantenimiento</strong>',
                dataIndex: 'tipo_mantenimientonombre',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Kilometraje</strong>',
                dataIndex: 'kilometraje',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Observaciones</strong>',
                dataIndex: 'observaciones',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'mantenimiento_tbar',
            height: 36,
            items: []
        },
        features: [groupingFeature],
        plugins: 'gridfilters',
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_mantenimiento'),
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('mantenimiento_btn_mod'))
                    Ext.getCmp('mantenimiento_btn_mod').setDisabled(selected.length === 0);
                if (Ext.getCmp('mantenimiento_btn_del'))
                    Ext.getCmp('mantenimiento_btn_del').setDisabled(selected.length === 0);
            }
        }
    });

    var panel_center = Ext.create('Ext.panel.Panel', {
        id: 'panel_center',
        width: '100%',
        border: true,
        frame: true,
        layout: {
            type: 'hbox',
            pack: 'start',
            align: 'stretch'
        },
        items: [grid_vehiculos, grid_mantenimiento]
    });

    var _panel_mantenimiento = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_mantenimiento',
        border: true,
        frame: true,
        layout: {
            type: 'hbox',
            align: 'stretch',
            padding: 2
        },
        items: [panetree, panel_center]
    });
    App.render(_panel_mantenimiento);
});