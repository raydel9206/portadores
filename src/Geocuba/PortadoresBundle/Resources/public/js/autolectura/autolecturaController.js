Ext.onReady(function () {

    Ext.define('Autolecturas', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'serviciosid', type: 'string'},
            {name: 'nombreserviciosid', type: 'string'},
            {name: 'lectura_pico', type: 'number'},
            {name: 'lectura_mad', type: 'number'},
            {name: 'lectura_dia', type: 'number'},
            {name: 'lectura_pico_maxD', type: 'number'},
            {name: 'lectura_mad_maxD', type: 'number'},
            {name: 'lectura_dia_maxD', type: 'number'},
            {name: 'lectura_reactivo', type: 'number'},
            {name: 'consumo', type: 'number'},
            {name: 'consumo_total_mad', type: 'number'},
            {name: 'consumo_total_dia', type: 'number'},
            {name: 'fecha_lectura', type: 'date'},
            {name: 'fecha'},
            {name: 'consumo_total_real', type: 'number'},
            {name: 'consumo_total_plan', type: 'number'},
            {name: 'consumo_total_porciento', type: 'number'},
            {name: 'consumo_pico_plan', type: 'number'},
            {name: 'consumo_pico_real', type: 'number'},
            {name: 'consumo_pico_porciento', type: 'number'},
            {name: 'lectura_total', type: 'number'},
            {name: 'mes', type: 'string'},
            {name: 'anno', type: 'number'}
        ]
    });

    let store = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_autolectura',
        model: 'Autolecturas',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/autolectura_tresescalas/getautolecturasbyservicios'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: 'fecha_lectura',
        listeners: {
            beforeload: function (This, operation) {
                let servicio_selected = Ext.getCmp('id_grid_autolecturaservicios').getSelectionModel().getLastSelected();
                Ext.getCmp('real_mxm_dmd').setText('<strong>Demanda Contratada: </strong> ' + 0)
                if (servicio_selected !== undefined)
                    operation.setParams({
                        'id': servicio_selected.data.id
                    });
            },
            load: function (This, records, successful, operation) {
                if (records.length === 0) {
                    App.showAlert('No existen autolecturas para el servicio seleccionado', 'danger', 3500);
                }
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

    let paneltree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        id: 'paneltree',
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
                grid_autolecturaeste.setDisabled(!record);
                grid_autolecturaeste.focus();
                store.removeAll();
                _storeservicios.load();
                paneltree.collapse();
            }
        }
    });


    let _storeservicios = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_servicios_autolectura',
        fields: [
            {name: 'id'},
            {name: 'nombre_servicio'},
            {name: 'MaximaDemandaContratada'},
            {name: 'MaximaDemandaContratada'},
            {name: 'MaximaDemandaContratada'},
            {name: 'MaximaDemandaContratada'},
            {name: 'MaximaDemandaContratada'},
            {name: 'MaximaDemandaContratada'},
            {name: 'turno_trabajo'},
            {name: 'tipo_servicio'},
            {name: 'banco_pcu'},
            {name: 'banco_pfe'},
            {name: 'capac_banco_transf'},
            {name: 'control'},
            {name: 'factor_metrocontador'},
            {name: 'codigo_cliente'},
            {name: 'turno_trabajo_horas'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/servicio/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        filters: [{
            property: 'servicio_mayor',
            value: /true/
        }],
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation) {
                operation.setParams({
                    unidadid: (Ext.getCmp('paneltree').getSelectionModel().getLastSelected() !== undefined) ? Ext.getCmp('paneltree').getSelectionModel().getLastSelected().data.id : null,
                });
            }
        }
    });

    let grid_autolecturacenter = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_autolectura',
        region: 'center',
        columnWidth: '50',
        flex: 5,
        disabled: true,
        columnLines: true,
        viewConfig: {
            stripeRows: true
        },
        store: store,
        columns: [
            {
                text: '<strong>Fecha</strong>',
                dataIndex: 'fecha_lectura',
                width: 300, align: 'center',
                xtype: 'datecolumn',
                format: 'D,j M, Y '
            },
            {
                text: '<strong>Demanda Contratada:</strong>',
                id: 'demanda_contratada',
                columns: [
                    {
                        text: '<strong>Energia Activa</strong>',
                        columns: [
                            {
                                text: '<strong>Lecturas</strong>',
                                columns: [

                                    {
                                        text: '<strong> Pico</strong>',
                                        width: 100,
                                        align: 'center',

                                        dataIndex: 'lectura_pico'


                                    }, {
                                        text: '<strong> Madrugada</strong>',
                                        width: 100, align: 'center',
                                        dataIndex: 'lectura_mad'

                                    },
                                    {
                                        text: '<strong>Dia</strong>',
                                        dataIndex: 'lectura_dia',
                                        width: 100, align: 'center'

                                    },
                                    {
                                        text: '<strong>Total</strong>',
                                        id: 'lectura_total',
                                        dataIndex: 'lectura_total'
                                    }
                                ]
                            }
                        ]
                    }, {
                        text: '<strong>Energia Reactiva</strong>',
                        columns: [
                            {
                                text: '<strong> Reactivo</strong>',
                                dataIndex: 'lectura_reactivo',
                                width: 100


                            },
                            {
                                text: '<strong> Consumo</strong>',
                                dataIndex: 'consumo',
                                width: 100, align: 'center'
                            }
                        ]
                    }
                ]

            },
            {
                text: '<strong>Real Demanda Maxima</strong>',
                id: 'real_mxm_dmd',
                columns: [{
                    text: '<strong>Pico</strong>',
                    dataIndex: 'lectura_pico_maxD',
                    width: 100
                }, {
                    text: '<strong>Madrugada</strong>',
                    dataIndex: 'lectura_mad_maxD',
                    width: 100,

                },
                    {
                        text: '<strong>Dia</strong>',
                        dataIndex: 'lectura_dia_maxD',
                        width: 100,

                    }

                ]
            },
            {
                text: '<strong>Consumo Total</strong>',
                columns: [
                    {
                        text: '<strong>Madrugada</strong>',
                        dataIndex: 'consumo_total_mad',
                        width: 100, align: 'center',
                        filter: 'string',
                        renderer: function (val2, met, record, a, b, c, d) {
                            return Ext.util.Format.round(val2, 2)
                        }

                    },
                    {
                        text: '<strong>Dia</strong>',
                        dataIndex: 'consumo_total_dia',
                        width: 100, align: 'center',
                        filter: 'string',
                        renderer: function (val2, met, record, a, b, c, d) {
                            return Ext.util.Format.round(val2, 2)
                        }

                    },
                    {
                        text: '<strong>Plan</strong>',
                        dataIndex: 'consumo_total_plan',
                        width: 100, align: 'center',
                        renderer: function (val2, met, record, a, b, c, d) {
                            return Ext.util.Format.round(val2, 2)
                        }

                    },
                    {
                        text: '<strong>Real</strong>',
                        dataIndex: 'consumo_total_real',
                        width: 100, align: 'center',
                        filter: 'string',
                        renderer: function (val2, met, record, a, b, c, d) {
                            if (record.get('consumo_total_real') >= record.get('consumo_total_plan')) {
                                met.style = 'font-style:italic !important;font-weight: bold;background: #F22222;';
                                return Ext.util.Format.round(val2,2);
                            } else
                                return Ext.util.Format.round(val2, 2)
                        }
                    }, {
                        text: '<strong>%</strong>',
                        width: 100, align: 'center',
                        renderer: function (value, metaData, record) {
                            if (record.get('consumo_pico_real') === null) {
                                return '-';
                            }
                            return Ext.util.Format.round((record.get('consumo_total_real') * 100 / record.get('consumo_total_plan')), 2);
                        }
                    }

                ]
            },

            {
                text: '<strong>Consumo Pico</strong>',
                columns: [
                    {
                        text: '<strong>Plan</strong>',
                        dataIndex: 'consumo_pico_plan',
                        align: 'center',
                        filter: 'string',
                        width: 120,
                        renderer: function (val2, met, record, a, b, c, d) {
                            return Ext.util.Format.round(val2, 2)
                        }
                    },
                    {
                        text: '<strong>Real</strong>',
                        //dataIndex: 'consumo_pico_real ',
                        //filter: 'string'
                        dataIndex: 'consumo_pico_real',
                        //width: 100,
                        align: 'center',
                        filter: 'string',
                        width: 120,
                        renderer: function (val2, met, record, a, b, c, d) {
                            return Ext.util.Format.round(val2, 2)
                        }
                    },
                    {
                        text: '<strong>%</strong>',
                        align: 'center',
                        width: 120,
                        renderer: function (val2, met, record, a, b, c, d) {
                            if (record.get('consumo_pico_real') === null) {
                                return '-';

                            } else if (record.get('consumo_pico_real') >= record.get('consumo_pico_plan')) {
                                met.style = 'font-style:italic !important;font-weight: bold;background: #F22222;';
                                if (record.get('consumo_pico_plan') > 0) {
                                    return Ext.util.Format.round((record.get('consumo_pico_real') * 100 / record.get('consumo_pico_plan')), 2);
                                } else {
                                    return '-'
                                }
                            } else {
                                if (record.get('consumo_pico_plan') > 0) {
                                    return Ext.util.Format.round((record.get('consumo_pico_real') * 100 / record.get('consumo_pico_plan')), 2);
                                } else {
                                    return '-'
                                }
                            }


                        }

                    },
                    {
                        text: '<strong>F.Potecia</strong>',
                        id: 'factor_potencia',
                        dataIndex: 'factor_potencia',
                        renderer: function (val2, met, record, a, b, c, d) {
                            return Ext.util.Format.round(val2, 2);
                        }
                    }, {
                        text: '<strong>Pérdidas</strong>',
                        id: 'perdidas_transf',
                        dataIndex: 'perdidas_transf',
                        renderer: function (val2, met, record, a, b, c, d) {
                            return Ext.util.Format.round(val2, 2);
                        }
                    }
                ]
            }


        ],
        tbar: {
            id: 'autolectura_tbar',
            height: 36
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('autolectura_tbar').items.each(
                    function (item, index, length) {
                        item.setDisabled(item.getXType() === 'button' && selected.length === 0)
                    }
                );
            }
        }
    });

    let grid_autolecturasur = Ext.create('Ext.panel.Panel', {
        id: 'id_detalles',
        region: 'east',
        hidden: true,
        title: 'Pérdidas',
        columnLines: true,
        height: 50,
        width: 260,
        frame: true,
        flex: 2,
        collapsible: true,
        items: [

            {
                xtype: 'fieldcontainer',
                flex: 1,
                layout: 'vbox',
                border: true,
                // margin: '10 10 10 10',
                collapsible: false,
                labelAlign: 'right',


                items: [{
                    xtype: 'fieldcontainer',
                    flex: 1,
                    width: 388,
                    layout: 'anchor',
                    border: false,
                    margin: '10 10 10 10',
                    collapsible: false,
                    labelAlign: 'right',

                    items: [
                        {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Penalizaci&oacuten M&aacutenxima Demandas</strong>',
                            //labelWidth: 220,
                            id: 'penalizacionMD',
                            name: 'penalizacionMD'
                            //value: penalizacion
                        }, {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Penalizaci&oacuten Factor de Potencia</strong>',
                            //labelWidth: 210,
                            id: 'id_penalizacion',
                            name: 'id_penalizacion'
                            //value: Ext.util.Format.round(pena_factorP1, 2)

                        }]
                },

                    {
                        xtype: 'fieldcontainer',
                        flex: 1,
                        width: 388,
                        layout: 'anchor',
                        border: false,
                        margin: '10 10 10 10',
                        collapsible: false,
                        labelAlign: 'right',


                        items: [
                            {
                                xtype: 'displayfield',
                                fieldLabel: '<strong>P&eacuterdidas de Transformaci&oacuten</strong>',
                                //labelWidth: 280,
                                id: 'perdidas',
                                name: '',
                                //value:Ext.util.Format.round(Pt, 3)
                            }


                        ]

                    }
                ]
            }
        ]
    });

    let grid_autolecturaeste = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_autolecturaservicios',
        title: 'Servicios',
        region: 'west',
        width: 280,
        disabled: true,
        columnLines: true,
        collapsible: true,
        collapsed: false,
        hideHeaders: true,
        border: true,
        header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'},},
        store: _storeservicios,
        columns: [
            {
                text: '<strong>Servicios</strong>',
                dataIndex: 'nombre_servicio',
                width: 280,
                align: 'center'
            }
        ],
        listeners: {
            selectionchange: function (This, selected, e) {
                let idservicios = {};
                if (selected.length !== 0) {
                    grid_autolecturacenter.setDisabled(false);
                    idservicios.id = selected[0].id;
                    store.load({params: {id: selected[0].id}});
                }
            }
        }
    });

    let grid_acumulados = Ext.create('Ext.panel.Panel', {
        id: 'id_acumulados',
        region: 'east',
        hidden: true,
        columnLines: true,
        title: 'Acumulados kwhats',
        height: 50,
        width: 260,
        frame: true,
        collapsible: true,
        items: [
            {
                xtype: 'fieldcontainer',
                flex: 1,
                layout: 'vbox',
                border: true,
                // margin: '10 10 10 10',
                collapsible: false,
                labelAlign: 'right',


                items: [{
                    xtype: 'fieldcontainer',
                    flex: 1,
                    width: 230,

                    layout: 'anchor',
                    border: false,
                    margin: '10 10 10 10',
                    collapsible: false,
                    labelAlign: 'right',

                    items: [
                        {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Plan</strong>',
                            //labelWidth: 220,
                            id: 'id_plan',
                            name: 'id_plan'
                            //value: penalizacion
                        }, {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Real</strong>',
                            //labelWidth: 210,
                            id: 'id_real',
                            name: 'id_real'
                            //value: Ext.util.Format.round(pena_factorP1, 2)

                        }, {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Plan del Pico</strong>',
                            //labelWidth: 210,
                            id: 'id_plan_pico',
                            name: 'id_plan_pico'
                            //value: Ext.util.Format.round(pena_factorP1, 2)

                        }, {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Real del Pico</strong>',
                            //labelWidth: 210,
                            id: 'id_real_pico',
                            name: 'id_real_pico'
                            //value: Ext.util.Format.round(pena_factorP1, 2)

                        }, {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Diferencia</strong>',
                            //labelWidth: 210,
                            id: 'id_diferencia',
                            name: 'id_diferencia'
                            //value: Ext.util.Format.round(pena_factorP1, 2)

                        }, {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>% Plan-Real</strong>',
                            //labelWidth: 210,
                            id: 'id_plan_real',
                            name: 'id_plan_real'
                            //value: Ext.util.Format.round(pena_factorP1, 2)

                        }]


                }
                ]
            }
        ]
    });

    let _panel_autolectura = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_autolectura',
        title: 'Autolecturas Tres Escalas',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',

        items: [paneltree, grid_autolecturaeste, grid_autolecturacenter, grid_autolecturasur, grid_acumulados]
    });

    App.render(_panel_autolectura);
});