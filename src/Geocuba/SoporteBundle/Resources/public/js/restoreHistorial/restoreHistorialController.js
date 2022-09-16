Ext.onReady(function () {
    let firstTime = true;
    let ntipo_combustibleidprecio = undefined;
    let saldo_inicial = 0;
    let actionAnt = 'add';

    let groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Tarjeta: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });


    let store_liq_to_restore = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_liq_to_restore',
        fields: [
            {name: 'id'},
            {name: 'no_vale'},
            {name: 'npersonaid'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/soporte/restoreHistorial/loadLiqRest'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        groupField: 'tarjeta',
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                Ext.getCmp('grid_liq_del').getSelectionModel().deselectAll();
                let selected = Ext.getCmp('grid_ant_del').getSelectionModel().getLastSelected();
                console.log(selected);
                if (selected !== undefined) {
                    operation.setParams({
                        anticipo: selected.data.anticipo,
                        unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                        tarjeta: selected.data.tarjetaid,
                    });
                }
            }
        }
    });

    let store_persona = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_persona',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/persona/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
        }

    });

    let store_vehiculo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo_anticipo',
        fields: [
            {name: 'id'},
            {name: 'matricula'},
            {name: 'tipo_combustibleid'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadVehiculoAnticipo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
        }
    });

    let store_trabajo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_trabajo_anticipo',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'codigo_nombre'},
            {name: 'fecha_ini'},
            {name: 'fecha_fin'},
            {name: 'tipo_combustibleid'},
            {name: 'tipo_combustible'},
            {name: 'monedaid'},
            {name: 'moneda'},
            {name: 'cantidad'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadTrabajoAnticipo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    let store_centro_costo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_centro_costo',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/centrocosto/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
        }
    });

    let store_familia = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_familia',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/familia/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    let store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta_anticipo',
        fields: [
            {name: 'id'},
            {name: 'ncajaid'},
            {name: 'ntipo_combustibleid'},
            {name: 'nombretipo_combustibleid'},
            {name: 'preciotipo_combustibleid'},
            {name: 'nmonedaid'},
            {name: 'nunidadid'},
            {name: 'nombreunidadid'},
            {name: 'nro_tarjeta'},
            {name: 'importe'},
            {name: 'fecha_registro'},
            {name: 'fecha_vencimieno'},
            {name: 'fecha_baja'},
            {name: 'causa_baja'},
            {name: 'reserva'},
            {name: 'exepcional'}
        ],
        groupField: 'nombreunidadid',

        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadTarjetaAnticipo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            // beforeload: function (This, operation, eOpts) {
            //     operation.setParams({
            //         tipo_combustible_id: record.data.tipo_combustibleid,
            //         unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
            //     });
            // },
            load: function (This, records, successful, eOpts) {
                var record = Ext.getCmp('vehiculoid').getStore().findRecord('id', Ext.getCmp('vehiculoid').getValue());
                if (Ext.getCmp('tarjetaid') !== undefined && record) {
                    Ext.getCmp('tarjetaid').setValue(record.data.tarjetas[0]);
                }
            }
        }
    });

    let store_servicentro = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_nservicentroidliquidacion',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/servicentro/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 10000,
        autoLoad: true
    });

    let store_subactividad = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_nsubactividadidliquidacion',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/actividad/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    let store_view = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_liq',
        fields: [
            {name: 'nro_vale'},
            {name: 'importe'},
            {name: 'cant_litros'},
            {name: 'id'},
            {name: 'importe_final'},
            {name: 'fecha_registro'},
            {name: 'fecha_vale'},
            {name: 'hora_vale'},
            {name: 'nsubactividadid'},
            {name: 'nfamilia'},
            {name: 'nservicentroid'},
            {name: 'ncentrocostoid'},
            {name: 'importe_inicial'},
            {name: 'npersonaid'},
            // {name: 'nunidadid'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadAnticipoLiquidaciones'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'fecha_vale',
            direction: 'ASC'
        }, {
            property: 'hora_vale',
            direction: 'ASC'
        }],
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    id: Ext.getCmp('id_grid_anticipo').getSelection()[0].data.id,
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    tarjeta: Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected().data.tarjetaid,
                    action: 'no_restore',
                    mes: App.selected_month,
                    anno: App.selected_year
                });
            },
            load: function (This, records, successful, eOpts) {
                if (Ext.getCmp('aceptar_liquidacion_id'))
                    Ext.getCmp('aceptar_liquidacion_id').setDisabled(false);

                Ext.Array.each(records, function (record) {
                    if (!record.data.historial)
                        saldo_inicial = Ext.Number.correctFloat(Ext.Number.from(saldo_inicial, 1) - Ext.Number.from(record.data.importe, 1));
                });

                calcular_saldo_inicial(This);
                recalcular_liquidaciones(Ext.getCmp('grid_liq'));

                if (Ext.getCmp('importe_inicial'))
                    Ext.getCmp('importe_inicial').setValue(saldo_inicial);
            }
        }
    });

    let store_view_rest = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_liq_rest',
        fields: [
            {name: 'nro_vale'},
            {name: 'importe'},
            {name: 'cant_litros'},
            {name: 'id'},
            {name: 'importe_final'},
            {name: 'fecha_registro'},
            {name: 'fecha_vale'},
            {name: 'hora_vale'},
            {name: 'nsubactividadid'},
            {name: 'nfamilia'},
            {name: 'nservicentroid'},
            {name: 'ncentrocostoid'},
            {name: 'importe_inicial'},
            {name: 'npersonaid'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anticipo/loadAnticipoLiquidaciones'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'fecha_vale',
            direction: 'ASC'
        }, {
            property: 'hora_vale',
            direction: 'ASC'
        }],
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    id: Ext.getCmp('grid_ant_del').getSelection()[0].data.anticipo,
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    tarjeta: Ext.getCmp('grid_ant_del').getSelectionModel().getLastSelected().data.tarjetaid,
                    action: 'to_restore',
                    mes: App.selected_month,
                    anno: App.selected_year
                });
            },
            load: function (This, records, successful, eOpts) {
                if (Ext.getCmp('aceptar_liq_id'))
                    Ext.getCmp('aceptar_liq_id').setDisabled(false);

                Ext.Array.each(records, function (record) {
                    if (!record.data.historial)
                        saldo_inicial = Ext.Number.correctFloat(Ext.Number.from(saldo_inicial, 1) - Ext.Number.from(record.data.importe, 1));
                });

                calcular_saldo_inicial(This);
                recalcular_liquidaciones(Ext.getCmp('grid_liq'));

                if (Ext.getCmp('importe_inicial'))
                    Ext.getCmp('importe_inicial').setValue(saldo_inicial);
            }
        }
    });

    let store_ant_to_restore = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_ant_to_restore',
        fields: [
            {name: 'id'},
            {name: 'no_vale'},
            {name: 'consecutivo'},
            {name: 'npersonaid'},
            {name: 'to_restore'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/soporte/restoreHistorial/loadAntRest'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        sorters: [{property: 'fecha', direction: 'ASC'}, {property: 'hora', direction: 'ASC'}],
        groupField: 'tarjeta',
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                operation.setParams({
                    unidadid: panetree.getSelectionModel().getLastSelected().data.id,
                });
            }
        }
    });

    let store_rec_to_restore = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_rec_to_restore',
        fields: [
            {name: 'id'},
            {name: 'no_vale'},
            {name: 'no_factura'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/soporte/restoreHistorial/loadRecRest'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        groupField: 'tarjeta',
        sorters: [{property: 'fecha', direction: 'ASC'}],
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation, eOpts) {
                operation.setParams({
                    unidadid: panetree.getSelectionModel().getLastSelected().data.id,
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
                if (Ext.getCmp('arbolunidades') !== undefined)
                    Ext.getCmp('arbolunidades').getSelectionModel().deselectAll();
            }
        }
    });


    Ext.define('Soporte.anticipo.Window', {
        extend: 'Ext.window.Window',
        width: 600,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    bodyPadding: 5,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'fieldcontainer',
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [{
                                xtype: 'fieldcontainer',
                                flex: 1,
                                layout: {
                                    type: 'vbox',
                                    align: 'stretch'
                                },
                                bodyPadding: 5,
                                margin: '10 10 0 10',
                                items: [
                                    {
                                        xtype: 'fieldcontainer',
                                        layout: {
                                            type: 'hbox',
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                xtype: 'datefield',
                                                name: 'fecha',
                                                id: 'fecha',
                                                flex: 0.5,
                                                margin: '0 5 0 0',
                                                editable: false,
                                                value: new Date(),
                                                listeners: {
                                                    afterrender: function (This) {
                                                        let dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                                                        let anno = App.selected_year;
                                                        // let min = new Date(App.selected_month + '/' + 1 + '/' + anno);
                                                        // let max = new Date(App.selected_month + '/' + dias + '/' + anno);
                                                        // This.setMinValue(min);
                                                        // This.setMaxValue(max);
                                                    }
                                                },
                                                format: 'd/m/Y',
                                                fieldLabel: 'Fecha',
                                                afterLabelTextTpl: [
                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                ]
                                            },
                                            {
                                                xtype: 'timefield',
                                                increment: 5,
                                                flex: 0.5,
                                                margin: '0 0 0 5',
                                                name: 'hora',
                                                id: 'hora',
                                                fieldLabel: 'Hora',
                                                afterLabelTextTpl: [
                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'combobox',
                                        name: 'vehiculoid',
                                        id: 'vehiculoid',
                                        fieldLabel: 'Vehículo',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        store: store_vehiculo,
                                        displayField: 'matricula',
                                        valueField: 'id',
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione el vehículo...',
                                        selectOnFocus: true,
                                        editable: true,
                                        listeners: {
                                            change: function (This, newValue) {
                                                let record = store_vehiculo.findRecord('id', newValue);
                                                let tarjetaCmb = Ext.getCmp('tarjetaid');
                                                tarjetaCmb.enable();
                                                tarjetaCmb.getStore().load({
                                                    params: {
                                                        tipo_combustible_id: record.data.tipo_combustibleid,
                                                        unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                                                    }
                                                }, {
                                                    callback: function (r, options, success) {
                                                        Ext.getCmp('importeLbl').setText('');
                                                        Ext.getCmp('cantidadid').setMaxValue(0);
                                                        Ext.getCmp('importeid').setMaxValue(0);
                                                    }
                                                });
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'combobox',
                                        name: 'npersonaid',
                                        id: 'npersonaid',
                                        fieldLabel: 'Persona ',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        store: store_persona,
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione la persona...',
                                        selectOnFocus: true,
                                        editable: true
                                    }, {
                                        xtype: 'combobox',
                                        name: 'trabajoid',
                                        id: 'trabajoid',
                                        fieldLabel: 'Trabajo/Proyecto',
                                        store: store_trabajo,
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione el trabajo...',
                                        selectOnFocus: true,
                                        editable: true,
                                        allowBlank: true
                                    }]
                            },
                                {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    bodyPadding: 5,
                                    margin: '10 10 0 10',
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'No. Anticipo',
                                            disabled: true,
                                            readOnly: true,
                                            name: 'no_vale',
                                            id: 'no_vale',
                                            allowBlank: true
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'tarjetaid',
                                            id: 'tarjetaid',
                                            disabled: true,
                                            fieldLabel: 'Tarjeta',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            store: store_tarjeta,
                                            displayField: 'nro_tarjeta',
                                            valueField: 'id',
                                            typeAhead: false,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione la tarjeta...',
                                            selectOnFocus: false,
                                            editable: false,
                                            listeners: {
                                                change: function (This, newValue, oldValue, eOpts) {
                                                    let record = This.selection;
                                                    if (record !== null) {
                                                        let trabajoCmb = Ext.getCmp('trabajoid');
                                                        trabajoCmb.getStore().load({
                                                            params: {
                                                                tarjetaid: This.getValue(),
                                                                vehiculoid: Ext.getCmp('vehiculoid').getValue(),
                                                                unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                                                            }
                                                        });
                                                        trabajoCmb.enable();
                                                        if (firstTime) {
                                                            firstTime = false;
                                                        }
                                                        else {
                                                            Ext.getCmp('npersonaid').select((record.data.personas.length > 0 ? record.data.personas[0] : null));
                                                            let importe = parseFloat(record.data.importe);
                                                            let cant_litros = Ext.util.Format.round(importe / parseFloat(record.data.preciotipo_combustibleid), 4);
                                                            Ext.getCmp('importeLbl').setText('Importe en tarjeta $' + importe + ' equivalente a ' + cant_litros + ' litros');
                                                            Ext.getCmp('cantidadid').setMaxValue(cant_litros);
                                                            Ext.getCmp('importeid').setMaxValue(importe);

                                                            if (actionAnt === 'mod') {
                                                                let selected = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                                                                let cant = parseFloat(selected.data.cantidad);
                                                                Ext.getCmp('cantidadid').setMaxValue(cant + cant_litros);
                                                                Ext.getCmp('importeid').setMaxValue((cant + cant_litros) * parseFloat(record.data.preciotipo_combustibleid));
                                                            } else {
                                                                Ext.getCmp('cantidadid').setMaxValue(cant_litros);
                                                                Ext.getCmp('importeid').setMaxValue(importe);
                                                            }

                                                            Ext.getCmp('cantidadid').enable();
                                                            Ext.getCmp('importeid').enable();
                                                            Ext.getStore('id_store_trabajo_anticipo').load({
                                                                params: {
                                                                    vehiculoid: Ext.getCmp('vehiculoid').getValue(),
                                                                    tarjetaid: This.getValue(),
                                                                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                                                                }
                                                            });
                                                        }
                                                    }
                                                    else {
                                                        Ext.getCmp('importeLbl').setText('');
                                                        Ext.getCmp('cantidadid').setMaxValue(0);
                                                        Ext.getCmp('importeid').setMaxValue(0);

                                                        Ext.getCmp('cantidadid').disable();
                                                        Ext.getCmp('importeid').disable();
                                                        Ext.getStore('id_store_trabajo_anticipo').load({
                                                            params: {
                                                                vehiculoid: Ext.getCmp('vehiculoid').getValue(),
                                                                tarjetaid: This.getValue(),
                                                                unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        }, {
                                            xtype: 'numberfield',
                                            fieldLabel: 'Cantidad(L)',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            disabled: true,
                                            decimalSeparator: '.',
                                            decimalPrecision: 4,
                                            value: 0,
                                            minValue: 0.01,
                                            name: 'cantidad',
                                            id: 'cantidadid',
                                            listeners: {
                                                change: function (This) {
                                                    if (Ext.getCmp('tarjetaid').selection !== null)
                                                        Ext.getCmp('importeid').setValue(This.getValue() * parseFloat(Ext.getCmp('tarjetaid').selection.data.preciotipo_combustibleid));
                                                    else
                                                        Ext.getCmp('importeid').setValue(0);
                                                }
                                            }
                                        }, {
                                            xtype: 'numberfield',
                                            fieldLabel: 'Importe',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            disabled: true,
                                            decimalSeparator: '.',
                                            value: 0,
                                            minValue: 0.01,
                                            name: 'importe',
                                            id: 'importeid',
                                            readOnly: true
                                        }]
                                }]
                        },
                        {
                            xtype: 'checkboxgroup',
                            columns: 2,
                            vertical: true,
                            allowBlank: true,
                            bodyPadding: 5,
                            margin: '0 10 10 10',
                            items: [
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Tránsito',
                                    labelAlign: 'left',
                                    labelWidth: 50,
                                    name: 'transito',
                                    inputValue: '1'
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Terceros',
                                    labelAlign: 'left',
                                    labelWidth: 50,
                                    name: 'terceros',
                                    inputValue: '1'
                                }
                            ]
                        },
                        {
                            xtype: 'label',
                            id: 'importeLbl',
                            text: '',
                            style: {
                                color: 'red',
                                textAlign: 'center'
                            }
                        },
                        {
                            xtype: 'checkboxfield',
                            name: 'excepcional',
                            boxLabel: 'Excepcionalidad',
                            margin: '-10 0 0 10',
                            listeners: {
                                change: function (This, newValue, oldValue) {
                                    Ext.getCmp('motivo').setHidden(!newValue);
                                    Ext.getCmp('motivo').setDisabled(!newValue);
                                }
                            }
                        },
                        {
                            xtype: 'textareafield',
                            fieldLabel: 'Motivo',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            allowBlank: true,
                            name: 'motivo',
                            id: 'motivo',
                            hidden: true,
                            padding: '0 10 0 10'
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    Ext.define('Soporte.liquidacion.Window', {
        extend: 'Ext.window.Window',
        width: 800,
        height: 600,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    id: 'form_liq',
                    frame: true,
                    bodyPadding: 5,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        labelAlign: 'top',
                        msgTarget: 'side',
                        allowBlank: false
                    },
                    items: [{
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'fieldcontainer',
                                flex: 1,
                                layout: {
                                    type: 'vbox',
                                    align: 'stretch'
                                },
                                border: false,
                                bodyPadding: 5,
                                margin: '10 10 10 10',
                                items: [
                                    {
                                        xtype: 'textfield',
                                        name: 'liquidacionid',
                                        id: 'liquidacionid',
                                        allowBlank: true,
                                        hidden: true,
                                    },
                                    {
                                        xtype: 'combobox',
                                        name: 'npersonaid',
                                        id: 'npersonaid',
                                        fieldLabel: 'Persona ',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        store: store_persona,
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione la persona...',
                                        selectOnFocus: true,
                                        editable: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'nro_vale',
                                        id: 'nro_vale',
                                        fieldLabel: 'No. vale',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        maskRe: /[0-9]/
                                    },
                                    {
                                        xtype: 'numberfield',
                                        name: 'importe_inicial',
                                        id: 'importe_inicial',
                                        decimalSeparator: '.',
                                        decimalPrecision: 2,
                                        fieldLabel: 'Importe inicial',
                                        readOnly: true,
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        value: 0,
                                        minValue: 0
                                    }
                                ]
                            },
                            {
                                xtype: 'fieldcontainer',
                                flex: 1,
                                layout: {
                                    type: 'vbox',
                                    align: 'stretch'
                                },
                                border: false,
                                bodyPadding: 5,
                                margin: '10 10 10 10',
                                items: [
                                    {
                                        xtype: 'combobox',
                                        name: 'ncentrocostoid',
                                        id: 'ncentrocostoid',
                                        fieldLabel: 'Centro de costo',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        store: store_centro_costo,
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione centro de costo...',
                                        selectOnFocus: true,
                                        editable: true,
                                        listeners: {
                                            select: function (This, records, eOpts) {
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'combobox',
                                        name: 'nservicentroid',
                                        id: 'nservicentroid',
                                        fieldLabel: 'Servicentro',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        store: Ext.getStore('id_store_nservicentroidliquidacion'),
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione el servicentro...',
                                        selectOnFocus: true,
                                        editable: true
                                    },
                                    {
                                        xtype: 'numberfield',
                                        name: 'cant_litros',
                                        id: 'cant_litros',
                                        fieldLabel: 'Cantidad de litros',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        decimalSeparator: '.',
                                        decimalPrecision: 4,
                                        value: 0,
                                        minValue: 0.01,
                                        listeners: {
                                            change: function (This) {
                                                if (ntipo_combustibleidprecio !== undefined) {
                                                    let importe = This.getValue() * parseFloat(ntipo_combustibleidprecio);
                                                    Ext.getCmp('importe').setValue(importe);
                                                    Ext.getCmp('importe_final').setValue(Ext.getCmp('importe_inicial').getValue() - importe);
                                                } else {
                                                    Ext.getCmp('importe').setValue(0);
                                                    Ext.getCmp('importe_final').setValue(0);
                                                }
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'numberfield',
                                        name: 'importe',
                                        id: 'importe',
                                        fieldLabel: 'Importe',
                                        readOnly: true,
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        decimalSeparator: '.',
                                        decimalPrecision: 2,
                                        value: 0,
                                        //minValue: 0.01
                                    }
                                ]
                            },
                            {
                                xtype: 'fieldcontainer',
                                flex: 1,
                                layout: {
                                    type: 'vbox',
                                    align: 'stretch'
                                },
                                border: false,
                                bodyPadding: 5,
                                margin: '10 10 10 10',
                                items: [
                                    {
                                        xtype: 'combobox',
                                        name: 'nfamilia',
                                        id: 'nfamilia',
                                        fieldLabel: 'Familia',
                                        store: store_familia,
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione la familia ...',
                                        selectOnFocus: true,
                                        editable: true,
                                        allowBlank: true
                                    },
                                    {
                                        xtype: 'combobox',
                                        name: 'nsubactividadid',
                                        id: 'nsubactividadid',
                                        fieldLabel: 'Actividad',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        store: store_subactividad,
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        typeAhead: true,
                                        queryMode: 'local',
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        emptyText: 'Seleccione la subactividad ...',
                                        selectOnFocus: true,
                                        editable: true
                                    },
                                    {
                                        xtype: 'fieldcontainer',
                                        layout: {
                                            type: 'hbox',
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                xtype: 'datefield',
                                                name: 'fecha_vale',
                                                id: 'fecha_vale',
                                                flex: 0.5,
                                                fieldLabel: 'Fecha del vale',
                                                afterLabelTextTpl: [
                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                ],
                                                listeners: {
                                                    afterrender: function (This) {
                                                        //TODO Poner de min Value la fecha del anticipo
                                                        // var anticipo = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                                                        let dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                                                        let anno = App.selected_year;
                                                        let min = new Date(App.selected_month + '/' + 1 + '/' + anno);
                                                        let max = new Date(App.selected_month + '/' + dias + '/' + anno);
                                                        // This.setMinValue(anticipo.data.fecha_anticipo);
                                                        //This.setMinValue(min);
                                                        //This.setMaxValue(max);
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'timefield',
                                                increment: 5,
                                                flex: 0.5,
                                                margin: '0 0 0 5',
                                                name: 'hora_vale',
                                                id: 'hora_vale',
                                                fieldLabel: 'Hora',
                                                afterLabelTextTpl: [
                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                ]
                                            }]
                                    },
                                    {
                                        xtype: 'numberfield',
                                        name: 'importe_final',
                                        id: 'importe_final',
                                        margin: '0 0 0 0',
                                        decimalSeparator: '.',
                                        decimalPrecision: 2,
                                        fieldLabel: 'Importe final',
                                        readOnly: true,
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        value: 0,
                                        minValue: 0
                                    }
                                ]
                            }]
                    }, {
                        xtype: 'fieldcontainer',
                        items: [{
                            xtype: 'gridpanel',
                            id: 'grid_liq',
                            height: 300,
                            store: store_view,
                            border: true,
                            viewConfig: {
                                getRowClass: function (record, rowIndex, rowParams, store) {
                                    if (!record.get('historial')) return 'row-error';
                                }
                            },
                            columns: [
                                {
                                    dataIndex: 'ncentrocostoid',
                                    hidden: true,
                                    renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
                                        if (!value) {
                                            console.log(meta)
                                        }
                                    }
                                },
                                {
                                    text: '<strong>No.Vale</strong>',
                                    dataIndex: 'nro_vale',
                                    filter: 'string',
                                    flex: .2,
                                },
                                {
                                    text: '<strong>Servicentro</strong>',
                                    dataIndex: 'nservicentroid_nombre',
                                    filter: 'string',
                                    flex: .5
                                },
                                {
                                    text: '<strong>Importe</strong>',
                                    dataIndex: 'importe',
                                    filter: 'string',
                                    flex: .2,
                                    align: 'right',
                                    formatter: "number('0.00')"
                                },
                                {
                                    text: '<strong>Cant.Litros</strong>',
                                    dataIndex: 'cant_litros',
                                    filter: 'string',
                                    flex: 0.2,
                                    align: 'right',
                                    formatter: "number('0.00')"
                                },
                                {text: '<strong>Fecha</strong>', dataIndex: 'fecha_vale', filter: 'string', flex: .2},
                                {
                                    text: '<strong>Hora</strong>', dataIndex: 'hora_vale', filter: 'string', flex: .2,
                                    renderer: function (value) {
                                        if (typeof value === 'string')
                                            return value;
                                        return Ext.Date.format(value, 'h:i A');
                                    }
                                },
                                {
                                    text: '<strong>Importe final</strong>',
                                    dataIndex: 'importe_final',
                                    filter: 'string',
                                    flex: .2,
                                    formatter: "number('0.00')"
                                }
                            ],
                            tbar: ['->', {
                                xtype: 'button',
                                text: 'Adicionar',
                                iconCls: 'fas fa-plus-square text-primary',
                                handler: function () {
                                    if (Ext.getCmp('form_liq').isValid()) {
                                        calcular_saldo_inicial(Ext.getCmp('grid_liq').getStore());
                                        var fecha_vale_obj = Ext.getCmp('fecha_vale').getValue();
                                        var hora_vale_obj = Ext.getCmp('hora_vale').getValue();

                                        Ext.getCmp('grid_liq').getStore().add({
                                            liquidacionid: Ext.getCmp('liquidacionid').getValue(),
                                            nro_vale: Ext.getCmp('nro_vale').getValue(),
                                            importe: Ext.getCmp('importe').getValue(),
                                            cant_litros: Ext.getCmp('cant_litros').getValue(),
                                            fecha_registro: Ext.Date.format(new Date(), 'd/m/Y'),
                                            fecha_vale: Ext.getCmp('fecha_vale').getRawValue(),
                                            hora_vale: Ext.getCmp('hora_vale').getValue(),
                                            nsubactividadid: Ext.getCmp('nsubactividadid').getValue(),
                                            nfamilia: Ext.getCmp('nfamilia').getValue(),
                                            nservicentroid: Ext.getCmp('nservicentroid').getValue(),
                                            nservicentroid_nombre: Ext.getCmp('nservicentroid').getRawValue(),
                                            ncentrocostoid: Ext.getCmp('ncentrocostoid').getValue(),
                                            importe_inicial: Ext.getCmp('importe_inicial').getValue(),
                                            npersonaid: Ext.getCmp('npersonaid').getValue(),
                                            fecha_servicio: Ext.Date.format(new Date(fecha_vale_obj.getFullYear(), fecha_vale_obj.getMonth(), fecha_vale_obj.getDate(), hora_vale_obj.getHours(), hora_vale_obj.getMinutes()), 'd/m/Y H:i:s'),
                                        });

                                        recalcular_liquidaciones(Ext.getCmp('grid_liq'));

                                        Ext.getCmp('nro_vale').reset();
                                        Ext.getCmp('cant_litros').reset();
                                        Ext.getCmp('importe').reset();
                                        Ext.getCmp('importe_final').reset();
                                        Ext.getCmp('fecha_vale').reset();
                                        Ext.getCmp('hora_vale').reset();
                                        // Ext.getCmp('nsubactividadid').reset();
                                        Ext.getCmp('nfamilia').reset();
                                        Ext.getCmp('nservicentroid').reset();
                                        // Ext.getCmp('ncentrocostoid').reset();
                                        Ext.getCmp('liquidacionid').reset();
                                        Ext.getCmp('importe_inicial').setValue(saldo_inicial);
                                    }
                                }
                            }, {
                                xtype: 'button',
                                text: 'Eliminar',
                                iconCls: 'fas fa-minus-square text-primary',
                                handler: function () {
                                    let select = Ext.getCmp('grid_liq').getSelectionModel().getLastSelected();
                                    saldo_inicial += Ext.Number.correctFloat(Ext.Number.from(select.data.importe, 1));
                                    store_view.remove(select);
                                    calcular_saldo_inicial(Ext.getCmp('grid_liq').getStore());
                                    recalcular_liquidaciones(Ext.getCmp('grid_liq'));
                                    Ext.getCmp('grid_liq').getView().refresh();
                                    Ext.getCmp('importe_inicial').setValue(saldo_inicial);
                                }
                            }],
                            listeners: {
                                rowdblclick: function (This, record, tr, rowIndex, e, eOpts) {
                                    // if(record.data.historial)
                                    saldo_inicial = Ext.Number.correctFloat(Ext.Number.from(saldo_inicial, 1) + Ext.Number.from(record.data.importe, 1));
                                    Ext.getCmp('grid_liq').getStore().remove(record);
                                    Ext.getCmp('nro_vale').reset();
                                    Ext.getCmp('cant_litros').reset();
                                    Ext.getCmp('importe').reset();
                                    Ext.getCmp('importe_final').reset();
                                    Ext.getCmp('fecha_vale').reset();
                                    Ext.getCmp('hora_vale').reset();
                                    // Ext.getCmp('nsubactividadid').reset();
                                    Ext.getCmp('nfamilia').reset();
                                    Ext.getCmp('nservicentroid').reset();
                                    // Ext.getCmp('ncentrocostoid').reset();
                                    Ext.getCmp('liquidacionid').reset();

                                    Ext.getCmp('liquidacionid').setValue(record.data.liquidacionid);
                                    Ext.getCmp('hora_vale').setValue(record.data.hora_vale);
                                    Ext.getCmp('fecha_vale').setValue(record.data.fecha_vale);
                                    Ext.getCmp('nro_vale').setValue(record.data.nro_vale);
                                    if (record.data.ncentrocostoid)
                                        Ext.getCmp('ncentrocostoid').setValue(record.data.ncentrocostoid);
                                    if (record.data.nsubactividadid)
                                        Ext.getCmp('nsubactividadid').setValue(record.data.nsubactividadid);
                                    if (record.data.nservicentroid)
                                        Ext.getCmp('nservicentroid').setValue(record.data.nservicentroid);
                                    if (record.data.nfamilia)
                                        Ext.getCmp('nfamilia').setValue(record.data.nfamilia);
                                    if (record.data.importe) {
                                        Ext.getCmp('importe').setValue(record.data.importe);
                                        if (record.data.importe_final)
                                            Ext.getCmp('importe_inicial').setValue(Ext.Number.correctFloat(Ext.Number.from(record.data.importe_final) + Ext.Number.from(record.data.importe)));
                                        else
                                            Ext.getCmp('importe_inicial').setValue(Ext.Number.from(record.data.importe));
                                    }
                                    if (record.data.cant_litros)
                                        Ext.getCmp('cant_litros').setValue(record.data.cant_litros);
                                }
                            }
                        }]
                    }
                    ],
                    listeners: {
                        afterrender: function (This) {
                            let selected = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                            if (selected !== null) {
                                App.mask();
                                App.request('GET', App.buildURL('/portadores/anticipo/getCantLtsLiquidacionAnticipo'), {id: selected.data.tarjetaid}, null, null,
                                    function (response) {
                                        if (response) {
                                            saldo_inicial = Ext.Number.from(response.rows.importe);
                                            ntipo_combustibleidprecio = response.rows.ntipo_combustibleidprecio;

                                            if (Ext.getCmp('cant_litros'))
                                                Ext.getCmp('cant_litros').setValue(0);
                                            if (Ext.getCmp('importe'))
                                                Ext.getCmp('importe').setValue(0);
                                            if (Ext.getCmp('importe_final'))
                                                Ext.getCmp('importe_final').setValue(0);

                                            Ext.getCmp('grid_liq').getStore().load();
                                            App.unmask();
                                        }
                                    }, null, null, true, false);

                                if (Ext.getCmp('npersonaid'))
                                    Ext.getCmp('npersonaid').select(selected.data.npersonaid);
                                if (Ext.getCmp('ncentrocostoid'))
                                    Ext.getCmp('ncentrocostoid').setValue(selected.data.centrocostoid);
                                if (Ext.getCmp('nsubactividadid'))
                                    Ext.getCmp('nsubactividadid').setValue(selected.data.actividadid);
                            }
                        }
                    }
                }
            ];

            this.callParent();
        }
    });

    Ext.define('Soporte.recarga.Window', {
        extend: 'Ext.window.Window',
        width: 220,
        plain: true,
        resizable: false,
        modal: true,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
                    id: 'form_recarga_tarjetaid',
                    bodyPadding: 10,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'fecha_recarga',
                            id: 'fecha_recargaid',
                            editable: false,
                            value: new Date(),
                            format: 'd/m/Y',
                            fieldLabel: 'Fecha',
                            labelWidth: 80,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        },
                        {
                            xtype: 'timefield',
                            increment: 5,
                            name: 'hora_recarga',
                            id: 'hora_recarga',
                            fieldLabel: 'Hora',
                            labelWidth: 80,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'No.Factura',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 80,
                            maxLength: 255,
                            name: 'no_factura',
                            id: 'no_facturaid',
                            allowBlank: true
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'No.Vale',
                            hideTrigger: true,
                            labelWidth: 80,
                            decimalSeparator: '.',
                            maxLength: 50,
                            name: 'no_vale',
                            id: 'no_valeid',
                            allowBlank: true
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Importe',
                            hideTrigger: true,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 80,
                            decimalSeparator: '.',
                            minValue: 0.01,
                            value: 0,
                            name: 'importe_recarga',
                            id: 'importe_recargaid'
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });

    Ext.define('Soporte.liq.Window', {
        extend: 'Ext.window.Window',
        width: 800,
        height: 600,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    id: 'form_liq_restore',
                    frame: true,
                    bodyPadding: 5,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        labelAlign: 'top',
                        msgTarget: 'side',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'fieldcontainer',
                            layout: 'hbox',
                            items: [
                                {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    border: false,
                                    bodyPadding: 5,
                                    margin: '10 10 10 10',
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            name: 'liquidacionid',
                                            id: 'liquidacionid',
                                            allowBlank: true,
                                            hidden: true,
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'npersonaid',
                                            id: 'npersonaid',
                                            fieldLabel: 'Persona ',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            store: store_persona,
                                            displayField: 'nombre',
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione la persona...',
                                            selectOnFocus: true,
                                            editable: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            name: 'nro_vale',
                                            id: 'nro_vale',
                                            fieldLabel: 'No. vale',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            maskRe: /[0-9]/
                                        },
                                        {
                                            xtype: 'numberfield',
                                            name: 'importe_inicial',
                                            id: 'importe_inicial',
                                            decimalSeparator: '.',
                                            decimalPrecision: 2,
                                            fieldLabel: 'Importe inicial',
                                            readOnly: true,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            value: 0,
                                            minValue: 0
                                        }
                                    ]
                                },
                                {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    border: false,
                                    bodyPadding: 5,
                                    margin: '10 10 10 10',
                                    items: [
                                        {
                                            xtype: 'combobox',
                                            name: 'ncentrocostoid',
                                            id: 'ncentrocostoid',
                                            fieldLabel: 'Centro de costo',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            store: store_centro_costo,
                                            displayField: 'nombre',
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione centro de costo...',
                                            selectOnFocus: true,
                                            editable: true,
                                            listeners: {
                                                select: function (This, records, eOpts) {
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'nservicentroid',
                                            id: 'nservicentroid',
                                            fieldLabel: 'Servicentro',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            store: Ext.getStore('id_store_nservicentroidliquidacion'),
                                            displayField: 'nombre',
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione el servicentro...',
                                            selectOnFocus: true,
                                            editable: true
                                        },
                                        {
                                            xtype: 'numberfield',
                                            name: 'cant_litros',
                                            id: 'cant_litros',
                                            fieldLabel: 'Cantidad de litros',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            decimalSeparator: '.',
                                            decimalPrecision: 4,
                                            value: 0,
                                            minValue: 0.01,
                                            listeners: {
                                                change: function (This) {
                                                    if (ntipo_combustibleidprecio !== undefined) {
                                                        let importe = This.getValue() * parseFloat(ntipo_combustibleidprecio);
                                                        Ext.getCmp('importe').setValue(importe);
                                                        Ext.getCmp('importe_final').setValue(Ext.getCmp('importe_inicial').getValue() - importe);
                                                    } else {
                                                        Ext.getCmp('importe').setValue(0);
                                                        Ext.getCmp('importe_final').setValue(0);
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'numberfield',
                                            name: 'importe',
                                            id: 'importe',
                                            fieldLabel: 'Importe',
                                            readOnly: true,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            decimalSeparator: '.',
                                            decimalPrecision: 2,
                                            value: 0
                                        }
                                    ]
                                },
                                {
                                    xtype: 'fieldcontainer',
                                    flex: 1,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    border: false,
                                    bodyPadding: 5,
                                    margin: '10 10 10 10',
                                    items: [
                                        {
                                            xtype: 'combobox',
                                            name: 'nfamilia',
                                            id: 'nfamilia',
                                            fieldLabel: 'Familia',
                                            store: store_familia,
                                            displayField: 'nombre',
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione la familia ...',
                                            selectOnFocus: true,
                                            editable: true,
                                            allowBlank: true
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'nsubactividadid',
                                            id: 'nsubactividadid',
                                            fieldLabel: 'Actividad',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            store: store_subactividad,
                                            displayField: 'nombre',
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione la subactividad ...',
                                            selectOnFocus: true,
                                            editable: true
                                        },
                                        {
                                            xtype: 'fieldcontainer',
                                            layout: {
                                                type: 'hbox',
                                                align: 'stretch'
                                            },
                                            items: [
                                                {
                                                    xtype: 'datefield',
                                                    name: 'fecha_vale',
                                                    id: 'fecha_vale',
                                                    flex: 0.5,
                                                    fieldLabel: 'Fecha del vale',
                                                    afterLabelTextTpl: [
                                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                    ],
                                                    listeners: {
                                                        afterrender: function (This) {
                                                            //TODO Poner de min Value la fecha del anticipo
                                                            // var anticipo = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                                                            let dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                                                            let anno = App.selected_year;
                                                            let min = new Date(App.selected_month + '/' + 1 + '/' + anno);
                                                            let max = new Date(App.selected_month + '/' + dias + '/' + anno);
                                                            // This.setMinValue(anticipo.data.fecha_anticipo);
                                                            //This.setMinValue(min);
                                                            //This.setMaxValue(max);
                                                        }
                                                    }
                                                },
                                                {
                                                    xtype: 'timefield',
                                                    increment: 5,
                                                    flex: 0.5,
                                                    margin: '0 0 0 5',
                                                    name: 'hora_vale',
                                                    id: 'hora_vale',
                                                    fieldLabel: 'Hora',
                                                    afterLabelTextTpl: [
                                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                    ]
                                                }]
                                        },
                                        {
                                            xtype: 'numberfield',
                                            name: 'importe_final',
                                            id: 'importe_final',
                                            margin: '0 0 0 0',
                                            decimalSeparator: '.',
                                            decimalPrecision: 2,
                                            fieldLabel: 'Importe final',
                                            readOnly: true,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            value: 0,
                                            // minValue: 0
                                        }
                                    ]
                                }]
                        }, {
                            xtype: 'fieldcontainer',
                            items: [{
                                xtype: 'gridpanel',
                                id: 'grid_liq',
                                height: 300,
                                store: store_view_rest,
                                border: true,
                                viewConfig: {
                                    getRowClass: function (record, rowIndex, rowParams, store) {
                                        if (!record.get('historial')) return 'row-error';
                                    }
                                },
                                columns: [
                                    {
                                        dataIndex: 'ncentrocostoid',
                                        hidden: true,
                                        renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
                                            if (!value) {
                                                console.log(meta)
                                            }
                                        }
                                    },
                                    {
                                        text: '<strong>No.Vale</strong>',
                                        dataIndex: 'nro_vale',
                                        filter: 'string',
                                        flex: .2,
                                    },
                                    {
                                        text: '<strong>Servicentro</strong>',
                                        dataIndex: 'nservicentroid_nombre',
                                        filter: 'string',
                                        flex: .5
                                    },
                                    {
                                        text: '<strong>Importe</strong>',
                                        dataIndex: 'importe',
                                        filter: 'string',
                                        flex: .2,
                                        align: 'right',
                                        formatter: "number('0.00')"
                                    },
                                    {
                                        text: '<strong>Cant.Litros</strong>',
                                        dataIndex: 'cant_litros',
                                        filter: 'string',
                                        flex: 0.2,
                                        align: 'right',
                                        formatter: "number('0.00')"
                                    },
                                    {
                                        text: '<strong>Fecha</strong>',
                                        dataIndex: 'fecha_vale',
                                        filter: 'string',
                                        flex: .2
                                    },
                                    {
                                        text: '<strong>Hora</strong>',
                                        dataIndex: 'hora_vale',
                                        filter: 'string',
                                        flex: .2,
                                        renderer: function (value) {
                                            if (typeof value === 'string')
                                                return value;
                                            return Ext.Date.format(value, 'h:i A');
                                        }
                                    },
                                    {
                                        text: '<strong>Importe final</strong>',
                                        dataIndex: 'importe_final',
                                        filter: 'string',
                                        flex: .2,
                                        formatter: "number('0.00')"
                                    }
                                ],
                                tbar: ['->', {
                                    xtype: 'button',
                                    text: 'Adicionar',
                                    iconCls: 'fas fa-plus-square text-primary',
                                    handler: function () {
                                        if (Ext.getCmp('form_liq_restore').isValid()) {
                                            calcular_saldo_inicial(Ext.getCmp('grid_liq').getStore());
                                            var fecha_vale_obj = Ext.getCmp('fecha_vale').getValue();
                                            var hora_vale_obj = Ext.getCmp('hora_vale').getValue();

                                            Ext.getCmp('grid_liq').getStore().add({
                                                liquidacionid: Ext.getCmp('liquidacionid').getValue(),
                                                nro_vale: Ext.getCmp('nro_vale').getValue(),
                                                importe: Ext.getCmp('importe').getValue(),
                                                cant_litros: Ext.getCmp('cant_litros').getValue(),
                                                fecha_registro: Ext.Date.format(new Date(), 'd/m/Y'),
                                                fecha_vale: Ext.getCmp('fecha_vale').getRawValue(),
                                                hora_vale: Ext.getCmp('hora_vale').getValue(),
                                                nsubactividadid: Ext.getCmp('nsubactividadid').getValue(),
                                                nfamilia: Ext.getCmp('nfamilia').getValue(),
                                                nservicentroid: Ext.getCmp('nservicentroid').getValue(),
                                                nservicentroid_nombre: Ext.getCmp('nservicentroid').getRawValue(),
                                                ncentrocostoid: Ext.getCmp('ncentrocostoid').getValue(),
                                                importe_inicial: Ext.getCmp('importe_inicial').getValue(),
                                                npersonaid: Ext.getCmp('npersonaid').getValue(),
                                                fecha_servicio: Ext.Date.format(new Date(fecha_vale_obj.getFullYear(), fecha_vale_obj.getMonth(), fecha_vale_obj.getDate(), hora_vale_obj.getHours(), hora_vale_obj.getMinutes()), 'd/m/Y H:i:s'),
                                            });

                                            recalcular_liquidaciones(Ext.getCmp('grid_liq'));

                                            Ext.getCmp('nro_vale').reset();
                                            Ext.getCmp('cant_litros').reset();
                                            Ext.getCmp('importe').reset();
                                            Ext.getCmp('importe_final').reset();
                                            Ext.getCmp('fecha_vale').reset();
                                            Ext.getCmp('hora_vale').reset();
                                            // Ext.getCmp('nsubactividadid').reset();
                                            Ext.getCmp('nfamilia').reset();
                                            Ext.getCmp('nservicentroid').reset();
                                            // Ext.getCmp('ncentrocostoid').reset();
                                            Ext.getCmp('liquidacionid').reset();
                                            Ext.getCmp('importe_inicial').setValue(saldo_inicial);
                                        }
                                    }
                                }, {
                                    xtype: 'button',
                                    text: 'Eliminar',
                                    iconCls: 'fas fa-minus-square text-primary',
                                    handler: function () {
                                        let select = Ext.getCmp('grid_liq').getSelectionModel().getLastSelected();
                                        saldo_inicial += Ext.Number.correctFloat(Ext.Number.from(select.data.importe, 1));
                                        store_view.remove(select);
                                        calcular_saldo_inicial(Ext.getCmp('grid_liq').getStore());
                                        recalcular_liquidaciones(Ext.getCmp('grid_liq'));
                                        Ext.getCmp('grid_liq').getView().refresh();
                                        Ext.getCmp('importe_inicial').setValue(saldo_inicial);
                                    }
                                }],
                                listeners: {
                                    rowdblclick: function (This, record, tr, rowIndex, e, eOpts) {
                                        saldo_inicial = Ext.Number.correctFloat(Ext.Number.from(saldo_inicial, 1) + Ext.Number.from(record.data.importe, 1));
                                        Ext.getCmp('grid_liq').getStore().remove(record);
                                        Ext.getCmp('nro_vale').reset();
                                        Ext.getCmp('cant_litros').reset();
                                        Ext.getCmp('importe').reset();
                                        Ext.getCmp('importe_final').reset();
                                        Ext.getCmp('fecha_vale').reset();
                                        Ext.getCmp('hora_vale').reset();
                                        Ext.getCmp('nfamilia').reset();
                                        Ext.getCmp('nservicentroid').reset();
                                        Ext.getCmp('liquidacionid').reset();

                                        Ext.getCmp('liquidacionid').setValue(record.data.liquidacionid);
                                        Ext.getCmp('hora_vale').setValue(record.data.hora_vale);
                                        Ext.getCmp('fecha_vale').setValue(record.data.fecha_vale);
                                        Ext.getCmp('nro_vale').setValue(record.data.nro_vale);
                                        if (record.data.ncentrocostoid)
                                            Ext.getCmp('ncentrocostoid').setValue(record.data.ncentrocostoid);
                                        if (record.data.nsubactividadid)
                                            Ext.getCmp('nsubactividadid').setValue(record.data.nsubactividadid);
                                        if (record.data.nservicentroid)
                                            Ext.getCmp('nservicentroid').setValue(record.data.nservicentroid);
                                        if (record.data.nfamilia)
                                            Ext.getCmp('nfamilia').setValue(record.data.nfamilia);
                                        if (record.data.importe) {
                                            Ext.getCmp('importe').setValue(record.data.importe);
                                            if (record.data.importe_final)
                                                Ext.getCmp('importe_inicial').setValue(Ext.Number.correctFloat(Ext.Number.from(record.data.importe_final) + Ext.Number.from(record.data.importe)));
                                            else
                                                Ext.getCmp('importe_inicial').setValue(Ext.Number.from(record.data.importe));
                                        }
                                        if (record.data.cant_litros)
                                            Ext.getCmp('cant_litros').setValue(record.data.cant_litros);
                                    }
                                }
                            }]
                        }
                    ],
                    listeners: {
                        afterrender: function (This) {
                            let selected = Ext.getCmp('grid_ant_del').getSelectionModel().getLastSelected();
                            if (selected !== null) {
                                App.mask();
                                App.request('GET', App.buildURL('/portadores/anticipo/getCantLtsLiquidacionAnticipo'), {id: selected.data.tarjetaid}, null, null,
                                    function (response) {
                                        if (response) {
                                            saldo_inicial = Ext.Number.from(response.rows.importe);
                                            ntipo_combustibleidprecio = response.rows.ntipo_combustibleidprecio;

                                            if (Ext.getCmp('cant_litros'))
                                                Ext.getCmp('cant_litros').setValue(0);
                                            if (Ext.getCmp('importe'))
                                                Ext.getCmp('importe').setValue(0);
                                            if (Ext.getCmp('importe_final'))
                                                Ext.getCmp('importe_final').setValue(0);

                                            Ext.getCmp('grid_liq').getStore().load();
                                            App.unmask();
                                        }
                                    }, null, null, true, false);

                                if (Ext.getCmp('npersonaid'))
                                    Ext.getCmp('npersonaid').select(selected.data.npersonaid);
                                if (Ext.getCmp('ncentrocostoid'))
                                    Ext.getCmp('ncentrocostoid').setValue(selected.data.centrocostoid);
                                if (Ext.getCmp('nsubactividadid'))
                                    Ext.getCmp('nsubactividadid').setValue(selected.data.actividadid);
                            }
                        }
                    }
                }
            ];

            this.callParent();
        }
    });


    let grid_ant_del = Ext.create('Ext.grid.Panel', {
        scrollable: 'vertical',
        id: 'grid_ant_del',
        // flex: 4,
        height: 380,
        width: '100%',
        viewConfig: {
            getRowClass: function (record, rowIndex, rowParams, store) {
                if (record.get('to_restore')) return 'row-error';
            }
        },
        features: [groupingFeature],
        columns: [
            {
                xtype: 'gridcolumn',
                dataIndex: 'fecha_anticipo',
                flex: .8,
                text: '<b>Fecha</b>'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'consecutivo',
                hidden: true,
            }, {
                xtype: 'hidden',
                dataIndex: 'npersonaid',
            }, {
                xtype: 'hidden',
                dataIndex: 'to_restore',
            }, {
                xtype: 'gridcolumn',
                dataIndex: 'no_vale',
                flex: .8,
                text: '<b>No.Vale</b>'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'vehiculo',
                flex: .6,
                text: '<b>Vehículo</b>'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'tarjeta',
                flex: .8,
                text: '<b>Tarjeta</b>'
            }
        ],
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    xtype: 'button',
                    width: 100,
                    disabled: true,
                    id: 'btn_mod_ant',
                    text: 'Modificar',
                    tooltip: 'Modificar anticipos',
                    glyph: 0xf2d2,
                    handler: function () {
                        let firstTime = false;
                        let selection = Ext.getCmp('grid_ant_del').getSelectionModel().getLastSelected();

                        let window_mod = Ext.create('Soporte.anticipo.Window', {
                            title: 'Restaurar anticipo',
                            id: 'window_mod_anticipo_id',
                            listeners: {
                                afterrender: function () {
                                    Ext.getCmp('cantidadid').enable();
                                    Ext.getCmp('importeid').enable();
                                }
                            },
                            buttons: [
                                {
                                    text: 'Aceptar',
                                    width: 70,
                                    handler: function () {
                                        let form = window_mod.down('form').getForm();
                                        if (form.isValid()) {
                                            let obj = form.getValues();
                                            obj.id = selection.data.id;
                                            obj.consecutivo = selection.data.consecutivo;
                                            App.request('POST', App.buildURL('/soporte/restoreHistorial/modAntRest'), obj, null, null,
                                                function (response) { // success_callback
                                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                        firstTime = true;
                                                        window_mod.close();
                                                        Ext.getCmp('grid_ant_del').getStore().load();
                                                    } else {
                                                        if (response && response.hasOwnProperty('errors') && response.errors) {
                                                            window_mod.down('form').getForm().markInvalid(response.errors);
                                                        }
                                                        window_mod.show();
                                                    }
                                                },
                                                function (response) { // failure_callback
                                                    window_mod.show();
                                                }
                                            );
                                        }
                                    }
                                },
                                {
                                    text: 'Cancelar',
                                    width: 70,
                                    handler: function () {
                                        firstTime = true;
                                        Ext.getCmp('window_mod_anticipo_id').close();
                                    }
                                }
                            ]
                        });
                        window_mod.show();
                        window_mod.down('form').loadRecord(selection);
                        Ext.getCmp('importeid').setValue(selection.data.importe);
                    }
                }, {
                    xtype: 'button',
                    width: 100,
                    disabled: true,
                    id: 'btn_rest_ant',
                    text: 'Restaurar',
                    tooltip: 'Restaurar anticipos',
                    glyph: 0xf2d2,
                    handler: function () {
                        firstTime = false;
                        let lock = false;
                        let selection = Ext.getCmp('grid_ant_del').getSelectionModel().getLastSelected();
                        let store_ant = Ext.getCmp('grid_ant_del').getStore();
                        Ext.Array.each(store_ant.data.items, function (valor, index) {
                            if (selection.data.id !== valor.data.id && valor.data.to_restore) {
                                lock = true;
                                return false;
                            }
                        });

                        if (lock) {
                            App.showAlert('Debe restaurar primero el anticipo señalado, desecharlo o cambiar el orden de restauración', 'warning');
                        } else {
                            let window = Ext.create('Soporte.anticipo.Window', {
                                title: 'Restaurar anticipo',
                                id: 'window_anticipo_id',
                                listeners: {
                                    afterrender: function () {
                                        Ext.getCmp('cantidadid').enable();
                                        Ext.getCmp('importeid').enable();
                                    }
                                },
                                buttons: [
                                    {
                                        text: 'Aceptar',
                                        width: 70,
                                        handler: function () {
                                            let form = window.down('form').getForm();
                                            if (form.isValid()) {
                                                let obj = form.getValues();
                                                obj.id = selection.data.id;
                                                obj.consecutivo = selection.data.consecutivo;
                                                App.request('POST', App.buildURL('/soporte/restoreHistorial/restaurar'), obj, null, null,
                                                    function (response) { // success_callback
                                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                            firstTime = true;
                                                            window.close();
                                                            Ext.getCmp('grid_ant_del').getStore().load();
                                                        } else {
                                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                                window.down('form').getForm().markInvalid(response.errors);
                                                            }
                                                            window.show();
                                                        }
                                                    },
                                                    function (response) { // failure_callback
                                                        window.show();
                                                    }
                                                );
                                            }
                                        }
                                    },
                                    {
                                        text: 'Cancelar',
                                        width: 70,
                                        handler: function () {
                                            firstTime = true;
                                            Ext.getCmp('window_anticipo_id').close();
                                        }
                                    }
                                ]
                            });
                            window.show();
                            window.down('form').loadRecord(selection);
                            Ext.getCmp('importeid').setValue(selection.data.importe);
                        }
                    }
                },
                {
                    xtype: 'button',
                    width: 100,
                    disabled: true,
                    id: 'btn_desc_ant',
                    text: 'Descartar',
                    tooltip: 'Descartar anticipo',
                    glyph: 0xf0e2,
                    handler: function () {
                        firstTime = false;
                        let selection = Ext.getCmp('grid_ant_del').getSelectionModel().getLastSelected();
                        Ext.Msg.show({
                            title: '¿Desea descartar el anticipo seleccionado?',
                            message: Ext.String.format('¿Está seguro que desea descartar el anticipo <span class="font-italic font-weight-bold">{0}</span>?', selection.data.no_vale),
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.Msg.QUESTION,
                            fn: function (btn) {
                                if (btn === 'yes') {
                                    App.request('POST', App.buildURL('/soporte/restoreHistorial/descartarAnt'),
                                        {id: selection.data.id}, null, null, function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                Ext.getCmp('grid_ant_del').getStore().reload();
                                            }
                                        });
                                }
                            }
                        });
                    }
                },
                {
                    xtype: 'button',
                    width: 100,
                    disabled: true,
                    id: 'btn_reorder_ant',
                    text: 'Desmarcar',
                    tooltip: 'Desmarcar anticipo',
                    glyph: 0xf161,
                    handler: function () {
                        firstTime = false;
                        let selection = Ext.getCmp('grid_ant_del').getSelectionModel().getLastSelected();
                        Ext.Msg.show({
                            title: '¿Desea desmarcar el anticipo seleccionado?',
                            message: Ext.String.format('¿Está seguro que desea desmarcar el anticipo <span class="font-italic font-weight-bold">{0}</span>?', selection.data.no_vale),
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.Msg.QUESTION,
                            fn: function (btn) {
                                if (btn === 'yes') {
                                    App.request('POST', App.buildURL('/soporte/restoreHistorial/desmarcarAnt'),
                                        {id: selection.data.id}, null, null, function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                Ext.getCmp('grid_ant_del').getStore().reload();
                                                Ext.getCmp('btn_reorder_ant').setDisabled(true);
                                            }
                                        });
                                }
                            }
                        });
                    }
                },
            ]
        }],
        store: Ext.getStore('store_ant_to_restore'),
        listeners: {
            selectionchange: function (This, selections, options) {
                if (selections.length > 0) {
                    if (selections[0].data.to_restore) {
                        Ext.getCmp('btn_reorder_ant').setDisabled(false);
                    } else {
                        Ext.getCmp('btn_reorder_ant').setDisabled(true);
                    }
                    Ext.getCmp('btn_rest_ant').setDisabled(false);
                    Ext.getCmp('btn_mod_ant').setDisabled(false);
                    Ext.getCmp('btn_desc_ant').setDisabled(false);
                    Ext.getCmp('grid_rec_del').getSelectionModel().deselectAll();
                    store_liq_to_restore.load({
                        callback: function (r, options, success) {
                            if (success) {
                                if (r.length > 0) {
                                    Ext.getCmp('grid_liq_del').expand(true);
                                    Ext.getCmp('btn_gest_liq').setDisabled(false);
                                } else {
                                    Ext.getCmp('grid_liq_del').expand(false);
                                    Ext.getCmp('btn_gest_liq').setDisabled(true);
                                }
                            }
                        }
                    })

                } else {
                    Ext.getCmp('btn_rest_ant').setDisabled(true);
                    Ext.getCmp('btn_mod_ant').setDisabled(true);
                    Ext.getCmp('btn_desc_ant').setDisabled(true);
                    Ext.getCmp('btn_reorder_ant').setDisabled(true);
                }
            }
        },
    });

    let grid_liq_del = Ext.create('Ext.grid.Panel', {
        region: 'east',
        id: 'grid_liq_del',
        title: 'Liquidaciones',
        width: '35%',
        collapsible: true,
        store: store_liq_to_restore,
        columns: [
            {
                xtype: 'gridcolumn',
                flex: .1,
                dataIndex: 'nro_vale',
                text: '<b>No. Vale</b>'
            }, {
                xtype: 'gridcolumn',
                flex: .1,
                dataIndex: 'cant_litros',
                text: '<b>Cantidad</b>'
            },
        ],
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    xtype: 'button',
                    width: 100,
                    id: 'btn_gest_liq',
                    text: 'Gestionar',
                    disabled: true,
                    tooltip: 'Gestionar Liquidaciones',
                    glyph: 0xf2d2,
                    handler: function () {
                        store_subactividad.load({
                            params: {
                                tipo_combustibleid: Ext.getCmp('grid_ant_del').getSelectionModel().getLastSelected().data.tipo_combustible_id
                            }
                        });
                        Ext.create('Soporte.liq.Window', {
                            title: 'Liquidación',
                            id: 'window_liq_id',
                            buttons: [
                                {
                                    text: 'Aceptar',
                                    id: 'aceptar_liq_id',
                                    width: 70,
                                    disabled: true,
                                    handler: function () {
                                        let selected = Ext.getCmp('grid_ant_del').getSelectionModel().getLastSelected();
                                        let window = Ext.getCmp('window_liq_id');
                                        let form = window.down('form').getForm();
                                        if (selected !== undefined) {
                                            let liquidaciones = [];
                                            let store_correcto = true;

                                            let flag = false;
                                            let flag2 = false;
                                            let total_combustible = 0;
                                            Ext.Array.each(store_view_rest.data.items, function (value) {
                                                total_combustible += Ext.Number.from(value.data.cant_litros);
                                                if (value.data.nservicentroid === '' || value.data.nsubactividadid === '') {
                                                    flag = true;
                                                }
                                                liquidaciones.push(value.data);
                                            });

                                            if (flag) {
                                                App.showAlert('Inserte todos los datos necesarios de cada liquidación', 'danger');
                                                return;
                                            }

                                            if (total_combustible > selected.data.cantidad) {
                                                App.showAlert('Las liquidaciones exceden la cantidad de litros del Anticipo', 'danger');
                                                return;
                                            }

                                            let obj = {};
                                            obj.liquidaciones = Ext.encode(liquidaciones);
                                            obj.anticipoid = selected.data.anticipo;
                                            window.hide();
                                            App.request('POST', App.buildURL('/soporte/restoreHistorial/addLiqToRestore'), obj, null, null,
                                                function (response) {
                                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                        form.reset();
                                                        Ext.getCmp('grid_ant_del').store.on('load', function () {
                                                            let view = Ext.getCmp('grid_ant_del').getView(),
                                                                selModel = Ext.getCmp('grid_ant_del').getSelectionModel(),
                                                                lastSelected = selModel.getLastSelected();
                                                            view.select(lastSelected);
                                                        });

                                                        Ext.getCmp('grid_ant_del').store.load();
                                                        Ext.getCmp('grid_liq_del').getStore().load();
                                                        Ext.getCmp('btn_reorder_ant').setDisabled(false);
                                                        window.close();
                                                    } else {
                                                        if (response && response.hasOwnProperty('errors') && response.errors) {
                                                            window.down('form').getForm().markInvalid(response.errors);
                                                            window.show();
                                                        }
                                                    }
                                                },
                                                function (response) { // failure_callback
                                                    window.show();
                                                }
                                            );

                                        }
                                        window.show();
                                    }
                                },
                                {
                                    text: 'Cancelar',
                                    width: 70,
                                    handler: function () {
                                        store_view_rest.removeAll();
                                        Ext.getCmp('window_liq_id').close()
                                    }
                                }
                            ],
                            listeners: {
                                afterrender: function () {
                                    Ext.getCmp('nro_vale').disable();
                                    Ext.getCmp('cant_litros').disable();
                                    Ext.getCmp('fecha_vale').disable();
                                    Ext.getCmp('hora_vale').disable();
                                }
                            }
                        }).show();
                    }
                }
            ]
        }],
        collapsed: true,
    });

    let grid_rec_del = Ext.create('Ext.grid.Panel', {
        title: 'Recargas',
        scrollable: 'vertical',
        id: 'grid_rec_del',
        // flex: 4,
        height: '150',
        width: '100%',
        columns: [
            {
                xtype: 'gridcolumn',
                text: '<b>Fecha</b>',
                dataIndex: 'fecha_recarga',
                flex: 1,
            }, {
                xtype: 'gridcolumn',
                text: '<b>Hora</b>',
                dataIndex: 'hora_recarga',
                flex: 1,
            },
            {
                xtype: 'gridcolumn',
                text: '<b>Tarjeta</b>',
                flex: 1,
                dataIndex: 'tarjeta',
            }, {
                xtype: 'gridcolumn',
                dataIndex: 'importe_recarga',
                text: '<b>Importe</b>',
                flex: 1,
            }
        ],
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    xtype: 'button',
                    width: 150,
                    id: 'btn_rest_rec',
                    text: 'Restaurar recarga',
                    disabled: true,
                    tooltip: 'Restaurar recarga',
                    glyph: 0xf2d2,
                    handler: function () {
                        firstTime = false;
                        let selection = Ext.getCmp('grid_rec_del').getSelectionModel().getLastSelected();
                        let window_form = Ext.create('Soporte.recarga.Window', {
                            title: 'Recarga de Tarjeta',
                            id: 'recarga_tarjeta_win',
                            buttons: [
                                {
                                    text: 'Aceptar',
                                    width: 70,
                                    handler: function () {
                                        let form = Ext.getCmp('form_recarga_tarjetaid');
                                        if (form.isValid()) {
                                            Ext.getCmp('recarga_tarjeta_win').hide();
                                            let obj = form.getValues();
                                            if (parseInt(selection.data.estado) === 0) {
                                                obj.entrada = false;
                                                obj.recarga = true;
                                                obj.salida = false;
                                            }

                                            obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                            obj.tarjetaid = selection.data.idtarjeta;
                                            obj.monedaid = selection.data.nmonedaid;
                                            obj.id = selection.data.id;


                                            let stringQuery = `?view_id=${App.route}`;
                                            stringQuery = Object.keys(obj).reduce((stringQuery, key) => stringQuery + `&${key}=${obj[key]}`, stringQuery);

                                            App.request('POST', App.buildURL('/soporte/restoreHistorial/restaurarRec'), obj, null, null,
                                                function (response) { // success_callback
                                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                        firstTime = true;
                                                        Ext.getCmp('recarga_tarjeta_win').close();
                                                        Ext.getCmp('grid_rec_del').getStore().load();
                                                        //TODO Reporte de Recarga de Tarjeta
                                                        window.open(App.buildURL('/soporte/tarjeta/exportRecargaTarjeta') + stringQuery);

                                                    } else {
                                                        if (response && response.hasOwnProperty('errors') && response.errors) {
                                                            window_form.down('form').getForm().markInvalid(response.errors);
                                                        }
                                                        Ext.getCmp('recarga_tarjeta_win').show();
                                                    }
                                                },
                                                function (response) { // failure_callback
                                                    Ext.getCmp('recarga_tarjeta_win').show();
                                                }
                                            );
                                        }
                                    }
                                },
                                {
                                    text: 'Cancelar',
                                    width: 70,
                                    handler: function () {
                                        firstTime = true;
                                        Ext.getCmp('recarga_tarjeta_win').close();
                                    }
                                }
                            ]
                        }).show();
                        window_form.down('form').loadRecord(selection);
                    }
                }, {
                    xtype: 'button',
                    width: 150,
                    disabled: true,
                    id: 'btn_desc_rec',
                    text: 'Descartar',
                    tooltip: 'Descartar recarga',
                    glyph: 0xf0e2,
                    handler: function () {
                        firstTime = false;
                        let selection = Ext.getCmp('grid_rec_del').getSelectionModel().getLastSelected();
                        Ext.Msg.show({
                            title: '¿Desea descartar la recarga seleccionada?',
                            message: Ext.String.format('¿Está seguro que desea descartar la recarga <span class="font-italic font-weight-bold">{0}</span>? ', selection.data.no_factura),
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.Msg.QUESTION,
                            fn: function (btn) {
                                if (btn === 'yes') {
                                    App.request('POST', App.buildURL('/soporte/restoreHistorial/descartarRec'),
                                        {id: selection.data.id}, null, null, function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                Ext.getCmp('grid_rec_del').getStore().reload();
                                            }
                                        });
                                }
                            }
                        });
                    }
                }
            ]
        }],
        store: Ext.getStore('store_rec_to_restore'),
        listeners: {
            selectionchange: function (This, selections, options) {
                if (selections.length > 0) {
                    Ext.getCmp('btn_rest_rec').setDisabled(false);
                    Ext.getCmp('btn_desc_rec').setDisabled(false);
                    Ext.getCmp('grid_ant_del').getSelectionModel().deselectAll();
                } else {
                    Ext.getCmp('btn_rest_rec').setDisabled(true);
                    Ext.getCmp('btn_desc_rec').setDisabled(true);
                }
            }
        },
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
        header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'}},
        layout: 'fit',
        columns: [
            {xtype: 'treecolumn', iconCls: Ext.emptyString, width: 450, dataIndex: 'nombre'}
        ],
        root: {
            text: 'root',
            expanded: true
        },
        listeners: {
            select: function (This, record, index, eOpts) {
                store_ant_to_restore.load();
                store_rec_to_restore.load();

                if (record) {
                    if (Ext.getStore('id_store_vehiculo_anticipo')) {
                        Ext.getStore('id_store_vehiculo_anticipo').load();
                    }
                    if (Ext.getStore('id_store_persona')) {
                        Ext.getStore('id_store_persona').load();
                    }
                    if (Ext.getStore('id_store_centro_costo'))
                        Ext.getStore('id_store_centro_costo').load();

                }
            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                panel_liquidaciones.collapse();
            }
        }
    });

    let _panelCenter = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_center',
        title: 'Anticipos',
        region: 'center',
        width: '100%',
        layout: {
            type: 'vbox'
        },
        items: [grid_ant_del, grid_rec_del],
    });

    let _panel = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_anticipo',
        title: 'Anticipos',
        frame: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, _panelCenter, grid_liq_del],
    });
    App.render(_panel);

    let recalcular_liquidaciones = function (grid_liquidaciones) {
        var store_liquidaciones = grid_liquidaciones.getStore();
        store_liquidaciones.sort('fecha_servicio', 'ASC');
        Ext.Array.each(store_liquidaciones.data.items, function (item) {
            item.data.importe_inicial = saldo_inicial;
            saldo_inicial = Ext.Number.correctFloat(saldo_inicial - Ext.Number.from(item.data.importe, 1));
            item.data.importe_final = saldo_inicial;
        });
        grid_liquidaciones.getView().refresh();
    };

    let calcular_saldo_inicial = function (store_liquidaciones) {
        Ext.Array.each(store_liquidaciones.data.items, function (item) {
            saldo_inicial = Ext.Number.correctFloat(Ext.Number.from(saldo_inicial, 1) + Ext.Number.from(item.data.importe, 1));
        });
    }
});
