Ext.onReady(function () {

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
                    while (!Ext.getCmp('id_grid_anticipo') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_anticipo'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let textSearch = Ext.create('Ext.form.field.Text', {
        width: 100,
        emptyText: 'No. Vale...',
        enableKeyEvents: true,
        listeners: {
            keydown: function (This, e) {
                if (e.keyCode === 13) {
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
                if (e.keyCode === 13) {
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
                if (e.keyCode === 13) {
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

    let cmbSearch = Ext.create('Ext.form.ComboBox', {
        width: 100,
        emptyText: 'Estado...',
        store: cmbSearchStore,
        queryMode: 'local',
        displayField: 'name',
        valueField: 'id',
        value: -1,
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

    let btn_abrir = Ext.create('Ext.button.MyButton', {
        width: 60,
        id: 'btn_abrir',
        height: 25,
        text: 'Abrir',
        disabled: true,
        glyph: 0xf3c1,
        handler: function () {
            Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Esta seguro que desea abrir el anticipo: ' + Ext.getCmp('id_grid_anticipo').getSelection()[0].data.no_vale + ' ?', function (btn) {
                if (btn === 'yes') {
                    let obj = {};
                    obj.anticipo = Ext.getCmp('id_grid_anticipo').getSelection()[0].data.id;
                    App.request('POST', App.buildURL('/soporte/anticipoEventos/abrir'), obj, null, null,
                        function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_anticipo').getStore().load();
                                btn_abrir.setDisabled(true);
                            }
                        }
                    );
                }
            });
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

    let store_anticipo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_anticipo',
        fields: [
            {name: 'id'},
            {name: 'ntipo_combustibleid'},
            {name: 'nombretipo_combustibleid'},
            {name: 'nmonedaid'}
        ],

        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadAnticipo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                if (Ext.getCmp('id_grid_anticipo'))
                    Ext.getCmp('id_grid_anticipo').getSelectionModel().deselectAll();
                operation.setParams({
                    vale: textSearch.getValue(),
                    vehiculo: textSearch1.getValue(),
                    estado: cmbSearch.getValue(),
                    tarjeta: textSearch2.getValue(),
                    mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                    anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    let panetree = Ext.create('Ext.tree.Panel', {
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
                grid.enable();
                store_anticipo.load();
            }
        }


    });


    let grid = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_anticipo',
        region: 'center',
        width: '100%',
        height: '100%',
        disabled: true,
        store: store_anticipo,
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
                renderer: function (val, met) {

                    if (val === 'SI') {
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
                    if (val === 'SI') {
                        return '<span class="badge-true">SI</span>';
                    } else {
                        return '<span class="badge-false">NO</span>';
                    }
                }
            },
        ],
        tbar: {
            id: 'anticipo_post_tbar',
            height: 36,
            items: [mes_anno, textSearch, textSearch1, textSearch2, cmbSearch, btnSearch, btnClearSearch, '-', btn_abrir]
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (selected.length !== 0) {
                    btn_abrir.setDisabled(false);
                }
            }
        }
    });

    let panel_anticipo_evento = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_anticipo_evento',
        title: 'Eventos anticipo',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });

    App.render(panel_anticipo_evento);
});