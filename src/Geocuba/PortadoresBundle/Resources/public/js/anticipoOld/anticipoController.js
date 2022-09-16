/**
 * Created by javier on 30/05/2016.
 */

Ext.onReady(function () {

    let mes = null;

    let mes_anno = Ext.create('Ext.form.field.Month', {
        format: 'm, Y',
        id: 'mes_anno',
        // fieldLabel: 'Date',
        width: 90,
        value: new Date(App.selected_month + '/1/' + App.selected_year),
        renderTo: Ext.getBody(),
        listeners: {
            boxready: function () {
                let me = this;
                me.selectMonth = new Date(App.selected_month + '/1/' + App.selected_year);

                let assignGridPromise = new Promise((resolve, reject) => {
                    let i = 0;
                    while(!Ext.getCmp('id_grid_anticipo') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_anticipo'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
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

    let textSearch = Ext.create('Ext.form.field.Text', {
        width: 100,
        emptyText: 'No. Vale...',
        enableKeyEvents: true,
        listeners: {
            keydown: function (This, e) {
                if (e.keyCode == 13) {
                    Ext.getCmp('id_grid_anticipo').getStore().currentPage = 1;
                    Ext.getCmp('id_grid_anticipo').getStore().load();
                }
            }
        }
    });
    let textSearch1 = Ext.create('Ext.form.field.Text', {
        width: 100,
        emptyText: 'Vehículo...',
        enableKeyEvents: true,
        listeners: {
            keydown: function (This, e) {
                if (e.keyCode == 13) {
                    Ext.getCmp('id_grid_anticipo').getStore().currentPage = 1;
                    Ext.getCmp('id_grid_anticipo').getStore().load();
                }
            }
        }
    });
    let textSearch2 = Ext.create('Ext.form.field.Text', {
        width: 100,
        emptyText: 'Tarjeta...',
        maskRe: /^[0-9]/,
        enableKeyEvents: true,
        listeners: {
            keydown: function (This, e) {
                if (e.keyCode == 13) {
                    Ext.getCmp('id_grid_anticipo').getStore().currentPage = 1;
                    Ext.getCmp('id_grid_anticipo').getStore().load();
                }
            }
        }
    });
    let cmbSearchStore = Ext.create('Ext.data.Store', {
        fields: ['abbr', 'name'],
        data: [
            {"id": "-1", "name": "Todos"},
            {"id": "0", "name": "Cerrados"},
            {"id": "1", "name": "Abiertos"}
        ]
    });

// Create the combo box, attached to the states data store
    let cmbSearch = Ext.create('Ext.form.ComboBox', {
        width: 100,
        emptyText: 'Estado...',
        store: cmbSearchStore,
        queryMode: 'local',
        displayField: 'name',
        valueField: 'id',
        value: 1,
        listeners: {
            select: function () {
                Ext.getCmp('id_grid_anticipo').getStore().currentPage = 1;
                Ext.getCmp('id_grid_anticipo').getStore().load();
            }
        }
    });
    let btnSearch = Ext.create('Ext.button.MyButton', {
        width: 25,
        height: 25,
        iconCls: 'fas fa-search text-primary',
        handler: function () {
            Ext.getCmp('id_grid_anticipo').getStore().currentPage = 1;
            Ext.getCmp('id_grid_anticipo').getStore().load();
        }
    });
    let btnClearSearch = Ext.create('Ext.button.MyButton', {
        width: 25,
        height: 25,
        iconCls: 'fas fa-eraser text-primary',
        handler: function () {
            textSearch.reset();
            textSearch1.reset();
            mes_anno.reset();
            cmbSearch.reset();
            Ext.getCmp('id_grid_anticipo').getStore().loadPage(1);
        }
    });

    let store_anticipo = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_anticipo',
        fields: [
            {name: 'id'},
            {name: 'fecha'},
            {name: 'hora'},
            {name: 'fecha_anticipo'},
            {name: 'no_vale'},
            {name: 'choferid'},
            {name: 'chofer'},
            {name: 'vehiculoid'},
            {name: 'vehiculo'},
            {name: 'tarjetaid'},
            {name: 'tarjeta'},
            {name: 'importe'},
            {name: 'cantidad'},
            {name: 'abierto'},
            {name: 'trabajoid'},
            {name: 'trabajo'},
            {name: 'transito'},
            {name: 'terceros'},
            {name: 'transito_plugin'},
            {name: 'excepcional_plugin'},
            {name: 'terceros_plugin'},
            {name: 'tipo_combustible_id'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadAnticipo'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                // if (Ext.getCmp('id_grid_anticipo'))
                //     Ext.getCmp('id_grid_anticipo').getSelectionModel().deselectAll();
                operation.setParams({
                    unidadid: panetree.getSelectionModel().getLastSelected().data.id,
                    vale: textSearch.getValue(),
                    vehiculo: textSearch1.getValue(),
                    estado: cmbSearch.getValue(),
                    mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                    anno: Ext.getCmp('mes_anno').getValue().getFullYear()
                });
            }
        }
    });

    let grid = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_anticipo',
        disabled: true,
        store: store_anticipo,
        region: 'center',
        columns: [
            {
                xtype: 'gridcolumn',
                dataIndex: 'no_vale',
                flex: .8,
                text: '<b>No.Vale</b>'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'fecha_anticipo',
                flex: .8,
                text: '<b>Fecha</b>'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'vehiculo',
                flex: .6,
                text: '<b>Vehículo</b>'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'tarjeta',
                flex: .8,
                text: '<b>Tarjeta</b>'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'importe_tarjeta',
                flex: .5,
                text: '<b>Imp. Tarjeta</b>',
                align: 'right'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'importe',
                flex: .5,
                text: '<b>Importe</b>',
                align: 'right'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'cantidad',
                flex: .5,
                text: '<b>Cantidad</b>',
                align: 'right'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'transito_plugin',
                flex: .5,
                text: '<b>Tránsito</b>',
                align: 'center',
                renderer: function (val,met) {

                    if (val == 'SI') {
                        return '<span class="badge-true">SI</span>';
                    } else {
                        return '<span class="badge-false">NO</span>';
                    }
                }
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'abierto',
                flex: .5,
                text: '<b>Cerrado</b>',
                align: 'center',
                renderer: function (val) {
                    if (!val) {
                        return '<span class="badge-true">SI</span>';
                    } else {
                        return '<span class="badge-false">NO</span>';
                    }
                }
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'excepcional_plugin',
                flex: .5,
                hidden: true,
                text: '<b>Excepcional</b>',
                align: 'center',
                renderer: function (val) {
                    if (val == 'SI') {
                        return '<span class="badge-true">SI</span>';
                    } else {
                        return '<span class="badge-false">NO</span>';
                    }
                }
            },
        ],
        plugins: ['gridfilters', {
            ptype: 'rowexpander',
            rowBodyTpl: new Ext.XTemplate(
                '<p><b>Chofer:</b> {nombrepersonaid}</p>',
                '<p><b>Trabajo:</b> {trabajo}</p>',
                '<p><b>Transito:</b> {transito_plugin}</p>',
                '<p><b>Terceros:</b> {terceros_plugin}</p>',
                '<p><b>Excepcional:</b> {excepcional_plugin}</p>',
                '<p><b>Motivo:</b> {motivo}</p>'
            )
        }],
        tbar: {
            id: 'anticipo_tbar',
            height: 36,
            items: [mes_anno, textSearch, textSearch1, /*textSearch2,*/ cmbSearch, btnSearch, btnClearSearch, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('store_anticipo'),
            displayInfo: true,
            // plugins: new Ext.ux.ProgressBarPager()
        },
        enableLocking: true,
        listeners: {
            selectionchange: function (This, selections, options) {
                if (selections.length > 0) {
                    if (Ext.getCmp('anticipo_btn_mod') != undefined)
                        Ext.getCmp('anticipo_btn_mod').setDisabled(!selections[0].data.abierto);
                    if (Ext.getCmp('anticipo_btn_del') != undefined)
                        Ext.getCmp('anticipo_btn_del').setDisabled(!selections[0].data.abierto);
                    if (Ext.getCmp('anticipo_cerrar_btn') != undefined)
                        Ext.getCmp('anticipo_cerrar_btn').setDisabled(!selections[0].data.abierto);
//                    if (Ext.getCmp('anticipo_liquidaciones_btn') != undefined)
//                        Ext.getCmp('anticipo_liquidaciones_btn').setDisabled(!selections[0].data.abierto);
                    if (Ext.getCmp('anticipo_print_btn') != undefined)
                        Ext.getCmp('anticipo_print_btn').setDisabled(selections[0].data.abierto);

                    if (Ext.getCmp('liquidacion_btn_add') != undefined)
                        Ext.getCmp('liquidacion_btn_add').setDisabled(!selections[0].data.abierto);

                    // Ext.getStore('id_store_persona_liquidacion').load();
                    grid_liquidaciones.getStore().load();
                    panel_liquidaciones.expand();

                } else {
                    if (Ext.getCmp('anticipo_btn_mod') != undefined)
                        Ext.getCmp('anticipo_btn_mod').setDisabled(selections.length == 0);
                    if (Ext.getCmp('anticipo_btn_del') != undefined)
                        Ext.getCmp('anticipo_btn_del').setDisabled(selections.length == 0);
                    if (Ext.getCmp('anticipo_cerrar_btn') != undefined)
                        Ext.getCmp('anticipo_cerrar_btn').setDisabled(selections.length == 0);
//                    if (Ext.getCmp('anticipo_liquidaciones_btn') != undefined)
//                        Ext.getCmp('anticipo_liquidaciones_btn').setDisabled(selections.length == 0);
                    if (Ext.getCmp('anticipo_print_btn') != undefined)
                        Ext.getCmp('anticipo_print_btn').setDisabled(selections.length == 0);

                    if (Ext.getCmp('liquidacion_btn_add') != undefined)
                        Ext.getCmp('liquidacion_btn_add').setDisabled(selections.length == 0);
                    grid_liquidaciones.getStore().removeAll();
                    panel_liquidaciones.collapse();
                }
            },
            // enable: function( This, eOpts ){
            //     if(Ext.getCmp('anticipo_print_btn'))
            //         Ext.getCmp('anticipo_print_btn').setDisabled(true);
            //     if(Ext.getCmp('anticipo_cerrar_btn'))
            //         Ext.getCmp('anticipo_cerrar_btn').setDisabled(true);
            //     // grid.getView().refresh();
            //
            // }
        }
    });

    let store_liquidaciones = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_liquidacion',
        fields: [
            {name: 'id'},
            {name: 'nvehiculoid'},
            {name: 'ntarjetaid'},
            {name: 'npersonaid'},
            {name: 'nsubactividadid'},
            {name: 'ntarjetaidnro'},
            {name: 'nservicentroid'},
            {name: 'nro_vale'},
            {name: 'importe'},
            {name: 'importe_inicial'},
            {name: 'importe_final'},
            {name: 'cant_litros'},
            {name: 'fecha_vale'}
        ],
        groupField: 'ntarjetaidnro',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadAnticipoLiquidaciones'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_liquidacion').getSelectionModel().deselectAll();
                let selected = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                if (selected != undefined) {
                    operation.setParams({
                        id: selected.data.id
                    });
                }
            }
        }
    });

    let grid_liquidaciones = Ext.create('Ext.grid.Panel', {
        xtype: 'gridpanel',
        id: 'id_grid_liquidacion',
        region: 'west',
        width: '25%',
        border: true,
        store: store_liquidaciones,
        viewConfig: {
            getRowClass: function(record, rowIndex, rowParams, store) {
                if (!record.get('historial')) return 'row-error';
            }
        },
        columns: [
            {text: '<strong>No. Vale</strong>', dataIndex: 'nro_vale', filter: 'string', flex: 0.5},
            {text: '<strong>Imp.</strong>', dataIndex: 'importe', filter: 'string', flex: 0.6},
            {
                text: '<strong>Cant. Litros</strong>',
                dataIndex: 'cant_litros',
                filter: 'string',
                flex: 0.6,
                align: 'center'
            },
            {text: '<strong>Fecha</strong>', dataIndex: 'fecha_vale', filter: 'string', flex: 1},
            {text: '<strong>Imp. Final</strong>', dataIndex: 'importe_final', filter: 'string', flex: 1},
        ],
        tbar: {
            id: 'anticipo_liquidacion_tbar',
            height: 36,
            items: []
        },
        plugins: 'gridfilters',
    });

    let panel_liquidaciones = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_liquidaciones',
        title: 'Liquidaciones',
        region: 'east',
        collapsible: true,
        collapsed: true,
        width: '40%',
        height: '96%',
        layout: 'fit',
        items: [grid_liquidaciones],
        listeners: {
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                panetree.collapse();
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
        // reserveScrollbar: true,
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
            select: function (This, record, index, eOpts) {
                grid.setDisabled(false);
                store_anticipo.load();
                if (record) {
                    if (Ext.getStore('id_store_vehiculo_anticipo')) {
                        Ext.getStore('id_store_vehiculo_anticipo').load();
                    }
                    if (Ext.getStore('id_store_persona')) {
                        Ext.getStore('id_store_persona').load();
                    }

                    // Ext.getStore('id_store_vehiculo_anticipo').load({params: {unidadid: record.id}});
                    // Ext.getStore('id_store_persona_anticipo').load({params: {unidadid: record.id}});
                    if (Ext.getStore('id_store_centro_costo'))
                        Ext.getStore('id_store_centro_costo').load();

                }
            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                panel_liquidaciones.collapse();
            }
        }


    });

    let _panel = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_anticipo',
        title: 'Anticipos',
        // border: true,
        frame: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid, panel_liquidaciones],
    });
    App.render(_panel);

    function CierreMes(pmes, btn) {
        mes = pmes;
        let _bbar = Ext.getCmp('anticipo_panel_bbar');
        _bbar.items.each(function (element) {
            element.setStyle({
                background: '#F5F5F5'
            });
        });

        btn.setStyle({
            background: '#C1DDF1'
        });

        Ext.getCmp('id_grid_anticipo').getStore().load({
            params: {
                mes: pmes,
                unidad: Ext.getCmp('arbolunidades').getSelection().data.id
            }
        });


    }

});

