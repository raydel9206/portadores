/**
 * Created by rherrerag on 1/8/2018.
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
                    while (!Ext.getCmp('grid_reporte_analisis') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('grid_reporte_analisis'));
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

    var analisis = Ext.create('Ext.data.JsonStore', {
        storeId: 'analisis',
        fields: [
            {name: 'matricula'},
            {name: 'persona'},
            {name: 'servicentro'},
            {name: 'servicio'},
            {name: 'nro_vale'},
            {name: 'fecha'},
            {name: 'hora'},
            {name: 'importe', type:'float'},
            {name: 'cantidad', type:'float'},
            {name: 'saldo_final', type:'float'},
            {name: 'cantidad_final', type:'float'},
            {name: 'cantidad_final', type:'float'}
        ],

        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/analisis/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                    id: Ext.getCmp('combo_search').getValue()
                });
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

    var cmbSearch = Ext.create('Ext.form.ComboBox', {
        width: 155,
        id: 'combo_search',
        store: store_tarjeta,
        displayField: 'nro_tarjeta',
        valueField: 'id',
        queryMode: 'local',
        forceSelection: true,
        emptyText: 'No. Tarjeta...',
        selectOnFocus: true,
        editable: true,
        allowBlank: false,
        listeners: {
            change: function (This, newValue) {
                Ext.getCmp('grid_reporte_analisis').getStore().currentPage = 1;
                Ext.getCmp('grid_reporte_analisis').getStore().load();

                if (Ext.getCmp('reporte_analisis_btn_print'))
                    Ext.getCmp('reporte_analisis_btn_print').setDisabled(!newValue)
                if (Ext.getCmp('reporte_analisis_btn_export'))
                    Ext.getCmp('reporte_analisis_btn_export').setDisabled(!newValue);

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

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_reporte_analisis',
        region: 'center',
        width: '75%',
        height: '100%',
        store: analisis,
        disabled: true,
        features: [{
            ftype: 'summary'
        }],
        columns: [
            {header: '<strong>No.</strong>', align: 'center', xtype: 'rownumberer', id: 'numero', width: 45},
            {
                header: '<strong>Fecha</strong>',
                align: 'center',
                dataIndex: 'fecha',
                width: 100
            },
            {
                header: '<strong>Hora</strong>',
                align: 'center',
                dataIndex: 'hora',
                width: 80
            },
            {
                header: '<strong>Servicentro</strong>',
                align: 'center',
                dataIndex: 'servicentro',
                width: 200
            },

            {
                text: '<strong>Operaciones en Importe</strong> ',
                id: 'operaciones_importe',
                columns: [
                    {
                        header: '<strong>Saldo <br>Inicial</strong>',
                        width: 100,
                        align: 'center',
                        dataIndex: 'saldo_inicial',
                        renderer: function (val, met, record)  {
                            if(record.get('servicentro')){
                                return Ext.util.Format.number(record.get('saldo_final')+record.get('importe'), '0.00');
                            }
                            else{
                                return Ext.util.Format.number(record.get('saldo_final')-record.get('importe'), '0.00');
                            }

                        },
                    },
                    {
                        header: '<strong>Consumo <br>o Carga</strong>',
                        width: 100,
                        align: 'center',
                        dataIndex: 'importe',
                        formatter: "number('0.00')",
                    },
                    {
                        header: '<strong>Saldo <br>Final</strong>',
                        width: 100,
                        align: 'center',
                        dataIndex: 'saldo_final',
                        formatter: "number('0.00')",
                    },
                ]
            },
            {
                text: '<strong>Operaciones en Litros</strong> ',
                id: 'operaciones_cantidad',
                columns: [
                    {
                        header: '<strong>Litros <br>Iniciales</strong>',
                        width: 100,
                        align: 'center',
                        dataIndex: 'cantidad_inicial',
                        renderer: function (val, met, record)  {
                            if(record.get('servicentro')){
                                return Ext.util.Format.number(record.get('cantidad_final')+record.get('cantidad'), '0.00');
                            }
                            else{
                                return Ext.util.Format.number(record.get('cantidad_final')-record.get('cantidad'), '0.00');
                            }

                        },
                    },
                    {
                        header: '<strong>Consumo <br>o Carga</strong>',
                        width: 100,
                        align: 'center',
                        dataIndex: 'cantidad',
                        formatter: "number('0.00')",
                    },
                    {
                        header: '<strong>Litros <br>Finales</strong>',
                        width: 100,
                        align: 'center',
                        dataIndex: 'cantidad_final',
                        formatter: "number('0.00')",
                    },
                ]
            },
            {
                header: '<strong>Asignado <br>al Equipo</strong>',
                width: 90,
                align: 'center',
                dataIndex: 'matricula',
            },
            {
                header: '<strong>Indice <br> Plan</strong>',
                width: 80,
                align: 'center',
                dataIndex: 'norma',
                formatter: "number('0.0')",
            },
            {
                header: '<strong>Responsable</strong>',
                width: 200,
                align: 'center',
                dataIndex: 'responsable',
            },
            {
                header: '<strong>Capacidad <br>del Tanque</strong>',
                width: 100,
                align: 'center',
                dataIndex: 'combustible',
            },
            {
                header: '<strong>Nivel de<br>Actividad</strong>',
                width: 100,
                align: 'center',
                dataIndex: 'kilometraje',
            }
        ],


        tbar: {
            id: 'tbar_reporte_analisis',
            // height: 36,
            items: [mes_anno, cmbSearch]
        }
    });

    var panelContainer = Ext.create('Ext.panel.Panel', {
        title: 'Análisis del Comportamiento de las Tarjetas',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });


    App.render(panelContainer);
});


