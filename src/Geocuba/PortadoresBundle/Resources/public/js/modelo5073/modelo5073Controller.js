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
                    while (!Ext.getCmp('grid_modelo5073') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('grid_modelo5073'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });

            }
        }
    });

    var store_modelo5073 = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_modelo5073Id',
        fields: [
            {name: 'id'},
            {name: 'unidadid'},
            {name: 'unidad_nombre'},
            {name: 'productos_id'},
            {name: 'producto'},
            {name: 'fila'},
            {name: 'um'},
            {name: 'inv_inicial', type: 'float'},
            {name: 'compras_cupet', type: 'float'},
            {name: 'otras_entradas', type: 'float'},
            {name: 'consumo_directo', type: 'float'},
            {name: 'consumo_indirecto', type: 'float'},
            {name: 'otras_salidas', type: 'float'},
            {name: 'inv_final', type: 'float'},
            {name: 'asignado_mes', type: 'float'},
            {name: 'efectua_carga', type: 'float'},
            {name: 'consumo', type: 'float', type: 'float'},
            {name: 'entregados_consumo', type: 'float'},
            {name: 'saldo_final_total', type: 'float'},
            {name: 'proximo_mes', type: 'float'},
            {name: 'disponible_fincimex', type: 'float'},
            {name: 'ca_real', type: 'float'},
            {name: 'ca_ano_anterior', type: 'float'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/modelo5073/load'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                //if(Ext.getCmp('generar_modelo5073'))
                 //   Ext.getCmp('generar_modelo5073').setDisabled(App.selected_month!=mes_anno.getValue().getMonth()+1 || App.selected_year!=mes_anno.getValue().getFullYear());
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear()
                });
            },
            load: function (This, records, successful, eOpts) {
                if (records.length === 0) {
                    App.showAlert('No existe modelo generado para el mes seleccionado', 'warning');
                }
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
        border: true,
        id: 'arbolunidades',
        hideHeaders: true,
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
                grid.getStore().load();
                grid.enable();
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_modelo5073',
        store: store_modelo5073,
        disabled: true,
        region: 'center',
        features: [{
            ftype: 'summary',
            dock:'bottom'
        }],
        columnLines: true,
        plugins: {
            ptype: 'cellediting',
            clicksToEdit: 1,
            listeners: {
                edit: function (editor, context, eOpts) {
                    let _grid = Ext.getCmp('grid_modelo5073');

                    if (Ext.getCmp('modelo5073_btn_guardar').isDisabled()) {
                        Ext.getCmp('modelo5073_btn_guardar').setDisabled(false);
                    }

                    Ext.getCmp('modelo50723_btn_export').setDisabled(true);

                    if (context.field === 'consumo_directo' || context.field === 'consumo_indirecto' || context.field === 'consumo') {
                        if (context.originalValue > context.value) {
                            context.record.data['ca_real'] = context.record.data['ca_real'] - (context.originalValue - context.value);
                            _grid.getView().refresh();
                        } else {
                            context.record.data['ca_real'] = context.record.data['ca_real'] + (context.value - context.originalValue);
                            _grid.getView().refresh();
                        }
                    }


                }
            }
        },
        columns: [
            {
                xtype: 'gridcolumn',text: '<strong>PRODUCTOS</strong>', dataIndex: 'producto', filter: 'string', width: 200,locked:true,
                summaryRenderer: function (value) {
                    return 'SUMA DE CONTROL (pagina 1 de 1)';
                },
            },
            {
                xtype: 'gridcolumn',text: '<strong>FILA</strong>', dataIndex: 'fila', align:'center', filter: 'string', width: 100,locked:true,
                summaryRenderer: function (value) {
                    return 999;
                },
            },
            {
                xtype: 'gridcolumn',text: '<strong>U.M</strong>', dataIndex: 'um', align:'center', filter: 'string', width: 100,locked:true,
            },
            {
                text: '<strong>BALANCE DEL MES EN FÍSICO </strong>',
                columns: [
                    {
                        text: '<strong>Inventario </br> Inicial</strong>',
                        dataIndex: 'inv_inicial',
                        width: 90, align: 'center', lockable: false,
                        renderer: function (value) {
                            if(value)
                            return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },


                    },
                    {
                        text: '<strong>Compras </br> </br> CUPET</strong>',
                        dataIndex: 'compras_cupet',
                        width: 90, align: 'center',
                        filter: 'string',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>Otras </br> entradas</strong>',
                        width: 90, align: 'center',
                        dataIndex: 'otras_entradas',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>CONSUMO</strong>',
                        columns: [
                            {
                                text: '<strong>Directo</strong>',
                                dataIndex: 'consumo_directo',
                                width: 90, align: 'center',
                                editor: {
                                    xtype: 'numberfield',
                                    decimalPrecision:3
                                },
                                renderer: function (value) {
                                    if(value)
                                        return Ext.util.Format.number(value, '0.00');
                                    return '';
                                },
                                summaryType: 'sum',
                                summaryRenderer: function (value) {
                                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                                },
                            },
                            {
                                text: '<strong>Indirecto</strong>',
                                dataIndex: 'consumo_indirecto',
                                width: 90, align: 'center',
                                filter: 'string',
                                editor: {
                                    xtype: 'numberfield',
                                    decimalPrecision:3
                                },
                                renderer: function (value) {
                                    if(value)
                                        return Ext.util.Format.number(value, '0.00');
                                    return '';
                                },
                                summaryType: 'sum',
                                summaryRenderer: function (value) {
                                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                                },
                            }
                        ]
                    },
                    {
                        text: '<strong>Otras </br> salidas</strong>',
                        dataIndex: 'otras_salidas',
                        width: 90, align: 'center',
                        filter: 'string',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>Inventario </br> final</strong>',
                        width: 90, align: 'center',
                        dataIndex: 'inv_final',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    }
                ]
            },
            {
                text: '<strong>OPERACIONES DEL MES EN MILES DE LITROS POR TARJETA MAGNÉTICA</strong>',
                columns: [
                    {
                        text: '<strong>Asignado </br> en el mes</strong>',
                        dataIndex: 'asignado_mes',
                        width: 90, align: 'center',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>Recibido </br> del que </br> efectúa </br> carga</strong>',
                        dataIndex: 'efectua_carga',
                        width: 90, align: 'center',
                        filter: 'string',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        width: 90, align: 'center',
                        dataIndex: 'consumo',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>Entregado </br> para </br> consumo</strong>',
                        dataIndex: 'entregados_consumo',
                        width: 90, align: 'center',
                        filter: 'string',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>Saldo final</strong>',
                        width: 90, align: 'center',
                        dataIndex: 'saldo_final_total',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>De ello:</br> Carga </br> para el </br> próximo </br> mes</strong>',
                        width: 90, align: 'center',
                        dataIndex: 'proximo_mes',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>De ello:</br> disponible en  </br> FINCIMEX </strong>',
                        width: 100, align: 'center',
                        dataIndex: 'disponible_fincimex',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    }
                ]
            },
            {
                text: '<strong>CONSUMO ACUMULADO</strong>',
                columns: [
                    {
                        text: '<strong>Real</strong>',
                        dataIndex: 'ca_real',
                        width: 90, align: 'center',
                        editor: {
                            xtype: 'numberfield',
                            decimalPrecision:3
                        },
                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    },
                    {
                        text: '<strong>Año </br> anterior</strong>',
                        dataIndex: 'ca_ano_anterior',
                        width: 90, align: 'center',
                        filter: 'string',

                        renderer: function (value) {
                            if(value)
                                return Ext.util.Format.number(value, '0.00');
                            return '';
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                        },
                    }
                ]
            }
        ],
        tbar: {
            id: 'modelo5073_tbar',
            height: 36,
            items: [mes_anno]
        },
        enableLocking: true,
        listeners: {
            // selectionchange: function (This, selected, e) {
            //     Ext.getCmp('modelo5073_tbar').items.each(
            //         function (item, index, length) {
            //             if (index != 0)
            //                 item.setDisabled(item.getXType() == 'button' && selected.length == 0)
            //         }
            //     );
            // }
        }
    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'modelo5073_panel_id',
        title: 'MODELO 5073',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid]
    });

    App.render(_panel);

})