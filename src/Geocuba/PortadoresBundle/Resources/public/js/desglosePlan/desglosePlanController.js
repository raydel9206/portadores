
Ext.onReady(function () {

    Ext.define('desgloseG', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'fecha_desglose', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'plan_pico', type: 'float'},
            {name: 'plan_diario', type: 'float'},
            {name: 'perdidasT', type: 'float'}
        ]
    });
    Ext.define('desgloseGG', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'dia', type: 'number'},
            {name: 'anno_contable', type: 'number'},
            {name: 'Enero_plan_pico', type: 'number'},
            {name: 'Enero_plan_diario', type: 'number'},
            {name: 'Febrero_plan_pico', type: 'number'},
            {name: 'Febrero_plan_diario', type: 'number'},
            {name: 'Marzo_plan_pico', type: 'number'},
            {name: 'Marzo_plan_diario', type: 'number'},
            {name: 'Abril_plan_pico', type: 'number'},
            {name: 'Abril_plan_diario', type: 'number'},
            {name: 'Mayo_plan_pico', type: 'number'},
            {name: 'Mayo_plan_diario', type: 'number'},
            {name: 'Junio_plan_pico', type: 'number'},
            {name: 'Junio_plan_diario', type: 'number'},
            {name: 'Julio_plan_pico', type: 'number'},
            {name: 'Julio_plan_diario', type: 'number'},
            {name: 'Agosto_plan_pico', type: 'number'},
            {name: 'Agosto_plan_diario', type: 'number'},
            {name: 'Septiembre_plan_pico', type: 'number'},
            {name: 'Septiembre_plan_diario', type: 'number'},
            {name: 'Octubre_plan_pico', type: 'number'},
            {name: 'Octubre_plan_diario', type: 'number'},
            {name: 'Noviembre_plan_pico', type: 'number'},
            {name: 'Noviembre_plan_diario', type: 'number'},
            {name: 'Diciembre_plan_pico', type: 'number'},
            {name: 'Diciembre_plan_diario', type: 'number'}
        ]
    });
    Ext.define('desglose_', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'mes', type: 'string'},
            {name: 'plan_total', type: 'number'},
            {name: 'plan_pico', type: 'number'}

        ]
    });
    Ext.define('desgloseQQQQQ', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'nromes', type: 'int'},
            {name: 'mes', type: 'string'},
            {name: 'cant_dias', type: 'int'}
        ]
    });

    let meses = Ext.create('Ext.data.Store', {
        model: 'desgloseQQQQQ',
        id: 'id_store_meses',
        data: [
            {id: '1', nromes: 1, mes: 'Enero', cant_dias: 31},
            {id: '2', nromes: 2, mes: 'Febrero', cant_dias: 28},
            {id: '3', nromes: 3, mes: 'Marzo', cant_dias: 31},
            {id: '4', nromes: 4, mes: 'Abril', cant_dias: 30},
            {id: '5', nromes: 5, mes: 'Mayo', cant_dias: 31},
            {id: '6', nromes: 6, mes: 'Junio', cant_dias: 30},
            {id: '7', nromes: 7, mes: 'Julio', cant_dias: 31},
            {id: '8', nromes: 8, mes: 'Agosto', cant_dias: 31},
            {id: '9', nromes: 9, mes: 'Septiembre', cant_dias: 30},
            {id: '10', nromes: 10, mes: 'Octubre', cant_dias: 31},
            {id: '11', nromes: 11, mes: 'Noviembre', cant_dias: 30},
            {id: '12', nromes: 12, mes: 'Diciembre', cant_dias: 31}
        ]
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

    let storeServicios = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_servicios',
        fields: [
            {name: 'id'},
            {name: 'nombre_servicio'},
            {name: 'codigo_cliente'},
            {name: 'factor_metrocontador'},
            {name: 'MaximaDemandaContratada'},
            {name: 'control'},
            {name: 'ruta'},
            {name: 'folio'},
            {name: 'direccion'},
            {name: 'factor_combustible'},
            {name: 'indice_consumo'},
            {name: 'consumo_prom_anno'},
            {name: 'consumo_prom_plan'},
            {name: 'consumo_prom_real'},
            {name: 'capac_banco_transf'},
            {name: 'tipo_servicio'},
            {name: 'turno_trabajo'},
            {name: 'nunidadid'},
            {name: 'nombreunidadid'},
            {name: 'provicianid'},
            {name: 'nombreprovicianid'},
            {name: 'tarifaid'},
            {name: 'nombretarifaid'},
            {name: 'nactividadid'},
            {name: 'nombrenactividadid'},
            {name: 'num_nilvel_actividadid'},
            {name: 'nombreum_nilvel_actividadid'},
            {name: 'servicio_mayor'},
            {name: 'servicio_prepago'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/servicio/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation) {
                operation.setParams({
                    unidadid: (Ext.getCmp('paneltree').getSelectionModel().getLastSelected() !== undefined) ? Ext.getCmp('paneltree').getSelectionModel().getLastSelected().data.id : null,
                });
            }
        }
    });

    let paneltree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: '15%',
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
            selectionchange: function (This, record, tr, rowIndex, e, eOpts) {
                store_servicios_desglose.removeAll();
                storeDesMes.removeAll();
                gridservicios.getStore().load();
                gridservicios.enable();
            }
        }
    });

    let store_servicios_desglose = Ext.create('Ext.data.JsonStore', {
        frame: true,
        storeId: 'id_store_desgloseelectricidad',
        fields: [
            {name: 'id'},
            {name: 'plan_total', type: 'float'},
            {name: 'plan_pico', type: 'float'},
            {name: 'perdidasT', type: 'float'},
            {name: 'mes'},
            {name: 'nombre_mes'},
            {name: 'fecha'},
            {name: 'idservicio'}

        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/desglosePlan/loaddesgloseservicios'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
    });

    let desglose_completo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_desgloseelectricidadAll',
        fields: [
            {name: 'id'},
            {name: 'plan_diario'},
            {name: 'plan_pico'},
            {name: 'mes'},
            {name: 'nombre_mes'},
            {name: 'fecha'},
            {name: 'id_desglose_servicio'}
        ],

        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/desglosePlan/loaddesglose'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false
    });

    let gridservicios = Ext.create('Ext.grid.Panel', {
        id: 'gridservicios',
        region: 'west',
        width: '15%',
        columnLines: false,
        hideHeaders: true,
        title: 'Servicios',
        // header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'},},
        collapsible: true,
        collapsed: false,
        disabled: true,
        store: storeServicios,
        columns: [
            {
                text: '<strong>Servicios</strong>',
                dataIndex: 'nombre_servicio',
                align: 'center',
                flex: 1
            }
        ],

        listeners: {
            selectionchange: function (This, selected, e) {
                if (selected.length !== 0) {
                    store_servicios_desglose.load({
                        params: {
                            idservicios: selected[0].id,
                        }
                    });
                    store_servicios_desglose.sort('mes', 'ASC');

                    Ext.getCmp('id_grid_desglosecenter').expand();
                    Ext.getCmp('grid_meses').collapse();
                    grid_desglosecenter.enable();
                    grid_desglosecenter.getSelectionModel().deselectAll();
                }
            }
        }
    });


    let storeDesMes = Ext.create('Ext.data.JsonStore', {
        id: 'storeDesMes',
        fields: [
            {name: 'fecha_desglose', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'plan_pico', type: 'float'},
            {name: 'plan_diario', type: 'float'},
            {name: 'perdidasT', type: 'float'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/desglosePlan/loaddesglosemes'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                let desglose = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected().data.id;
                let servicio = Ext.getCmp('gridservicios').getSelectionModel().getLastSelected().data.id;
                operation.setParams({
                    id_desglose: desglose,
                    servicio: servicio
                });

            },
            load: function (This, records, successful, operation) {
                if (records) {
                    if (records.length === 0) {
                        App.showAlert('El mes seleccionado no ha sido desglosado por dias', 'warning', 3500);
                        Ext.getCmp('grid_meses').collapse();
                    } else {
                        Ext.getCmp('grid_meses').expand();
                    }
                } else {
                    Ext.getCmp('grid_meses').collapse();
                }
            }
        }
    });

    let grid_desglosecenter = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_desglosecenter',
        region: 'center',
        width: '35%',
        disabled: true,
        border: true,
        // header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'},},
        columnLines: true,
        title: 'Desglose Plan Mensual Electricidad',
        features: [{
            ftype: 'summary'
        }],
        store: store_servicios_desglose,
        columns: [
            {
                flex: 1,
                xtype: 'gridcolumn',
                text: '<strong>Meses</strong>',
                dataIndex: 'nombre_mes',
                align: 'center',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return '<strong>Totales</strong>'
                }
            }, {
                text: '<strong>Plan en kWhats</strong>',
                columns: [
                    {
                        header: '<strong>Total</strong>',
                        width: 100,
                        dataIndex: 'plan_total',
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return Ext.util.Format.round(value, 2) + ' kWhats';
                        }
                    }, {
                        header: '<strong>Pico</strong>',
                        dataIndex: 'plan_pico',
                        width: 100,
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return Ext.util.Format.round(value, 2) + ' kWhats';
                        }
                    }, {
                        header: '<strong>Perdidas</strong>',
                        id: 'perdidas',
                        dataIndex: 'perdidasT',
                        width: 100,
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return Ext.util.Format.round(value, 2) + ' kWhats';
                        }
                    }
                ]
            }
        ],
        tbar: [
            {
                text: 'Desglosar',
                id: 'id_tbar_desglose',
                glyph: 0xf080,
                handler: function () {
                    Ext.create('Ext.window.Window', {
                        id: 'desglosemeses_id',
                        title: 'Desglose por meses ',
                        height: 270,
                        modal: true,
                        width: 340,
                        layout: 'fit',
                        items: [
                            {
                                xtype: 'fieldset',
                                layout: {
                                    type: 'vbox',
                                    labelAlign: 'top'
                                },
                                items: [
                                    {
                                        fieldLabel: 'Meses',
                                        xtype: 'combobox',
                                        store: meses,
                                        displayField: 'mes',
                                        valueField: 'nromes',
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione ...',
                                        editable: false,
                                        allowBlank: false,
                                        margin: '4 4 4 4',
                                        id: 'id_meses_desgloseserver'
                                    }, {
                                        xtype: 'numberfield',
                                        decimalSeparator: '.',
                                        name: 'anno_desglose',
                                        fieldLabel: 'Año',
                                        id: 'anno_desglose',
                                        value: App.selected_year,
                                        margin: '4 4 4 4',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        allowBlank: false
                                    },
                                    {
                                        xtype: 'numberfield',
                                        decimalSeparator: '.',
                                        name: 'plan_total',
                                        fieldLabel: 'Plan Total',
                                        id: 'id_plan_total',
                                        margin: '4 4 4 4',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        allowBlank: false
                                    }, {
                                        xtype: 'numberfield',
                                        decimalSeparator: '.',
                                        name: 'plan_pico',
                                        id: 'id_plan_pico',
                                        fieldLabel: 'Plan Pico',
                                        margin: '4 4 4 4',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        allowBlank: false
                                    }, {
                                        xtype: 'numberfield',
                                        decimalSeparator: '.',
                                        name: 'perdidasT',
                                        id: 'id_perdidasT',
                                        fieldLabel: 'Perdidas Trasnformacion',
                                        margin: '4 4 4 4',
                                        value: 0,
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        allowBlank: false
                                    }
                                ]
                            }
                        ],
                        buttons: [
                            {
                                text: 'Aceptar',
                                width: 70,
                                handler: function () {
                                    let url = App.buildURL('/portadores/desglosePlan/desgloseservicios');

                                    let mes = Ext.getCmp('id_meses_desgloseserver').getValue();
                                    let plan_total = Ext.getCmp('id_plan_total').getValue();
                                    let anno_desglose = Ext.getCmp('anno_desglose').getValue();
                                    let plan_pico = Ext.getCmp('id_plan_pico').getValue();
                                    let perdidasT = Ext.getCmp('id_perdidasT').getValue();
                                    let selection = Ext.getCmp('gridservicios').getSelectionModel().getLastSelected();
                                    store_servicios_desglose.filter('idservicios', selection.data.id);

                                    if (plan_pico >= plan_total) {
                                        App.showAlert('El plan Pico  no debe ser mayor o igual que el Plan Total', 'danger', 3500);
                                    } else if (store_servicios_desglose.findExact('mes', mes) !== -1 && store_servicios_desglose.findExact('anno', anno_desglose) !== -1) {
                                        App.showAlert('Ya ha desglosado el mes seleccionado', 'warning', 3500);
                                    } else {

                                        let valores = {};
                                        valores.mes = mes;
                                        valores.anno_desglose = anno_desglose;
                                        valores.plan_total = plan_total;
                                        valores.plan_pico = plan_pico;
                                        valores.perdidasT = perdidasT;
                                        valores.servicio = selection.data.id;

                                        App.request('POST', url, valores, null, null,
                                            function (response) {
                                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                    store_servicios_desglose.filter('idservicios', selection.data.id);
                                                    store_servicios_desglose.load({
                                                        params: {
                                                            idservicios: selection.data.id,
                                                        }
                                                    });
                                                    Ext.getCmp('desglosemeses_id').close()
                                                } else {
                                                    Ext.getCmp('desglosemeses_id').show();
                                                }
                                            },
                                            function (response) {// failure_callback
                                                Ext.getCmp('desglosemeses_id').show()
                                            });
                                    }
                                }
                            },
                            {
                                text: 'Cancelar',
                                width: 70,
                                handler: function () {
                                    Ext.getCmp('desglosemeses_id').close()
                                }
                            }
                        ]
                    }).show();
                }
            },
            {
                text: 'Modificar',
                id: 'id_tbar_mod',
                itemId: 'modif',
                disabled: true,
                glyph: 0xf303,
                handler: function () {
                    let selec = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected();
                    if (!selec) {
                        App.showAlert('Debe Seleccionar la última autolectura tomada', 'warning', 3500);
                    }else{
                        let win = Ext.create('Ext.window.Window', {
                            id: 'desglosemeses_idMod',
                            title: 'Modificar Desglose ' + selec.data.nombre_mes,
                            height: 270,
                            width: 330,
                            modal: true,
                            layout: 'fit',
                            items: [
                                {
                                    xtype: 'fieldset',
                                    layout: {
                                        type: 'vbox',
                                        labelAlign: 'top'
                                    },
                                    items: [
                                        {
                                            xtype: 'numberfield',
                                            decimalSeparator: '.',
                                            name: 'plan_total',
                                            fieldLabel: 'Plan Total',
                                            //columnWidth: 0.25,
                                            id: 'id_plan_totalMod',
                                            margin: '4 4 4 4',
                                            // width: 100,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            allowBlank: false,
                                            value: selec.data.plan_total
                                        }, {
                                            xtype: 'numberfield',
                                            decimalSeparator: '.',
                                            name: 'plan_pico',
                                            //columnWidth: 0.25,
                                            id: 'id_plan_picoMod',
                                            fieldLabel: 'Plan Pico',
                                            margin: '4 4 4 4',
                                            // width: 100,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            allowBlank: false,
                                            value: selec.data.plan_pico
                                        }, {
                                            xtype: 'numberfield',
                                            decimalSeparator: '.',
                                            name: 'perdidasT',
                                            //columnWidth: 0.25,
                                            id: 'perdidasT',
                                            fieldLabel: 'Perdidas',
                                            margin: '4 4 4 4',
                                            // width: 100,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            allowBlank: false,
                                            value: selec.data.perdidasT
                                        }

                                    ]
                                }


                            ],
                            buttons: [
                                {
                                    text: 'Aceptar',
                                    width: 70,
                                    handler: function () {
                                        let mes = selec.data.mes;
                                        let id = selec.data.id;
                                        let plan_total = Ext.getCmp('id_plan_totalMod').getValue();
                                        let plan_pico = Ext.getCmp('id_plan_picoMod').getValue();
                                        let perdidasT = Ext.getCmp('perdidasT').getValue();

                                        if (plan_pico >= plan_total) {
                                            App.showAlert('El plan Pico  no debe ser mayor que el Plan Total', 'warning', 3500);
                                        } else {
                                            let selection = Ext.getCmp('gridservicios').getSelectionModel().getLastSelected();
                                            let valores = {};
                                            valores.mes = mes;
                                            valores.id = id;
                                            valores.accion = 'Mod';
                                            valores.plan_total = plan_total;
                                            valores.perdidasT = perdidasT;
                                            valores.plan_pico = plan_pico;
                                            valores.servicio = selection.data.id;
                                            let url = App.buildURL('/portadores/desglosePlan/desgloseservicios');
                                            App.request('POST', url, valores, null, null,
                                                function (response) {
                                                    let selected = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected().data;
                                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                        store_servicios_desglose.filter('idservicios', selection.data.id);
                                                        store_servicios_desglose.load({
                                                            params: {
                                                                idservicios: selection.data.id,
                                                            },
                                                            callback: function () {
                                                                storeDesMes.load(
                                                                    {
                                                                        callback: function (r, options, success) {
                                                                            let plan_diario = 0;
                                                                            let plan_pico = 0;
                                                                            let perdidasT = 0;
                                                                            let plan_total = selected.plan_total;
                                                                            let perdidas_total = selected.perdidasT;
                                                                            let plan_pico_total = selected.plan_pico;
                                                                            for (let i = 0; i < r.length; i++) {
                                                                                plan_diario += r[i].data.plan_diario;
                                                                                plan_pico += r[i].data.plan_pico;
                                                                                perdidasT += r[i].data.perdidasT;
                                                                            }

                                                                            Ext.getCmp('dif_pd').setValue((plan_total - plan_diario).toFixed(2));
                                                                            Ext.getCmp('dif_pp').setValue((plan_pico_total - plan_pico).toFixed(2));
                                                                            Ext.getCmp('dif_p').setValue((perdidas_total - perdidasT).toFixed(2));
                                                                        }
                                                                    }
                                                                );
                                                                Ext.getCmp('grid_meses').getView().refresh();
                                                            }
                                                        });
                                                        win.close();
                                                    } else {
                                                        win.show();
                                                    }
                                                },
                                                function (response) {// failure_callback
                                                    win.show()
                                                });
                                            win.close()
                                        }
                                    }
                                }, {
                                    text: 'Cancelar',
                                    width: 70,
                                    handler: function () {
                                        win.close()
                                    }
                                }
                            ]// Let's put an empty grid in just to illustrate fit layout
                        }).show();
                    }
                }
            },
            {
                text: 'Desglosar Lineal',
                id: 'id_tbar_desglose_lineal',
                disabled: true,
                itemId: 'desglosar_lineal',
                glyph: 0xf201,
                handler: function () {
                    let selection = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected();
                    if (!selection) {
                        App.showAlert('Debe Seleccionar el mes a desglosar', 'warning', 3500);
                    } else {
                        desglose_completo.filter('id_desglose_servicio', selection.data.id);
                        let select = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected();
                        let objids = {};
                        objids.id_desglose = select.data.id;
                        let url = App.buildURL('/portadores/desglosePlan/loaddesglosemes');
                        App.request('GET', url, objids, null, null,
                            function (response) {
                                if (response && response.hasOwnProperty('success') && response.success) {
                                    if (response.rows.length !== 0) {
                                        App.showAlert('Mes desglosado. Modifíquelo', 'warning', 3500);
                                    } else {
                                        let selection_ = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected();
                                        let plan = parseFloat(select.data.plan_total);
                                        let pico = parseFloat(select.data.plan_pico);
                                        let perdidasT = parseFloat(select.data.perdidasT);
                                        Ext.create('Ext.window.Window', {
                                            id: 'desglose_id',
                                            title: 'Desglose ' + 'de ' + selection_.data.nombre_mes,
                                            height: 500,
                                            modal: true,
                                            width: 420,
                                            layout: 'fit',
                                            items: [{
                                                xtype: 'gridpanel',
                                                id: 'grid_eneroaa',
                                                frame: true,
                                                width: 200,
                                                height: 200,
                                                store: Ext.create('Ext.data.Store', {
                                                    model: 'desgloseG'
                                                }),
                                                tbar: [{
                                                    xtype: 'displayfield',
                                                    value: 0,
                                                    fieldLabel: 'Diferencia total',
                                                    id: 'display_total'
                                                }, '-', {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Diferencia pico',
                                                    value: 0,
                                                    id: 'display_pico'
                                                }],
                                                enableColumnHide: true,
                                                columns: [
                                                    {
                                                        text: 'Día',
                                                        width: 100,
                                                        sortable: true,
                                                        dataIndex: 'dia',
                                                        xtype: 'datecolumn',
                                                        format: 'l,j',
                                                        summaryRenderer: function (value, summaryData, dataIndex) {
                                                            return '<strong>Totales</strong>'
                                                        }
                                                    },
                                                    {
                                                        text: 'Plan Diario',
                                                        width: 100,
                                                        sortable: true,
                                                        dataIndex: 'plan_diario',
                                                        summaryType: 'sum',
                                                        editor: Ext.create('Ext.form.field.Number', {
                                                            decimalPrecision: 3,
                                                            minValue: 0
                                                        }),
                                                        renderer: function (value) {
                                                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                                                        }
                                                    },
                                                    {
                                                        text: 'Plan del Pico',
                                                        width: 100,
                                                        sortable: true,
                                                        dataIndex: 'plan_pico',
                                                        summaryType: 'sum',
                                                        editor: Ext.create('Ext.form.field.Number', {
                                                            decimalPrecision: 3,
                                                            minValue: 0
                                                        }),
                                                        renderer: function (value) {
                                                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                                                        }
                                                    },
                                                    {
                                                        text: 'Perdidas',
                                                        width: 100,
                                                        sortable: true,
                                                        dataIndex: 'perdidasT',
                                                        summaryType: 'sum',
                                                        editor: Ext.create('Ext.form.field.Number', {
                                                            minValue: 0
                                                        })
                                                    }
                                                ],
                                                listeners: {
                                                    beforerender: function (This, eOpts) {
                                                        let selectiongrid = Ext.getCmp('grid_eneroaa').getStore();
                                                        let id_desglose = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected().data.id;
                                                        Ext.getCmp('grid_eneroaa').getView().refresh();

                                                        let nromes = selection_.data.mes;
                                                        let record = meses.findRecord('nromes', nromes);
                                                        let cant_dias = record.data.cant_dias;

                                                        let valor_plandiario = (plan / cant_dias).toFixed(2);
                                                        let valor_planpico = (pico / cant_dias).toFixed(2);
                                                        let valor_perdidasT = (perdidasT / cant_dias).toFixed(2);

                                                        let okperdidas = valor_perdidasT * cant_dias;
                                                        let restoperdidas = perdidasT - okperdidas;

                                                        for (let i = 1; i < cant_dias + 1; i++) {
                                                            let fecha = new Date(App.selected_year, nromes - 1);
                                                            let fecha1 = new Date(fecha.getFullYear(), fecha.getMonth(), i);
                                                            Ext.getCmp('grid_eneroaa').getStore().add({
                                                                dia: fecha1,
                                                                plan_diario: valor_plandiario,
                                                                plan_pico: valor_planpico,
                                                                perdidasT: valor_perdidasT

                                                            });
                                                        }

                                                        let dist = calcularDistribucion(selectiongrid, id_desglose, selection_);
                                                        selectiongrid.each(function (r) {
                                                            let dia = Ext.util.Format.date(r.get('dia'), 'D,j');
                                                            let valordia = dia.substring(4, 6);
                                                            //TODO Arreglar sumar el resto
                                                            if (parseInt(valordia) === cant_dias) {
                                                                let var_recor = selectiongrid.findRecord('dia', r.get('dia'));
                                                                var_recor.data.plan_diario = Ext.util.Format.round(((plan - dist[0]) + parseFloat(valor_plandiario)), 2);
                                                                var_recor.data.plan_pico = Ext.util.Format.round(((pico - dist[1]) + parseFloat(valor_planpico)), 2);
                                                                var_recor.data.perdidasT = Ext.util.Format.round((restoperdidas + parseFloat(valor_perdidasT)), 2);
                                                            }
                                                            r.commit();
                                                        }, this);

                                                        let dist1 = calcularDistribucion(selectiongrid, id_desglose, selection_);
                                                        Ext.getCmp('display_total').setValue(Ext.util.Format.round(plan - dist1[0], 2));
                                                        Ext.getCmp('display_pico').setValue(Ext.util.Format.round(pico - dist1[1], 2));
                                                    },
                                                },
                                                selType: 'cellmodel',
                                                plugins: [
                                                    Ext.create('Ext.grid.plugin.CellEditing', {
                                                        clicksToEdit: 1,
                                                        listeners: {
                                                            edit: function (editor, e, eOpts) {
                                                                let selectiongrid = Ext.getCmp('grid_eneroaa').getStore();
                                                                let id_desglose = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected().data.id;
                                                                let distribuido = calcularDistribucion(selectiongrid, id_desglose, selection_);

                                                                Ext.getCmp('display_total').setValue(Ext.util.Format.round(plan - distribuido[0], 2));
                                                                Ext.getCmp('display_pico').setValue(Ext.util.Format.round(pico - distribuido[1], 2));
                                                                let position = e.rowIdx;
                                                                if (e.value > plan) {
                                                                    App.showAlert('El valor del Pico no debe ser mayor que el real', 'warning', 3500);
                                                                    Ext.getCmp('grid_eneroaa').getStore().getAt(position).set('plan_diario', 0);
                                                                    Ext.getCmp('grid_eneroaa').getStore().getAt(position).set('plan_pico', 0);
                                                                }
                                                            }
                                                        }
                                                    })
                                                ]


                                            }],
                                            buttons: [
                                                {
                                                    text: 'Aceptar',
                                                    width: 70,
                                                    handler: function () {
                                                        let selectiongrid = Ext.getCmp('grid_eneroaa').getStore();
                                                        let selected = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected();
                                                        let id_desglose = selected.data.id;
                                                        let distribucion = calcularDistribucion(selectiongrid, id_desglose, selection_);
                                                        let values = [];
                                                        selectiongrid.each(function (r) {
                                                            values = values.concat({
                                                                dia: r.get('dia'),
                                                                plan_pico: r.get('plan_pico'),
                                                                plan_diario: r.get('plan_diario'),
                                                                perdidasT: r.get('perdidasT'),
                                                                nromes: selection_.data.mes,
                                                                id_desglose: id_desglose

                                                            });
                                                        }, this);

                                                        if (distribucion[0] < plan || distribucion[1] < pico) {
                                                            App.showAlert('Por favor revice le falta por desglosar ', 'warning', 3500)
                                                        } else if (distribucion[0] > plan || distribucion[1] > pico) {
                                                            App.showAlert('Por favor revice no puede desglosar mas que el plan asignado ', 'warning', 3500)
                                                        }
                                                        else {
                                                            let urlDes = App.buildURL('/portadores/desglosePlan/desgloselectricidad');
                                                            console.log(values);
                                                            App.request('GET', urlDes, {datos: Ext.encode(values)}, null, null,
                                                                function (response) {
                                                                    if (response && response.hasOwnProperty('success') && response.success) {
                                                                        if (response) {
                                                                            Ext.getCmp('desglose_id').close();
                                                                            Ext.getCmp('grid_meses').expand();
                                                                            let datos_store = {};
                                                                            storeDesMes.load(
                                                                                {
                                                                                    callback: function (r, options, success) {
                                                                                        datos_store = storeDesMes.data.items;
                                                                                        let plan_diario = 0;
                                                                                        let plan_pico = 0;
                                                                                        let perdidasT = 0;
                                                                                        let plan_total = selected[0].data.plan_total;
                                                                                        let perdidas_total = selected[0].data.perdidasT;
                                                                                        let plan_pico_total = selected[0].data.plan_pico;
                                                                                        for (let i = 0; i < r.length; i++) {
                                                                                            plan_diario += r[i].data.plan_diario;
                                                                                            plan_pico += r[i].data.plan_pico;
                                                                                            perdidasT += r[i].data.perdidasT;
                                                                                        }
                                                                                        Ext.getCmp('dif_pd').setValue((plan_total - plan_diario).toFixed(2));
                                                                                        Ext.getCmp('dif_pp').setValue((plan_pico_total - plan_pico).toFixed(2));
                                                                                        Ext.getCmp('dif_p').setValue((perdidas_total - perdidasT).toFixed(2));
                                                                                    }
                                                                                }
                                                                            );
                                                                            Ext.getCmp('grid_meses').getView().refresh();

                                                                        }
                                                                    } else {
                                                                        Ext.getCmp('desglose_id').show();
                                                                    }
                                                                },
                                                                function (response) {// failure_callback
                                                                });
                                                        }

                                                    }
                                                }, {
                                                    text: 'Cancelar',
                                                    width: 70,
                                                    handler: function () {
                                                        Ext.getCmp('desglose_id').close()
                                                    }
                                                }
                                            ]// Let's put an empty grid in just to illustrate fit layout

                                        }).show();
                                    }
                                }
                            },
                            function (response) {
                            }, null, true);
                    }
                }
            }
        ],
        listeners: {
            selectionchange: function (This, selected, e) {
                est.enable();
                let objids = {};
                if (selected.length !== 0) {
                    objids.id_desglose = selected[0].data.id;
                    let datos_store = {};
                    storeDesMes.load({
                        callback: function (r, options, success) {
                            datos_store = storeDesMes.data.items;
                            let plan_diario = 0;
                            let plan_pico = 0;
                            let perdidasT = 0;
                            let plan_total = selected[0].data.plan_total;
                            let perdidas_total = selected[0].data.perdidasT;
                            let plan_pico_total = selected[0].data.plan_pico;
                            for (let i = 0; i < r.length; i++) {
                                plan_diario += r[i].data.plan_diario;
                                plan_pico += r[i].data.plan_pico;
                                perdidasT += r[i].data.perdidasT;
                            }

                            Ext.getCmp('dif_pd').setValue(plan_total - (plan_diario).toFixed(2));
                            Ext.getCmp('dif_pp').setValue(plan_pico_total - (plan_pico).toFixed(2));
                            Ext.getCmp('dif_p').setValue(perdidas_total - (perdidasT).toFixed(2));
                        }
                    });
                }
            }
        }
    });

    grid_desglosecenter.getSelectionModel().on('selectionchange', function (selModel, selections) {
        grid_desglosecenter.down('#modif').setDisabled(selections.length === 0);
        grid_desglosecenter.down('#desglosar_lineal').setDisabled(selections.length === 0);
    });

    let est = Ext.create('Ext.grid.Panel', {
        id: 'grid_meses',
        region: 'east',
        resizable: true,
        disabled: true,
        width: '35%',
        viewConfig: {
            stripeRows: true
        },
        selModel: {
            allowDeselect: true,
            mode: 'SIMPLE'
        },
        border: '10 5 3 10',
        collapsed: true,
        collapsible: true,
        collapseDirection: 'right',
        title: 'Plan de energía por meses y días',
        store: storeDesMes,
        selType: 'cellmodel',
        plugins: [
            Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 1,
                listeners: {
                    beforeedit: function (This, e, eOpts) {
                        let store = est.getStore().getData();
                        if (store.length > 0) {
                            let fechadesglose = Ext.util.Format.date(e.record.data.fecha_desglose, 'Y-m-d');
                            if (fechadesglose < e.record.data.fechaLastLect) {
                                App.showAlert('No puede modificar el desglose anterior a la última autolectura tomada para este servicio', 'warning', 3800);
                                return false;
                            }
                        }
                    },

                    edit: function (editor, e, eOpts) {
                        let store = est.getStore().getData();
                        let position = e.rowIdx;
                        let plan = Ext.getCmp('grid_meses').getStore().getAt(position).get('plan_diario');


                        if (e.value > plan) {
                            App.showAlert('El valor del Pico no debe ser mayor que el real', 'warning', 3500);
                            Ext.getCmp('grid_meses').getStore().getAt(position).set('plan_diario', 0);
                            Ext.getCmp('grid_meses').getStore().getAt(position).set('plan_pico', 0);
                        }

                        let selection = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected();
                        let plan_inicial = selection.data.plan_total;
                        let plan_pico_inicial = selection.data.plan_pico;
                        let perdidas_inicial = selection.data.perdidasT;


                        let suma_pd = 0;
                        let suma_pp = 0;
                        let suma_pt = 0;
                        for (let i = 0; i < store.length; i++) {
                            suma_pd += store.items[i].data.plan_diario;
                            suma_pp += store.items[i].data.plan_pico;
                            suma_pt += store.items[i].data.perdidasT;
                        }

                        Ext.getCmp('dif_pd').setValue((plan_inicial - suma_pd).toFixed(2));
                        Ext.getCmp('dif_pp').setValue((plan_pico_inicial - suma_pp).toFixed(2));
                        Ext.getCmp('dif_p').setValue((perdidas_inicial - suma_pt).toFixed(2));
                    }
                }
            })
        ],
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        }],

        columns: [
            {
                text: 'Día',
                width: 100,
                sortable: true,

                dataIndex: 'fecha_desglose',
                xtype: 'datecolumn',
                format: 'l,j',
                renderer: function (val2, met, record, a, b, c, d) {

                    let ds = Ext.util.Format.date(val2, 'l');
                    if (ds === 'Sábado') {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #EC9D9D;';
                        return Ext.util.Format.date(record.get('fecha_desglose'), 'l-j');
                    }
                    else if (ds === 'Domingo') {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #F22222;';
                        return Ext.util.Format.date(record.get('fecha_desglose'), 'l-j');
                    } else
                        return Ext.util.Format.date(record.get('fecha_desglose'), 'l-j');

                },
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return '<strong>Totales</strong>'
                }
            },
            {
                text: 'Plan Diario',
                width: 100,
                sortable: true,
                dataIndex: 'plan_diario',
                formatter: "number('0.00')",
                editor: Ext.create('Ext.form.field.Number', {
                    decimalSeparator: '.',
                    minValue: 0,
                }),
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            }, {
                text: 'Plan del Pico',
                width: 100,
                sortable: true,
                dataIndex: 'plan_pico',
                summaryType: 'sum',
                formatter: "number('0.00')",
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                },
                editor: Ext.create('Ext.form.field.Number', {
                    decimalSeparator: '.',
                    minValue: 0
                })
            }, {
                text: 'Perdidas',
                width: 100,
                sortable: true,
                dataIndex: 'perdidasT',
                summaryType: 'sum',
                formatter: "number('0.00')",
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                },
                editor: Ext.create('Ext.form.field.Number', {
                    decimalSeparator: '.',
                    minValue: 0
                })
            }
        ],
        tbar: [
            {
                text: 'Guardar',
                id: 'id_tbar_desglose_dias',
                glyph: 0xf0c7,
                handler: function () {
                    let select = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected();
                    let plan = select.data.plan_total;
                    let pico = select.data.plan_pico;
                    let selectiongridmod = Ext.getCmp('grid_meses').getStore();
                    let values1 = [];
                    selectiongridmod.each(function (r) {
                        values1 = values1.concat({
                            id: r.get('id'),
                            dia: r.get('dia'),
                            plan_pico: r.get('plan_pico'),
                            plan_diario: r.get('plan_diario'),
                            perdidasT: r.get('perdidasT'),
                            nromes: select.data.mes
                        });
                    }, this);

                    let total_pico = 0;
                    let total_diario = 0;
                    for (let i = 0; i < values1.length; i++) {
                        total_pico = Ext.util.Format.round((total_pico + values1[i]['plan_pico']), 3);
                        total_diario = Ext.util.Format.round((total_diario + values1[i]['plan_diario']), 3);
                    }

                    if (total_diario < plan) {
                        App.showAlert(`Falta por desglosar ${(plan - total_diario).toFixed(2)} del plan diario`, 'warning', 4000)
                    }
                    else if (total_pico < pico) {
                        App.showAlert(`Falta por desglosar ${(pico - total_pico).toFixed(2)} del plan pico`, 'warning', 4000)
                    }
                    else if (total_diario > plan) {
                        App.showAlert(`El desglose diario excede en ${(total_diario - plan).toFixed(2)} plan diario asignado`, 'warning', 4000)
                    }
                    else if (total_pico > pico) {
                        App.showAlert(`El desglose pico excede en ${(total_pico - pico).toFixed(2)} plan pico asignado`, 'warning', 4000)
                    }
                    else {
                        let url = App.buildURL('/portadores/desglosePlan/desgloselectricidadMod');
                        App.request('GET', url, {datosmod: JSON.stringify(values1)}, null, null,
                            function (response) {
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    let select = Ext.getCmp('id_grid_desglosecenter').getSelectionModel().getLastSelected();
                                    storeDesMes.load({
                                        params: {
                                            id_desglose: select.data.id
                                        }
                                    })
                                } else {
                                    Ext.getCmp('desglose_id').show();
                                }
                            },
                            function (response) {// failure_callback
                            });
                    }
                }
            }, '-',
            {
                xtype: 'displayfield',
                fieldLabel: '<strong>Dif PD</strong>',
                labelWidth: 50,
                formatter: "number('0.00')",
                id: 'dif_pd'
            }, '-', {
                xtype: 'displayfield',
                id: 'dif_pp',
                formatter: "number('0.00')",
                labelWidth: 50,
                fieldLabel: '<strong>Dif PP</strong>',

            }, '-', {
                xtype: 'displayfield',
                id: 'dif_p',
                formatter: "number('0.00')",
                labelWidth: 50,
                fieldLabel: '<strong>Dif PT</strong>',
            }
        ]

    });
    let _panel_desglose = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_desglose',
        title: 'Desglose del Plan Anual',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [paneltree, gridservicios, grid_desglosecenter, est],

    });
    App.render(_panel_desglose);
});


