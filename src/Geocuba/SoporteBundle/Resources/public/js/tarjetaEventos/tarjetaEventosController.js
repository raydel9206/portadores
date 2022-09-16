Ext.onReady(function () {

    let textSearch = Ext.create('Ext.form.field.Text', {
        width: 120,
        emptyText: 'No. Tarjeta...',
        id: 'buscar_tarjeta',
        maskRe: /[0-9]/,
        enableKeyEvents: true,
        listeners: {
            keyup: function (This, e, eOpts) {
                grid.getStore().filterBy(function (record) {
                    return record.data.nro_tarjeta.search(This.value) !== -1;
                }, this);
            },
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

    let store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta',
        fields: [
            {name: 'id'},
            // {name: 'ncajaid'},
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
            {name: 'importel'},
            {name: 'fecha_registro'},
            {name: 'fecha_vencimieno'},
            {name: 'fecha_baja'},
            {name: 'causa_baja'},
            {name: 'reserva'},
            {name: 'exepcional'},
            {name: 'estado'}
        ],
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
                    numero: textSearch.getValue(),
                    nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
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
                store_tarjeta.load();
            }
        }


    });

    let _btn_historial = Ext.create('Ext.button.MyButton', {
        id: '_btn_historial',
        text: 'Ver Historial',
        iconCls: 'fas fa-search text-primary',
        disabled: true,
        handler: function () {
            let mes_anno = Ext.create('Ext.form.field.Month', {
                format: 'm, Y',
                id: 'mes_anno',
                width: 90,
                value: new Date(App.selected_month + '/1/' + App.selected_year),
                renderTo: Ext.getBody(),
                listeners: {
                    boxready: function () {
                        let me = this;
                        me.selectMonth = new Date(App.selected_month + '/1/' + App.selected_year);

                        let assignGridPromise = new Promise((resolve, reject) => {
                            let i = 0;
                            while (!Ext.getCmp('gridHistorial') && i < 5) {
                                setTimeout(() => {
                                    i++;
                                }, 1000);
                            }
                            resolve(Ext.getCmp('gridHistorial'));
                        });
                        assignGridPromise.then((grid) => {
                            me.grid = grid;
                        });
                    }
                }
            });
            let _btn_limpiar = Ext.create('Ext.button.MyButton', {
                id: '_btn_limpiar',
                text: 'Limpiar Historial',
                glyph: 0xf2ed,
                disabled: true,
                handler: function () {
                    Ext.Msg.show({
                        title: '¿Corregir el historial?',
                        message: Ext.String.format('¿Está seguro que desea eliminar el historial de la tarjeta <span class="font-italic font-weight-bold">{0}</span> posterior a la fecha del evento seleccionado ?', Ext.getCmp('id_grid_tarjetas_hist').getSelection()[0].data.nro_tarjeta),
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                let obj = {};
                                obj.tarjeta = Ext.getCmp('id_grid_tarjetas_hist').getSelection()[0].data.id;
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelection()[0].data.id;
                                obj.date = Ext.getCmp('gridHistorial').getSelection()[0].data.date;
                                obj.hour = Ext.getCmp('gridHistorial').getSelection()[0].data.hour;
                                App.request('POST', App.buildURL('/soporte/tarjetaEventos/clean'), obj, null, null,
                                    function (response) {
                                        if (response && response.hasOwnProperty('success') && response.success) {
                                            Ext.getCmp('gridHistorial').getStore().reload();
                                        }
                                    });
                            }
                        }
                    });
                }
            });

            Ext.create('Ext.window.Window', {
                id: 'windowTarjetasHistorial',
                width: 800,
                height: 500,
                closable: true,
                scrollable: 'vertical',
                autoHeight: true,
                modal: true,
                draggable: true,
                resizable: true,
                title: Ext.String.format('Historial de la tarjeta <span class="font-italic">{0}</span>', Ext.getCmp('id_grid_tarjetas_hist').getSelection()[0].data.nro_tarjeta),
                tbar: [mes_anno, _btn_limpiar],
                items: [
                    {
                        xtype: 'gridpanel',
                        id: 'gridHistorial',
                        frame: true,
                        margin: '5 5 5 5',
                        store: Ext.create('Ext.data.JsonStore', {
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
                                {name: 'importel'},
                                {name: 'fecha_registro'},
                                {name: 'fecha_vencimieno'},
                                {name: 'fecha_baja'},
                                {name: 'causa_baja'},
                                {name: 'reserva'},
                                {name: 'exepcional'},
                                {name: 'estado'}
                            ],
                            proxy: {
                                type: 'ajax',
                                url: App.buildURL('/soporte/tarjetaEventos/loadHistorial'),
                                reader: {
                                    rootProperty: 'rows'
                                }
                            },
                            autoLoad: false,
                            listeners: {
                                beforeload: function (store, operation, eOpts) {
                                    let tarjeta = Ext.getCmp('id_grid_tarjetas_hist').getSelection()[0].data.id;
                                    if (!tarjeta) {
                                        App.showAlert("Escoja la tarjeta", 'info');
                                        return false;
                                    }
                                    operation.setParams({
                                        mes: mes_anno.getValue().getMonth() + 1,
                                        anno: mes_anno.getValue().getFullYear(),
                                        tarjeta: tarjeta,
                                    });
                                }
                            },
                            sorters: [{
                                property: 'fecha',
                                direction: 'ASC'
                            }],
                        }),
                        columns: [
                            {
                                text: '<strong>Fecha</strong>',
                                dataIndex: 'fecha',
                            },{
                                text: '<strong>FechaObj</strong>',
                                dataIndex: 'fechaObj',
                                hidden: true,
                            }, {
                                text: '<strong>Entrada:</strong>',
                                id: 'importe',
                                columns: [
                                    {
                                        text: '<strong>Importe</strong>',
                                        dataIndex: 'imp_entrada',
                                        filter: 'string',
                                        align: 'right',
                                        flex: 0.3
                                    },
                                    {
                                        text: '<strong>Cantidad</strong>',
                                        dataIndex: 'cant_entrada',
                                        filter: 'string',
                                        align: 'right',
                                        flex: 0.3
                                    }
                                ]

                            }, {
                                text: '<strong>Salida:</strong>',
                                id: 'salida',
                                columns: [
                                    {
                                        text: '<strong>Importe</strong>',
                                        dataIndex: 'imp_salida',
                                        filter: 'string',
                                        align: 'right',
                                        flex: 0.3
                                    },
                                    {
                                        text: '<strong>Cantidad</strong>',
                                        dataIndex: 'cant_salida',
                                        filter: 'string',
                                        align: 'right',
                                        flex: 0.3
                                    }
                                ]

                            }, {
                                text: '<strong>Saldo</strong>',
                                dataIndex: 'saldo',
                                filter: 'string',
                                align: 'right',
                                flex: 0.3
                            }, {
                                text: '<strong>Estado</strong>',
                                align: 'center',
                                dataIndex: 'estado',
                                flex: 0.3,
                                renderer: function (value, met) {
                                    if (value === 0) {
                                        // met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                                        return '<strong><span class="label-danger">En Caja</span></strong>';
                                    } else if (value === 1) {
                                        // met.style = 'font-style:italic !important;font-weight: bold;background: #0080FF;';
                                        return '<strong><span  class="label-primary">Recargada en Caja</span></strong>';
                                    } else if (value === 2) {
                                        // met.style = 'font-style:italic !important;font-weight: bold;background: #04B431;';
                                        return '<strong><span class="label-success">En Tránsito</span></strong>';
                                    } else if (value === 3) {
                                        // met.style = 'font-style:italic !important;font-weight: bold;background: #04B431;';
                                        return '<strong><span class="label-cancel">Cancelada</span></strong>';
                                    }

                                }
                            }
                        ],
                        listeners: {
                            selectionchange: function (This, selected, e) {
                                if (selected.length !== 0) {
                                    _btn_limpiar.setDisabled(false);
                                }
                            }
                        }
                    }],
                buttons: [
                    {
                        xtype: 'button',
                        text: 'Cerrar',
                        handler: function () {
                            mes_anno.destroy(true);
                            Ext.getCmp('windowTarjetasHistorial').close();
                        }
                    }
                ],
                listeners: {
                    afterrender: function () {
                        App.mask();
                        Ext.getCmp('gridHistorial').getStore().load({
                            callback: function () {
                                App.unmask();
                            }
                        });
                    }
                }
            }).show();
        }
    });

    let grid = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_tarjetas_hist',
        region: 'center',
        width: '100%',
        height: '100%',
        disabled: true,
        store: store_tarjeta,
        columns: [
            {
                text: '<strong>No. Tarjeta</strong>',
                dataIndex: 'nro_tarjeta',
                filter: 'string',
                align: 'center',
                flex: 0.5,
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('estado') === 3) {
                        return '<strong><span class="label-cancel">' + val2 + '</span></strong>';
                    } else {
                        return val2;
                    }
                }
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
                text: '<strong>Estado</strong>',
                align: 'center',
                dataIndex: 'estado',
                flex: 0.4,
                renderer: function (value, met) {
                    if (value === 0) {
                        // met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return '<strong><span class="label-danger">En Caja</span></strong>';
                    } else if (value === 1) {
                        // met.style = 'font-style:italic !important;font-weight: bold;background: #0080FF;';
                        return '<strong><span  class="label-primary">Recargada en Caja</span></strong>';
                    } else if (value === 2) {
                        // met.style = 'font-style:italic !important;font-weight: bold;background: #04B431;';
                        return '<strong><span class="label-success">En Tránsito</span></strong>';
                    } else if (value === 3) {
                        // met.style = 'font-style:italic !important;font-weight: bold;background: #04B431;';
                        return '<strong><span class="label-cancel">Cancelada</span></strong>';
                    }

                }
            }
        ],
        tbar: {
            id: 'tarjetas_post_tbar',
            height: 36,
            items: [textSearch, _btn_historial]
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (selected.length !== 0) {
                    _btn_historial.setDisabled(false);
                }
            }
        }
    });

    let panel_tarjetas_evento = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_tarjetas_evento',
        title: 'Modificar eventos tarjetas magnéticas',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });

    App.render(panel_tarjetas_evento);
});