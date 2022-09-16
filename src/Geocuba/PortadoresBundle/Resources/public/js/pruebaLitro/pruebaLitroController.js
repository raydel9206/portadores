/**
 * Created by Yosley on 5/11/15.
 */

Ext.onReady(function () {

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
            {name: 'provincia_nombre', type: -'string'},
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
        border:true,
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
                grid_pruebalitro.enable();
                Ext.getCmp('id_grid_pruebalitro').getStore().loadPage(1);
                if(Ext.getStore('id_store_vehiculo_unidad'))
                    Ext.getStore('id_store_vehiculo_unidad').load();
            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Vehículo a buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_pruebalitro').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value,
                                    nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
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
                        Ext.getCmp('id_grid_pruebalitro').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_pruebalitro').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_pruebalitro').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_pruebalitro').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_pruebalitro').getStore().loadPage(1);
                    }
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

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: 'Vehículo: {name} ' + ' ({rows.length})',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });

    var grid_pruebalitro = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_pruebalitro',
        flex: 1,
        region: 'center',
        width: '75%',
        disabled: true,
        store: Ext.create('Ext.data.JsonStore', {
            frame: true,
            storeId: 'id_store_pruebalitro',

            region: 'center',
            fields: [
                {name: 'id'},
                {name: 'fecha_prueba'},
                {name: 'responsable'},
                {name: 'indice'},
                {name: 'indice_far'},
                {name: 'nvehiculoid'},
                {name: 'nvehiculomatricula'}
            ],
            viewConfig: {forceFit: true},
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/prueba_litro/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            groupField: 'nvehiculomatricula',
            autoLoad: false,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    Ext.getCmp('id_grid_pruebalitro').getSelectionModel().deselectAll();
                    operation.setParams({
                        nombre: find_button.getValue(),
                        nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                    });
                }
            }
        }),
        columns: [
            {
                text: '<strong>Fecha de la Prueba</strong>',
                dataIndex: 'fecha_prueba',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Responsable</strong>',
                dataIndex: 'responsable',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Indice</strong>',
                dataIndex: 'indice',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Vehículo</strong>',
                dataIndex: 'nvehiculomatricula',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'pruebalitro_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_pruebalitro'),
            displayInfo: true,
        },
        features: [groupingFeature],
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('pruebalitro_btn_mod'))
                Ext.getCmp('pruebalitro_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('pruebalitro_btn_del'))
                Ext.getCmp('pruebalitro_btn_del').setDisabled(selected.length == 0);
            }
        }
    });


    var _panel_pruebalitro = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_pruebalitro',
        title: 'Pruebas de Litro',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_pruebalitro]
    });
    App.render(_panel_pruebalitro);
});
