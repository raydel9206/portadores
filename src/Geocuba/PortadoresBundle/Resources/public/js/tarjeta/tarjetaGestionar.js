
Ext.onReady(function () {

    let tree_store_by_unidad = Ext.create('Ext.data.TreeStore', {
        fields: [
            {name: 'id', type: 'string'},
            {name: 'nombre', type: 'string'},
            {name: 'siglas', type: 'string'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad/loadTree'),
            reader: {
                type: 'json',
            }
        },
        root: {
            expanded: true,
            children: []
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation) {
                operation.setParams({
                    unidad_id: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
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
        autoLoad: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_caja_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            // url: App.buildURL('/portadores/caja/loadCajaCombo'),
            url: App.buildURL('/portadores/caja/loadRoot'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
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
        storeId: 'id_store_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'matricula'},
            {name: 'tipo_combustibleid'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/loadCombo'),
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

    Ext.apply(Ext.form.field.VTypes, {
        daterange: function (val, field) {
            let date = field.parseDate(val);

            if (!date) {
                return false;
            }
            if (field.startDateField && (!this.dateRangeMax || (date.getTime() !== this.dateRangeMax.getTime()))) {
                let start = field.up('form').down('#' + field.startDateField);
                start.setMaxValue(date);
                start.validate();
                this.dateRangeMax = date;
            }
            else if (field.endDateField && (!this.dateRangeMin || (date.getTime() !== this.dateRangeMin.getTime()))) {
                let end = field.up('form').down('#' + field.endDateField);
                end.setMinValue(date);
                end.validate();
                this.dateRangeMin = date;
            }
            return true;
        },
        daterangeText: 'Start date must be less than end date'


    });

    Ext.define('Portadores.tarjeta.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: 550,
                    bodyPadding: 5,
                    layout: {
                        type: 'hbox',
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
                            flex: 1,
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            border: false,
                            bodyPadding: 5,
                            margin: '10 10 10 10',
                            collapsible: false,
                            items: [
                                {
                                    xtype: 'textfield',
                                    name: 'nro_tarjeta',
                                    id: 'nro_tarjeta',
                                    fieldLabel: 'Número de la tarjeta',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    maskRe: /[0-9]/,
                                    regex: /^[0-9]*$/,
                                    regexText: 'Este campo debe contener solo valores numéricos',
                                },

                                {
                                    xtype: 'datefield',
                                    name: 'fecha_registro',
                                    itemId: 'startdt',
                                    vtype: 'daterange',
                                    endDateField: 'enddt',
                                    id: 'fecha_registro',
                                    fieldLabel: 'Fecha de registro',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ]
                                },
                                {
                                    xtype: 'datefield',
                                    name: 'fecha_vencimieno',
                                    itemId: 'enddt',
                                    vtype: 'daterange',
                                    startDateField: 'startdt',
                                    id: 'fecha_vencimieno',
                                    fieldLabel: 'Fecha de vencimiento',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ]
                                },

                                {
                                    xtype: 'numberfield',
                                    name: 'importe',
                                    id: 'importe',
                                    fieldLabel: 'Importe',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    value: 0,
                                    minValue: 0,
                                    //  disabled: true,
                                    // readOnly: true,
                                    decimalSeparator: '.'
                                },
                                {
                                    xtype: 'checkbox',
                                    name: 'es_saldo_inicial',
                                    id: 'es_saldo_inicial',
                                    fieldLabel: 'Saldo Inicial',
                                    inputValue: true,
                                    labelWidth: 80,
                                    labelAlign: 'left',
                                    allowBlank: true,
                                    listeners: {
                                        change: function (This, newValue, oldValue, eOpts) {
                                            if (newValue) {
                                                Ext.getCmp('importe').setReadOnly(false);
                                            } else {
                                                Ext.getCmp('importe').setReadOnly(true);
                                            }


                                        }
                                    }
                                },
                                {
                                    xtype: 'checkbox',
                                    name: 'reserva',
                                    id: 'reserva',
                                    fieldLabel: 'Reserva',
                                    inputValue: true,
                                    labelWidth: 50,
                                    labelAlign: 'left',
                                    allowBlank: true
                                },
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
                            collapsible: false,
                            items: [
                                {
                                    xtype: 'combobox',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    name: 'nunidadid',
                                    id: 'nunidadid',
                                    fieldLabel: 'Unidad',
                                    labelWidth: 60,
                                    displayField: 'nombre',
                                    store: tree_store_by_unidad,
                                    valueField: 'id',
                                    queryMode: 'remote',
                                    forceSelection: true,
                                    allowBlank: false,
                                    editable: false,
                                    anyMatch: true,
                                    emptyText: 'Seleccione la unidad...',
                                },
                                {
                                    xtype: 'combobox',
                                    name: 'nmonedaid',
                                    id: 'nmonedaid',
                                    fieldLabel: 'Moneda',
                                    store: Ext.getStore('id_store_moneda_tarjeta'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione la Moneda...',
                                    selectOnFocus: true,
                                    editable: true,
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ]
                                },
                                {
                                    xtype: 'combobox',
                                    name: 'ntipo_combustibleid',
                                    id: 'ntipo_combustibleid',
                                    fieldLabel: 'Tipo de combustible',
                                    store: Ext.getStore('id_store_tipo_Combustible_tarjeta'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione tipo combustible...',
                                    selectOnFocus: true,
                                    editable: true,
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    listeners: {
                                        select: function (This) {
                                            if (!Ext.getCmp('exepcional').getValue()) {
                                                let store = Ext.getStore('id_store_tipo_Combustible_tarjeta');
                                                let find = store.findRecord('id', This.value);
                                                Ext.getCmp('importe').maxValue = parseFloat(find.data.maximo_tarjeta);
                                                if (Ext.getCmp('importe').getValue() > parseFloat(find.data.maximo_tarjeta))
                                                    Ext.getCmp('importe').reset()
                                            }
                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    name: 'centrocostoid',
                                    id: 'centrocostoid',
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
                                    name: 'ncajaid',
                                    id: 'ncajaid',
                                    fieldLabel: 'Caja',
                                    store: Ext.getStore('id_store_caja_tarjeta'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione la caja...',
                                    selectOnFocus: true,
                                    editable: true,
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ]
                                },
                                {
                                    xtype: 'checkbox',
                                    name: 'exepcional',
                                    id: 'exepcional',
                                    fieldLabel: 'Tarjeta Excepcional',
                                    inputValue: true,
                                    listeners: {
                                        change: function (This) {
                                            if (!This.value) {
                                                let store = Ext.getStore('id_store_tipo_Combustible_tarjeta');
                                                let find = store.findRecord('id', Ext.getCmp('ntipo_combustibleid').getValue());
                                                if (find !== null) {
                                                    Ext.getCmp('importe').maxValue = parseFloat(find.data.maximo_tarjeta);
                                                    if (Ext.getCmp('importe').getValue() > parseFloat(find.data.maximo_tarjeta))
                                                        Ext.getCmp('importe').reset()
                                                }
                                            }
                                        }
                                    },
                                    labelWidth: 120,
                                    labelAlign: 'left',
                                    allowBlank: true
                                }
                            ]
                        }
                    ]
                }
            ];
            this.callParent();
        },listeners: {
            afterrender: function (This, operation, eOpts) {
                tree_store_by_unidad.load();
            }
        }

    });

    Ext.define('Portadores.darbajatarjeta.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        plain: true,

        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: 250,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top',
                        allowBlank: false
                    },
                    bodyPadding: 5,
                    margin: '10 10 10 10',
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'fecha_baja',
                            id: 'fecha_baja',
                            fieldLabel: 'Fecha de baja',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        },
                        {
                            xtype: 'textareafield',
                            name: 'causa_baja',
                            id: 'causa_baja',
                            fieldLabel: 'Causa de la baja',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        }
                    ]

                }
            ];

            this.callParent();
        },
    });

    Ext.define('Portadores.tarjetascanceladas.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        plain: true,

        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: 250,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top',
                        allowBlank: false
                    },
                    bodyPadding: 5,
                    margin: '10 10 10 10',
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'fecha_cancel',
                            id: 'fecha_cancel',
                            fieldLabel: 'Fecha de Cancelación',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        },
                        {
                            xtype: 'textareafield',
                            name: 'motivo',
                            id: 'motivo',
                            fieldLabel: 'Motivo de la Cancelación',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        }
                    ]

                }
            ];

            this.callParent();
        }
    });

    Ext.define('Portadores.tarjeta_vehiculo.Window', {
        extend: 'Ext.window.Window',
        width: 300,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [{
                xtype: 'form',
                frame: true,
                bodyPadding: 10,
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                fieldDefaults: {
                    msgTarget: 'side',
                    labelAlign: 'left',
                    allowBlank: false
                },
                items: [{
                    xtype: 'combobox',
                    name: 'vehiculoid',
                    id: 'vehiculoid',
                    fieldLabel: 'Vehículo',
                    afterLabelTextTpl: [
                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                    ],
                    labelWidth: 55,
                    store: store_vehiculo,
                    displayField: 'matricula',
                    valueField: 'id',
                    queryMode: 'local',
                    typeAhead: true,
                    editable: true,
                    forceSelection: true,
                    triggerAction: 'all',
                    emptyText: 'Seleccione el medio...',
                    selectOnFocus: true
                }]
            }];

            this.callParent();
        }
    });

    Ext.define('Portadores.tarjeta_persona.Window', {
        extend: 'Ext.window.Window',
        width: 300,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [{
                xtype: 'form',
                frame: true,
                bodyPadding: 10,
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                fieldDefaults: {
                    msgTarget: 'side',
                    labelAlign: 'left',
                    allowBlank: false
                },
                items: [{
                    xtype: 'combobox',
                    name: 'personaid',
                    id: 'personaid',
                    fieldLabel: 'Persona',
                    afterLabelTextTpl: [
                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                    ],
                    labelWidth: 55,
                    store: store_persona,
                    displayField: 'nombre',
                    valueField: 'id',
                    queryMode: 'local',
                    typeAhead: true,
                    editable: true,
                    forceSelection: true,
                    triggerAction: 'all',
                    emptyText: 'Seleccione la persona...',
                    selectOnFocus: true
                }]
            }];

            this.callParent();
        }
    });

    Ext.define('Portadores.recarga.Window', {
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
                            listeners: {
                                afterrender: function (This) {
                                    let dias = App.getDaysInMonth(App.current_year, App.current_month);
                                    let anno = App.current_year;
                                    let min = new Date(App.current_month + '/' + 1 + '/' + anno);
                                    let max = new Date();
                                    This.setMinValue(min);
                                    This.setMaxValue(max);
                                }
                            },
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
                            id: 'importe_recargaid',
                            listeners: {
                                change: function (This) {
                                    let selection = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected().data;
                                    if (!selection.exepcional) {
                                        let store = Ext.getStore('id_store_tipo_Combustible_tarjeta');
                                        let find = store.findRecord('id', selection.ntipo_combustibleid);
                                        Ext.getCmp('importe_recargaid').maxValue = parseFloat(find.data.maximo_tarjeta) - selection.importe;
                                    }
                                }
                            }
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });

    Ext.define('Portadores.corregirtarjeta.Window', {
        extend: 'Ext.window.Window',
        width: 300,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    bodyPadding: 10,
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
                            xtype: 'label',
                            text: 'Esta acción eliminará todas las recargas y liquidaciones introducidas a la tarjeta seleccionada a partir de:',
                            style: {
                                color: 'red',
                                textAlign: 'center'
                            }
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
                                            This.setMinValue(min);
                                            This.setMaxValue(max);
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
                                    increment: 15,
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
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });

    let Saldo = Ext.create('Ext.button.MyButton', {
        id: 'saldocuenta_id',
        text: 'Disponibilidad',
        iconCls: 'fas fa-file-invoice-dollar text-primary',
        // disabled: true,
        width: 150,
        handler: function (This, e) {
            let unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
            Ext.create('Ext.window.Window', {
                title: 'Disponibilidad',
                height: 165,
                width: 430,
                modal: true,
                resizable: false,
                layout: 'fit',
                items: [
                    {
                        xtype: 'grid',
                        id: 'id_grid_saldo',
                        disableSelection: true,
                        store: Ext.create('Ext.data.JsonStore', {
                            id: 'store_saldo',
                            fields: [
                                {name: 'combustible'},
                                {name: 'disponible'}
                            ],
                            proxy: {
                                type: 'ajax',
                                url: App.buildURL('/portadores/tarjeta/loadDispFincimex'),
                                reader: {
                                    rootProperty: 'rows'
                                }
                            },
                            autoLoad: true,
                            listeners: {
                                beforeload: function (This, operation, eOpts) {
                                    operation.setParams({
                                        unidadid: unidadid
                                    });
                                }
                            }
                        }),
                        columns: [
                            {
                                text: '<strong>Tipo Combustible</strong>',
                                dataIndex: 'combustible',
                                flex: 1,
                                align: 'center'
                            }, {
                                text: '<strong>Cantidad</strong>',
                                dataIndex: 'disponible',
                                flex: 1,
                                formatter: "number('0.00')",
                                align: 'right'
                            },

                        ]

                    },
                ],
            }).show();
            // Ext.getCmp('id_grid_saldo').getStore().loadData(response.rows);

        }

    });

    let _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'tarjeta_btn_add',
        text: 'Menu',
        disabled: true,
        iconCls: 'fas fa-bars text-primary',
        // cls: 'fa fa-pencil-square-o fa-1_4',
        width: 100,
        menu: [
            {
                text: 'Adicionar',
                iconCls: 'text-primary',
                glyph: 0xf0fe,
                handler: function (This, e) {
                    Ext.create('Portadores.tarjeta.Window', {
                        title: 'Adicionar tarjeta',
                        id: 'window_tarjeta_id',
                        buttons: [
                            {
                                text: 'Aceptar',
                                width: 70,
                                handler: function () {
                                    let window_form = Ext.getCmp('window_tarjeta_id');
                                    let form = window_form.down('form').getForm();
                                    if (form.isValid()) {
                                        window_form.hide();
                                        let obj = {}
                                        obj = form.getValues();

                                        App.request('POST', App.buildURL('/portadores/tarjeta/addTarjeta'), obj, null, null,
                                            function (response) { // success_callback
                                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                    form.reset();
                                                    Ext.getStore('id_store_tarjeta').loadPage(1);
                                                } else {
                                                    if (response && response.hasOwnProperty('errors') && response.errors) {
                                                        window_form.down('form').getForm().markInvalid(response.errors);
                                                    }
                                                }
                                                window_form.show();
                                            },
                                            function (response) { // failure_callback
                                                window_form.show();
                                            }
                                        );

                                    }
                                }
                            },
                            {
                                text: 'Cancelar',
                                width: 70,
                                handler: function () {
                                    Ext.getCmp('window_tarjeta_id').close()
                                }
                            }
                        ]
                    }).show();
                }
            },
            {
                text: 'Modificar',
                id: 'id_modificar',
                iconCls: 'text-primary',
                glyph: 0xf044,
                disabled: true,
                handler: function (This, e) {
                    let selection = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected();
                    let window_form = Ext.create('Portadores.tarjeta.Window', {
                        title: 'Modificar tarjeta',
                        id: 'window_tarjeta_id',
                        buttons: [
                            {
                                text: 'Aceptar',
                                width: 70,
                                handler: function () {
                                    let form = window_form.down('form').getForm();
                                    let obj = form.getValues();
                                    obj.id = selection.data.id;
                                    let store = Ext.getStore('id_store_tipo_Combustible_tarjeta');
                                    let find = store.findRecord('id', obj.ntipo_combustibleid);
                                    let maximo = parseFloat(find.data.maximo_tarjeta);
                                    if (obj.importe > maximo) {
                                        App.showAlert('El valor no debe ser mayor que el valor limite de la tarjeta', 'danger');
                                    } else {
                                        if (form.isValid()) {
                                            window_form.hide();
                                            App.request('POST', App.buildURL('/portadores/tarjeta/modTarjeta'), obj, null, null,
                                                function (response) { // success_callback
                                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                        Ext.getStore('id_store_tarjeta').loadPage(1);
                                                        window_form.close();
                                                    } else {
                                                        if (response && response.hasOwnProperty('errors') && response.errors) {
                                                            window_form.down('form').getForm().markInvalid(response.errors);
                                                        }
                                                    }
                                                    // window_form.show();
                                                },
                                                function (response) { // failure_callback
                                                    window_form.show();
                                                }
                                            );

                                        }
                                    }


                                }
                            },
                            {
                                text: 'Cancelar',
                                width: 70,
                                handler: function () {
                                    Ext.getCmp('window_tarjeta_id').close();
                                }
                            }
                        ]
                    });
                    window_form.show();
                    // Ext.getCmp('importe').setReadOnly(true);
                    // Ext.getCmp('importe').setDisabled(true);
                    window_form.down('form').loadRecord(selection);
                }

            },
            {
                text: 'Canceladas',
                menu: [
                    {
                        text: 'Cancelar',
                        id: 'btn_cancelarTarjeta',
                        disabled: true,
                        iconCls: 'text-primary',
                        glyph: 0xf05e,
                        handler: function () {

                            Ext.Msg.show({
                                title: '¿Cancelar tarjeta?',
                                message: '¿Está seguro que desea cancelar la tarjeta seleccionada?',
                                buttons: Ext.Msg.YESNO,
                                icon: Ext.Msg.QUESTION,
                                fn: function (btn) {
                                    if (btn === 'yes') {
                                        let selection = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected();
                                        if (selection.data.estado !== 3) {
                                            let window_form = Ext.create('Portadores.tarjetascanceladas.Window', {
                                                title: 'Cancelar Tarjeta',
                                                id: 'window_tarjetacanceladas_id',
                                                buttons: [
                                                    {
                                                        text: 'Aceptar',
                                                        width: 70,
                                                        handler: function () {
                                                            let form = window_form.down('form').getForm();
                                                            if (form.isValid()) {
                                                                window_form.hide();
                                                                let obj = form.getValues();
                                                                obj.id = selection.data.id;
                                                                obj.nro_tarjeta = selection.data.nro_tarjeta;
                                                                obj.tarjetaid = selection.data.id;
                                                                obj.nunidadid = selection.data.nunidadid;
                                                                obj.accion = '';

                                                                App.request('POST', App.buildURL('/portadores/tarjeta/cancelTarjeta'), obj, null, null,
                                                                    function (response) { // success_callback
                                                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                            window_form.close();
                                                                            Ext.getCmp('id_grid_tarjeta').getStore().load();
                                                                        } else {
                                                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                                                window_form.down('form').getForm().markInvalid(response.errors);
                                                                            }
                                                                            window_form.show();
                                                                        }
                                                                    },
                                                                    function (response) { // failure_callback
                                                                        window_form.show();
                                                                    }
                                                                );

                                                            }
                                                        }
                                                    },
                                                    {
                                                        text: 'Cancelar',
                                                        width: 70,
                                                        handler: function () {
                                                            Ext.getCmp('window_tarjetacanceladas_id').close();
                                                        }
                                                    }
                                                ]
                                            });
                                            window_form.show();
                                        } else {
                                            App.showAlert('La Tarjeta está Cancelada', 'warning');
                                        }
                                    } else if (btn === 'no') {
                                        close();
                                    }
                                }
                            });

                        }
                    },
                    /*  {
                          text: 'Quitar Cancelacion',
                          id: 'btn_delcancelarTarjeta',
                          disabled: true,
                          handler: function () {
                              App.ConfirmMessage(function () {
                                  let selection = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected();
                                  if (selection.data.estado == 3) {
                                      let obj={};
                                      obj.id = selection.data.id;
                                      obj.nro_tarjeta = selection.data.nro_tarjeta;
                                      obj.tarjetaid = selection.data.id;
                                      obj.nunidadid = selection.data.nunidadid;
                                      obj.accion='quitar';

                                      let _result = App.PerformSyncServerRequest(Routing.generate('canceltarjetaTarjeta'), obj);
                                      App.HideWaitMsg();
                                      if (_result.success) {
                                          window.close();
                                          Ext.getCmp('id_grid_tarjeta').getSelectionModel().deselectAll();
                                          Ext.getCmp('id_grid_tarjeta').getStore().load();
                                      }
                                      else {
                                          window.show();
                                          form.markInvalid(_result.message);
                                      }
                                      App.showAlert( _result.message, _result.cls);
                                  } else {
                                      App.showAlert( 'La Tarjeta está Cancelada', 'warning');
                                  }
                              }, "ESTA USTED SEGURO DE QUITAR ESTADO DE CANCELADO A LA TARJETA SELECCIONADA.?");
                          }
                      },*/
                    {
                        text: 'Listado de Canceladas',
                        id: 'btn_listado_candeladas',
                        iconCls: 'text-primary',
                        glyph: 0xf46d,
                        disabled: true,
                        handler: function (This, e) {

                            let window_form = Ext.create('Ext.window.Window', {

                                title: 'Tarjetas Canceladas ',
                                plain: true,
                                resizable: false,
                                id: 'principal_candeladas',
                                modal: true,
                                items: [

                                    {
                                        xtype: 'grid',
                                        autoShow: true,
                                        border: false,
                                        height: 300,
                                        width: 500,
                                        minHeight: 100,
                                        minWidth: 200,
                                        columns: [
                                            {
                                                text: '<strong>No. tarjeta</strong>',
                                                dataIndex: 'nro_tarjeta',
                                                filter: 'string',
                                                flex: 3
                                            },
                                            {
                                                text: '<strong>Fecha Cancelación</strong>',
                                                dataIndex: 'fecha_cancelacion',
                                                filter: 'string',
                                                flex: 3
                                            }
                                        ],
                                        plugins: [{
                                            rowBodyTpl: new Ext.XTemplate(
                                                '<div class="card p-1">',
                                                '   <div class="card">',
                                                '       <tpl>',
                                                '           <div class="card-header text-center">',
                                                '               <strong>Otros datos de interés</strong> <em class="text-muted"></em>',
                                                '           </div>',
                                                '       </tpl>',
                                                '       <table class="table table-bordered table-hover table-responsive-md mb-0">',
                                                '           <tpl if="Ext.isEmpty(id)">',
                                                '               <tr class="text-center">',
                                                '                   <td colspan="4"><span class="badge badge-secondary">No tiene mantenimientos asociados</span></td>',
                                                '                </tr>',
                                                '            <tpl else>',
                                                '            <thead class="text-center">',
                                                '               <tr>',
                                                '                   <th scope="col">Motivo:</th>',
                                                '               </tr>',
                                                '             </thead>',
                                                '             <tbody>',
                                                '               <tpl>',
                                                '                   <tr class="text-center">',
                                                '                       <td>{motivo}</td>',
                                                '                    </tr>',
                                                '                </tpl>',
                                                '              </tbody>',
                                                '           </tpl>',
                                                '       </table>',
                                                '   </div>',
                                                '</div>'
                                            ),
                                            ptype: 'rowexpander'
                                        }],
                                        features: [groupingFeature],// One header just for show. There's no data,
                                        store: Ext.create('Ext.data.ArrayStore', {
                                            storeId: 'id_store_persona_tarjetacanceladas',
                                            fields: [
                                                {name: 'id'},
                                                {name: 'nro_tarjeta'},
                                                {name: 'fecha_cancelacion'},
                                                {name: 'nunidadid'},
                                                {name: 'nombreunidadid'},
                                                {name: 'motivo'},
                                                {name: 'usuario'},
                                            ],
                                            groupField: 'nombreunidadid',
                                            proxy: {
                                                type: 'ajax',
                                                url: App.buildURL('/portadores/tarjeta/loadcanceladasTarjeta'),
                                                reader: {
                                                    rootProperty: 'rows'
                                                }
                                            },

                                            autoLoad: true,
                                            listeners: {
                                                beforeload: function (This, operation) {
                                                    operation.setParams({
                                                        nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                                                    })
                                                }
                                            }


                                        }) // A dummy empty data store
                                    }


                                ],  // Let's put an empty grid in just to illustrate fit layout
                                buttons: [

                                    {
                                        text: 'Cerrar',
                                        width: 70,
                                        handler: function () {
                                            Ext.getCmp('principal_candeladas').close();
                                        }
                                    }
                                    //{
                                    //    text: 'Detalles',
                                    //    width: 70,
                                    //    handler: function () {
                                    //        Ext.getCmp('principaldetalles').show();
                                    //    }
                                    //}

                                ]
                            }).show();

                            //window.down('form').loadRecord(selection);
                        }
                    }

                ]

            },
            {
                text: 'Bajas',
                // cls: 'fa fa-times-circle fa-1_4',
                menu: [
                    {
                        text: 'Dar Baja',
                        id: 'tarjeta_btn_darbaja',
                        disabled: true,
                        iconCls: 'text-primary',
                        glyph: 0xf410,
                        handler: function (This, e) {
                            let selection = Ext.getCmp('id_grid_tarjeta').getSelection();

                            if (selection[0].data.estado === 3) {

                                let window_form = Ext.create('Portadores.darbajatarjeta.Window', {
                                    title: 'Dar baja a la tarjeta',
                                    id: 'window_darbajatarjeta_id',
                                    buttons: [
                                        {
                                            text: 'Aceptar',
                                            width: 70,
                                            handler: function () {
                                                let form = window_form.down('form').getForm();
                                                if (form.isValid()) {
                                                    window_form.hide();
                                                    let obj = form.getValues();
                                                    obj.id = selection[0].data.id;

                                                    App.request('POST', App.buildURL('/portadores/tarjeta/darbajaTarjeta'), obj, null, null,
                                                        function (response) { // success_callback
                                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                window_form.close();
                                                                Ext.getCmp('id_grid_tarjeta').getStore().load();

                                                            } else {
                                                                if (response && response.hasOwnProperty('errors') && response.errors) {
                                                                    window_form.down('form').getForm().markInvalid(response.errors);
                                                                }
                                                            }
                                                            // window_form.show();
                                                        },
                                                        function (response) { // failure_callback
                                                            window_form.show();
                                                        }
                                                    );

                                                }
                                            }
                                        },
                                        {
                                            text: 'Cancelar',
                                            width: 70,
                                            handler: function () {
                                                Ext.getCmp('window_darbajatarjeta_id').close();
                                            }
                                        }
                                    ]
                                });
                                window_form.show();
                                //window.down('form').loadRecord(selection);
                            } else {
                                App.showAlert('Por favor cancele la Tarjeta', 'warning');
                            }
                        }
                    },
                    {
                        text: 'Listado de Bajas',
                        iconCls: 'text-primary',
                        glyph: 0xf46d,
                        handler: function (This, e) {

                            let window_form = Ext.create('Ext.window.Window', {
                                // layout: 'fit',
                                title: 'Tarjetas dadas de baja ',
                                //x: 350,
                                //y: 200,
                                plain: true,
                                resizable: false,
                                id: 'principal',
                                modal: true,
                                items: [

                                    {
                                        xtype: 'grid',

                                        autoShow: true,
                                        border: false,
                                        height: 300,
                                        width: 420,
                                        minHeight: 100,
                                        minWidth: 200,
                                        columns: [

                                            {
                                                text: '<strong>No. tarjeta</strong>',
                                                dataIndex: 'nro_tarjeta',
                                                filter: 'string',
                                                width: 200,
                                            },

                                            {
                                                text: '<strong>Fecha de la baja</strong>',
                                                dataIndex: 'fecha_baja',
                                                filter: 'string',
                                                width: 200,
                                            },
                                            // {
                                            //     text: '<strong>Causa de la baja</strong>',
                                            //     dataIndex: 'causa_baja',
                                            //     filter: 'string',
                                            //     width: 300,
                                            // }

                                        ],
                                        plugins: [{
                                            rowBodyTpl: new Ext.XTemplate(
                                                "<table align='left' width='100%' style='margin-top: 10px; margin-bottom: 15px; background: #f0f0f0;'>" +
                                                "<tr>" +
                                                "<td style='padding-right: 20px; padding-top: 10px; padding-left: 10px;'><p><b>Causa:</b> {causa_baja}</p></td>" +
                                                "</tr>" +
                                                "</table>"
                                            ),
                                            ptype: 'rowexpander'
                                        }],
                                        features: [groupingFeature],// One header just for show. There's no data,
                                        store: Ext.create('Ext.data.ArrayStore', {

                                            storeId: 'id_store_tarjeta_historial',
                                            fields: [
                                                {name: 'id'},
                                                {name: 'nro_tarjeta'},
                                                {name: 'fecha_baja'},
                                                {name: 'causa_baja'},
                                                {name: 'nunidadid'},
                                                {name: 'nombreunidadid'},
                                                {name: 'ncajaid'},
                                                {name: 'ncajaidnombre'}
                                            ],
                                            groupField: 'nombreunidadid',
                                            proxy: {
                                                type: 'ajax',
                                                url: App.buildURL('/portadores/tarjeta/loadbajasTarjeta'),
                                                reader: {
                                                    rootProperty: 'rows'
                                                }
                                            },

                                            autoLoad: true,
                                            listeners: {
                                                beforeload: function (This, operation, eOpts) {
                                                    operation.setParams({
                                                        nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                                                    });
                                                }
                                            }


                                        }) // A dummy empty data store
                                    }


                                ],  // Let's put an empty grid in just to illustrate fit layout
                                buttons: [

                                    {
                                        text: 'Cerrar',
                                        width: 70,
                                        handler: function () {
                                            Ext.getCmp('principal').close();
                                        }
                                    }
                                    //{
                                    //    text: 'Detalles',
                                    //    width: 70,
                                    //    handler: function () {
                                    //        Ext.getCmp('principaldetalles').show();
                                    //    }
                                    //}

                                ]
                            }).show();

                            //window_form.down('form').loadRecord(selection);
                        }
                    }
                ]

            }
        ],

    });

    let groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: 'Unidad: {name} ' + ' ({rows.length})',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });


    let _btn_Recarga = Ext.create('Ext.button.MyButton', {
        id: 'tarjeta_btn_recarga',
        text: 'Recargas',
        disabled: true,
        width: 110,
        iconCls: 'fas fa-money-check-alt text-primary',
        menu: [
            {
                text: 'Acciones de Recarga',
                menu: [
                    {

                        width: 130,
                        text: 'Entrar a caja',
                        id: 'btn_entrar_caja',
                        iconCls: 'text-primary',
                        glyph: 0xf2f6,
                        handler: function () {
                            let selected = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected().data;
                            let obj = {};
                            obj.entrada = true;
                            obj.recarga = false;
                            obj.salida = false;
                            obj.tarjetaid = selected.id;
                            obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;

                            App.request('POST', App.buildURL('/portadores/tarjeta/recargaTarjetas'), obj, null, null,
                                function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('id_grid_tarjeta').getStore().load();
                                    } else {
                                        if (response && response.hasOwnProperty('errors') && response.errors) {
                                            window_form.down('form').getForm().markInvalid(response.errors);
                                        }
                                    }
                                    // window_form.show();
                                },
                                function (response) { // failure_callback
                                    // window_form.show();
                                }
                            );

                        }
                    },
                    {

                        scale: 'medium',
                        width: 120,
                        text: 'Recargar',
                        id: 'id_recarga',
                        // hidden: true,
                        iconCls: 'text-primary',
                        glyph: 0xf155,
                        handler: function (This, e) {
                            let selected = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected().data;
                            if (selected.estado === 2 && selected.importe > 0) {
                                App.showAlert('Debe pasar la tarjeta por caja primero. Escoja una de las opciones habilitadas del menu Acciones ...', 'danger');
                            }
                            else if (selected.estado === 1) {
                                App.showAlert('Esta tarjeta ya ha sido recargada y se mantiene en caja. Escoja una de las opciones habilitadas del menu Acciones ... para darle salida', 'danger');
                            }
                            else {
                                let window_form = Ext.create('Portadores.recarga.Window', {
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
                                                    if (parseInt(selected.estado) === 0) {
                                                        obj.entrada = false;
                                                        obj.recarga = true;
                                                        obj.salida = false;
                                                    }

                                                    obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                                    obj.tarjetaid = selected.id;
                                                    obj.monedaid = selected.nmonedaid;


                                                    let stringQuery = `?view_id=${App.route}`;
                                                    stringQuery = Object.keys(obj).reduce((stringQuery, key) => stringQuery + `&${key}=${obj[key]}`, stringQuery);

                                                    App.request('POST', App.buildURL('/portadores/tarjeta/recargaTarjetas'), obj, null, null,
                                                        function (response) { // success_callback
                                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                form.reset();
                                                                Ext.getCmp('recarga_tarjeta_win').close();
                                                                Ext.getCmp('id_grid_tarjeta').getStore().load();
                                                                //TODO Reporte de Recarga de Tarjeta
                                                                window.open(App.buildURL('/portadores/tarjeta/exportRecargaTarjeta') + stringQuery);

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
                                                Ext.getCmp('recarga_tarjeta_win').close();
                                            }
                                        }
                                    ]
                                }).show();
                            }
                        }
                    },
                    {
                        scale: 'medium',
                        text: 'Entrar a caja y Recargar',
                        id: 'btn_entrar_caja_recargar',
                        handler: function () {
                            let selected = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected().data;
                            let window_form = Ext.create('Portadores.recarga.Window', {
                                title: 'Recarga de Tarjeta',
                                id: 'recarga_tarjeta_win',
                                buttons: [
                                    {
                                        text: 'Aceptar',
                                        width: 70,
                                        handler: function () {
                                            let form = window_form.down('form').getForm();
                                            if (form.isValid()) {
                                                window_form.hide();
                                                let obj = form.getValues();
                                                obj.entrada = true;
                                                obj.recarga = true;
                                                obj.salida = false;
                                                obj.tarjetaid = selected.id;
                                                obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                                obj.monedaid = selected.nmonedaid;

                                                App.request('POST', App.buildURL('/portadores/tarjeta/recargaTarjetas'), obj, null, null,
                                                    function (response) { // success_callback
                                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                            window_form.close();
                                                            Ext.getCmp('id_grid_tarjeta').getStore().load();
                                                            //TODO Arreglar Comprobante de Recarga
                                                            // window.open(App.buildURL('/portadores/tarjeta/exportRecargaTarjeta', obj), '_blank', '');

                                                        } else {
                                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                                window_form.down('form').getForm().markInvalid(response.errors);
                                                            }
                                                        }
                                                        window_form.show();
                                                    },
                                                    function (response) { // failure_callback
                                                        window_form.show();
                                                    }
                                                );
                                            }
                                        }
                                    },
                                    {
                                        text: 'Cancelar',
                                        width: 70,
                                        handler: function () {
                                            Ext.getCmp('recarga_tarjeta_win').close();
                                        }
                                    }
                                ]
                            }).show();
                        }
                    },
                    {
                        scale: 'medium',
                        text: 'Entrar a caja, Recargar y Salida',
                        id: 'btn_entrar_caja_recargar_salir',
                        handler: function () {
                            let selected = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected().data;
                            let windows = Ext.create('Portadores.recarga.Window', {
                                title: 'Recarga de Tarjeta',
                                id: 'recarga_tarjeta_win',
                                buttons: [
                                    {
                                        text: 'Aceptar',
                                        width: 70,
                                        handler: function () {
                                            let form = windows.down('form').getForm();
                                            if (form.isValid()) {

                                                let obj = form.getValues();
                                                obj.entrada = true;
                                                obj.recarga = true;
                                                obj.salida = true;
                                                obj.tarjetaid = selected.id;
                                                obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                                obj.monedaid = selected.nmonedaid;

                                                App.request('POST', App.buildURL('/portadores/tarjeta/recargaTarjetas'), obj, null, null,
                                                    function (response) { // success_callback
                                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors

                                                            Ext.getCmp('recarga_tarjeta_win').close();
                                                            Ext.getCmp('id_grid_tarjeta').getStore().load();
                                                            //TODO Arreglar Comprobante de Recarga
                                                            // window.open(App.buildURL('/portadores/tarjeta/exportRecargaTarjeta', obj), '_blank', '');

                                                        } else {
                                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                                window.down('form').getForm().markInvalid(response.errors);
                                                            }
                                                        }
                                                        // window.show();
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
                                            Ext.getCmp('recarga_tarjeta_win').close();
                                        }
                                    }
                                ]
                            }).show();
                        }
                    },
                    {

                        scale: 'medium',
                        width: 125,
                        text: 'Salir de caja',
                        id: 'btn_salir_caja',
                        iconCls: 'text-primary',
                        glyph: 0xf2f5,
                        handler: function () {
                            let selected = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected().data;
                            let obj = {};
                            obj.entrada = false;
                            obj.recarga = false;
                            obj.salida = true;
                            obj.tarjetaid = selected.id;

                            App.request('POST', App.buildURL('/portadores/tarjeta/recargaTarjetas'), obj, null, null,
                                function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('id_grid_tarjeta').getStore().load();
                                    } else {
                                        if (response && response.hasOwnProperty('errors') && response.errors) {
                                           App.showAlert('Falló la operación', 'danger', 3500);
                                        }
                                    }
                                },
                                function (response) { // failure_callback
                                    App.showAlert('Falló la operación', 'danger', 3500);
                                }
                            );

                        }
                    },

                ]
            },
            {
                text: 'Historial de Recargas',
                iconCls: 'text-primary',
                glyph: 0xf46d,
                handler: function (This, e) {
                    let selection = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected().data;
                    let windowR = Ext.create('Ext.window.Window', {
                        title: 'Historial de Recargas ',
                        id: 'historial_id_windo',
                        width: 480,
                        height: 400,
                        plain: true,
                        resizable: false,
                        modal: true,
                        items: [
                            {
                                xtype: 'grid',
                                id: 'grid_historial',
                                // autoShow: true,
                                border: false,
                                width: '100%',
                                height: 300,
                                columns: [
                                    {
                                        text: '<strong>No. Factura</strong>',
                                        dataIndex: 'nro_factura',
                                        align: 'center',
                                        filter: 'string',
                                        flex: .3
                                    },
                                    {
                                        text: '<strong>No. Vale</strong>',
                                        dataIndex: 'nro_vale',
                                        align: 'center',
                                        filter: 'string',
                                        flex: .3
                                    },
                                    {
                                        text: '<strong>Fecha</strong>',
                                        dataIndex: 'fecha',
                                        align: 'center',
                                        filter: 'string',
                                        flex: .4
                                    },
                                    {
                                        text: '<strong>Monto</strong>',
                                        dataIndex: 'monto_recarga',
                                        align: 'center',
                                        filter: 'string',
                                        flex: .3
                                    }
                                ],
                                store: Ext.create('Ext.data.ArrayStore', {
                                    storeId: 'id_store_tarjeta_historial',
                                    fields: [
                                        {name: 'id'},
                                        {name: 'nro_factura'},
                                        {name: 'nro_vale'},
                                        {name: 'fecha'},
                                        {name: 'monto_recarga'}
                                    ],
                                    proxy: {
                                        type: 'ajax',
                                        url: App.buildURL('/portadores/tarjeta/historialTarjeta'),
                                        reader: {
                                            rootProperty: 'rows'
                                        }
                                    },
                                    autoLoad: false,
                                    listeners: {
                                        beforeload: function (This, operation) {
                                            operation.setParams({
                                                tarjetaid: selection.id,
                                                desde: Ext.Date.format(Ext.getCmp('desde_date').getValue(), 'Y-m-d'),
                                                hasta: Ext.Date.format(Ext.getCmp('hasta_date').getValue(), 'Y-m-d')
                                            })
                                        },
                                        load: function (This, records, successful, eOpts) {
                                            if (records.length === 0) {
                                                App.showAlert('No existe historial para la tarjeta seleccionada', 'warning');
                                            }
                                        },

                                    }
                                }),
                                listeners: {
                                    afterrender: function (This, operation) {
                                        let selection = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected().data;
                                        Ext.getCmp('grid_historial').getStore().load();
                                        Ext.getCmp('historial_id_windo').setTitle('Historial de Recargas: ' + selection.nro_tarjeta);
                                    }
                                }
                            }
                        ],
                        tbar: {
                            id: 'tbar_historial',
                            height: 36,
                            items: [
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Desde',
                                    width: 150,
                                    labelWidth: 40,
                                    id: 'desde_date',
                                    name: 'desde_date',
                                    value: new Date(App.selected_month + '/' + 1 + '/' + App.selected_year),
                                    maxValue: new Date(),
                                    editable: false,
                                }, {
                                    xtype: 'datefield',
                                    fieldLabel: 'Hasta',
                                    labelWidth: 40,
                                    width: 150,
                                    name: 'hasta_date',
                                    id: 'hasta_date',
                                    value: new Date(),
                                    maxValue: new Date(),
                                    editable: false,
                                },
                                {
                                    xtype: 'button',
                                    width: 25,
                                    height: 25,
                                    id: 'id_button',
                                    tooltip: 'Buscar',
                                    iconCls: 'fas fa-search text-primary',
                                    handler: function () {
                                        Ext.getCmp('grid_historial').getStore().load();
                                    }
                                },
                                {
                                    xtype: 'button',
                                    width: 25,
                                    height: 25,
                                    tooltip: 'Limpiar',
                                    iconCls: 'fas fa-eraser text-primary',
                                    handler: function () {
                                        Ext.getCmp('desde_date').reset();
                                        Ext.getCmp('hasta_date').reset();
                                        Ext.getCmp('grid_historial').getStore().load();
                                    }
                                },
                                {
                                    xtype: 'button',
                                    id: 'tarjeta_btn_revertir',
                                    text: 'Revertir',
                                    iconCls: 'fas fa-undo-alt text-primary',
                                    width: 80,
                                    handler: function (This, e) {
                                        let window_form = Ext.create('Portadores.corregirtarjeta.Window', {
                                            title: 'Corregir Tarjeta',
                                            id: 'window_corregirtarjeta_id',
                                            buttons: [
                                                {
                                                    text: 'Aceptar',
                                                    width: 70,
                                                    handler: function () {
                                                        let selection = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected();
                                                        let form = window_form.down('form').getForm();
                                                        if (form.isValid()) {
                                                            window_form.hide();
                                                            let obj = form.getValues();
                                                            obj.id = selection.data.id;
                                                            obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                                            App.request('POST', App.buildURL('/portadores/tarjeta/corregirTarjetas'), obj, null, null,
                                                                function (response) {
                                                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                        form.reset();
                                                                        window_form.close();
                                                                        Ext.getCmp('grid_historial').getStore().load();
                                                                        Ext.getCmp('id_grid_tarjeta').getStore().load();
                                                                    } else {
                                                                        if (response && response.hasOwnProperty('errors') && response.errors) {
                                                                            window_form.down('form').getForm().markInvalid(response.errors);
                                                                        }
                                                                        window_form.show();
                                                                    }
                                                                },
                                                                function (response) {
                                                                }
                                                            );

                                                        }
                                                    }
                                                },
                                                {
                                                    text: 'Cancelar',
                                                    width: 70,
                                                    handler: function () {
                                                        Ext.getCmp('window_corregirtarjeta_id').close();
                                                    }
                                                }
                                            ]
                                        }).show();
                                    }
                                },
                                {
                                    xtype: 'button',
                                    id: 'tarjeta_btn_cancelar',
                                    text: 'Eliminar',
                                    // iconCls: 'fa  fa-dollar fa-1_4',
                                    disabled: true,
                                    hidden: true,
                                    width: 90,
                                    handler: function () {

                                        App.ConfirmMessage(function () {
                                            let selection = Ext.getCmp('grid_historial').getSelectionModel().getLastSelected();

                                            let obj = {};
                                            obj.id = selection.data.id;
                                            obj.monto_recarga = selection.data.monto_recarga;
                                            obj.tarjetaid = selection.data.tarjetaid;
                                            obj.accion = 'del';

                                            let _result = App.PerformSyncServerRequest(Routing.generate('eliminarRecargaTarjeta'), obj);
                                            App.HideWaitMsg();
                                            App.showAlert(_result.message, _result.cls);
                                            Ext.getCmp('grid_historial').getStore().load();
                                            Ext.getCmp('id_grid_tarjeta').getStore().load();
                                        }, "Está seguro que desea eliminar la Recarga. Esta acción no se podrá deshacer");
                                    }
                                },
                                {
                                    text: 'Modificar',
                                    id: 'tarjeta_btn_modf',
                                    disabled: true,
                                    hidden: true,
                                    width: 90,
                                    handler: function () {


                                        Ext.create('Ext.window.Window', {
                                            title: 'Modificar',
                                            id: 'id_modf_recarga',
                                            height: 100,
                                            width: 300,
                                            layout: 'fit',
                                            items: [
                                                {  // Let's put an empty grid in just to illustrate fit layout
                                                    xtype: 'textfield',
                                                    name: 'monto_new',
                                                    id: 'monto_new',
                                                    fieldLabel: 'Monto',
                                                    allowBlank: false  // requires a non-empty value
                                                }, {  // Let's put an empty grid in just to illustrate fit layout
                                                    xtype: 'datefield',
                                                    name: 'fecha_recarga',
                                                    id: 'fecha_recarga',
                                                    fieldLabel: 'Monto',
                                                    allowBlank: false  // requires a non-empty value
                                                }
                                            ],
                                            buttons: [
                                                {
                                                    text: 'Aceptar',

                                                    handler: function () {
                                                        let selection = Ext.getCmp('grid_historial').getSelectionModel().getLastSelected();
                                                        let obj = {};
                                                        obj.id = selection.data.id;
                                                        obj.monto_recarga = selection.data.monto_recarga;
                                                        obj.tarjetaid = selection.data.tarjetaid;
                                                        obj.accion = 'mod';
                                                        obj.monto_new = Ext.getCmp('monto_new').getValue();

                                                        // App.request('DELETE', App.buildURL('/portadores/tarjeta/eliminarRecarga'), obj , null, null, function (response) { // success_callback
                                                        //     if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                        //         Ext.getCmp('id_modf_recarga').close();
                                                        //         Ext.getCmp('grid_historial').getStore().load();
                                                        //         Ext.getCmp('id_grid_tarjeta').getStore().load();
                                                        //     }
                                                        // });

                                                        App.ShowWaitMsg();
                                                        let _result = App.PerformSyncServerRequest(Routing.generate('eliminarRecargaTarjeta'), obj);
                                                        App.HideWaitMsg();
                                                        App.showAlert(_result.message, _result.cls);


                                                    }


                                                },
                                                {
                                                    text: 'Cerrar',
                                                    handler: function () {
                                                        Ext.getCmp('id_modf_recarga').close();
                                                    }
                                                }
                                            ]
                                        }).show();


                                        // Ext.create('Ext.form.Panel', {
                                        //     title: 'Contact Info',
                                        //     width: 300,
                                        //     bodyPadding: 10,
                                        //     renderTo: Ext.getBody(),
                                        //     items: [{
                                        //         xtype: 'textfield',
                                        //         name: 'monto_new',
                                        //         id: 'monto_new',
                                        //         fieldLabel: 'Monto',
                                        //         allowBlank: false  // requires a non-empty value
                                        //     }],
                                        //     buttons: [
                                        //         {
                                        //             text: 'dds',
                                        //
                                        //         }
                                        //     ]
                                        // });


                                    }
                                }
                            ]
                        },
                        buttons: [
                            {
                                text: 'Cerrar',
                                width: 70,
                                handler: function () {
                                    Ext.getCmp('historial_id_windo').close();
                                }
                            },
                            // {
                            //    text: 'Imprimir',
                            //    width: 70,
                            //    handler: function () {
                            //        Ext.getCmp('historial_id_windo').show();
                            //    }
                            // }

                        ],
                    }).show();


                }
            },

        ]


    });

    let _tbar = Ext.getCmp('tarjeta_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btn_Recarga);
    _tbar.add('-');
    _tbar.add(Saldo);

    _tbar.setHeight(36);

    let _btnAddTarjetavehiculo = Ext.create('Ext.button.MyButton', {
        id: 'tarjeta_vehiculo_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.tarjeta_vehiculo.Window', {
                title: 'Asignar a vehículo',
                id: 'window_tarjeta_vehiculo_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let window_form = Ext.getCmp('window_tarjeta_vehiculo_id');
                            let form = window_form.down('form').getForm();
                            if (form.isValid()) {
                                window_form.hide();
                                let selected = Ext.getCmp('id_grid_tarjeta').getSelection();
                                if (selected !== undefined) {
                                    let obj = form.getValues();
                                    obj.tarjetaid = selected[0].data.id;
                                    App.request('POST', App.buildURL('/portadores/tarjeta/addTarjetaVehiculo'), obj, null, null,
                                        function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                form.reset();
                                                window_form.close();
                                                Ext.getStore('id_store_tarjeta_vehiculo').load();
                                            } else {
                                                if (response && response.hasOwnProperty('errors') && response.errors) {
                                                    Ext.getCmp('window_tarjeta_vehiculo_id').down('form').getForm().markInvalid(response.errors);
                                                }
                                                window_form.show();
                                            }
                                        },
                                        function (response) { // failure_callback
                                            window_form.show();
                                        }
                                    );
                                }
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_tarjeta_vehiculo_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    let _btnDelTarjetavehiculo = Ext.create('Ext.button.MyButton', {
        id: 'tarjeta_vehiculo_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            Ext.Msg.show({
                title: '¿Eliminar vehículo?',
                message: '¿Está seguro que desea eliminar la asignación seleccionada?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        let selection = Ext.getCmp('id_grid_tarjeta_vehiculo').getSelection();
                        App.request('DELETE', App.buildURL('/portadores/tarjeta/delTarjetaVehiculo'), {id: selection[0].data.id}, null, null,
                            function (response) {
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    Ext.getCmp('id_grid_tarjeta_vehiculo').getStore().load();
                                    Ext.getCmp('tarjeta_vehiculo_btn_del').setDisabled(true);
                                }
                            });
                    } else if (btn === 'no') {
                        close();
                    }
                }
            });

        }
    });

    let _tbar_tarjeta_vehiculo = Ext.getCmp('tarjeta_vehiculo_tbar');
    _tbar_tarjeta_vehiculo.add(_btnAddTarjetavehiculo);
    _tbar_tarjeta_vehiculo.add('-');
    _tbar_tarjeta_vehiculo.add(_btnDelTarjetavehiculo);
    _tbar_tarjeta_vehiculo.setHeight(36);

    let _btnAddTarjetapersona = Ext.create('Ext.button.MyButton', {
        id: 'tarjeta_persona_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.tarjeta_persona.Window', {
                title: 'Asignar a persona',
                id: 'window_tarjeta_persona_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let window_form = Ext.getCmp('window_tarjeta_persona_id');
                            let form = window_form.down('form').getForm();
                            if (form.isValid()) {
                                window_form.hide();
                                let selected = Ext.getCmp('id_grid_tarjeta').getSelection();
                                if (selected !== undefined) {
                                    let obj = form.getValues();
                                    obj.tarjetaid = selected[0].data.id;

                                    App.request('POST', App.buildURL('/portadores/tarjeta/addTarjetaPersona'), obj, null, null,
                                        function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                form.reset();
                                                Ext.getStore('id_store_tarjeta_persona').load();
                                                window_form.close();
                                                // Ext.getCmp('tarjeta_persona_btn_add').setDisabled(true)

                                            } else {
                                                if (response && response.hasOwnProperty('errors') && response.errors) {
                                                    Ext.getCmp('window_tarjeta_persona_id').down('form').getForm().markInvalid(response.errors);
                                                }
                                                window_form.show();
                                            }
                                        },
                                        function (response) { // failure_callback
                                            window_form.show();
                                        }
                                    );
                                }
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_tarjeta_persona_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    let _btnDelTarjetapersona = Ext.create('Ext.button.MyButton', {
        id: 'tarjeta_persona_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            Ext.Msg.show({
                title: '¿Eliminar persona?',
                message: '¿Está seguro que desea eliminar la asignación seleccionada?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        let selection = Ext.getCmp('id_grid_tarjeta_persona').getSelection();
                        App.request('DELETE', App.buildURL('/portadores/tarjeta/delTarjetaPersona'), {id: selection[0].data.id}, null, null,
                            function (response) {
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    Ext.getCmp('id_grid_tarjeta_persona').getStore().load();
                                    Ext.getCmp('tarjeta_persona_btn_del').setDisabled(true);
                                }
                            });
                    } else if (btn === 'no') {
                        close();
                    }
                }
            });

        }
    });

    let _tbar_tarjeta_persona = Ext.getCmp('tarjeta_persona_tbar');
    _tbar_tarjeta_persona.add(_btnAddTarjetapersona);
    _tbar_tarjeta_persona.add('-');
    _tbar_tarjeta_persona.add(_btnDelTarjetapersona);
    _tbar_tarjeta_persona.setHeight(36);

});
