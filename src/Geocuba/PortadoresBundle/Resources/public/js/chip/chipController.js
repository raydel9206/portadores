/**
 * Created by javier on 30/05/2016.
 */

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
                    while (!Ext.getCmp('id_grid_chip') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_chip'));
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
                grid_chips.getSelectionModel().deselectAll();
            }
        }
    });

    let store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nro_tarjeta'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadTarjetaAnticipoCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                    anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                });
            }
        }
    });

    let textSearch1 = Ext.create('Ext.form.ComboBox', {
        id: 'tarjeta_combo',
        width: 155,
        store: store_tarjeta,
        displayField: 'ntarjetaidnro',
        valueField: 'ntarjetaid',
        queryMode: 'local',
        emptyText: 'Tarjeta...',
    });

    let textSearch = Ext.create('Ext.form.field.Text', {
        width: 80,
        emptyText: 'No. Vale...',
        enableKeyEvents: true,
        listeners: {
            keydown: function (This, e) {
                if (e.keyCode == 13) {
                    grid_chips.getStore().loadPage(1);
                }
            }
        }
    });

    let btnSearch = Ext.create('Ext.button.MyButton', {
        width: 25,
        height: 25,
        iconCls: 'fas fa-search text-primary',
        handler: function () {
            Ext.getCmp('id_grid_chip').getStore().load();
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
            grid_chips.getStore().loadPage(1);

        }
    });

    let store_liquidaciones = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_liquidacion',
        fields: [
            {name: 'id'},
            {name: 'nvehiculoid'},
            {name: 'ntarjetaid'},
            {name: 'ntarjetaidnro'},
            {name: 'npersonaid'},
            {name: 'tipo_combustible_id'},
            {name: 'tipo_combustible'},
            {name: 'nactividadid'},
            {name: 'nservicentroid'},
            {name: 'nro_vale'},
            {name: 'importe'},
            {name: 'importe_inicial'},
            {name: 'importe_final'},
            {name: 'cant_litros'},
            {name: 'fecha_servicio'},
            {name: 'fecha_vale'},
            {name: 'hora_vale'},
            {name: 'fecha_registro'}

        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadLiquidaciones'),
            reader: {
                rootProperty: 'rows'
            }
        },
        groupField: 'ntarjetaidnro',
        pageSize: 25,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_chip').getSelectionModel().deselectAll();
                operation.setParams({
                    // tipo_combustibleid: tipo_combustible.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                    nro_vale: textSearch.getValue(),
                    tarjeta: textSearch1.getValue()
                });
            }
        }
    });

    let grid_chips = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_chip',
        region: 'center',
        width: '75%',
        disabled: true,
        store: store_liquidaciones,
        columns: [
            {
                text: '<strong>Fecha de Servicio</strong>',
                dataIndex: 'fecha_servicio',
                filter: 'string',
                flex: .4,
                align: 'center'
            },
            {
                text: '<strong>Fecha de Entrega</strong>',
                dataIndex: 'fecha_registro',
                filter: 'string',
                flex: .3,
                align: 'center'
            },
            {
                text: '<strong>Tipo Combustible</strong>',
                dataIndex: 'tipo_combustible',
                filter: 'string',
                flex: 0.4,
                align: 'center'
            },
            {
                text: '<strong>No.<br>del Chip</strong>',
                dataIndex: 'nro_vale',
                filter: 'string',
                flex: 0.2,
                align: 'center'
            },
            {text: '<strong>Matrícula</strong>', dataIndex: 'matricula', filter: 'string', flex: 0.2, align: 'center'},
            {
                text: '<strong>Nombre del Chofer</strong>',
                dataIndex: 'nombre_chofer',
                filter: 'string',
                flex: 0.5,
                align: 'center'
            },
        ],
        tbar: {
            id: 'chip_tbar',
            height: 36,
            items: [mes_anno, textSearch, textSearch1, btnSearch, btnClearSearch]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            store: Ext.getStore('id_store_liquidacion'),
            displayInfo: true
        },
        // plugins: ['gridfilters', {
        //     ptype: 'rowexpander',
        //     rowBodyTpl: new Ext.XTemplate(
        //         '<p><b>No. Tarjeta:</b> {ntarjetaidnro}</p>',
        //     )
        // }],
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
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('chip_btn_mod'))
                    Ext.getCmp('chip_btn_mod').setDisabled(selected.length == 0 || !selected[0].data.abierto);
                if (Ext.getCmp('chip_btn_del'))
                    Ext.getCmp('chip_btn_del').setDisabled(selected.length == 0 || !selected[0].data.abierto);

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
            {xtype: 'treecolumn', iconCls: Ext.emptyString, width: 400, dataIndex: 'nombre'}
        ],
        root: {
            text: 'root',
            expanded: true
        },
        listeners: {
            select: function (This, record, index, eOpts) {
                if (record) {
                    store_tarjeta.load();
                    if (Ext.getStore('id_store_tarjeta_imprimir'))
                        Ext.getStore('id_store_tarjeta_imprimir').load();


                    Ext.getCmp('id_grid_chip').getStore().load();
                    grid_chips.setDisabled(false);
                }
            }
        }
    });

    let _panel = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_anticipo',
        title: 'Chips',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_chips],
    });
    App.render(_panel);
});

