/**
 * Created by Yosley on 20/05/2016.
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
        frame: true,
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
                grid_accidentes.enable();
                Ext.getCmp('id_grid_accidentes').getStore().loadPage(1);
                if (Ext.getStore('id_store_vehiculo_unidad'))
                    Ext.getStore('id_store_vehiculo_unidad').load();
                if (Ext.getStore('id_store_persona'))
                    Ext.getStore('id_store_persona').load();
            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Vehículo a buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_accidentes').getStore().on({
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
                        Ext.getCmp('id_grid_accidentes').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_accidentes').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_accidentes').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_accidentes').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_accidentes').getStore().loadPage(1);
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

    var store_accidentes = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_accidentes',
        fields: [
            {name: 'id'},
            {name: 'vehiculoid'},
            {name: 'vehiculo_marca'},
            {name: 'vehiculo_matricula'},
            {name: 'chofer'},
            {name: 'asignado'},
            {name: 'nota_informativa'},
            {name: 'fecha_accidente'},
            {name: 'fecha_indemnizacion'},
            {name: 'importe_indemnizacion'},
            {name: 'nunidadid'},
            {name: 'nunidadnombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/accidente/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_accidentes').getSelectionModel().deselectAll();
                operation.setParams({
                    matricula: find_button.getValue(),
                    nunidadid: (Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected() != undefined) ? Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id : null
                });
            },
            load: function (This, operation, eOpts) {
                if (Ext.getCmp('accidentes_btn_export'))
                    Ext.getCmp('accidentes_btn_export').setDisabled(This.getCount() == 0);
            },
        }
    });

    // var textSearch = Ext.create('Ext.form.field.Text', {
    //     width: 200,
    //     emptyText: 'Matrícula a buscar...',
    //     id: 'buscar_accidentes',
    //     enableKeyEvents: true,
    //     listeners: {
    //         keydown: function (This, e) {
    //             if (e.keyCode == 13) {
    //                 Ext.getCmp('id_grid_accidentes').getStore().currentPage = 1;
    //                 Ext.getCmp('id_grid_accidentes').getStore().load();
    //             }
    //         }
    //     }
    // });
    // var btnSearch = Ext.create('Ext.button.MyButton', {
    //     width: 30,
    //     height: 28,
    //     tooltip: 'Buscar',
    //     iconCls: 'fas fa-search text-primary',
    //     handler: function () {
    //         Ext.getCmp('id_grid_accidentes').getStore().currentPage = 1;
    //         Ext.getCmp('id_grid_accidentes').getStore().load();
    //     }
    // });
    // var btnClearSearch = Ext.create('Ext.button.MyButton', {
    //     width: 30,
    //     height: 28,
    //     tooltip: 'Limpiar',
    //     iconCls: 'fas fa-eraser text-primary',
    //     handler: function () {
    //         textSearch.reset();
    //         Ext.getCmp('id_grid_accidentes').getStore().currentPage = 1;
    //         Ext.getCmp('id_grid_accidentes').getStore().load();
    //     }
    // });


    var grid_accidentes = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_accidentes',
        store: store_accidentes,
        flex: 1,
        disabled: true,
        columns: [
            {text: '<strong>Tipo de vehiculo</strong>', dataIndex: 'vehiculo_marca', flex: 1},
            {text: '<strong>Matr&iacute;cula</strong>', dataIndex: 'vehiculo_matricula', flex: 1},
            {text: '<strong>Nombre del Chofer</strong>', dataIndex: 'chofer', flex: 1},
            {text: '<strong>Asignado a</strong>', dataIndex: 'asignado', flex: 1},
            {text: '<strong>Fecha accidente</strong>', dataIndex: 'fecha_accidente', flex: 1},
            // {text: '<strong>Nota informativa</strong>', dataIndex: 'nota_informativa', flex: 1},
            {text: '<strong>Fecha indemnizacion</strong>', dataIndex: 'fecha_indemnizacion', flex: 1},
            {text: '<strong>Importe indemnizacion</strong>', dataIndex: 'importe_indemnizacion', flex: 1},
        ],
        tbar: {
            id: 'accidentes_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_accidentes'),
            displayInfo: true,
        },
        plugins: ['gridfilters', {
            ptype: 'rowexpander',
            rowBodyTpl: new Ext.XTemplate(
                '<b>Nota Informativa:</b> <br><p>{nota_informativa}</p>')
        }],
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('accidentes_btn_mod'))
                    Ext.getCmp('accidentes_btn_mod').setDisabled(selected.length == 0);
                if (Ext.getCmp('accidentes_btn_del'))
                    Ext.getCmp('accidentes_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    // var store_unidades = Ext.create('Ex t.data.TreeStore', {
    //     fields: [
    //         {name: 'id'},
    //         {name: 'nombre'},
    //         {name: 'nombre_conocido'},
    //         {name: 'codigo_reeup'},
    //         {name: 'sentai'},
    //         {name: 'direccion'},
    //         {name: 'provincia_nombre'},
    //
    //
    //     ],
    //     proxy: {
    //         type: 'ajax',
    //         url: Routing.generate('loadlocalUnidad'),
    //         reader: {
    //             rootProperty: 'children',
    //         }
    //     },
    //     root: {
    //         text: 'root',
    //         expanded: true
    //     },
    //     autoLoad: false
    // });
    // var panetree = Ext.create('Ext.tree.Panel', {
    //     title: 'Unidades',
    //     store: store_unidades,
    //     id: 'arbolunidades',
    // hideHeaders: true,
    //     width: 280,
    //     // height: App.GetDesktopHeigth() - 75,
    //     rootVisible: false,
    //
    //     collapsible: true,
    //     // collapsed: false,
    //     collapseDirection: 'left',
    //     header: {             style: {                 backgroundColor: 'white',                 borderBottom: '1px solid #c1c1c1 !important'             },         },
    //     columns: [
    //         {xtype: 'treecolumn', iconCls: Ext.emptyString, width: 450, dataIndex: 'nombre'}
    //     ],
    //     root: {
    //         text: 'root',
    //         expanded: true
    //     },
    //     listeners: {
    //         rowdblclick: function (This, record, tr, rowIndex, e, eOpts) {
    //             console.log(record.id)
    //
    //
    //             if (record) {
    //                 Ext.getCmp('id_grid_accidentes').getStore().load();
    //
    //             }
    //
    //         }
    //     }
    // });
    var panel_accidentes = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_accidentes',
        title: 'Accidentes',
        frame: true,
        layout: {
            type: 'hbox',       // Arrange child items vertically
            align: 'stretch',    // Each takes up full width
            padding: 2
        },
        items: [panetree, grid_accidentes]
    });

    App.render(panel_accidentes);
});