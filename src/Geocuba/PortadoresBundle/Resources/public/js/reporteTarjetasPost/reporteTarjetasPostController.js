/**
 * Created by orlando on 06/01/2017.
 */

Ext.onReady(function () {
    var tarjetas = null;

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
                    while (!Ext.getCmp('id_grid_tarjetas_post') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_tarjetas_post'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
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

    var store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta',
        fields: [
            {name: 'nro_tarjeta'},
            {name: 'id'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        sorters: [{property: 'nro_tarjeta', direction: 'ASC'}],
        listeners: {
            beforeload: function (store, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
        }
    });

    // var periodo = App.PerformSyncServerRequest(Routing.generate('getCurrentPeriodo'),{});

    var store_tarjetas_post = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjetas_post',
        fields: [
            {name: 'id'},
            {name: 'nro_tarjeta'},
            {name: 'tipo'},
            {name: 'importe'},
            {name: 'caja'},
            {name: 'tipo_combustible'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta_post/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'nro_tarjeta',
            direction: 'ASC'
        }],
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                if (!tarjetas) {
                    App.showAlert("Escoja las tarjetas a pasar por POST", 'info');
                    return false;
                }
                operation.setParams({
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                    tarjetas: tarjetas,
                    nunidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
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
                grid.enable();
                store_tarjeta.load();
            }
        }


    });

    var _btn_selec_tarjetas_post = Ext.create('Ext.button.MyButton', {
        id: '_btn_selec_tarjetas_post',
        text: 'Seleccionar tarjetas',
        iconCls: 'fas fa-search text-primary',
        handler: function () {
            Ext.create('Ext.window.Window', {
                id: 'windowTarjetasPost',
                width: 500,
                closable: true,
                // forceFit: false,
                scrollable: 'vertical',
                autoHeight: true,
                modal: true,
                draggable: true,
                resizable: true,
                title: 'Tarjetas para POST',
                layout: {
                    type: 'vbox',
                },
                items: [{
                    xtype: 'fieldcontainer',
                    width: '95%',
                    items: [
                        {
                            xtype: 'tagfield',
                            width: '95%',
                            id: 'tarjetas_tagfield',
                            labelAlign: 'left',
                            growMax: 200,
                            padding: 10,
                            labelWidth: 65,
                            editable: true,
                            store: store_tarjeta,
                            fieldLabel: 'Seleccione:',
                            displayField: 'nro_tarjeta',
                            valueField: 'nro_tarjeta',
                            filterPickList: true,
                            queryMode: 'local'
                        }]
                }, {
                    xtype: 'fieldcontainer',
                    items: [{
                        xtype: 'button',
                        margin: '10 0 10 120',
                        text: 'Seleccionar Todas',
                        handler: function () {
                            var store = Ext.getCmp('tarjetas_tagfield').getStore();
                            var tag = Ext.getCmp('tarjetas_tagfield');
                            var send = [];
                            if (store.data.length != 0) {
                                Ext.Array.each(store.data.items, function (valor) {
                                    send.push(valor.data);
                                });
                                var value = tag.getValue();
                                for (var i = 0; i < send.length; i++) {
                                    if (value == '')
                                        value = send[i].nro_tarjeta;
                                    else
                                        value += ',' + send[i].nro_tarjeta;
                                }
                                tag.setValue(value);
                            }
                        }
                    },
                        {
                            xtype: 'button',
                            margin: '10 10 10 5',
                            text: 'Deseleccionar Todas',
                            handler: function () {
                                Ext.getCmp('tarjetas_tagfield').setValue();
                            }
                        }
                    ]
                }
                ],
                buttons: [
                    {
                        xtype: 'button',
                        text: 'Aceptar',
                        handler: function () {
                            tarjetas = Ext.JSON.encode(Ext.getCmp('tarjetas_tagfield').getValue());
                            Ext.getCmp('id_grid_tarjetas_post').getStore().load();
                            Ext.getCmp('windowTarjetasPost').close();
                        }
                    },
                    {
                        xtype: 'button',
                        text: 'Cancelar',
                        handler: function () {
                            Ext.getCmp('windowTarjetasPost').close();
                        }
                    }
                ]
            }).show();
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_tarjetas_post',
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        store: store_tarjetas_post,
        columns: [
            {text: '<strong>ID</strong>', dataIndex: 'id', hidden: true},
            {text: '<strong>Nro Tarjeta</strong>', dataIndex: 'nro_tarjeta', flex: 1, align: 'center'},
            {text: '<strong>Tipo</strong>', dataIndex: 'tipo', filter: 'string', flex: .6, align: 'center'},
            {text: '<strong>Importe</strong>', dataIndex: 'importe', filter: 'string', flex: .7, align: 'right'},
            {text: '<strong>Caja</strong>', dataIndex: 'caja', filter: 'string', flex: 1, align: 'center'},
            {
                text: '<strong>Tipo de Combustible</strong>',
                dataIndex: 'tipo_combustible',
                filter: 'string',
                flex: 1,
                align: 'center'
            }
        ],
        tbar: {
            id: 'tarjetas_post_tbar',
            height: 36,
            items: [mes_anno, _btn_selec_tarjetas_post]
        },
        plugins: 'gridfilters'
    });

    var panel_tarjetas_post = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_tarjetas_post',
        title: 'Pase de lasTarjetas por el Post',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });

    App.render(panel_tarjetas_post);
});