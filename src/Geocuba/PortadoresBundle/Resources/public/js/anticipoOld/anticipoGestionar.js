/**
 * Created by javier on 16/05/16.
 */

Ext.onReady(function () {

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
                if (Ext.getCmp('tarjetaid') != undefined && record) {
                    Ext.getCmp('tarjetaid').setValue(record.data.tarjetas[0]);
                }
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

    Ext.create('Ext.data.JsonStore', {
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
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    tipo_combustibleid: Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected().data.tipo_combustible_id
                });
            }
        }
    });

    let store_actividad = Ext.create('Ext.data.JsonStore', {
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
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    tipo_combustibleid: Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected().data.tipo_combustible_id
                });
            }
        }
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
                    mes: App.selected_month,
                    anno: App.selected_year
                    // sin_anticipo: true,
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

    // Ext.create('Ext.data.ChainedStore', {
    //     storeId: 'id_store_persona_liquidacion',
    //     fields: [
    //         {name: 'id'},
    //         {name: 'nombre'}
    //     ],
    //     proxy: {
    //         type: 'ajax',
    //         url: App.buildURL('/portadores/persona/loadCombo'),
    //         reader: {
    //             rootProperty: 'rows'
    //         }
    //     },
    //     pageSize: 1000,
    //     autoLoad: false,
    //     listeners: {
    //         beforeload: function (This, operation, eOpts) {
    //             operation.setParams({
    //                 unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
    //             });
    //         }
    //     }
    // });

    Ext.define('Portadores.anticipo.Window', {
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
                                                        let min = new Date(App.selected_month + '/' + 1 + '/' + anno);
                                                        let max = new Date(App.selected_month + '/' + dias + '/' + anno);
                                                        //This.setMinValue(min);
                                                        //This.setMaxValue(max);
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
                                                setTimeout(function () {
                                                    var record = store_vehiculo.findRecord('id', newValue);

                                                    let tarjetaCmb = Ext.getCmp('tarjetaid');
                                                    tarjetaCmb.getStore().load({
                                                        params: {
                                                            tipo_combustible_id: record.data.tipo_combustibleid,
                                                            unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                                                        }
                                                    });
                                                    tarjetaCmb.enable();
                                                    Ext.getCmp('importeLbl').setText('');
                                                    Ext.getCmp('cantidadid').setMaxValue(0);
                                                    Ext.getCmp('importeid').setMaxValue(0);
                                                }, 200);

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
                                                    var record = This.selection;
                                                    // Ext.getCmp('npersonaid').reset();
                                                    if (record !== null) {
                                                        // trabajoCmb.reset();
                                                        let trabajoCmb = Ext.getCmp('trabajoid');
                                                        trabajoCmb.getStore().load({
                                                            params: {
                                                                tarjetaid: This.getValue(),
                                                                vehiculoid: Ext.getCmp('vehiculoid').getValue()
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

                                                            Ext.getCmp('cantidadid').enable();
                                                            Ext.getCmp('importeid').enable();
                                                            Ext.getStore('id_store_trabajo_anticipo').load({
                                                                params: {
                                                                    vehiculoid: Ext.getCmp('vehiculoid').getValue(),
                                                                    tarjetaid: This.getValue()
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
                                                                tarjetaid: This.getValue()
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

//liquidacion

    Ext.define('Portadores.liquidacion.Window', {
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
                                                if (ntipo_combustibleidprecio != undefined) {
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
                                    // if (select.data.historial)
                                    saldo_inicial += Ext.Number.correctFloat(Ext.Number.from(select.data.importe, 1));
                                    store_view.remove(select);
                                    calcular_saldo_inicial(Ext.getCmp('grid_liq').getStore());
                                    recalcular_liquidaciones(Ext.getCmp('grid_liq'));
                                    Ext.getCmp('grid_liq').getView().refresh();
                                    // Ext.getCmp('form_liq').reset();
                                    // Ext.getCmp('nro_vale').reset();
                                    // Ext.getCmp('cant_litros').reset();
                                    // Ext.getCmp('importe').reset();
                                    // Ext.getCmp('importe_final').reset();
                                    // Ext.getCmp('fecha_vale').reset();
                                    // Ext.getCmp('hora_vale').reset();
                                    // Ext.getCmp('nsubactividadid').reset();
                                    // Ext.getCmp('nfamilia').reset();
                                    // Ext.getCmp('nservicentroid').reset();
                                    // Ext.getCmp('ncentrocostoid').reset();
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

    let ntipo_combustibleidprecio = undefined;

    let saldo_inicial = 0;

    var firstTime = true;
    let _btnMenu = Ext.create('Ext.button.MyButton', {
        id: 'tarjeta_btn_add',
        text: 'Menu',
        iconCls: 'fas fa-bars text-primary',
        width: 100,
        menu: [
            {
                id: 'anticipo_btn_add',
                text: 'Adicionar',
                // iconCls: 'fas fa-plus-square text-primary',
                glyph: 0xf0fe,
                width: 100,
                handler: function (This, e) {
                    firstTime = false;
                    Ext.create('Portadores.anticipo.Window', {
                        title: 'Adicionar anticipo',
                        id: 'window_anticipo_id',
                        buttons: [
                            {
                                text: 'Aceptar',
                                width: 70,
                                handler: function () {
                                    let window = Ext.getCmp('window_anticipo_id');
                                    let form = window.down('form').getForm();
                                    if (form.isValid()) {
                                        window.hide();
                                        App.request('POST', App.buildURL('/portadores/anticipo/add'), form.getValues(), null, null,
                                            function (response) {
                                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                    firstTime = true;
                                                    window.close();
                                                    Ext.getCmp('id_grid_anticipo').getStore().load();
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
                    }).show();
                }
            },
            {
                id: 'anticipo_btn_mod',
                text: 'Modificar',
                glyph: 0xf044,
                disabled: true,
                width: 100,
                handler: function (This, e) {
                    let selection = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();


                    let window = Ext.create('Portadores.anticipo.Window', {
                        title: 'Modificar anticipo',
                        id: 'window_anticipo_id',
                        listeners: {
                            afterrender: function () {
                                Ext.getCmp('no_vale').enable();
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
                                        App.request('POST', App.buildURL('/portadores/anticipo/mod'), obj, null, null,
                                            function (response) { // success_callback
                                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                    firstTime = true;
                                                    window.close();
                                                    Ext.getCmp('id_grid_anticipo').getStore().load();
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
            },
            {
                id: 'anticipo_btn_del',
                text: 'Cancelar',
                // iconCls: 'fas fa-ban text-primary',
                glyph: 0xf2ed,
                disabled: true,
                width: 100,
                handler: function (This, e) {
                    let selection = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                    Ext.Msg.show({
                        title: '¿Eliminar Anticipo?',
                        message: '¿Está seguro que desea cancelar el anticipo seleccionado?',
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                App.request('DELETE', App.buildURL('/portadores/anticipo/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('id_grid_anticipo').getStore().load();
                                    }
                                });
                            }
                        }
                    });
                }
            }
        ],

    });

    let _tbar = Ext.getCmp('anticipo_tbar');
    _tbar.add(_btnMenu);
    _tbar.setHeight(36);

    let action_handler = function (action) {
        store_subactividad.load();
        let selected = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
        if(!selected){
            Ext.getCmp('liquidacion_btn_mod').setDisabled(true);
        }
        Ext.create('Portadores.liquidacion.Window', {
            title: 'Liquidación',
            id: 'window_liquidacion_id',
            buttons: [
                {
                    text: 'Aceptar',
                    id: 'aceptar_liquidacion_id',
                    width: 70,
                    disabled: true,
                    handler: function () {
                        let window = Ext.getCmp('window_liquidacion_id');
                        let form = window.down('form').getForm();
                        // if (store_view.data.items.length > 0) {

                        if (selected !== undefined) {
                            let liquidaciones = [];
                            let store_correcto = true;

                            var flag = false;
                            var flag2 = false;
                            var total_combustible = 0;
                            Ext.Array.each(store_view.data.items, function (value) {
                                total_combustible += Ext.Number.from(value.data.cant_litros);
                                if (value.data.nservicentroid == '' || value.data.nsubactividadid == '') {
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
                            obj.anticipoid = selected.data.id;
                            obj.action = action;
                            window.hide();
                            App.request('POST', App.buildURL('/portadores/anticipo/addLiquidaciones'), obj, null, null,
                                function (response) {
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        form.reset();
                                        // ntipo_combustibleidprecio = response.rows.ntipo_combustibleidprecio;
                                        // Ext.getCmp('importe_inicial').setValue(response.rows.importe);
                                        Ext.getCmp('id_grid_liquidacion').getStore().load();
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
                        // }
                        // else {
                        //     App.showAlert('Debe insertar al menos una Liquidación', 'warning')
                        // }
                        window.show();
                    }
                },
                {
                    text: 'Cancelar',
                    width: 70,
                    handler: function () {
                        store_view.removeAll();
                        Ext.getCmp('window_liquidacion_id').close()
                    }
                }
            ],
            listeners: {
                afterrender: function () {
                    if(!selected.data.abierto && action === 'mod'){
                        Ext.getCmp('nro_vale').disable();
                        Ext.getCmp('cant_litros').disable();
                        Ext.getCmp('fecha_vale').disable();
                        Ext.getCmp('hora_vale').disable();
                    }
                }
            }
        }).show();
    };
    let _btnAddLiquidacion = Ext.create('Ext.button.MyButton', {
        id: 'liquidacion_btn_add',
        text: 'Liquidar',
        iconCls: 'fas fa-gas-pump text-primary',
        width: 100,
        disabled: true,
        handler: action_handler.bind(this, 'add')
    });

    let _btnModLiquidacion = Ext.create('Ext.button.MyButton', {
        id: 'liquidacion_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-gas-pump text-primary',
        width: 100,
        handler: action_handler.bind(this, 'mod')
    });

    let _tbarLiquidacion = Ext.getCmp('anticipo_liquidacion_tbar');
    _tbarLiquidacion.add(_btnAddLiquidacion);
    _tbarLiquidacion.add('-');
    _tbarLiquidacion.add(_btnModLiquidacion);
    _tbarLiquidacion.setHeight(36);

    var recalcular_liquidaciones = function (grid_liquidaciones) {
        var store_liquidaciones = grid_liquidaciones.getStore();
        store_liquidaciones.sort('fecha_servicio', 'ASC');
        Ext.Array.each(store_liquidaciones.data.items, function (item) {
            // if(item.data.importe_inicial) {
            item.data.importe_inicial = saldo_inicial;
            saldo_inicial = Ext.Number.correctFloat(saldo_inicial - Ext.Number.from(item.data.importe, 1));
            item.data.importe_final = saldo_inicial;
            // }
        });
        grid_liquidaciones.getView().refresh();
    };

    var calcular_saldo_inicial = function (store_liquidaciones) {
        Ext.Array.each(store_liquidaciones.data.items, function (item) {
            // if(item.data.historial)
            saldo_inicial = Ext.Number.correctFloat(Ext.Number.from(saldo_inicial, 1) + Ext.Number.from(item.data.importe, 1));
        });
    }
});