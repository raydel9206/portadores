Ext.onReady(function () {

    let store_moneda = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_moneda_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/moneda/loadMoneda'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    let _storec = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_Combustible_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tipocombustible/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
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
                if (Ext.getCmp('arbolunidades') !== undefined)
                    Ext.getCmp('arbolunidades').getSelectionModel().deselectAll();
            }
        }
    });

    let store_tarjeta_vehiculo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'vehiculoid'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadTarjetaVehiculo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                if (Ext.getCmp('tarjeta_vehiculo_btn_add')) {
                    Ext.getCmp('tarjeta_vehiculo_btn_add').setDisabled(true);
                }
                grid_tarjeta_vehiculo.getSelectionModel().deselectAll();
                operation.setParams({
                    tarjeta: grid_tarjeta.getSelectionModel().getLastSelected().data.id
                })
            },
            load: function (store, record, succefully) {
                if (record.length === 0) {
                    if (Ext.getCmp('tarjeta_vehiculo_btn_add')) {
                        Ext.getCmp('tarjeta_vehiculo_btn_add').setDisabled(false);
                    }
                }
            }
        }
    });

    let store_tarjeta_persona = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta_persona',
        fields: [
            {name: 'id'},
            {name: 'personaid'},
            {name: 'persona'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadTarjetaPersona'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                if (Ext.getCmp('tarjeta_persona_btn_add')) {
                    Ext.getCmp('tarjeta_persona_btn_add').setDisabled(true);
                }
                grid_tarjeta_persona.getSelectionModel().deselectAll();
                operation.setParams({
                    tarjeta: grid_tarjeta.getSelectionModel().getLastSelected().data.id
                })
            },
            load: function (store, record) {
                if (record.length === 0) {
                    if (Ext.getCmp('tarjeta_persona_btn_add')) {
                        Ext.getCmp('tarjeta_persona_btn_add').setDisabled(false);
                    }
                }
            }
        }
    });

    let grid_tarjeta_persona = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_tarjeta_persona',
        title: 'Persona',
        region: 'center',
        flex: 1,
        store: store_tarjeta_persona,
        columns: [
            {
                text: '<strong>Nombre</strong>',
                dataIndex: 'persona',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'tarjeta_persona_tbar',
            height: 36,
            items: []
        },
        plugins: 'gridfilters',
        listeners: {
            select: function (This, selected, e) {
                if (Ext.getCmp('tarjeta_persona_btn_del').isDisabled())
                    Ext.getCmp('tarjeta_persona_btn_del').setDisabled(selected.length === 0);
            }
        }
    });

    let grid_tarjeta_vehiculo = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_tarjeta_vehiculo',
        title: 'Medio Técnico',
        flex: 1,
        region: 'north',
        store: store_tarjeta_vehiculo,
        columns: [
            {
                text: '<strong>Matrícula</strong>',
                dataIndex: 'vehiculo',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'tarjeta_vehiculo_tbar',
            height: 36,
            items: []
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('tarjeta_vehiculo_btn_del').isDisabled())
                    Ext.getCmp('tarjeta_vehiculo_btn_del').setDisabled(selected.length === 0);
            },

        }
    });

    let panel_asignado = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_asignado',
        title: 'Asignada a:',
        region: 'east',
        collapsible: true,
        collapsed: true,
        border: true,
        width: '25%',
        height: '100%',
        layout: 'border',
        items: [grid_tarjeta_vehiculo, grid_tarjeta_persona],
        listeners: {
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                panetree.collapse();
            }
        }
    });

    let textSearch = Ext.create('Ext.form.field.Text', {
        width: 120,
        emptyText: 'No. Tarjeta...',
        id: 'buscar_tarjeta',
        maskRe: /[0-9]/,
        enableKeyEvents: true,
        listeners: {
            keyup: function (This, e, eOpts) {
                grid_tarjeta.getStore().filterBy(function (record) {
                    return record.data.nro_tarjeta.search(This.value) !== -1;
                }, this);
            },
        }
    });

    var btnSearch = Ext.create('Ext.button.MyButton',{
        width:30,
        height:28,
        tooltip:'Buscar',
        iconCls:'fas fa-search text-primary',
        handler: function(){
            grid_tarjeta.getStore().addFilter(
                [
                    {
                        "operator": "like",
                        "value": textSearch.getValue(),
                        "property": "nro_tarjeta"
                    }
                ]
            );
        }
    });

    let store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'ntipo_combustibleid'},
            {name: 'nombretipo_combustibleid'},
            {name: 'nmonedaid'},
            {name: 'nombremonedaid'},
            {name: 'centrocostoid'},
            {name: 'centrocostonombre'},
            {name: 'nunidadid'},
            {name: 'nombreunidadid'},
            {name: 'nro_tarjeta'},
            {name: 'importe'},
            {name: 'persona'},
            {name: 'vehiculo'},
            {name: 'importel'},
            {name: 'fecha_registro'},
            {name: 'fecha_vencimieno'},
            {name: 'fecha_baja'},
            {name: 'causa_baja'},
            {name: 'reserva'},
            {name: 'exepcional'},
            {name: 'estado'}
        ],
        groupField: 'centrocostonombre',
        sorters: ['nombretipo_combustibleid', 'nro_tarjeta', 'nombreunidadid'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadTarjeta'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                if (Ext.getCmp('id_grid_tarjeta'))
                    Ext.getCmp('id_grid_tarjeta').getSelectionModel().deselectAll();
                operation.setParams({
                    nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    let states = Ext.create('Ext.data.Store', {
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        data: [
            {'id': 0, 'nombre': 'En Caja'},
            {'id': 1, 'nombre': 'Recargada en Caja'},
            {'id': 2, 'nombre': 'En Tránsito'},
            {'id': 3, 'nombre': 'Cancelada'},
        ]
    });

    let tipos_combustible = Ext.create('Ext.form.ComboBox', {
        store: _storec,
        width: 120,
        queryMode: 'local',
        displayField: 'nombre',
        valueField: 'id',
        id: 'id_tipos_combustible',
        emptyText: 'Combustible...',
        listeners: {
            select: function (This, record, eOpts) {
                Ext.getCmp('id_grid_tarjeta').getStore().filter('ntipo_combustibleid', record.id);
            }
        }

    });

    let estados = Ext.create('Ext.form.ComboBox', {
        store: states,
        width: 130,
        queryMode: 'local',
        displayField: 'nombre',
        valueField: 'id',
        id: 'id_estados',
        emptyText: 'Estados...',
        listeners: {
            select: function (This, record, eOpts) {
                Ext.getCmp('id_grid_tarjeta').getStore().filter('estado', record.id);
            }
        }

    });

    let monedas = Ext.create('Ext.form.ComboBox', {
        store: store_moneda,
        width: 90,
        queryMode: 'local',
        displayField: 'nombre',
        valueField: 'id',
        id: 'id_monedas',
        emptyText: 'Monedas...',
        listeners: {
            select: function (This, record, eOpts) {

                Ext.getCmp('id_grid_tarjeta').getStore().filter('nmonedaid', record.id);
            }
        }

    });

    let btnClearSearch = Ext.create('Ext.button.MyButton', {
        width: 25,
        height: 25,
        tooltip: 'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function () {
            estados.reset();
            tipos_combustible.reset();
            monedas.reset();
            textSearch.reset();
            Ext.getCmp('id_grid_tarjeta').getStore().clearFilter();
        }
    });

    let groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Centro de Costo: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });

    let grid_tarjeta = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_tarjeta',
        features: [groupingFeature],
        store: store_tarjeta,
        region: 'center',
        disabled: true,
        columns: [
            {
                text: '<strong>No. Tarjeta</strong>',
                dataIndex: 'nro_tarjeta',
                filter: 'string',
                align: 'center',
                flex: 0.5
            },
            {
                text: '<strong>Tipo de Combustible</strong>',
                dataIndex: 'nombretipo_combustibleid',
                filter: 'string',
                align: 'center',
                flex: 0.5
            },
            {
                text: '<strong>Moneda</strong>',
                dataIndex: 'nombremonedaid',
                filter: 'string',
                align: 'center',
                flex: 0.3
            },
            {
                text: '<strong>Importe($)</strong>',
                dataIndex: 'importe',
                filter: 'string',
                align: 'right',
                flex: 0.3
            },
            {
                text: '<strong>Importe(L)</strong>',
                dataIndex: 'importel',
                filter: 'string',
                align: 'right',
                flex: 0.3
            },
            {
                text: '<strong>Reserva</strong>',
                align: 'center',
                dataIndex: 'reserva',
                xtype: 'booleancolumn',
                flex: 0.2,
                trueText: '<span class="badge-true">Sí</span>',
                falseText: '<span class="badge-false">No</span>'
            },
            {
                text: '<strong>Excepcional</strong>',
                align: 'center',
                dataIndex: 'exepcional',
                xtype: 'booleancolumn',
                flex: 0.2,
                trueText: '<span class="badge-true">Sí</span>',
                falseText: '<span class="badge-false">No</span>'
            },
            {
                text: '<strong>Estado</strong>',
                align: 'center',
                dataIndex: 'estado',
                flex: 0.4,
                renderer: function (value, met) {
                    if (value === 0) {
                        // met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return '<strong><span class="badge badge-pill badge-primary">En Caja</span></strong>';
                    } else if (value === 1) {
                        // met.style = 'font-style:italic !important;font-weight: bold;background: #0080FF;';
                        return '<strong><span  class="badge badge-pill badge-secondary">Recargada en Caja</span></strong>';
                    } else if (value === 2) {
                        // met.style = 'font-style:italic !important;font-weight: bold;background: #04B431;';
                        return '<strong><span class="badge badge-pill badge-warning">En Tránsito</span></strong>';
                    } else if (value === 3) {
                        // met.style = 'font-style:italic !important;font-weight: bold;background: #04B431;';
                        return '<strong><span class="badge badge-pill badge-danger">Cancelada</span></strong>';
                    }
                }
            }
        ],
        tbar: {
            id: 'tarjeta_tbar',
            height: 36,
            items: [textSearch, tipos_combustible, monedas, estados, btnSearch, btnClearSearch, '-']
        },

        plugins: ['gridfilters',{
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
                '                   <th scope="col">Fecha de registro:</th>',
                '                   <th scope="col">Fecha de vencimiento:</th>',
                '                   <th scope="col">Persona:</th>',
                '                   <th scope="col">Medio:</th>',
                '               </tr>',
                '             </thead>',
                '             <tbody>',
                '               <tpl>',
                '                   <tr class="text-center">',
                '                       <td>{fecha_registro}</td>',
                '                       <td>{fecha_vencimieno}</td>',
                '                       <td>{persona}</td>',
                '                       <td>{vehiculo}</td>',
                '                    </tr>',
                '                </tpl>',
                '              </tbody>',
                '           </tpl>',
                '       </table>',
                '   </div>',
                '</div>'
            ),
            ptype: 'rowexpander'
        }],
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('btn_cancelarTarjeta'))
                    Ext.getCmp('btn_cancelarTarjeta').setDisabled(selected.length === 0);
                if (Ext.getCmp('tarjeta_btn_recarga'))
                    Ext.getCmp('tarjeta_btn_recarga').setDisabled(selected.length === 0);
                if (Ext.getCmp('id_modificar'))
                    Ext.getCmp('id_modificar').setDisabled(selected.length === 0);
                if (Ext.getCmp('btn_entrar_caja'))
                    Ext.getCmp('btn_entrar_caja').setDisabled(selected.length === 0);
                if (Ext.getCmp('btn_entrar_caja_recargar_salir'))
                    Ext.getCmp('btn_entrar_caja_recargar_salir').setDisabled(selected.length === 0);
                if (Ext.getCmp('btn_entrar_caja_recargar'))
                    Ext.getCmp('btn_entrar_caja_recargar').setDisabled(selected.length === 0);
                if (Ext.getCmp('btn_salir_caja'))
                    Ext.getCmp('btn_salir_caja').setDisabled(selected.length === 0);
                if (Ext.getCmp('id_recarga'))
                    Ext.getCmp('id_recarga').setDisabled(selected.length === 0);
            },
            select: function (This, record, index, eOpts) {
                panel_asignado.setCollapsed(false);
                grid_tarjeta_vehiculo.store.removeAll();
                grid_tarjeta_persona.store.removeAll();

                Ext.getStore('id_store_vehiculo').clearFilter();
                Ext.getStore('id_store_vehiculo').filter('tipo_combustibleid', record.data.ntipo_combustibleid);

                Ext.getCmp('id_grid_tarjeta_vehiculo').getStore().load();
                Ext.getCmp('id_grid_tarjeta_persona').getStore().load();

                if (record.data.estado === 0) {
                    Ext.getCmp('btn_cancelarTarjeta').setDisabled(false);
                    Ext.getCmp('tarjeta_btn_recarga').setDisabled(false);
                    Ext.getCmp('id_modificar').setDisabled(false);
                    Ext.getCmp('btn_entrar_caja').setHidden(true);
                    Ext.getCmp('btn_entrar_caja_recargar_salir').setHidden(true);
                    Ext.getCmp('btn_entrar_caja_recargar').setHidden(true);
                    Ext.getCmp('btn_salir_caja').setHidden(false);
                    Ext.getCmp('id_recarga').setHidden(false);

                } else if (record.data.estado === 2) {
                    Ext.getCmp('btn_cancelarTarjeta').setDisabled(false);
                    Ext.getCmp('tarjeta_btn_recarga').setDisabled(false);
                    Ext.getCmp('id_modificar').setDisabled(false);
                    Ext.getCmp('btn_entrar_caja').setHidden(false);
                    Ext.getCmp('btn_entrar_caja_recargar_salir').setHidden(false);
                    Ext.getCmp('btn_entrar_caja_recargar').setHidden(false);
                    Ext.getCmp('btn_salir_caja').setHidden(true);
                    Ext.getCmp('id_recarga').setHidden(true);
                } else if (record.data.estado === 1) {
                    Ext.getCmp('btn_cancelarTarjeta').setDisabled(false);
                    Ext.getCmp('tarjeta_btn_recarga').setDisabled(false);
                    Ext.getCmp('id_modificar').setDisabled(false);
                    Ext.getCmp('btn_entrar_caja').setHidden(true);
                    Ext.getCmp('btn_entrar_caja_recargar_salir').setHidden(true);
                    Ext.getCmp('btn_entrar_caja_recargar').setHidden(true);
                    Ext.getCmp('btn_salir_caja').setHidden(false);
                    Ext.getCmp('id_recarga').setHidden(true);

                } else if (record.data.estado === '') {
                    Ext.getCmp('btn_cancelarTarjeta').setDisabled(false);
                    Ext.getCmp('tarjeta_btn_recarga').setDisabled(false);
                    Ext.getCmp('id_modificar').setDisabled(false);
                    Ext.getCmp('btn_entrar_caja').setHidden(false);
                    Ext.getCmp('btn_entrar_caja_recargar_salir').setHidden(false);
                    Ext.getCmp('btn_entrar_caja_recargar').setHidden(false);
                    Ext.getCmp('btn_salir_caja').setHidden(true);
                    Ext.getCmp('id_recarga').setHidden(true);

                } else if (record.data.estado === 3) {

                    Ext.getCmp('btn_cancelarTarjeta').setDisabled(true);
                    Ext.getCmp('tarjeta_btn_recarga').setDisabled(true);
                    Ext.getCmp('id_modificar').setDisabled(true);
                    if (Ext.getCmp('tarjeta_btn_darbaja'))
                        Ext.getCmp('tarjeta_btn_darbaja').setDisabled(false);
                }
            }
        }
    });

    let panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        id: 'arbolunidades',
        hideHeaders: true,
        width: 280,
        rootVisible: false,
        border: true,
        collapsible: true,
        collapsed: false,
        region: 'west',
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
                if (record) {
                    grid_tarjeta.setDisabled(false);
                    Ext.getCmp('id_grid_tarjeta').getStore().load({
                        params: {
                            nunidadid: record.id
                        }
                    });
                    Ext.getStore('id_store_centro_costo').load({
                        params: {
                            unidadid: record.id
                        }
                    });
                    Ext.getStore('id_store_caja_tarjeta').load({
                        params: {
                            unidadid: record.id
                        }
                    });

                    if (Ext.getStore('id_store_persona'))
                        Ext.getStore('id_store_persona').load();
                    if (Ext.getStore('id_store_vehiculo'))
                        Ext.getStore('id_store_vehiculo').load();

                    Ext.getCmp('tarjeta_btn_add').setDisabled(false);
                    if (Ext.getCmp('btn_listado_candeladas'))
                        Ext.getCmp('btn_listado_candeladas').enable();
                }

            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                panel_asignado.collapse();
            }
        }
    });

    let _panel_tarjeta = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_tarjeta',
        title: 'Tarjetas',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid_tarjeta, panel_asignado]
    });

    App.render(_panel_tarjeta);
});

