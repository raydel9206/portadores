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
                    while(!Ext.getCmp('id_grid_anexo_unico') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_anexo_unico'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Vehículo a buscar...',
        width: 150,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_anexo_unico').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value,
                                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
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
                        Ext.getCmp('id_grid_anexo_unico').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_anexo_unico').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_anexo_unico').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_anexo_unico').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_anexo_unico').getStore().loadPage(1);
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

    var store_combustible_kilometros = Ext.create('Ext.data.Store', {
        id: 'combustibleStoreId',
        fields:[
            {name: 'fecha', type: 'date', dateFormat: 'd/m/Y'},
            {name: 'nro_tarjeta', type: 'string'},
            {name: 'kilometraje', type: 'number'},
            {name: 'comb_abast', type: 'number'},
            {name: 'comb_est_tanke', type: 'number'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anexo_unico/loadCombKilometros'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    anexoid: grid_anexo_unico.getSelectionModel().getLastSelected().data.id
                });
            },
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
                grid_anexo_unico.enable();
                Ext.getCmp('id_grid_anexo_unico').getStore().loadPage(1);
                if (Ext.getStore('vehiculo_store'))
                    Ext.getStore('vehiculo_store').load();
                if (Ext.getStore('person_store'))
                    Ext.getStore('person_store').load();
            },
            expand: function (This) {
                gridAbastecidos.collapse();
            }
        }


    });

    var store_anexounico = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_anexo_unico',
        fields: [
            {name: 'id'},
            {name: 'persona'},
            {name: 'npersonaid'},
            {name: 'npersona'},
            {name: 'nvehiculoid'},
            {name: 'vehiculo'},
            {name: 'matricula'},
            {name: 'norma_plan'},
            {name: 'kilometraje_mes_anterior'},
            {name: 'combustible_estimado_tanque'},
            {name: 'kilometraje_proximo_mantenimiento'},
            {name: 'fecha_anexo'},
            {name: 'kilometraje_cierre_mes'},
            {name: 'kilometraje'},
            {name: 'combustible_estimado_tanque_cierre'},
            {name: 'comb_estimado_tanke'},
            {name: 'kilometros_total_recorrido'},
            {name: 'combustible_total_consumido'},
            {name: 'indice_real'},
            {name: 'por_ciento_indice_real_plan'},
            {name: 'tipo_mantenimiento_id'},
            {name: 'tipo_mantenimiento'},
            {name: 'kilometraje_mantenimiento'},
            {name: 'observaciones'},
            {name: 'mes'},
            {name: 'combustible_total_abastecido'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anexo_unico/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        // groupField: 'mes',
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                if (Ext.getCmp('id_grid_anexo_unico'))
                    Ext.getCmp('id_grid_anexo_unico').getSelectionModel().deselectAll();
                operation.setParams({
                    nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    matricula: find_button.getValue(),
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),

                });
            }
        }
    });

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Mes:  {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        ftype: 'groupingsummary',
        startCollapsed: false,
        // ftype: 'grouping'
    });

    var grid_anexo_unico = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_anexo_unico',
        region: 'center',
        width: '75%',
        disabled: true,
        features: [groupingFeature],
        store: store_anexounico,
        // flex: 1,
        columns: [
            {
                text: '<strong>Fecha del<br> Anexo</strong>',
                width: 90,
                dataIndex: 'fecha_anexo'
            },
            {
                text: '<strong>Matrícula</strong>',
                width: 75,
                dataIndex: 'matricula'
            },
            {
                text: '<strong>Datos Cierre Mes Anterior</strong>',
                // flex: 1,
                columns: [
                    {
                        text: '<strong>Kilometraje</strong>',
                        tooltip: 'Kilometraje mes anterior',
                        width: 90,
                        dataIndex: 'kilometraje_mes_anterior'
                    },
                    {
                        text: '<strong>Comb. Est. <br>Tanque</strong>',
                        tooltip: 'Combustible estimado en tanque mes anterior',
                        width: 90,
                        dataIndex: 'combustible_estimado_tanque'
                    }
                ]
            },
            {
                text: '<strong>Combustible <br> Total <br>Abastecido</strong>',
                tooltip: 'Combustible total abastecido',
                width: 80,
                dataIndex: 'combustible_total_abastecido'
            },
            {
                text: '<strong>Datos Cierre Mes Actual</strong>',
                // flex: 1,
                columns: [
                    {
                        text: '<strong>Kilometraje</strong>',
                        width: 90,
                        dataIndex: 'kilometraje_cierre_mes'
                    },
                    {
                        text: '<strong>Comb. Est. <br>Tanque</strong>',
                        width: 90,
                        dataIndex: 'combustible_estimado_tanque_cierre'
                    }
                ]
            },
            {
                text: '<strong>Kms. <br>Total <br>Recorridos</strong>',
                tooltip: 'Kilómetros total recorridos',
                width: 80,
                dataIndex: 'kilometros_total_recorrido',
                filter: 'string',
                // summaryType: 'sum',
                // summaryRenderer: function (value) {
                //     return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                // }
            },
            {
                text: '<strong>Comb. <br>Total<br> Consumido</strong>',
                tooltip: 'Combustible total consumido',
                width: 80,
                dataIndex: 'combustible_total_consumido'
            },
            {
                text: '<strong>Índice <br>Consumo <br> Plan (Km/L)</strong>',
                tooltip: 'Índice Consumo Plan (Km/l)',
                width: 80,
                dataIndex: 'norma_plan',
                // summaryType: function()
                // {return '<strong>TOTAL</strong> '}
            },
            {
                text: '<strong>Índice <br>Consumo <br>Real<br> (Km/L)</strong>',
                // flex: 1,
                columns: [
                    {
                        text: '<strong>Real</strong>',
                        width: 70,
                        dataIndex: 'indice_real'
                    },
                    {
                        text: '<strong>% Real/Plan</strong>',
                        tooltip: 'Por ciento indice Real/Plan',
                        width: 80,
                        dataIndex: 'por_ciento_indice_real_plan'
                    }
                ]
            }
        ],
        tbar: {
            id: 'anexo_unico_tbar',
            height: 36,
            items: [
                mes_anno,'-',find_button,'-'
            ]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_anexo_unico'),
            displayInfo: true,

        },
        plugins: [
            'gridfilters',
            {
                ptype: 'rowexpander',
                rowBodyTpl: new Ext.XTemplate(
                    // '<b>Fecha registro:</b>{fecha_anexo}</p><br>',
                    '<p><b>Tipo mantenimiento:</b> {tipo_mantenimiento} ', '<br>',
                    '<b>Kilometraje:</b> {kilometraje_mantenimiento} ', '<br>',
                    '<p><b>Observaciones:</b> {observaciones}</p>')
            }
        ],
        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('anexo_unico_tbar').items.each(
                    function (item, index, length) {
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)

                    }
                );

                store_combustible_kilometros.load();

                panetree.collapse();
                gridAbastecidos.expand();

                function CallBack(resonse) {
                    if (resonse.success) {
                        // Ext.getStore('id_store_abastecidos').loadData(resonse.grid);
                        // Ext.getStore('storeGraficaAbastecidosId').loadData(resonse.grafica);
                        // Ext.getCmp('grafico_anexo_id').performLayout();
                        // Ext.getCmp('grafico_anexo_id').redraw();
                        // Ext.getCmp('grafico_anexo_id').renderFrame();
                        // Ext.getCmp('grafico_anexo_id').performLayout();
                    }
                }

                // App.PerformServerRequest(Routing.generate('loadAnexoUnicoCombKilometros'),{anexoid: selected[0].data.id}, CallBack);
                // Ext.getCmp('id_panel_info_anexo_unico').expand();
            }
        }
    });

    var gridAbastecidos = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_abastecidos',
        title: 'Combustible y kilómetros',
        region: 'east',
        width: '30%',
        height: '100%',
        collapsible: true,
        collapsed: true,
        columns: [
            {text: 'Fecha', dataIndex: 'fecha', width: 90,
                renderer: function (value, met, record, a, b, c, d) {
                    return Ext.Date.format(value, 'd/m/Y');
                }},
            {text: 'Nro. <br>Tarjeta', dataIndex: 'nro_tarjeta', width: 140},
            {text: 'Kilometraje', dataIndex: 'kilometraje', width: 90},
            {text: 'Comb. <br>Abastecido', dataIndex: 'comb_abast', width: 90},
            {text: 'Comb. Est. <br>Tanque', dataIndex: 'comb_est_tanke', width: 90},
            {text: 'Indice <br>Real', dataIndex: 'indice', width: 80, formatter: "number('0.00')"}
        ],
        store: store_combustible_kilometros,
        listeners: {
            expand: function (This) {
                panetree.collapse();
            }
        }

    });

    // var panelGrafico = Ext.create('Ext.panel.Panel', {
    //     title: 'Gráfico informativo',
    //     id: 'id_grafico_abastecidos',
    //     width: '100%',
    //     height: 280,
    //     bodyPadding: 5,
    //     items: [
    //         {
    //             xtype: 'cartesian',
    //             width: 540,
    //             height: 220,
    //             animate: true,
    //             shadow: false,
    //             floatable: false,
    //             style: 'background: #fff;',
    //             insetPadding: 40,
    //             //interactions: 'crosszoom',
    //             store: Ext.create('Ext.data.JsonStore', {
    //                 storeId: 'storeGraficaAbastecidosId',
    //                 fields: [
    //                     {name: 'fecha'},
    //                     {name: 'indice_mas_5', type: 'float'},
    //                     {name: 'indice', type: 'float'},
    //                     {name: 'indice_menos_5', type: 'float'}
    //                 ],
    //                 data: [
    //                     {fecha: '05/12/2015', indice_mas_5: 10.02, indice: 9.96, indice_menos_5: 9.12},
    //                     {fecha: '10/12/2015', indice_mas_5: 10.02, indice: 9.58, indice_menos_5: 9.12},
    //                     {fecha: '12/12/2015', indice_mas_5: 10.02, indice: 9.54, indice_menos_5: 9.12},
    //                     {fecha: '16/12/2015', indice_mas_5: 10.02, indice: 9.65, indice_menos_5: 9.12},
    //                     {fecha: '19/12/2015', indice_mas_5: 10.02, indice: 10.15, indice_menos_5: 9.12},
    //                     {fecha: '22/12/2015', indice_mas_5: 10.02, indice: 9.42, indice_menos_5: 9.12},
    //                     {fecha: '25/12/2015', indice_mas_5: 10.02, indice: 7.90, indice_menos_5: 9.12},
    //                     {fecha: '28/12/2015', indice_mas_5: 10.02, indice: 5.53, indice_menos_5: 9.12},
    //                     {fecha: '30/12/2015', indice_mas_5: 10.02, indice: 7.44, indice_menos_5: 9.12}
    //                 ]
    //             }),
    //             axes: [
    //                 {
    //                     type: 'numeric',
    //                     fields: ['indice_mas_5', 'indice', 'indice_menos_5'],
    //                     position: 'left',
    //                     grid: true,
    //                     label: {
    //                         renderer: function (v) {
    //                             return v;
    //                         }
    //                     }
    //                 },
    //                 {
    //                     type: 'category',
    //                     fields: 'fecha',
    //                     position: 'bottom',
    //                     grid: true,
    //                     label: {
    //                         rotate: {
    //                             degrees: -45
    //                         }
    //                     }
    //                 }
    //             ],
    //             series: [
    //                 {
    //                     type: 'line',
    //                     axis: 'left',
    //                     title: 'Índice más 5%',
    //                     xField: 'fecha',
    //                     yField: 'indice_mas_5',
    //                     colors: ['#0000F0']
    //                 },
    //                 {
    //                     type: 'line',
    //                     axis: 'left',
    //                     title: 'Índice real',
    //                     xField: 'fecha',
    //                     yField: 'indice',
    //                     colors: ['#FF0000'],
    //                     marker: {
    //                         radius: 2
    //                     },
    //                     tips: {
    //                         trackMouse: true,
    //                         style: 'background: #FFF',
    //                         height: 20,
    //                         renderer: function (storeItem, item) {
    //                             var title = item.series.title;
    //                             this.setTitle('Fecha: ' + storeItem.get('fecha') + '->' + storeItem.get('indice'));
    //                         }
    //                     }
    //                 },
    //                 {
    //                     type: 'line',
    //                     axis: 'left',
    //                     title: 'Índice menos 5%',
    //                     xField: 'fecha',
    //                     yField: 'indice_menos_5',
    //                     colors: ['#0000F0']
    //                 }
    //             ]
    //         }
    //     ]
    // });

    // var panelDerecho = Ext.create('Ext.panel.Panel', {
    //     id: 'id_panel_info_anexo_unico',
    //     title: 'Información',
    //     region: 'east',
    //     collapsible: true,
    //     collapsed: true,
    //     width: 550,
    //     layout: 'border',
    //     bodyPadding: 5,
    //     plugins: 'responsive',
    //     items: [gridAbastecidos,
    //         {
    //             xtype: 'cartesian',
    //             title: 'Gráfico informativo',
    //             id: 'grafico_anexo_id',
    //             region: 'center',
    //             width: 540,
    //             height: 220,
    //             animate: true,
    //             shadow: false,
    //             floatable: true,
    //             style: 'background: #fff;',
    //             insetPadding: 40,
    //             interactions: 'crosszoom',
    //             store: Ext.create('Ext.data.JsonStore', {
    //                 storeId: 'storeGraficaAbastecidosId',
    //                 fields: [
    //                     {name: 'fecha'},
    //                     {name: 'indice_mas_5', type: 'float'},
    //                     {name: 'indice', type: 'float'},
    //                     {name: 'indice_menos_5', type: 'float'}
    //                 ],
    //                 data: [
    //                     {fecha: '05/12/2015', indice_mas_5: 10.02, indice: 9.96, indice_menos_5: 9.12},
    //                     {fecha: '10/12/2015', indice_mas_5: 10.02, indice: 9.58, indice_menos_5: 9.12},
    //                     {fecha: '12/12/2015', indice_mas_5: 10.02, indice: 9.54, indice_menos_5: 9.12},
    //                     {fecha: '16/12/2015', indice_mas_5: 10.02, indice: 9.65, indice_menos_5: 9.12},
    //                     {fecha: '19/12/2015', indice_mas_5: 10.02, indice: 10.15, indice_menos_5: 9.12},
    //                     {fecha: '22/12/2015', indice_mas_5: 10.02, indice: 9.42, indice_menos_5: 9.12},
    //                     {fecha: '25/12/2015', indice_mas_5: 10.02, indice: 7.90, indice_menos_5: 9.12},
    //                     {fecha: '28/12/2015', indice_mas_5: 10.02, indice: 5.53, indice_menos_5: 9.12},
    //                     {fecha: '30/12/2015', indice_mas_5: 10.02, indice: 7.44, indice_menos_5: 9.12}
    //                 ]
    //             }),
    //             axes: [
    //                 {
    //                     type: 'numeric',
    //                     fields: ['indice_mas_5', 'indice', 'indice_menos_5'],
    //                     position: 'left',
    //                     grid: true,
    //                     label: {
    //                         renderer: function (v) {
    //                             return v;
    //                         }
    //                     }
    //                 },
    //                 {
    //                     type: 'category',
    //                     fields: 'fecha',
    //                     position: 'bottom',
    //                     grid: true,
    //                     label: {
    //                         rotate: {
    //                             degrees: -45
    //                         }
    //                     }
    //                 }
    //             ],
    //             series: [
    //                 {
    //                     type: 'line',
    //                     axis: 'left',
    //                     title: 'Índice más 5%',
    //                     xField: 'fecha',
    //                     yField: 'indice_mas_5',
    //                     colors: ['#0000F0']
    //                 },
    //                 {
    //                     type: 'line',
    //                     axis: 'left',
    //                     title: 'Índice real',
    //                     xField: 'fecha',
    //                     yField: 'indice',
    //                     colors: ['#FF0000'],
    //                     marker: {
    //                         radius: 2
    //                     },
    //                     tips: {
    //                         trackMouse: true,
    //                         style: 'background: #FFF',
    //                         height: 20,
    //                         renderer: function (storeItem, item) {
    //                             var title = item.series.title;
    //                             this.setTitle('Fecha: ' + storeItem.get('fecha') + '->' + storeItem.get('indice'));
    //                         }
    //                     }
    //                 },
    //                 {
    //                     type: 'line',
    //                     axis: 'left',
    //                     title: 'Índice menos 5%',
    //                     xField: 'fecha',
    //                     yField: 'indice_menos_5',
    //                     colors: ['#0000F0']
    //                 }
    //             ]
    //         }
    //     ]
    // });

    var _panel_anexo_unico = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_anexo_unico',
        title: 'Anexos Únicos',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid_anexo_unico, gridAbastecidos]
    });
    App.render(_panel_anexo_unico);
});
