
Ext.onReady(function () {

    Ext.define('servicios', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'capacidad', type: 'float'},
        ]
    });

    var banco = Ext.create('Ext.data.Store', {
        storeId: 'id_store_banco_tranfs',
        fields: [
            {name: 'id'},
            {name: 'capacidad'},
            {name: 'tipo'},
            {name: 'pfe'},
            {name: 'pcu'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/banco_transformadores/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true

    });

    Ext.define('serviciosa', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'tipo_servicio', type: 'string'},


        ]
    });

    var tipo = Ext.create('Ext.data.Store', {
        model: 'serviciosa',
        data: [
            {id: '1', tipo_servicio: 'Monofásicos'},
            {id: '2', tipo_servicio: 'Trifásicos'},
        ]
    });

    var turnos = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_turnos',
        fields: [
            {name: 'id'},
            {name: 'turnos'},
            {name: 'horas'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/turno/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    var _storeprov = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_prov',
        fields: [
            {name: 'id'},
            {name: 'codigo'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/provincia/list'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    var _storemun = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_mun',
        fields: [
            {name: 'id'},
            {name: 'codigo'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/municipio/listMunicipio'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    var _storetarifa = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarifa',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarifa/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    var _storeactividad = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_activida',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'um_actividad'},
            {name: 'um_actividad_nombre'},
            {name: 'codigogae'},
            {name: 'codigomep'}

        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/actividad/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        // filters: [function (item) {
        //     if (item.get('codigogae') > 40000) {
        //         return item.get('id');
        //     }
        // }],
        pageSize: 1000,
        autoLoad: true


    });

    Ext.define('Portadores.servicios.Window', {
        extend: 'Ext.window.Window',
        width: 650,
        // height: 600,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    layout: 'fit',
                    bodyStyle: 'padding:5px 5px 0',
                    items: [
                        {
                            xtype: 'tabpanel',
                            activeTab: 0,
                            border: true,
                            autoDestroy: false,
                            items: [
                                {
                                    xtype: 'panel',
                                    title: 'Identificación servicio',
                                    flex: 1,
                                    closable: false,
                                    bodyPadding: 5,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    items: [
                                        {
                                            xtype: 'fieldcontainer',
                                            layout: {
                                                type: 'hbox',
                                                align: 'stretch'
                                            },
                                            items: [
                                                {
                                                    xtype: 'fieldcontainer',
                                                    flex: 1,
                                                    layout: {
                                                        type: 'vbox',
                                                        align: 'stretch'
                                                    },
                                                    bodyPadding: 5,
                                                    margin: '10 10 0 10',
                                                    fieldDefaults: {
                                                        msgTarget: 'side',
                                                        labelAlign: 'top',
                                                        allowBlank: false
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'nombre_servicio',
                                                            id: 'nombre_servicio',
                                                            fieldLabel: 'Nombre del Servicio',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            // maskRe: /[0-9a-zA-Z ]/
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'codigo_cliente',
                                                            id: 'codigo_cliente',
                                                            fieldLabel: 'Código Cliente',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            maskRe: /[0-9]/
                                                        },
                                                        {
                                                            xtype: 'combobox',
                                                            name: 'provicianid',
                                                            id: 'provicianid',
                                                            fieldLabel: 'Provincia',
                                                            store: _storeprov,
                                                            displayField: 'nombre',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            valueField: 'id',
                                                            typeAhead: true,
                                                            queryMode: 'local',
                                                            forceSelection: true,
                                                            triggerAction: 'all',
                                                            emptyText: 'Seleccione la Provincia...',
                                                            selectOnFocus: true,
                                                            editable: true,
                                                            listeners: {
                                                                select: function (This) {
                                                                    Ext.getCmp('municipioid').reset();
                                                                    Ext.getCmp('municipioid').enable();
                                                                    Ext.getCmp('municipioid').setReadOnly(false);
                                                                    Ext.getCmp('municipioid').getStore().load({params: {id: This.value}});

                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'combobox',
                                                            name: 'municipioid',
                                                            id: 'municipioid',
                                                            fieldLabel: 'Municipio',
                                                            store: _storemun,
                                                            displayField: 'nombre',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            valueField: 'id',
                                                            typeAhead: true,
                                                            queryMode: 'local',
                                                            forceSelection: true,
                                                            triggerAction: 'all',
                                                            emptyText: 'Seleccione municipio...',
                                                            selectOnFocus: true,
                                                            editable: true,
                                                            readOnly: true,
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
                                                    bodyPadding: 5,
                                                    margin: '10 10 0 10',
                                                    fieldDefaults: {
                                                        msgTarget: 'side',
                                                        labelAlign: 'top',
                                                        allowBlank: false
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'control',
                                                            id: 'control',
                                                            fieldLabel: 'Control',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            maskRe: /[0-9a-zA-Z ]/
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'ruta',
                                                            id: 'ruta',
                                                            fieldLabel: 'Ruta',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            maskRe: /[0-9a-zA-Z ]/
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'folio',
                                                            id: 'folio',
                                                            fieldLabel: 'Folio',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ]
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'numero',
                                                            id: 'numero',
                                                            fieldLabel: 'Número',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            maskRe: /[0-9a-zA-Z ]/
                                                        }
                                                    ]
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'textarea',
                                            name: 'direccion',
                                            id: 'direccion',
                                            fieldLabel: 'Dirección',
                                            margin: '0 10 10 10',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            msgTarget: 'side',
                                            labelAlign: 'top',
                                            allowBlank: false
                                        }
                                    ]
                                },
                                {
                                    xtype: 'panel',
                                    title: 'Datos técnicos ',
                                    flex: 1,
                                    bodyPadding: 5,
                                    closable: false,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    items: [
                                        {
                                            xtype: 'fieldcontainer',
                                            layout: {
                                                type: 'hbox',
                                                align: 'stretch'
                                            },
                                            items: [
                                                {
                                                    xtype: 'fieldcontainer',
                                                    flex: 1,
                                                    layout: {
                                                        type: 'vbox',
                                                        align: 'stretch'
                                                    },
                                                    bodyPadding: 5,
                                                    margin: '10 10 0 10',
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
                                                            bodyPadding: 5,
                                                            margin: '10 10 0 10',
                                                            items: [
                                                                {
                                                                    xtype: 'checkboxfield',
                                                                    name: 'servicio_mayor',
                                                                    id: 'servicio_mayor',
                                                                    boxLabel: 'Servicio mayor',
                                                                    margin: '10 10 0 10',
                                                                    width: '50%',
                                                                    afterLabelTextTpl: [
                                                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                    ],
                                                                    listeners: {
                                                                        change: function () {
                                                                            if (this.getValue() === true) {
                                                                                Ext.getCmp('cap_banco_mayor').setHidden(false);
                                                                                Ext.getCmp('cant_transf').setHidden(false);
                                                                                Ext.getCmp('metro_regresivo').setValue(false);
                                                                                Ext.getCmp('servicio_prepago').setValue(false);
                                                                                Ext.getCmp('cap_banco').setHidden(true);
                                                                            }
                                                                        }
                                                                    }
                                                                }, {
                                                                    xtype: 'checkboxfield',
                                                                    name: 'servicio_prepago',
                                                                    id: 'servicio_prepago',
                                                                    margin: '10 10 0 10',
                                                                    boxLabel: 'Servicio Menor',
                                                                    width: '50%',
                                                                    afterLabelTextTpl: [
                                                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                    ],
                                                                    listeners: {
                                                                        change: function () {
                                                                            if (this.getValue() === true) {
                                                                                Ext.getCmp('cap_banco_mayor').setHidden(false);
                                                                                Ext.getCmp('metro_regresivo').setHidden(false);
                                                                                Ext.getCmp('cant_transf').setHidden(false);
                                                                                Ext.getCmp('servicio_mayor').setValue(false);
                                                                                Ext.getCmp('cap_banco').setHidden(true);
                                                                            }else{
                                                                                Ext.getCmp('cap_banco_mayor').setHidden(true);
                                                                                Ext.getCmp('metro_regresivo').setHidden(true);
                                                                                Ext.getCmp('cant_transf').setHidden(true);
                                                                                Ext.getCmp('cap_banco').setHidden(false);
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            ]
                                                        }, {

                                                            xtype: 'checkboxfield',
                                                            name: 'metro_regresivo',
                                                            id: 'metro_regresivo',
                                                            hidden: true,
                                                            margin: '10 10 0 311',
                                                            boxLabel: 'Metro regresivo',
                                                        },

                                                        {
                                                            xtype: 'numberfield',
                                                            name: 'MaximaDemandaContratada',
                                                            id: 'MaximaDemandaContratada',
                                                            fieldLabel: 'Máx. Demanda Contratada(DC)',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            value: 0,
                                                            minValue: 0,
                                                            maskRe: /[0-9]/
                                                        },
                                                        {
                                                            xtype: 'numberfield',
                                                            name: 'factor_combustible',
                                                            id: 'factor_combustible',
                                                            fieldLabel: 'Factor Combustible',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            value: 0,
                                                            minValue: 0,
                                                            maskRe: /[0-9]/,
                                                            decimalPrecision: 4,
                                                            decimalSeparator: '.'
                                                        },
                                                        /* {
                                                         xtype:'numberfield',
                                                         name:'indice_consumo',
                                                         id:'indice_consumo',
                                                         fieldLabel:'Indice Consumo',
                                                         afterLabelTextTpl:[
                                                         '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                         ],
                                                         value:0,
                                                         minValue:0,
                                                         decimalSeparator:'.',
                                                         maskRe:/[0-9]/
                                                         }*/
                                                    ]
                                                },
                                                /* {
                                                 xtype:'fieldcontainer',
                                                 flex: 1,
                                                 layout:{
                                                 type:'vbox',
                                                 align:'stretch'
                                                 },
                                                 bodyPadding: 5,
                                                 margin: '10 10 0 10',
                                                 fieldDefaults:{
                                                 msgTarget:'side',
                                                 labelAlign:'top',
                                                 allowBlank:false
                                                 },
                                                 items:[
                                                 {
                                                 xtype:'numberfield',
                                                 name:'consumo_prom_anno',
                                                 id:'consumo_prom_anno',
                                                 fieldLabel:'Consumo Promedio Año',
                                                 afterLabelTextTpl:[
                                                 '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                 ],
                                                 value:0,
                                                 minValue:0,
                                                 maskRe:/[0-9]/
                                                 },
                                                 {
                                                 xtype:'numberfield',
                                                 name:'consumo_prom_plan',
                                                 id:'consumo_prom_plan',
                                                 fieldLabel:'Consumo Promedio Plan',
                                                 afterLabelTextTpl:[
                                                 '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                 ],
                                                 value:0,
                                                 minValue:0,
                                                 maskRe:/[0-9]/
                                                 },
                                                 {
                                                 xtype:'numberfield',
                                                 name:'consumo_prom_real',
                                                 id:'consumo_prom_real',
                                                 fieldLabel:'Consumo Promedio Real',
                                                 afterLabelTextTpl:[
                                                 '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                 ],
                                                 value:0,
                                                 minValue:0,
                                                 maskRe:/[0-9]/
                                                 }
                                                 ]
                                                 }*/
                                            ]
                                        }
                                    ]
                                },
                                {
                                    xtype: 'panel',
                                    title: 'Datos OBE',
                                    flex: 1,
                                    bodyPadding: 5,
                                    closable: false,
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    items: [
                                        {
                                            xtype: 'fieldcontainer',
                                            layout: {
                                                type: 'hbox',
                                                align: 'stretch'
                                            },
                                            items: [
                                                {
                                                    xtype: 'fieldcontainer',
                                                    flex: 1,
                                                    layout: {
                                                        type: 'vbox',
                                                        align: 'stretch'
                                                    },
                                                    bodyPadding: 5,
                                                    margin: '10 10 0 10',
                                                    fieldDefaults: {
                                                        msgTarget: 'side',
                                                        labelAlign: 'top',
                                                        allowBlank: false
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'combobox',
                                                            name: 'tipo_servicio',
                                                            id: 'tipo_servicio',
                                                            fieldLabel: 'Seleccione tipo servicio',
                                                            labelWidth: 140,
                                                            store: tipo,
                                                            displayField: 'tipo_servicio',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            valueField: 'id',
                                                            typeAhead: true,
                                                            queryMode: 'local',
                                                            forceSelection: true,
                                                            triggerAction: 'all',
                                                            emptyText: 'Seleccione la tipo servicio...',
                                                            selectOnFocus: true,
                                                            editable: true,
                                                            listeners: {
                                                                select: function (combo, record, eOpts) {
                                                                    banco.filter('tipo', record.data.tipo_servicio);
                                                                }
                                                            }
                                                        },
                                                        {
                                                            xtype: 'combobox',
                                                            name: 'turno_trabajo',
                                                            id: 'turno_trabajo',
                                                            fieldLabel: 'Seleccione Turno Trabajo',
                                                            labelWidth: 140,
                                                            store: turnos,
                                                            displayField: 'turno',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            valueField: 'id',
                                                            typeAhead: true,
                                                            queryMode: 'local',
                                                            forceSelection: true,
                                                            triggerAction: 'all',
                                                            emptyText: 'Seleccione el turno...',
                                                            selectOnFocus: true,
                                                            editable: true
                                                        },
                                                        {
                                                            xtype: 'fieldcontainer',
                                                            layout: 'hbox',
                                                            items: [{
                                                                xtype: 'numberfield',
                                                                name: 'cant_transf',
                                                                id: 'cant_transf',
                                                                fieldLabel: 'Cant. Tranf',
                                                                margin: '0 10 0 0',
                                                                labelWidth: 140,
                                                                allowBlank: true,
                                                                width: '50%',
                                                                emptyText: 'Cantidad...',
                                                                selectOnFocus: true,
                                                                editable: true,
                                                                listeners: {
                                                                    change: function () {
                                                                        if (this.getValue() == 0) {
                                                                            Ext.getCmp('fieldTransf').setHidden(true);
                                                                            Ext.getCmp('container1').setHidden(true);
                                                                            Ext.getCmp('container2').setHidden(true);
                                                                            Ext.getCmp('container3').setHidden(true);
                                                                            Ext.getCmp('container4').setHidden(true);
                                                                            Ext.getCmp('container5').setHidden(true);
                                                                        }
                                                                        if (this.getValue() == 1) {
                                                                            Ext.getCmp('fieldTransf').setHidden(false);
                                                                            Ext.getCmp('container1').setHidden(false);
                                                                            Ext.getCmp('container2').setHidden(true);
                                                                            Ext.getCmp('container3').setHidden(true);
                                                                            Ext.getCmp('container4').setHidden(true);
                                                                            Ext.getCmp('container5').setHidden(true);
                                                                        }
                                                                        if (this.getValue() == 2) {
                                                                            Ext.getCmp('fieldTransf').setHidden(false);
                                                                            Ext.getCmp('container1').setHidden(false);
                                                                            Ext.getCmp('container2').setHidden(false);
                                                                            Ext.getCmp('container3').setHidden(true);
                                                                            Ext.getCmp('container4').setHidden(true);
                                                                            Ext.getCmp('container5').setHidden(true);
                                                                        }
                                                                        if (this.getValue() == 3) {
                                                                            Ext.getCmp('fieldTransf').setHidden(false);
                                                                            Ext.getCmp('container1').setHidden(false);
                                                                            Ext.getCmp('container2').setHidden(false);
                                                                            Ext.getCmp('container3').setHidden(false);
                                                                            Ext.getCmp('container4').setHidden(true);
                                                                            Ext.getCmp('container5').setHidden(true);
                                                                        }
                                                                        if (this.getValue() == 4) {
                                                                            Ext.getCmp('fieldTransf').setHidden(false);
                                                                            Ext.getCmp('container1').setHidden(false);
                                                                            Ext.getCmp('container2').setHidden(false);
                                                                            Ext.getCmp('container3').setHidden(false);
                                                                            Ext.getCmp('container4').setHidden(false);
                                                                            Ext.getCmp('container5').setHidden(true);
                                                                        }
                                                                        if (this.getValue() == 5) {
                                                                            Ext.getCmp('fieldTransf').setHidden(false);
                                                                            Ext.getCmp('container1').setHidden(false);
                                                                            Ext.getCmp('container2').setHidden(false);
                                                                            Ext.getCmp('container3').setHidden(false);
                                                                            Ext.getCmp('container4').setHidden(false);
                                                                            Ext.getCmp('container5').setHidden(false);
                                                                        }
                                                                    }
                                                                }
                                                            }, {
                                                                xtype: 'numberfield',
                                                                fieldLabel: 'Cap. Banco',
                                                                labelWidth: 140,
                                                                id: 'cap_banco_mayor',
                                                                format: '0.00',
                                                                hidden: true,
                                                                allowBlank: true,
                                                                name: 'cap_banco_mayor',
                                                                margin: '0 1 0 0',
                                                                editable: false,
                                                                width: '50%'
                                                            }, {
                                                                xtype: 'combobox',
                                                                name: 'cap_banco',
                                                                id: 'cap_banco',
                                                                allowBlank: true,
                                                                store: banco,
                                                                queryMode: 'local',
                                                                displayField: 'capacidad',
                                                                valueField: 'id',
                                                                hidden: true,
                                                                fieldLabel: 'Cap. Banco. Tranf.',
                                                                labelWidth: 140,
                                                                afterLabelTextTpl: [
                                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                ]
                                                            }]

                                                        }
                                                    ]
                                                }, {
                                                    xtype: 'fieldcontainer',
                                                    flex: 1,
                                                    layout: {
                                                        type: 'vbox',
                                                        align: 'stretch'
                                                    },
                                                    bodyPadding: 5,
                                                    margin: '10 10 0 10',
                                                    fieldDefaults: {
                                                        msgTarget: 'side',
                                                        labelAlign: 'top',
                                                        allowBlank: false
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'combobox',
                                                            name: 'tarifaid',
                                                            id: 'tarifaid',
                                                            fieldLabel: 'Tarifa(NTA)',
                                                            labelWidth: 140,
                                                            store: _storetarifa,
                                                            displayField: 'nombre',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            valueField: 'id',
                                                            typeAhead: true,
                                                            queryMode: 'local',
                                                            forceSelection: true,
                                                            triggerAction: 'all',
                                                            emptyText: 'Seleccione la tarifa...',
                                                            selectOnFocus: true,
                                                            editable: true
                                                        },
                                                        {
                                                            xtype: 'combobox',
                                                            name: 'nactividadid',
                                                            id: 'nactividadid',
                                                            fieldLabel: 'Actividad ',
                                                            labelWidth: 140,

                                                            store: _storeactividad,
                                                            displayField: 'nombre',
                                                            afterLabelTextTpl: [
                                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                            ],
                                                            valueField: 'id',
                                                            typeAhead: true,
                                                            queryMode: 'local',
                                                            forceSelection: true,
                                                            triggerAction: 'all',
                                                            emptyText: 'Seleccione la Actividad...',
                                                            selectOnFocus: true,
                                                            editable: true,
                                                            listeners: {
                                                                select: function (combo, record, eOpts) {
                                                                    var var_recor = _storeactividad.findRecord('id', record.data.id);

                                                                    var um_actividadid = var_recor.data.um_actividad;
                                                                    var um_actividadnombre = var_recor.data.um_actividad_nombre;
                                                                    Ext.getCmp('nombreum_nilvel_actividadid').setValue(um_actividadnombre);
                                                                }

                                                            }
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'nombreum_nilvel_actividadid',
                                                            id: 'nombreum_nilvel_actividadid',
                                                            fieldLabel: 'UM Nivel Actividad  ',
                                                            labelWidth: 140,
                                                            allowBlank: true,

                                                        }
                                                    ]
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'fieldcontainer',
                                            width: '90%',
                                            id: 'fieldTransf',
                                            layout: 'hbox',
                                            items: [{
                                                xtype: 'fieldcontainer',
                                                hidden: true,
                                                id: 'container1',
                                                items: [{
                                                    xtype: 'combobox',
                                                    name: 'cap_transf1',
                                                    store: banco,
                                                    queryMode: 'local',
                                                    displayField: 'capacidad',
                                                    valueField: 'id',
                                                    format: '0.00',
                                                    id: 'cap_transf1',
                                                    width: 100,
                                                    margin: '10 0 10 10',
                                                    fieldLabel: 'CT1',
                                                    allowBlank: true,
                                                    labelWidth: 30,
                                                    // maxValue: 5,
                                                    // minValue: 0,
                                                    afterLabelTextTpl: [
                                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                    ],
                                                    listeners: {
                                                        select: function () {
                                                            var valor1 = this.getDisplayValue();
                                                            console.log('select ' + valor1)
                                                            Ext.getCmp('cap_banco_mayor').setValue(valor1)
                                                        }

                                                    }
                                                }]
                                            }, {
                                                xtype: 'fieldcontainer',
                                                hidden: true,
                                                id: 'container2',
                                                items: [{
                                                    xtype: 'combobox',
                                                    name: 'cap_transf2',
                                                    id: 'cap_transf2',
                                                    allowBlank: true,
                                                    store: banco,
                                                    queryMode: 'local',
                                                    displayField: 'capacidad',
                                                    valueField: 'id',
                                                    width: 100,
                                                    format: '0.00',
                                                    margin: '10 0 10 10',
                                                    fieldLabel: 'CT2',
                                                    labelWidth: 30,
                                                    // maxValue: 5,
                                                    // minValue: 0,
                                                    afterLabelTextTpl: [
                                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                    ],
                                                    listeners: {
                                                        select: function () {
                                                            var valor1 = parseFloat(Ext.getCmp('cap_transf1').getDisplayValue());
                                                            var valor2 = parseFloat(this.getDisplayValue());
                                                            var valor = valor1 + valor2;
                                                            Ext.getCmp('cap_banco_mayor').setValue(valor)
                                                        }

                                                    }
                                                }]
                                            },
                                                {
                                                    xtype: 'fieldcontainer',
                                                    hidden: true,
                                                    id: 'container3',
                                                    items: [{
                                                        xtype: 'combobox',
                                                        name: 'cap_transf3',
                                                        id: 'cap_transf3',
                                                        store: banco,
                                                        queryMode: 'local',
                                                        displayField: 'capacidad',
                                                        valueField: 'id',
                                                        allowBlank: true,
                                                        width: 100,
                                                        margin: '10 0 10 10',
                                                        fieldLabel: 'CT3',
                                                        format: '0.00',
                                                        labelWidth: 30,
                                                        // maxValue: 5,
                                                        // minValue: 0,
                                                        afterLabelTextTpl: [
                                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                        ],
                                                        listeners: {
                                                            select: function () {
                                                                var valor1 = parseFloat(Ext.getCmp('cap_transf1').getDisplayValue());
                                                                var valor2 = parseFloat(Ext.getCmp('cap_transf2').getDisplayValue());
                                                                var valor3 = parseFloat(this.getDisplayValue());
                                                                var valor = valor1 + valor2 + valor3;
                                                                Ext.getCmp('cap_banco_mayor').setValue(valor)
                                                            }

                                                        }
                                                    }]
                                                },
                                                {
                                                    xtype: 'fieldcontainer',
                                                    hidden: true,
                                                    id: 'container4',
                                                    items: [{
                                                        xtype: 'combobox',
                                                        name: 'cap_transf4',
                                                        id: 'cap_transf4',
                                                        store: banco,
                                                        queryMode: 'local',
                                                        displayField: 'capacidad',
                                                        allowBlank: true,
                                                        valueField: 'id',
                                                        width: 100,
                                                        margin: '10 0 10 10',
                                                        fieldLabel: 'CT4',
                                                        labelWidth: 30,
                                                        maxValue: 5,
                                                        minValue: 0,
                                                        afterLabelTextTpl: [
                                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                        ],
                                                        listeners: {
                                                            select: function () {
                                                                var valor1 = parseFloat(Ext.getCmp('cap_transf1').getDisplayValue());
                                                                var valor2 = parseFloat(Ext.getCmp('cap_transf2').getDisplayValue());
                                                                var valor3 = parseFloat(Ext.getCmp('cap_transf3').getDisplayValue());
                                                                var valor4 = parseFloat(this.getDisplayValue());
                                                                var valor = valor1 + valor2 + valor3 + valor4;
                                                                Ext.getCmp('cap_banco_mayor').setValue(valor)
                                                            }

                                                        }
                                                    }]
                                                },
                                                {
                                                    xtype: 'fieldcontainer',
                                                    hidden: true,
                                                    id: 'container5',
                                                    items: [{
                                                        xtype: 'combobox',
                                                        name: 'cap_transf5',
                                                        id: 'cap_transf5',
                                                        store: banco,
                                                        allowBlank: true,
                                                        queryMode: 'local',
                                                        displayField: 'capacidad',
                                                        valueField: 'id',
                                                        width: 100,
                                                        margin: '10 0 10 10',
                                                        fieldLabel: 'CT5',
                                                        labelWidth: 30,
                                                        maxValue: 5,
                                                        minValue: 0,
                                                        afterLabelTextTpl: [
                                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                        ],
                                                        listeners: {
                                                            select: function () {
                                                                var valor1 = parseFloat(Ext.getCmp('cap_transf1').getDisplayValue());
                                                                var valor2 = parseFloat(Ext.getCmp('cap_transf2').getDisplayValue());
                                                                var valor3 = parseFloat(Ext.getCmp('cap_transf3').getDisplayValue());
                                                                var valor4 = parseFloat(Ext.getCmp('cap_transf4').getDisplayValue());
                                                                var valor5 = parseFloat(this.getDisplayValue());
                                                                var valor = valor1 + valor2 + valor3 + valor4 + valor5;
                                                                Ext.getCmp('cap_banco_mayor').setValue(valor)
                                                            }

                                                        }
                                                    }]
                                                }
                                            ]
                                        }
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

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'servicios_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            // _storeactividad.filterBy(function (record, id) {
            //     console.log(record.get('codigogae') > 40000)
            //     if (record.get('codigogae') > 40000) {
            //         return record;
            //     }
            //
            // });

            Ext.create('Portadores.servicios.Window', {
                title: 'Adicionar Servicios',
                id: 'window_servicios_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_servicios_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/servicio/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('gridserviciosId').getStore().loadPage(1);
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                window.down('form').getForm().markInvalid(response.errors);
                                            }
                                        }
                                        window.show();
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
                            Ext.getCmp('window_servicios_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'servicios_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('gridserviciosId').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.servicios.Window', {
                title: 'Modificar servicios',
                id: 'window_servicios_id',
                listeners: {
                    afterrender: function (This) {
                        Ext.getCmp('provicianid').setListeners(
                            {
                                change: function (This, newValue, oldValue, eOpts) {
                                    Ext.getCmp('municipioid').getStore().load({
                                        params: {
                                            id: newValue
                                        }
                                    });
                                }
                            });

                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/servicio/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('gridserviciosId').getStore().loadPage(1);

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
                            Ext.getCmp('window_servicios_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'servicios_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('gridserviciosId').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar servicio?',
                message: Ext.String.format('¿Está seguro que desea eliminar el servicio <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre_servicio')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/servicio/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('gridserviciosId').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('servicios_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});