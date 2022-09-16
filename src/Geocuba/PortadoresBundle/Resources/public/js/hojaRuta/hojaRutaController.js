Ext.onReady(function () {
    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Vehículo a buscar...',
        width: '35%',
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_hoja_ruta').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    chapa: value,
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
                        Ext.getCmp('id_grid_hoja_ruta').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_hoja_ruta').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_hoja_ruta').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_hoja_ruta').getStore().loadPage(1, {params: {chapa: value}});
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
                        Ext.getCmp('id_grid_hoja_ruta').getStore().loadPage(1);
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

            // if (marked) {
            //     el.down(id + '-inputEl').addCls('x-form-invalid-field x-form-invalid-field-default');
            //     el.down(id + '-inputWrap').addCls('form-text-wrap-invalid');
            //     el.down(id + '-triggerWrap').addCls('x-form-trigger-wrap-invalid');
            // } else {
            //     el.down(id + '-inputEl').removeCls('x-form-invalid-field x-form-invalid-field-default');
            //     el.down(id + '-inputWrap').removeCls('form-text-wrap-invalid');
            //     el.down(id + '-triggerWrap').removeCls('x-form-trigger-wrap-invalid');
            // }
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
                grid_hoja_ruta.setDisabled(false);
                Ext.getCmp('id_grid_hoja_ruta').getStore().loadPage(1);
                if (Ext.getStore('id_store_vehiculo_unidad'))
                    Ext.getStore('id_store_vehiculo_unidad').load();
                if (Ext.getStore('id_store_persona_unidad'))
                    Ext.getStore('id_store_persona_unidad').load();
            }
        }


    });

    var grid_hoja_ruta = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_hoja_ruta',
        region:'center',
        width:'75%',
        disabled:true,
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'id_store_hoja_ruta',
            fields: [
                {name: 'id'},
                {name: 'fecha'},
                {name: 'numerohoja'},
                {name: 'capacidad', type: 'int'},
                {name: 'numero'},
                {name: 'entidad'},
                {name: 'organismo'},
                {name: 'lugarparqueo'},
                {name: 'servicioautorizado'},
                {name: 'kmsdisponible'},
                {name: 'observaciones'},
                {name: 'vehiculoid'},
                {name: 'vehiculo'},
                {name: 'habilitadaporid'},
                {name: 'habilitadapor'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/hoja_ruta/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: false,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    Ext.getCmp('id_grid_hoja_ruta').getSelectionModel().deselectAll();
                    operation.setParams({
                        chapa: find_button.getValue(),
                        unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                    });
                }
            }
        }),
        columns: [
            {
                text: '<strong>No. Hoja</strong>',
                tooltip: 'Índice Consumo Plan (Km/l)',
                flex: 1,
                dataIndex: 'numerohoja'
            }, {
                text: '<strong>Matrícula</strong>',
                flex: 1,
                dataIndex: 'vehiculo'
            },

            {
                text: '<strong>Fecha</strong>',
                tooltip: 'Kilómetros total recorridos',
                flex: 1,
                dataIndex: 'fecha'
            }
        ],
        tbar: {
            id: 'hoja_ruta_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_hoja_ruta'),
            displayInfo: false,
        },
        plugins: [
            'gridfilters',
            {
                ptype: 'rowexpander',
                rowBodyTpl: new Ext.XTemplate(
                    '<div class="card p-1">',
                    '   <div class="card">',
                    '       <tpl>',
                    '           <div class="card-header text-center">',
                    '               <strong>Otros datos de interés</strong> <em class="text-muted"></em>',
                    '           </div>',
                    '       </tpl>',
                    '       <table class="table table-bordered table-hover table-responsive-md mb-0">',
                    '           <tpl if="Ext.isEmpty(id)">',
                    '               <tr class="text-center">',
                    '                   <td colspan="4"><span class="badge badge-secondary">No tiene mantenimientos asociados</span></td>',
                    '                </tr>',
                    '            <tpl else>',
                    '            <thead class="text-center">',
                    '               <tr>',
                    '                   <th scope="col">Tipo mantenimiento:</th>',
                    '                   <th scope="col">Kilometraje:</th>',
                    '                   <th scope="col">Fecha registro:</th>',
                    '                   <th scope="col">Observaciones:</th>',
                    '               </tr>',
                    '             </thead>',
                    '             <tbody>',
                    '               <tpl>',
                    '                   <tr class="text-center">',
                    '                       <td>{tipo_mantenimiento}</td>',
                    '                       <td>{kilometraje_mantenimiento}</td>',
                    '                       <td>{fecha}</td>',
                    '                       <td>{observaciones}</td>',
                    '                    </tr>',
                    '                </tpl>',
                    '              </tbody>',
                    '           </tpl>',
                    '       </table>',
                    '   </div>',
                    '</div>'
                )
            }
        ],
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('hoja_ruta_btn_mod') != undefined)
                    Ext.getCmp('hoja_ruta_btn_mod').setDisabled(selected.length == 0);
                if (Ext.getCmp('hoja_ruta_btn_del') != undefined)
                    Ext.getCmp('hoja_ruta_btn_del').setDisabled(selected.length == 0);

                gridDesglose.store.removeAll();
                gridConductor.store.removeAll();
                panelCentral.setDisabled(selected.length == 0);
                if (Ext.getCmp('hoja_ruta_desglose_btn_add') != undefined) {
                    Ext.getCmp('hoja_ruta_desglose_btn_add').setDisabled(selected.length == 0);
                }
                if (Ext.getCmp('conductor_btn_add') != undefined) {
                    Ext.getCmp('conductor_btn_add').setDisabled(selected.length == 0);
                }
                if (selected.length > 0) {
                    gridDesglose.store.loadPage(1);
                    gridConductor.store.loadPage(1);
                }
            }
        }
    });

    var gridDesglose = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_desglose',
        title: 'Rutas',
        flex: 1,
        region: 'north',
        width:'100%',
        columns: [
            {
                text: 'Fecha', dataIndex: 'fecha', flex: .5,
                renderer: function (value, metaData, record) {
                    var fecha = Ext.Date.parse(record.data.fecha, 'd/m/Y');
                    return Ext.util.Format.date(fecha, 'd/m/Y');
                }
            },
            {
                text: '<strong>Hora</strong>',
                columns: [
                    {
                        text: '<strong>Salida</strong>',
                        flex: 1,
                        dataIndex: 'horasalida'
                    },
                    {
                        text: '<strong>Llegada</strong>',
                        flex: 1,
                        dataIndex: 'horallegada'
                    }
                ]
            },
            {
                text: 'Tiempo Horas', dataIndex: 'tiempohoras', flex: .5,
                renderer: function (value, metaData, record) {
                    var salida = Ext.Date.parse(record.data.horasalida, 'g:i A');
                    var llegada = Ext.Date.parse(record.data.horallegada, 'g:i A');

                    return Ext.util.Format.round((llegada - salida) / 3600000, 2);
                }
            },
            {
                text: '<strong>Kms Odómetro</strong>',
                columns: [
                    {
                        text: '<strong>Salida</strong>',
                        flex: 1,
                        dataIndex: 'kmssalida'
                    },
                    {
                        text: '<strong>Llegada</strong>',
                        flex: 1,
                        dataIndex: 'kmsllegada'
                    }
                ]
            },
            {
                text: 'Kms Total', dataIndex: 'kilometraje', flex: .5,
                renderer: function (value, metaData, record) {
                    return parseFloat(record.data.kmsllegada) - parseFloat(record.data.kmssalida);
                }
            }
        ],
        store: Ext.create('Ext.data.Store', {
            storeId: 'id_store_desglose',
            fields: [
                {name: 'id'},
                {name: 'fecha'},
                {name: 'horasalida'},
                {name: 'horallegada'},
                {name: 'kmssalida'},
                {name: 'kmsllegada'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/hoja_ruta/desglose/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: false,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    var selected = Ext.getCmp('id_grid_hoja_ruta').getSelectionModel().getLastSelected();
                    Ext.getCmp('id_grid_desglose').getSelectionModel().deselectAll();
                    operation.setParams({
                        hojarutaid: selected.data.id
                    });
                }
            }
        }),
        tbar: {
            id: 'hoja_ruta_desglose_tbar',
            height: 36,
            items: []
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_desglose'),
            displayInfo: false,
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('hoja_ruta_desglose_btn_del') != undefined)
                    Ext.getCmp('hoja_ruta_desglose_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var gridConductor = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_conductor',
        title: 'Conductores',
        region:'center',
        width:'100%',
        flex: .6,
        columns: [
            {text: 'Nombre', dataIndex: 'persona', flex: 1},
            {text: 'Licencia No.', dataIndex: 'nro_licencia', flex: 1}
        ],
        store: Ext.create('Ext.data.Store', {
            storeId: 'id_store_desglose',
            fields: [
                {name: 'id'},
                {name: 'personaid'},
                {name: 'persona'},
                {name: 'nro_licencia'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/hoja_ruta/conductor/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            pageSize: 1000,
            autoLoad: false,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    var selected = Ext.getCmp('id_grid_hoja_ruta').getSelectionModel().getLastSelected();
                    Ext.getCmp('id_grid_desglose').getSelectionModel().deselectAll();
                    operation.setParams({
                        hojarutaid: selected.data.id
                    });
                }


            },
        }),
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('conductor_btn_del') != undefined)
                    Ext.getCmp('conductor_btn_del').setDisabled(selected.length == 0);
            }


        },
        tbar: {
            id: 'grid_conductor_tbar',
            height: 36,
            items: []
        }
    });

    var panelCentral = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_info_hoja_ruta',
        region: 'east',
        width: '40%',
        disabled: true,
        border: true,
        height: '96%',
        layout: 'border',
        items: [gridDesglose, gridConductor]
    });
    var _panel_hoja_ruta = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_hoja_ruta',
        title: 'Hojas de Ruta',
        border:false,
        frame: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid_hoja_ruta, panelCentral]
    });
    App.render(_panel_hoja_ruta);
});