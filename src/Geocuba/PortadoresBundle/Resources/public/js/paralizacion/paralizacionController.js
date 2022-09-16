Ext.onReady(function () {

    var store_paralizacion = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_paralizacion',
        fields: [
            {name: 'id'},
            {name: 'motivo'},
            {name: 'en_sasa'},
            {name: 'nro_pedido'},
            {name: 'vehiculo_id'},
            {name: 'matricula'},
            {name: 'tipo_combustible'},
            {name: 'modelo'},
            {name: 'modeloid'},
            {name: 'marca'},
            {name: 'nunidadid'},
            {name: 'nunidadnombre'},
            {name: 'denominacion'},
            {name: 'fecha'}
        ],
        groupField: 'denominacion',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/paralizacion/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_paralizacion').getSelectionModel().deselectAll();
                if (Ext.getCmp('paralizacion_btn_export'))
                    Ext.getCmp('paralizacion_btn_export').setDisabled(true);
                operation.setParams({
                    matricula: find_button.getValue(),
                    nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            },
            load: function (This, eOpts) {
                if (Ext.getCmp('paralizacion_btn_export'))
                    Ext.getCmp('paralizacion_btn_export').setDisabled(false);
            }

        }
    });
    var store_cdt = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_cdt',
        fields: [
            {name: 'denominacionid'},
            {name: 'denominacion_nombre'},
            {name: 'total'},
            {name: 'paralizados'},
            {name: 'causas'},
            {name: 'cdt'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/paralizacion/cdt/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_paralizacion').getSelectionModel().deselectAll();
                if (Ext.getCmp('paralizacion_btn_print'))
                    Ext.getCmp('paralizacion_btn_print').setDisabled(true);

                operation.setParams({
                    nunidadid: (Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id !== undefined) ? Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id : null
                });
            },
            load: function (This, eOpts) {
                if (Ext.getCmp('paralizacion_btn_print'))
                    Ext.getCmp('paralizacion_btn_print').setDisabled(false);
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
        rootVisible: false,
        border: true,
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
                store_paralizacion.loadPage(1);
                panetree.collapse();
                store_cdt.load();
                grid_cdt.expand();
                grid_paralizacion.focus();
                grid_paralizacion.setDisabled(false);
                grid_cdt.setDisabled(false);

                if (Ext.getStore('id_store_vehiculo'))
                    Ext.getStore('id_store_vehiculo').load();
                if (Ext.getStore('id_store_persona'))
                    Ext.getStore('id_store_persona').load();
            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                grid_paralizacion.focus();
                grid_cdt.collapse();
            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Matricula...',
        width: '20%',
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_paralizacion').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    matricula: value,
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
                        Ext.getCmp('id_grid_paralizacion').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_paralizacion').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_paralizacion').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_paralizacion').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_paralizacion').getStore().loadPage(1);
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

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Denominación: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });
    var grid_paralizacion = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_paralizacion',
        features: [groupingFeature],
        region: 'center',
        width: '40%',
        flex: 1,
        border: false,
        disabled: true,
        store: store_paralizacion,
        columns: [
            {text: '<strong>Fecha</strong>', dataIndex: 'fecha', align: 'center',  flex:1},
            {
                text: '<strong>Fecha Marcha</strong>', dataIndex: 'fecha_marcha', align: 'center', flex:1,
                renderer: function (value) {
                    if (!value)
                        return ' ----------- ';
                    return value
                }
            },
            {text: '<strong>Matr&iacute;cula</strong>', dataIndex: 'matricula', align: 'center', flex:1},
            {text: '<strong>Tipo Comb.</strong>', dataIndex: 'tipo_combustible', align: 'center',  flex:1},
            {text: '<strong>Marca/Modelo</strong>', dataIndex: 'marca', align: 'center',  flex:1},
            {
                text: '<strong>En SASA</strong>', dataIndex: 'en_sasa', align: 'center',  flex:1,
                renderer: function (value) {
                    if (value)
                        return "<div class='badge-true'>Si</div>";
                    return "<div class='badge-false'>No</div>";
                }
            },
            {
                text: '<strong>No. Pedido</strong>', dataIndex: 'nro_pedido', align: 'center',  flex:1,
                renderer: function (value) {
                    if (value === '')
                        return ' ----------- ';
                    return value
                }
            }
        ],
        tbar: {
            id: 'paralizacion_tbar',
            height: 36,
            items: [find_button, '-']
        },
        // bbar: {
        //     xtype: 'pagingtoolbar',
        //     pageSize: 25,
        //     store: Ext.getStore('id_store_paralizacion'),
        //     displayInfo: true,
        // },
        plugins: ['gridfilters', {
            ptype: 'rowexpander',
            rowBodyTpl: new Ext.XTemplate(
                '<p><b>Motivo: </b>{motivo}<br></p>',
            )
        }],
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('paralizacion_btn_mod'))
                    Ext.getCmp('paralizacion_btn_mod').setDisabled(selected.length === 0);
                if (Ext.getCmp('paralizacion_btn_del'))
                    Ext.getCmp('paralizacion_btn_del').setDisabled(selected.length === 0);
                if (Ext.getCmp('paralizacion_btn_marcha'))
                    Ext.getCmp('paralizacion_btn_marcha').setDisabled(selected.length === 0);
            }
        }
    });
    var grid_cdt = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_cdt',
        region: 'east',
        width: '35%',
        collapsible: true,
        collapsed: true,
        columnLines: true,
        disabled: true,
        border: true,
        title: 'Coeficiente de Disposición Técnica',
        store: store_cdt,
        columns: [
            {
                text: '<strong>Denominación</strong>', dataIndex: 'denominacion_nombre', align: 'center', width: 120,
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>Total</strong>');
                }
            },
            {
                text: '<strong>Total</strong>', dataIndex: 'total', align: 'center', width: 100,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                }
            },
            {
                text: '<strong>Paralizados</strong>', dataIndex: 'paralizados', align: 'center', width: 100,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                }
            },
            {
                text: '<strong>CDT</strong>', dataIndex: 'cdt', align: 'center', width: 100,
                summaryType: 'average',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                }
            }
        ],
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        }],
        tbar: {
            id: 'cdt_tbar',
            height: 36,

        },
        listeners: {
            rowdblclick: function (This, record, tr, rowIndex, e, eOpts) {
                if (record) {
                    var obj = {};
                    obj.unidad = record.id;
                }
            },

            expand: function (This, record, tr, rowIndex, e, eOpts) {
                grid_paralizacion.focus();
                panetree.collapse();
                // store_cdt.load();
            }
        }


    });

    var panel_paralizacion = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_paralizacion',
        title: 'Paralizaciones',
        border: false,
        frame: true,
        layout: 'border',
        padding: '2 0 0',
        // tools:[
        //     {
        //         type: 'help',
        //         // callback: onHelp(),
        //         tooltip: 'Ayuda',
        //         handler:function () {
        //             onHelp();
        //         },
        //         scope: this
        //
        //     },{
        //         type: 'gear',
        //         // callback: onHelp(),
        //         tooltip: 'Video Ayuda',
        //         handler:function () {
        //             onVideoHelp();
        //         },
        //         scope: this
        //
        //     }
        //
        //
        //
        // ],
        items: [panetree, grid_cdt, grid_paralizacion]
    });

    App.render(panel_paralizacion);
});


function onVideoHelp() {

    Ext.create('Ext.window.Window', {
        title: 'Ayuda',
        height: 630,
        width: 1010,
        //  layout: 'fit',
        html: ' <video src="../../web/videos/gestionvehiculo.mp4" controls autoplay height="600px" width="1000px"  >HTML5 Video is required for this example</video>',
    }).show();
    console.log('video')


}

function onHelp() {

    window.open(Vehiculo);
}