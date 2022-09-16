let cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
    clicksToEdit: 1
});

Ext.onReady(function () {

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
                grid.setDisabled(!record);
            }
        }
    });

    let store_parte_diario = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_parte_diario',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'servicioid', type: 'string'},
            {name: 'nombre_servicio', type: 'string'},
            {name: 'codigo_cliente', type: 'number'},
            {name: 'control', type: 'string'},
            {name: 'ruta', type: 'string'},
            {name: 'osde', type: 'string'},
            {name: 'oace', type: 'string'},
            {name: 'folio', type: 'string'},
            {name: 'nunidadid', type: 'string'},
            {name: 'nombreunidadid', type: 'string'},
            {name: 'provicianid', type: 'string'},
            {name: 'nombreprovicianid', type: 'string'},
            {name: 'plan_diario', type: 'number'},
            {name: 'consumo_total_dia', type: 'number'},
            {name: 'real_plan', type: 'number'},
            {name: 'porciento', type: 'number'},
            {name: 'real_plan_acum', type: 'number'},
            {name: 'porcientoacumulado', type: 'number'},

            {name: 'acumulado_real', type: 'number'},
            {name: 'acumulado_plan', type: 'number'},
            {name: 'acumulado_real_pico', type: 'string'},
            {name: 'acumulado_plan_pico', type: 'string'},
            {name: 'plan_mes', type: 'number'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/parte_diario/getParte'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation) {
                operation.setParams({
                    fecha: Ext.util.Format.date(Ext.getCmp('fecha_parte').getValue(), 'Y-m-d'),
                    unidadid: (Ext.getCmp('paneltree').getSelectionModel().getLastSelected() !== undefined) ? Ext.getCmp('paneltree').getSelectionModel().getLastSelected().data.id : null,
                    provincia: Ext.getCmp('comboProvince').getValue()
                });
            }
        }
    });

    let store_provincia = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_parte_diario',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/provincia/list'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });

    let _btn_partediario = Ext.create('Ext.button.Button', {
        id: '_btn_partediario',
        text: 'Obtener Parte',
        glyph: 0xf062,
        handler: function (This, e) {
            let fechaParte = Ext.getCmp('fecha_parte').getValue();
            let unidadidParte = Ext.getCmp('paneltree').getSelectionModel().getLastSelected();

            if (!fechaParte || unidadidParte === undefined) {
                App.showAlert('Asegúrese de seleccionar la unidad y la fecha correspondiente al parte', 'warning', 3000);
            } else {
                store_parte_diario.load();
            }
        }
    });

    let _btn_Print = Ext.create('Ext.button.Button', {
        id: 'parte_diario_energia_btn_print',
        text: 'Imprimir',
        glyph: 0xf02f,
        handler: function (This, e) {
            let store = Ext.getCmp('grid_parte_diario_energia').getStore();
            let obj = {};
            let send = [];

            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });

            obj.store = Ext.encode(send);
            obj.fecha = Ext.getCmp('fecha_parte').getValue();
            let url = App.buildURL('/portadores/parte_diario/print');
            App.request('POST', url, obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        let newWindow = window.open('', '', 'width=1200, height=700'),
                            document = newWindow.document.open();
                        document.write(response.html);
                        setTimeout(() => {
                            newWindow.print();
                        }, 500);
                        document.close();
                    }
                }, null, null, true
            );
        }
    });

    let _btnExport = Ext.create('Ext.button.Button', {
        id: 'planificacion_combustible_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        // disabled: true,
        handler: function (This, e) {
            let store = Ext.getCmp('grid_parte_diario_energia').getStore();
            let obj = {};
            let send = [];

            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });

            obj.store = Ext.encode(send);
            obj.fecha = Ext.getCmp('fecha_parte').getValue();
            let url = App.buildURL('/portadores/parte_diario/print');
            App.request('POST', url, obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                    }
                },
                function (response) { // failure_callback
                }
            );
        }
    });

    let grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_parte_diario_energia',
        store: store_parte_diario,
        features: [{
            ftype: 'summary',
            // dock: 'bottom'
        }],
        columns: [
            {
                text: '<strong>Provincia</strong>',
                dataIndex: 'nombreprovicianid',
                filter: 'string',
                width: 150,
                align: 'center',
                summaryType: 'count',
                summaryRenderer: function (value) {
                    return 'Totales';
                },
            }, {
                text: '<strong>Municipio</strong>',
                dataIndex: 'municipio',
                filter: 'string',
                width: 150,
                align: 'center'
            },
            {
                text: '<strong>OACE</strong>',
                filter: 'string',
                width: 80,
                align: 'center',
                dataIndex: 'oace'
            },
            {
                text: '<strong>OSDE</strong>', filter: 'string', width: 80, align: 'center', dataIndex: 'osde'
            },
            {
                text: '<strong>CODCLI</strong>',
                dataIndex: 'codigo_cliente',
                filter: 'string',
                width: 80,
                align: 'center'
            },
            {
                text: '<strong>Control</strong>',
                dataIndex: 'control',
                filter: 'string',
                width: 100,
                align: 'center'
            },
            {
                text: '<strong>Ruta</strong>', dataIndex: 'ruta', filter: 'string', width: 80, align: 'center'
            },
            {
                text: '<strong>Folio</strong>', dataIndex: 'folio', filter: 'string', width: 80, align: 'center'
            },
            {
                text: '<strong>Nombre del Servicio</strong>',
                dataIndex: 'nombre_servicio',
                filter: 'string',
                width: 200,
                align: 'center'
            },
            {
                text: '<strong>Nombre de la Empresa</strong>',
                dataIndex: 'nombreunidadid',
                filter: 'string',
                width: 200,
                align: 'center'
            },
            {
                text: '<strong>Plan Total del Mes </br>(MWh)</strong>',
                dataIndex: 'plan_mes',
                filter: 'string',
                width: 150,
                id: 'plan_mes',
                align: 'center',
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                },
            },
            {
                text: '<strong>Plan Acumulado </br>(MWh)</strong>',
                dataIndex: 'acumulado_plan',
                filter: 'string',
                width: 150,
                align: 'center',
                id: 'plan_acumulado',
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                },
            },
            {
                text: '<strong>Real acumulado</br>(MWh)</strong>',
                dataIndex: 'acumulado_real',
                width: 150,
                align: 'center',
                sortable: false,
                groupable: false,
                summaryType: 'sum',
                id: 'real_acumulado',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                },
            }, {
                text: '<strong>Real-Plan Acumulado</br>(MWh)</strong>',
                dataIndex: 'real_plan_acum',
                width: 150,
                align: 'center',
                editor: {xtype: 'numberfield'},
                sortable: false,
                id: 'real_plan',
                groupable: false,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                },
            }, {
                text: '<strong>% Acumulado</br>(MWh)</strong>',
                dataIndex: 'porcientoacumulado',
                width: 150,
                align: 'center',
                sortable: false,
                groupable: false,
                id: 'porciento_acumulado',
                summaryType: 'average',
                renderer: function (value) {
                    return '<strong>' + value + '%</strong>';
                },
                summaryRenderer: function (value, summaryData, dataIndex) {
                    let plan_acumulado = summaryData.plan_acumulado;
                    let real_acumulado = summaryData.real_acumulado;

                    let porciento = (real_acumulado / plan_acumulado) * 100;
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(porciento, '0.00'));
                },
            },
            {
                text: '<strong>Plan día</br>(MWh)</strong>',
                dataIndex: 'plan_diario',
                filter: 'string',
                width: 100,
                align: 'center',
                sortable: false,
                groupable: false,
                id:'plan_dia',
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                },
            },
            {
                text: '<strong>Real día</br>(MWh)</strong>',
                dataIndex: 'consumo_total_dia',
                filter: 'string',
                width: 100,
                align: 'center',
                id:'real_dia',
                sortable: false,
                groupable: false,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.0000'));
                },
            },
            {
                text: '<strong>Real-Plan</strong>',
                dataIndex: 'real_plan',
                filter: 'string',
                width: 100,
                align: 'center',
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                },
            },
            {
                text: '<strong>%</strong>', dataIndex: 'porciento', filter: 'string', width: 100, align: 'center',
                summaryType: 'average',
                renderer: function (value) {
                    return '<strong>' + value + '%</strong>';
                },
                summaryRenderer: function (value, summaryData, dataIndex) {
                    let plan_dia = summaryData.plan_dia;
                    let real_dia = summaryData.real_dia;

                    let porciento = (real_dia / plan_dia) * 100;
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(porciento, '0.00'));
                },
            }
        ],
        region: 'center',
        disabled: true,
        tbar: {
            id: 'parte_diario_energia_tbar',
            height: 36,
            items: [{
                xtype: 'datefield',
                id: 'fecha_parte',
                name: 'fecha_parte',
            }, {
                xtype: 'combobox',
                id: 'comboProvince',
                name: 'comboProvince',
                fieldLabel: 'Provincia',
                store: store_provincia,
                displayField: 'nombre',
                labelWidth: 60,
                valueField: 'id',
                queryMode: 'local',
                forceSelection: true,
                triggerAction: 'all',
                emptyText: 'Seleccione...',
                editable: true, triggers: {
                    clear: {
                        cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                        hidden: false,
                        handler: function () {
                            this.setValue(null);
                            // indFormulas.clearFilter();
                            this.reset();
                        }
                    }
                }


            }, '-', _btn_partediario, _btn_Print, _btnExport]
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('parte_diario_energia_tbar').items.each(
                    function (item, index, length) {
                        item.setDisabled(item.getXType() === 'button' && selected.length === 0)
                    }
                );
            }
        }
    });

    let _panel = Ext.create('Ext.panel.Panel', {
        id: 'parte_diario_energia_panel_id',
        title: 'Parte Diario de Energía Eléctrica',
        border: true,
        frame: true,
        layout: 'border',
        items: [paneltree, grid]
    });
    App.render(_panel);
});