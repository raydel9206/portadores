/**
 * Created by rherrerag on 13/12/2017.
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
                    while (!Ext.getCmp('grid_reporte_conciliacion_semanal') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('grid_reporte_conciliacion_semanal'));
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

    var store = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_conciliacion',
        fields: [
            {name: 'id_vehiculo'},
            {name: 'matricula'},
            {name: 'nroOrden'},
            {name: 'litros_transporte-semana1'},
            {name: 'litros_transporte-semana2'},
            {name: 'litros_transporte-semana3'},
            {name: 'litros_transporte-semana4'},
            {name: 'litros_transporte-semana5'},
            {name: 'litros_transporte-semana6'},
            {name: 'litros_pe-semana1'},
            {name: 'litros_pe-semana2'},
            {name: 'litros_pe-semana3'},
            {name: 'litros_pe-semana4'},
            {name: 'litros_pe-semana5'},
            {name: 'litros_pe-semana6'},
            {name: 'diferencia_pe-semana1'},
            {name: 'diferencia_pe-semana2'},
            {name: 'diferencia_pe-semana3'},
            {name: 'diferencia_pe-semana4'},
            {name: 'diferencia_pe-semana5'},
            {name: 'diferencia_pe-semana6'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/conciliacion_mensual/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'nroOrden',
            direction: 'ASC'
        }],
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear()
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
                grid.getStore().load();
            }
        }


    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_reporte_conciliacion_semanal',
        store: store,
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        columns: [
            {xtype: 'rownumberer', width: 20}
            , {
                text: '<strong>Vehiculo</strong>',
                dataIndex: 'matricula',
                align: 'center',
                width: 90
            }, {
                text: '<strong>Sem 1</strong>',
                columns: [{
                    text: '<strong>Transp</strong>',
                    dataIndex: 'litros_transporte-semana1',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }, {
                    text: '<strong>PE</strong>',
                    dataIndex: 'litros_pe-semana1',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }]
            }, {
                text: '<strong>Sem 2</strong>',
                columns: [{
                    text: '<strong>Transp</strong>',
                    dataIndex: 'litros_transporte-semana2',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }, {
                    text: '<strong>PE</strong>',
                    dataIndex: 'litros_pe-semana2',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }]
            }, {
                text: '<strong>Sem 3</strong>',
                columns: [{
                    text: '<strong>Transp</strong>',
                    dataIndex: 'litros_transporte-semana3',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }, {
                    text: '<strong>PE</strong>',
                    dataIndex: 'litros_pe-semana3',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }]
            }, {
                text: '<strong>Sem 4</strong>',
                columns: [{
                    text: '<strong>Transp</strong>',
                    dataIndex: 'litros_transporte-semana4',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }, {
                    text: '<strong>PE</strong>',
                    dataIndex: 'litros_pe-semana4',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }]
            }, {
                text: '<strong>Sem 5</strong>',
                columns: [{
                    text: '<strong>Transp</strong>',
                    dataIndex: 'litros_transporte-semana5', renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }, {
                    text: '<strong>PE</strong>',
                    dataIndex: 'litros_pe-semana5',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }]
            }, {
                text: '<strong>Sem 6</strong>',
                columns: [{
                    text: '<strong>Transp</strong>',
                    dataIndex: 'litros_transporte-semana6', renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }, {
                    text: '<strong>PE</strong>',
                    dataIndex: 'litros_pe-semana6',
                    renderer: function (value) {
                        if (value > 0)
                            return value;
                        else
                            return 0;
                    }
                }]
            }, {
                text: '<strong>1 Sem</strong>',
                dataIndex: 'diferencia_pe-semana1',
                renderer: function (value) {
                    if (value > 0)
                        return '<div class="badge badge-pill badge-danger">' + value + '</div>';
                    else if (value = 0)
                        return 0;
                    else
                        return value;
                }
            }, {
                text: '<strong>2 Sem</strong>',
                dataIndex: 'diferencia_pe-semana2',
                renderer: function (value) {
                    if (value > 0)
                        return '<div class="badge badge-pill badge-danger">' + value + '</div>';
                    else if (value = 0)
                        return 0;
                    else
                        return value;
                }
            }, {
                text: '<strong>3 Sem</strong>',
                dataIndex: 'diferencia_pe-semana3',
                renderer: function (value) {
                    if (value > 0)
                        return '<div class="badge badge-pill badge-danger">' + value + '</div>';
                    else if (value = 0)
                        return 0;
                    else
                        return value;
                }
            }, {
                text: '<strong>4 Sem</strong>',
                dataIndex: 'diferencia_pe-semana4',
                renderer: function (value) {
                    if (value > 0)
                        return '<div class="badge badge-pill badge-danger">' + value + '</div>';
                    else if (value = 0)
                        return 0;
                    else
                        return value;
                }
            }, {
                text: '<strong>5 Sem</strong>',
                dataIndex: 'diferencia_pe-semana5',
                renderer: function (value) {
                    if (value > 0)
                        return '<div class="badge badge-pill badge-danger">' + value + '</div>';
                    else if (value = 0)
                        return 0;
                    else
                        return value;
                }
            }, {
                text: '<strong>6 Sem</strong>',
                dataIndex: 'diferencia_pe-semana6',
                renderer: function (value) {
                    if (value > 0)
                        return '<div class="badge badge-pill badge-danger">' + value + '</div>';
                    else if (value = 0)
                        return 0;
                    else
                        return value;
                }
            }
        ],

        tbar: {
            id: 'tbar_conciliacion_semanal',
            height: 36,
            items: [mes_anno]
        },
        // bbar: {
        //     xtype: 'pagingtoolbar',
        //     pageSize: 25,
        //     store: store,
        //     displayInfo: true,
        //     plugins: new Ext.ux.ProgressBarPager()
        // }
    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'panel_id_conciliacion_semanal',
        title: 'Conciliación Mensual de los Registros de Combustible',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });
    App.render(_panel);
});
