Ext.onReady(function () {


    let tipo_combustible = Ext.create('Ext.form.ComboBox', {
        id: 'nTipoCombustibleId',
        name: 'ntipo_combustibleid',
        fieldLabel: 'Tipo de combustible',
        labelWidth: 120,
        width: 280,
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'storeTipoCombustible',
            fields: [
                {name: 'id'},
                {name: 'nombre'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('portadores/tipocombustible/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: true,
        }),
        displayField: 'nombre',
        valueField: 'id',
        typeAhead: true,
        queryMode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        selectOnFocus: true,
        editable: true,
        listeners: {
            change: function (This, newValue, oldValue, eOpts) {
                // console.log('aaa');
                grid_vehiculo.getStore().load();
            }
        }
    });

    let tipo_medio = Ext.create('Ext.form.ComboBox', {
        id: 'nTipoMedioId',
        name: 'ntipo_medioid',
        fieldLabel: 'Denominación',
        labelWidth: 120,
        width: 280,
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'storeTipomedio',
            fields: [
                {name: 'id'},
                {name: 'nombre'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/denominacionvehiculo/loadCombo'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: true,
        }),
        displayField: 'nombre',
        valueField: 'id',
        typeAhead: true,
        queryMode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        selectOnFocus: true,
        editable: true,
        listeners: {
            change: function (This, newValue, oldValue, eOpts) {
                // console.log('aaa');
                grid_vehiculo.getStore().load();
            }
        }
    });

    let btnClearSearch = Ext.create('Ext.button.MyButton', {
        width: 25,
        height: 25,
        tooltip: 'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function () {
            tipo_combustible.reset();
            tipo_medio.reset();
            find_button.reset();
        }
    });

    var store_vehiculo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'nmarca_vehiculoid'},
            {name: 'nmarca_vehiculo'},
            {name: 'nmodelo_vehiculoid'},
            {name: 'nestado_tecnicoid'},
            {name: 'ndenominacion_vehiculoid'},
            {name: 'ndenominacion_vehiculo'},
            {name: 'nunidadid'},
            {name: 'nunidad'},
            {name: 'nro_orden'},
            {name: 'odometro'},
            {name: 'persona_nombre'},
            {name: 'persona_id'},
            {name: 'ntipo_combustibleid'},
            {name: 'ntipo_combustible'},
            {name: 'matricula'},
            {name: 'norma'},
            {name: 'norma_far'},
            {name: 'norma_lubricante'},
            {name: 'norma_liquido_freno'},
            {name: 'nro_inventario'},
            {name: 'nro_serie_carreceria'},
            {name: 'nro_serie_motor'},
            {name: 'color'},
            {name: 'nro_circulacion'},
            {name: 'fecha_expiracion_circulacion'},
            {name: 'fecha_expiracion_circulacion_compare'},
            {name: 'anno_fabricacion'},
            {name: 'fecha_expiracion_somaton'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: ['nro_orden', 'ndenominacion_vehiculo'],
        groupField: 'ndenominacion_vehiculo',
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_vehiculo').getSelectionModel().deselectAll();
                operation.setParams({
                    nombre: find_button.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    tipoCombustible: tipo_combustible.getValue(),
                    tipoMedio: tipo_medio.getValue(),
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
                grid_vehiculo.enable();
                Ext.getCmp('id_grid_vehiculo').getStore().loadPage(1);
                if (Ext.getStore('id_store_persona_chofer'))
                    Ext.getStore('id_store_persona_chofer').load();

                if (Ext.getStore('id_store_to_traslate'))
                    Ext.getStore('id_store_to_traslate').load();

                if (Ext.getStore('id_store_list_traslate'))
                    Ext.getStore('id_store_list_traslate').load();

                if (Ext.getStore('id_store_area'))
                    Ext.getStore('id_store_area').load();

            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                panel_mantenimiento.collapse();
            }
        }
    });

    var store_mantenimiento = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_mantenimiento',
        fields: [
            {name: 'id'},
            {name: 'tipo_mantenimiento_id'},
            {name: 'tipo_mantenimiento'},
            {name: 'km'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/mantenimiento/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_mantenimiento').getSelectionModel().deselectAll();
                operation.setParams({
                    marcaid: Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected().data.nmarca_vehiculoid
                });
            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Medio...',
        width: 115,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_vehiculo').getStore().on({
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
                        Ext.getCmp('id_grid_vehiculo').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_vehiculo').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_vehiculo').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_vehiculo').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_vehiculo').getStore().loadPage(1);
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

    var grid_vehiculo = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_vehiculo',
        region: 'center',
        width: '75%',
        disabled: true,
        store: store_vehiculo,
        columns: [
            {
                text: '<strong>Matrícula o Denominación</strong>',
                dataIndex: 'matricula',
                filter: 'string',
                locked: true,
                align: 'center',
                width: 180
            },
            {
                text: '<strong>Empresa</strong>',
                dataIndex: 'empresa',
                filter: 'string',
                align: 'center',
                flex: 1
            },
            {
                text: '<strong>Unidad</strong>',
                dataIndex: 'nunidad',
                filter: 'string',
                align: 'center',
                flex: 1
            },
            {
                text: '<strong>Tipo de <br>combustible</strong>',
                dataIndex: 'ntipo_combustible',
                filter: 'string',
                align: 'center',
                width: 150
            },
            {
                text: '<strong>Norma</strong>',
                dataIndex: 'norma',
                hidden: true,
                filter: 'string',
                align: 'center',
                width: 90
            },
            {
                text: '<strong>Norma FAR</strong>',
                dataIndex: 'norma_far',
                filter: 'string',
                align: 'center',
                width: 90,
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('factor')) {
                        return val2 + ' / ' + record.get('factor');
                    } else {
                        return val2;
                    }

                }
            },
            {
                text: '<strong>No. <br>inventario</strong>',
                dataIndex: 'nro_inventario',
                filter: 'string',
                width: 100
            },
            {

                text: '<strong>No.Serie Motor</strong>',
                dataIndex: 'nro_serie_motor',
                filter: 'string',
                width: 120
            }, {

                text: '<strong>No.Serie Carrocería</strong>',
                dataIndex: 'nro_serie_carreceria',
                filter: 'string',
                width: 120
            }, {

                text: '<strong>Od&oacute;metro</strong>',
                dataIndex: 'odometro',
                filter: 'string',
                align: 'center',
                width: 80,
                renderer: function (value) {
                    if (value) {
                        return '<strong><span class="badge badge-pill badge-success">Si</span></strong>';
                    } else {
                        return '<strong><span class="badge badge-pill badge-danger">No</span></strong>';
                    }
                }
            }, {

                text: '',
                filter: 'string',
                align: 'center',
                width: 100,
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('embarcacion') === true) {
                        return '<strong><span class="badge badge-pill badge-dark">Embarcación</span></strong>';
                    } else if (record.get('equipoTecn') === true) {
                        return '<strong><span class="badge badge-pill badge-info">Tecnológico</span></strong>';
                    } else {
                        return '<strong><span class="badge badge-pill badge-success">Vehículo</span></strong>';
                    }

                },
            }, {

                text: '<strong>Nro. Orden</strong>',
                dataIndex: 'nro_orden',
                filter: 'string',
                align: 'center',
                width: 80
            }
        ],
        tbar: {
            id: 'vehiculo_tbar',
            height: 36,
            items: ['  ', find_button, tipo_combustible, tipo_medio, btnClearSearch, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_vehiculo'),
            displayInfo: true,
        },
        plugins: ['gridfilters', {
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
                '                   <th scope="col">Asignado A:</th>',
                '                   <th scope="col">Marca:</th>',
                '                   <th scope="col">No de Serie  del Motor:</th>',
                '                   <th scope="col">Color:</th>',
                '                   <th scope="col">Año de Fabricación:</th>',
                '               </tr>',
                '             </thead>',
                '             <tbody>',
                '               <tpl>',
                '                   <tr class="text-center">',
                '                       <td>{persona_nombre}</td>',
                '                       <td>{nmarca_vehiculo}</td>',
                '                       <td>{nro_serie_motor}</td>',
                '                       <td>{color}</td>',
                '                       <td>{anno_fabricacion}</td>',
                '                    </tr>',
                '                </tpl>',
                '              </tbody>',
                '           </tpl>',
                '       </table>',
                '   </div>',
                '</div>'
            )
        }],
        features: [
            {
                ftype: 'grouping',
                groupHeaderTpl: [
                    '<b>{name:this.formatName}</b>',
                    {
                        formatName: function (name) {
                            return Ext.String.trim(name);
                        }
                    }
                ]
            }
        ],
        viewConfig: {
            getRowClass: function (row, index) {
                var cls = '';
                var hoy = new Date();
                hoy = Ext.Date.format(hoy, 'm/d/Y');
                var fecha = row.get('fecha_expiracion_circulacion_compare');
                var fecha_hoy = new Date(hoy);
                var fecha_grid = new Date(fecha);
                if (fecha_grid < fecha_hoy) {
                    cls = 'Expirado';
                }
                return cls;
            }
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('vehiculo_btn_mod'))
                    Ext.getCmp('vehiculo_btn_mod').setDisabled(selected.length === 0);

                if (Ext.getCmp('vehiculo_btn_trasladar'))
                    Ext.getCmp('vehiculo_btn_trasladar').setDisabled(selected.length === 0);

                if (Ext.getCmp('vehiculo_btn_del'))
                    Ext.getCmp('vehiculo_btn_del').setDisabled(selected.length === 0);

                grid_mantenimiento.store.removeAll();
                grid_vehiculo_persona.store.removeAll();
                if (Ext.getCmp('mantenimiento_btn_add'))
                    Ext.getCmp('mantenimiento_btn_add').setDisabled(selected.length === 0);
                if (Ext.getCmp('vehiculo_persona_btn_add'))
                    Ext.getCmp('vehiculo_persona_btn_add').setDisabled(selected.length === 0);
                if (selected.length > 0) {
                    grid_mantenimiento.store.load({
                        params: {
                            vehiculoid: selected[0].data.id
                        }
                    });
                    grid_vehiculo_persona.store.load({
                        params: {
                            vehiculoid: selected[0].data.id
                        }
                    });
                    panel_mantenimiento.enable();
                    panel_mantenimiento.expand();
                }
                else {
                    panel_mantenimiento.disable();
                    panel_mantenimiento.collapse();
                }
            }
        }
    });

    var grid_mantenimiento = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_mantenimiento',
        title: 'Mantenimientos',
        flex: 1,
        region: 'north',
        store: store_mantenimiento,
        columns: [
            {
                text: '<strong>Tipo de <br>mantenimiento</strong>',
                dataIndex: 'tipo_mantenimiento',
                filter: 'string',
                flex: .7
            },
            {
                text: '<strong>Km / Horas</strong>',
                dataIndex: 'km',
                filter: 'string',
                flex: .3
            }
        ],
        tbar: {
            id: 'mantenimiento_tbar',
            height: 36,
            items: [{
                xtype: 'displayfield',
                width: 50
            }, '-', {
                xtype: 'displayfield',
                value: '<span style="color:cornflowerblue;font-weight:bold" data-qtip="Información"> Mantenimentos con KM / Horas asignados</span>',
                id: 'display_info'
            }, '-']
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

    var store_vehiculo_persona = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo_persona',
        fields: [
            {name: 'id'},
            {name: 'personaid'},
            {name: 'persona'},
            {name: 'choferid'},
            {name: 'nro_licencia'},
            {name: 'fecha_expiracion_licencia'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/asignacion/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_mantenimiento').getSelectionModel().deselectAll();
                operation.setParams({
                    vehiculoid: Ext.getCmp('id_grid_vehiculo').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    var grid_vehiculo_persona = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_vehiculo_persona',
        title: 'Asignar Chofer',
        width: '75%',
        region: 'center',
        flex: 1,
        store: store_vehiculo_persona,
        columns: [
            {
                text: '<strong>Nombre</strong>',
                dataIndex: 'persona',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'vehiculo_persona_tbar',
            height: 36,
            items: []
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('vehiculo_persona_btn_mod'))
                    Ext.getCmp('vehiculo_persona_btn_mod').setDisabled(selected.length === 0);
                if (Ext.getCmp('vehiculo_persona_btn_del'))
                    Ext.getCmp('vehiculo_persona_btn_del').setDisabled(selected.length === 0);
            }
        }
    });

    var panel_mantenimiento = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_mantenimiento',
        title: 'Otros Datos',
        region: 'east',
        width: '28%',
        collapsible: true,
        border: true,
        collapsed: true,
        height: '100%',
        layout: 'border',
        items: [grid_mantenimiento, grid_vehiculo_persona],
        listeners: {
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                panetree.collapse();
            }
        }
    });

    var panel_vehiculo = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_vehiculo',
        title: 'Técnica general',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_vehiculo, panel_mantenimiento]
    });

    App.render(panel_vehiculo);

});