function calcularDistribucion(selectiongrid, id_desglose, selection_) {
    let values = [];
    selectiongrid.each(function (r) {
        values = values.concat({
            dia: r.get('dia'),
            plan_pico: r.get('plan_pico'),
            plan_diario: r.get('plan_diario'),
            perdidasT: r.get('perdidasT'),
            nromes: selection_.data.mes,
            id_desglose: id_desglose

        });
    }, this);
    let total_pico = 0;
    let total_diario = 0;
    for (let i = 0; i < values.length; i++) {
        total_pico = Ext.util.Format.round((total_pico + values[i]['plan_pico']), 3);
        total_diario = Ext.util.Format.round(total_diario + values[i]['plan_diario'], 3);
    }

    return [total_diario, total_pico]
}

function dia_semana(fecha) {
    fecha = fecha.split('/');
    if (fecha.length !== 3) {
        return null;
    }
    //Vector para calcular día de la semana de un año regular.
    let regular = [0, 3, 3, 6, 1, 4, 6, 2, 5, 0, 3, 5];
    //Vector para calcular día de la semana de un año bisiesto.
    let bisiesto = [0, 3, 4, 0, 2, 5, 0, 3, 6, 1, 4, 6];
    //Vector para hacer la traducción de resultado en día de la semana.
    let semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    //Día especificado en la fecha recibida por parametro.
    let dia = fecha[0];
    //Módulo acumulado del mes especificado en la fecha recibida por parametro.
    let mes = fecha[1] - 1;
    //Año especificado por la fecha recibida por parametros.
    let anno = fecha[2];
    //Comparación para saber si el año recibido es bisiesto.
    if ((anno % 4 === 0) && !(anno % 100 == 0 && anno % 400 != 0))
        mes = bisiesto[mes];
    else
        mes = regular[mes];
    //Se retorna el resultado del calculo del día de la semana.
    return semana[Math.ceil(Math.ceil(Math.ceil((anno - 1) % 7) + Math.ceil((Math.floor((anno - 1) / 4) - Math.floor((3 * (Math.floor((anno - 1) / 100) + 1)) / 4)) % 7) + mes + dia % 7) % 7)];
}