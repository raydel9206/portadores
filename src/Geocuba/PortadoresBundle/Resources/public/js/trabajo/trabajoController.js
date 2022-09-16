/**
 * Created by yosley on 05/10/2015.
 */

Ext.onReady(function () {

    var store_trabajo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_trabajo',
        fields: [
            {name: 'id'},
            {name: 'codigo'},
            {name: 'nombre'},
            {name: 'fecha_ini'},
            {name: 'nunidadid'},
            {name: 'ncentrocosto'},
            {name: 'centro_costo'},
            {name: 'destinoid'},
            {name: 'destino'},
            {name: 'fecha_fin'}
        ],
        groupField: 'centro_costo',
        sorters: 'nunidadid',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/trabajo/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_trabajo').getSelectionModel().deselectAll();
                operation.setParams({
                    fecha: fecha.getRawValue(),
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

    var store_asignacion = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_asignacion',
        fields: [
            {name: 'id'},
            {name: 'trabajoid'},
            {name: 'tipo_combustibleid'},
            {name: 'tipo_combustible'},
            {name: 'monedaid'},
            {name: 'moneda'},
            {name: 'cantidad'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/trabajo/asignacion/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_asignacion').getSelectionModel().deselectAll();

                var selection = Ext.getCmp('id_grid_trabajo').getSelectionModel().getLastSelected();
                if (selection != undefined) {
                    operation.setParams({
                        id: selection.data.id
                    });
                }
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
                grid_trabajo.enable();
                Ext.getCmp('id_grid_trabajo').getStore().loadPage(1);
                if(Ext.getStore('id_store_centro_costo'))
                    Ext.getStore('id_store_centro_costo').load();
            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                grid_asignacion.collapse();
            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Trabajo a buscar...',
        width: 180,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_trabajo').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value,
                                    fecha: fecha.getRawValue(),
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
                        Ext.getCmp('id_grid_trabajo').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_trabajo').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_trabajo').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_trabajo').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_trabajo').getStore().loadPage(1);
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

    var fecha = Ext.create('Ext.form.field.Date', {
        width: 135,
        emptyText: 'Fecha a buscar...',
        selectOnFocus: true,
        editable: false,
        format: 'd/m/Y',
        listeners: {
            change: function (This, newValue) {
                if(Ext.getCmp('arbolunidades').getSelection().length>0)
                Ext.getCmp('id_grid_trabajo').getStore().load();
            }
        }
    });

    var btnClearSearch = Ext.create('Ext.button.MyButton', {
        width: 26,
        height: 25,
        tooltip: 'Limpiar Fecha',
        iconCls: 'fas fa-eraser text-primary',
        handler: function () {
            fecha.reset();
            Ext.getCmp('id_grid_trabajo').getStore().currentPage = 1;
            Ext.getCmp('id_grid_trabajo').getStore().load();
        }
    });
    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Centro de Costo: {name}' + '({rows.length})</b>',
        hideGroupedHeader: false,
        startCollapsed: false,
        ftype: 'grouping'
    });

    var grid_trabajo = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_trabajo',
        region: 'center',
        width: '35%',
        disabled:true,
        features: [groupingFeature],
        store: store_trabajo,
        columns: [
            {
                text: '<div style="text-align: center"><strong>C&oacute;digo</strong></div>',
                dataIndex: 'codigo',
                filter: 'string',
                flex: .2
            },
            {
                text: '<div style="text-align: center"><strong>Nombre</strong></div>',
                dataIndex: 'nombre',
                filter: 'string',
                flex: 1
            },
            {
                text: '<div style="text-align: center"><strong>Fecha inicio</strong></div>',
                dataIndex: 'fecha_ini',
                filter: 'string',
                flex: .3
            },
            {
                text: '<div style="text-align: center"><strong>Fecha fin</strong></div>',
                dataIndex: 'fecha_fin',
                filter: 'string',
                flex: .3
            }
        ],
        tbar: {
            id: 'trabajo_tbar',
            height: 36,
            items: [find_button, fecha, btnClearSearch, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_trabajo'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: {
                fn: function (View, selected, options) {
                    if (selected.length > 0){
                        grid_asignacion.enable();
                        // grid_asignacion.mask('Loading...')
                        grid_asignacion.getStore().load();
                    }
                    else
                        Ext.getCmp('id_grid_asignacion').getStore().removeAll();

                    panetree.setCollapsed(selected.length != 0);
                    grid_asignacion.setCollapsed(selected.length == 0);
                    if(Ext.getCmp('trabajo_btn_del'))
                    Ext.getCmp('trabajo_btn_del').setDisabled(selected.length == 0);
                    if(Ext.getCmp('trabajo_btn_mod'))
                    Ext.getCmp('trabajo_btn_mod').setDisabled(selected.length == 0);
                    if(Ext.getCmp('asignacion_btn_add'))
                    Ext.getCmp('asignacion_btn_add').setDisabled(selected.length == 0);
                },
                scope: this
            }
        }
    });
    var grid_asignacion = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_asignacion',
        store: store_asignacion,
        region: 'east',
        width: '30%',
        title: 'Asignación de combustible',
        border:true,
        collapsible: true,
        collapsed: true,
        // disabled:true,
        columns: [
            {
                text: '<div style="text-align: center"><strong>Tipo de <br>combustible</strong></div>',
                dataIndex: 'tipo_combustible',
                filter: 'string',
                flex: .8
            },
            {
                text: '<div style="text-align: center"><strong>Moneda</strong></div>',
                dataIndex: 'moneda',
                filter: 'string',
                flex: .4
            },
            {
                text: '<div style="text-align: center"><strong>Cantidad</strong></div>',
                dataIndex: 'cantidad',
                filter: 'string',
                flex: .4
            }
        ],
        tbar: {
            id: 'asignacion_tbar',
            height: 36
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (View, selected, options) {
                if(Ext.getCmp('asignacion_btn_mod'))
                Ext.getCmp('asignacion_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('asignacion_btn_del'))
                Ext.getCmp('asignacion_btn_del').setDisabled(selected.length == 0);
            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                panetree.collapse();
            }

        }
    });

    var panel_trabajo = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_trabajo',
        title: 'Trabajos/Proyectos',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_trabajo, grid_asignacion]
    });


    App.render(panel_trabajo);


});