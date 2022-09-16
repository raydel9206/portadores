Ext.onReady(function () {

    let cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });

    let _storeservicios = Ext.create('Ext.data.JsonStore', {
        storeId: '_storeservicios',
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

    let acumulados = Ext.create('Ext.data.JsonStore', {
        storeId: '_storesacumulados',
        fields: [
            {name: 'id'},
            {name: 'id_plan'},
            {name: 'id_real'},
            {name: 'id_plan_pico'},
            {name: 'id_real_pico'},
            {name: 'id_diferencia'},
            {name: 'id_plan_real'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/autolectura_tresescalas/acumulados'),
            reader: {
                rootProperty: 'array'
            }
        },
        autoLoad: false,
        listeners: {
            load: function (This, records, successful, operation) {
                if (records) {
                    if (records !== 0) {
                        Ext.getCmp('id_plan').setValue(Ext.util.Format.round(records[0].data.acumulado_plan, 2));
                        Ext.getCmp('id_real').setValue(Ext.util.Format.round(records[0].data.acumulado_real, 2));
                        Ext.getCmp('id_plan_pico').setValue(Ext.util.Format.round(records[0].data.acumulado_plan_pico, 2));
                        Ext.getCmp('id_real_pico').setValue(Ext.util.Format.round(records[0].data.acumulado_real_pico, 2));
                        Ext.getCmp('id_diferencia').setValue(Ext.util.Format.round(records[0].data.diferencia, 2));
                        Ext.getCmp('id_plan_real').setValue(Ext.util.Format.round(records[0].data._plan_real, 2));
                    }
                }

            }
        }
    });

    let store_autoinspeccion = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_autoinspeccion',
        fields: [
            {name: 'id'},
            {name: 'id_plan'},
            {name: 'id_real'},
            {name: 'id_plan_pico'},
            {name: 'id_real_pico'},
            {name: 'id_diferencia'},
            {name: 'id_plan_real'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/autolectura_tresescalas/loadAutoinspeccion'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
    });

    Ext.define('Portadores.autolectura.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        plain: true,
        resizable: false,

        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: '100%',
                    height: '100%',
                    layout: 'anchor',
                    bodyStyle: 'padding:5px 5px 0',
                    fieldDefaults: {
                        labelAlign: 'top',
                        msgTarget: 'qtip'
                    },
                    items: [
                        {

                            xtype: 'fieldset',
                            flex: 1,
                            title: 'Lectura Metro Activo',

                            layout: 'hbox',
                            border: true,
                            // margin: '10 10 10 10',
                            collapsible: true,
                            collapsed: false,
                            labelAlign: 'right',
                            labelWidth: 90,
                            //msgTarget: 'qtip',
                            items: [

                                {

                                    xtype: 'textfield',
                                    name: 'lectura_pico',
                                    id: 'lectura_pico',
                                    margin: '10 10 10 10',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    fieldLabel: 'Lectura Pico',
                                    // allowBlank: false, // requires a non-empty value
                                    maskRe: /[0-9 .]/,
                                    bodyPadding: 10,
                                    value: 0


                                },
                                {

                                    xtype: 'textfield',
                                    name: 'lectura_mad',
                                    margin: '10 10 10 10',
                                    id: 'lectura_mad',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    fieldLabel: 'Lectura Madrugada',
                                    // allowBlank: false, // requires a non-empty value
                                    maskRe: /[0-9 .]/,
                                    bodyPadding: 10,
                                    value: 0


                                },
                                {

                                    xtype: 'textfield',
                                    name: 'lectura_dia',
                                    margin: '10 10 10 10',
                                    id: 'lectura_dia',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    fieldLabel: 'Lectura Día',
                                    // allowBlank: false, // requires a non-empty value
                                    maskRe: /[0-9 .]/,
                                    bodyPadding: 10,
                                    value: 0


                                }
                            ]
                        },
                        {

                            xtype: 'fieldset',
                            flex: 1,
                            layout: 'hbox',
                            title: 'Lectura Máxima Demanda',
                            collapsible: true,
                            border: true,
                            //margin: '10 10 10 10',
                            collapsed: false,
                            labelAlign: 'right',
                            labelWidth: 90,
                            items: [

                                {

                                    xtype: 'textfield',
                                    name: 'lectura_pico_maxD',
                                    id: 'lectura_pico_maxD',
                                    margin: '10 10 10 10',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    fieldLabel: 'Lectura Pico',
                                    // allowBlank: false, // requires a non-empty value
                                    maskRe: /[0-9 .]/,
                                    bodyPadding: 10,
                                    value: 0


                                },
                                {

                                    xtype: 'textfield',
                                    name: 'lectura_mad_maxD',
                                    margin: '10 10 10 10',
                                    id: 'lectura_mad_maxD',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    fieldLabel: 'Lectura Madrugada',
                                    // allowBlank: false, // requires a non-empty value
                                    maskRe: /[0-9 .]/,
                                    bodyPadding: 10,
                                    value: 0


                                },
                                {

                                    xtype: 'textfield',
                                    name: 'lectura_dia_maxD',
                                    margin: '10 10 10 10',
                                    id: 'lectura_dia_maxD',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    fieldLabel: 'Lectura Día',
                                    maskRe: /[0-9 .]/,
                                    bodyPadding: 10,
                                    value: 0

                                }


                            ]

                        },

                        {

                            xtype: 'fieldcontainer',
                            flex: 1,
                            layout: 'hbox',
                            border: false,
                            margin: '10 10 10 10',
                            collapsible: false,
                            labelAlign: 'right',
                            labelWidth: 90,
                            items: [

                                {

                                    xtype: 'textfield',
                                    name: 'lectura_reactivo',
                                    margin: '10 10 10 10',
                                    id: 'lectura_reactivo',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    fieldLabel: 'Lectura Reactivo',
                                    //allowBlank: false, // requires a non-empty value
                                    maskRe: /[0-9 .]/,
                                    bodyPadding: 10,
                                    value: 0

                                },
                                {
                                    xtype: 'datefield',
                                    name: 'fecha_lectura',
                                    id: 'fecha_lectura',
                                    margin: '10 10 10 10',
                                    fieldLabel: 'Fecha de Autolectura',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    // listeners: {
                                    //     afterrender: function (This) {
                                    //         This.setMaxValue(new Date());
                                    //     }
                                    // }
                                }
                            ]
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });


    Ext.define('bitacora', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'nromes', type: 'int'},
            {name: 'mes', type: 'string'},
            {name: 'cant_dias', type: 'int'},
            {name: 'id_l', type: 'string'},
            {name: 'lectura', type: 'string'},
        ]
    });

    Ext.define('Lecturas', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_l', type: 'string'},
            {name: 'lectura', type: 'string'}
        ]
    });

    let meses = Ext.create('Ext.data.JsonStore', {
        model: 'bitacora',
        id: 'id_store_meses',
        data: [
            {id: '1', nromes: 1, mes: 'Enero', cant_dias: 31},
            {id: '2', nromes: 2, mes: 'Febrero', cant_dias: 28},
            {id: '3', nromes: 3, mes: 'Marzo', cant_dias: 31},
            {id: '4', nromes: 4, mes: 'Abril', cant_dias: 31},
            {id: '5', nromes: 5, mes: 'Mayo', cant_dias: 31},
            {id: '6', nromes: 6, mes: 'Junio', cant_dias: 31},
            {id: '7', nromes: 7, mes: 'Julio', cant_dias: 31},
            {id: '8', nromes: 8, mes: 'Agosto', cant_dias: 31},
            {id: '9', nromes: 9, mes: 'Septiembre', cant_dias: 31},
            {id: '10', nromes: 10, mes: 'Octubre', cant_dias: 31},
            {id: '11', nromes: 11, mes: 'Noviembre', cant_dias: 30},
            {id: '12', nromes: 12, mes: 'Diciembre', cant_dias: 31}
        ]
    });

    let store_bitacora = Ext.create('Ext.data.JsonStore', {
        id: 'store_bitacora',
        fields: [
            {id: '1', nromes: 1, mes: 'Enero', cant_dias: 31},
            {id: '2', nromes: 2, mes: 'Febrero', cant_dias: 28},
            {id: '3', nromes: 3, mes: 'Marzo', cant_dias: 31},
            {id: '4', nromes: 4, mes: 'Abril', cant_dias: 31},
            {id: '5', nromes: 5, mes: 'Mayo', cant_dias: 31},
            {id: '6', nromes: 6, mes: 'Junio', cant_dias: 31},
            {id: '7', nromes: 7, mes: 'Julio', cant_dias: 31},
            {id: '8', nromes: 8, mes: 'Agosto', cant_dias: 31},
            {id: '9', nromes: 9, mes: 'Septiembre', cant_dias: 31},
            {id: '10', nromes: 10, mes: 'Octubre', cant_dias: 31},
            {id: '11', nromes: 11, mes: 'Noviembre', cant_dias: 30},
            {id: '12', nromes: 12, mes: 'Diciembre', cant_dias: 31}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/autolectura_tresescalas/bitacora'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            load: function (This, records, successful, operation) {
                if (records) {
                    if (records !== 0) {
                        Ext.getCmp('bitacora_print').enable();
                        Ext.getCmp('bitacora_autoinspeccion').enable();
                    }
                } else {
                    Ext.getCmp('grid_bitacora').getStore().removeAll();
                    App.showAlert('Al Mes Seleccionado no se le ha realizado la Autolectura', 'danger', 3500);
                }
            }
        }
    });

    let tipo_lectura = Ext.create('Ext.data.Store', {
        model: 'Lecturas',
        id: 'id_store_tipolectura',
        data: [
            {id_l: 'ln', lectura: 'Lectura Normal'},
            {id_l: 'lp', lectura: 'Lectura Pico'}
        ]
    });

    let _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'autolecturan_btn_add',
        text: 'Autolectura',
        glyph: 0xf0e7,
        width: 100,
        handler: function (This, e) {
            let serviciosid = Ext.getCmp('id_grid_autolecturaservicios').getSelectionModel().getLastSelected();
            if (serviciosid) {
                let obj = {};
                obj.servicio = serviciosid.data.id;
                obj.mes = App.selected_month;
                let result_desglose = false;
                App.request('GET', App.buildURL('/portadores/autolectura_tresescalas/existedesglose'), obj, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) {
                            result_desglose = true;
                        }
                    },
                    function (response) {
                    }, null, true);

                setTimeout(() => {
                    if (result_desglose === true) {
                        Ext.create('Portadores.autolectura.Window', {
                            title: 'Autolectura',
                            id: 'window_autolectura_id',
                            buttons: [
                                {
                                    text: 'Aceptar',
                                    width: 70,
                                    handler: function () {
                                        let window = Ext.getCmp('window_autolectura_id');
                                        let form = window.down('form').getForm();
                                        let datos = form.getValues();
                                        datos.serviciosid = serviciosid.data.id;
                                        if (form.isValid()) {
                                            App.request('POST', App.buildURL('/portadores/autolectura_tresescalas/addAutolecturaTresescalas'), datos, null, null,
                                                function (response) {
                                                    if (response && response.hasOwnProperty('success') && response.success) {
                                                        window.close();
                                                        Ext.getCmp('id_grid_autolectura').getStore().load({
                                                            params: {
                                                                'id': serviciosid
                                                            }
                                                        });
                                                        Ext.getCmp('id_grid_autolectura').getView();
                                                    } else {
                                                        window.show();
                                                        form.markInvalid(response.message);
                                                    }
                                                }, function (response) {

                                                });
                                        }
                                    }
                                }, {
                                    text: 'Cancelar',
                                    width: 70,
                                    handler: function () {
                                        Ext.getCmp('window_autolectura_id').close()
                                    }
                                }
                            ]
                        }).show();
                    } else {
                        App.showAlert('No Puede Realizar Autolectura de un servicio no desglosado', 'danger', 3500);
                    }
                }, 2000);
            } else {
                App.showAlert('Información', 'Seleccione un Servicio', 'warning', 3500)
            }
        }
    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'autolecturan_btn_mod',
        text: 'Modificar',
        glyph: 0xf303,
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_autolectura').getSelectionModel().getLastSelected();
            let service = Ext.getCmp('id_grid_autolecturaservicios').getSelectionModel().getLastSelected();

            if (!selection) {
                App.showAlert('Por favor seleccione la autolectura del ultimo dia ', 'warning', 3500);
            }
            else {
                let object = {};
                object.fecha = selection.data.fecha;
                object.servicio = service.id;
                App.request('GET', App.buildURL('/portadores/autolectura_tresescalas/getIsLastLectMayor'), object, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) {
                            let win = Ext.create('Portadores.autolectura.Window', {
                                title: 'Autolectura',
                                id: 'window_autolectura_id',
                                buttons: [
                                    {
                                        text: 'Aceptar',
                                        width: 70,
                                        handler: function () {
                                            let form = win.down('form').getForm();
                                            if (form.isValid()) {
                                                win.hide();
                                                let obj = form.getValues();
                                                obj.id = selection.data.id;
                                                obj.fecha_autolectura = selection.data.fecha_lectura;
                                                obj.servicio = service.id;
                                                App.request('POST', App.buildURL('/portadores/autolectura_tresescalas/modAutolecturaTresescalas'), obj, null, null,
                                                    function (response) {
                                                        if (response && response.hasOwnProperty('success') && response.success) {
                                                            win.close();
                                                            Ext.getCmp('id_grid_autolectura').getStore().load({
                                                                id: service
                                                            });
                                                        } else {
                                                            win.show();
                                                            form.markInvalid(response.message);
                                                        }
                                                    }, function (response) {
                                                    });
                                            }
                                        }
                                    },
                                    {
                                        text: 'Cancelar',
                                        width: 70,
                                        handler: function () {
                                            Ext.getCmp('window_autolectura_id').close();
                                        }
                                    }
                                ]
                            }).show();
                            win.down('form').loadRecord(selection);
                        } else {
                            App.showAlert('No puede entrar autolecturas de dias anteriores a la ultima autolectura tomada para este servicio.', 'danger', 3500);
                        }
                    },
                    function (response) {
                    }, null, true);

            }
        }
    });

    let _btn_bitacora = Ext.create('Ext.button.MyButton', {
        id: 'autolecturan__btn_bitacora',
        text: 'Bitácora',
        tooltip: 'Se muestra la bitacora',
        glyph: 0xf037,
        width: 100,
        handler: function () {
            let servicioid = Ext.getCmp('id_grid_autolecturaservicios').getSelectionModel().getLastSelected();
            if (servicioid) {
                Ext.create('Ext.window.Window', {
                    title: 'Bitácora',
                    id: 'window_bitacora_id',
                    height: 600,
                    width: 1150,
                    modal: true,
                    layout: 'fit',
                    items: [
                        {
                            xtype: 'gridpanel',
                            frame: true,
                            id: 'grid_bitacora',
                            margin: '5 5 5 5',
                            columns: [
                                {
                                    text: '<strong>Día</strong>',
                                    dataIndex: 'dia',
                                    width: 80,
                                    align: 'center',
                                },
                                {
                                    text: '<strong>Lectura del contador</strong>',
                                    columns: [
                                        {
                                            text: '<strong>MAD</strong>',
                                            dataIndex: 'lectura_mad',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            // cls: 'grid-header-phone',
                                            groupable: false
                                        },

                                        {
                                            text: '<strong>DIA</strong>',
                                            dataIndex: 'lectura_dia',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            groupable: false
                                        },

                                        {
                                            text: '<strong>PICO</strong>',
                                            dataIndex: 'lectura_pico',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            groupable: false
                                        }, {
                                            text: '<strong>REACT</strong>',
                                            dataIndex: 'lectura_react',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            groupable: false
                                        }
                                    ]
                                },
                                {
                                    text: '<strong>Consumo Diario Kwh Y Kvarh</strong>',
                                    columns: [
                                        {
                                            text: '<strong>MAD</strong>',
                                            dataIndex: 'consumo_mad',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            formatter: "number('0.00')",
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>DIA</strong>',
                                            dataIndex: 'consumo_dia',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>PICO</strong>',
                                            dataIndex: 'consumo_pico',
                                            formatter: "number('0.00')",
                                            width: 100,
                                            align: 'center',
                                            sortable: true,
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>REACT</strong>',
                                            dataIndex: 'consumo_total_react',
                                            formatter: "number('0.00')",
                                            width: 100,
                                            align: 'center',
                                            sortable: true,
                                            groupable: false
                                        }
                                    ]
                                }, {
                                    text: '<strong>Consumo diario Pico KWh</strong>',
                                    columns: [
                                        {
                                            text: '<strong>Plan pico</strong>',
                                            dataIndex: 'plan_pico',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            formatter: "number('0.00')",
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Real Pico</strong>',
                                            dataIndex: 'real_pico',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Plan - Real</strong>',
                                            dataIndex: 'real_plan',
                                            formatter: "number('0.00')",
                                            width: 100,
                                            align: 'center',
                                            sortable: true,
                                            groupable: false
                                        }
                                    ]
                                }, {
                                    text: '<strong>Consumo diario Total KWh</strong>',
                                    columns: [
                                        {
                                            text: '<strong>Plan</strong>',
                                            dataIndex: 'plan_total',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            formatter: "number('0.00')",
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Real</strong>',
                                            dataIndex: 'consumo_total',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Plan - Real</strong>',
                                            dataIndex: 'consumo_total_real_plan',
                                            formatter: "number('0.00')",
                                            width: 100,
                                            align: 'center',
                                            sortable: true,
                                            groupable: false
                                        }
                                    ]
                                }, {
                                    text: '<strong>Consumo acumulado Pico KWh</strong>',
                                    columns: [
                                        {
                                            text: '<strong>Plan Pico</strong>',
                                            dataIndex: 'consumo_pico_acum',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            formatter: "number('0.00')",
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Real Pico</strong>',
                                            dataIndex: 'real_pico_acum',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Plan - Real</strong>',
                                            dataIndex: 'acum_pico_real',
                                            formatter: "number('0.00')",
                                            width: 100,
                                            align: 'center',
                                            sortable: true,
                                            groupable: false
                                        }
                                    ]
                                }, {
                                    text: '<strong>Consumo acumulado Total KWh</strong>',
                                    columns: [
                                        {
                                            text: '<strong>Plan</strong>',
                                            dataIndex: 'consumo_acum',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            formatter: "number('0.00')",
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Real</strong>',
                                            dataIndex: 'real_acum',
                                            width: 100, align: 'center',
                                            sortable: true,
                                            groupable: false
                                        },
                                        {
                                            text: '<strong>Plan - Real</strong>',
                                            dataIndex: 'acum_real',
                                            formatter: "number('0.00')",
                                            width: 100,
                                            align: 'center',
                                            sortable: true,
                                            groupable: false
                                        }, {
                                            text: '<strong>FP</strong>',
                                            dataIndex: 'fp',
                                            formatter: "number('0.00')",
                                            width: 100,
                                            align: 'center',
                                            sortable: true,
                                            groupable: false
                                        }
                                    ]
                                },
                                {
                                    text: '<strong>Firma Responsable</strong>',
                                    width: 250,
                                    align: 'center',
                                    filter: 'string',
                                    sortable: true,
                                    groupable: false

                                }
                            ],
                            store: store_bitacora
                        }
                    ],
                    dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [
                            {
                                xtype: 'combobox',
                                fieldLabel: 'Servicios',
                                id: 'serviciosid',
                                labelWidth: 60,
                                store: _storeservicios,
                                displayField: 'nombre_servicio',
                                valueField: 'id',
                                value: servicioid.data.id,
                                emptyText: 'Seleccione el servicio...'
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: 'Mes a visualizar',
                                id: 'id_combo',
                                labelWidth: 100,
                                store: meses,
                                queryMode: 'local',
                                displayField: 'mes',
                                valueField: 'nromes',
                                value: App.selected_month,
                                emptyText: 'Seleccione el mes...'
                            },
                            {
                                xtype: 'button',
                                width: 30,
                                height: 28,
                                tooltip: 'Buscar',
                                iconCls: 'fas fa-search text-primary',
                                handler: function () {
                                    let servicio = Ext.getCmp('serviciosid').getValue();
                                    if (!servicio) {
                                        App.showAlert('Asegúrese de seleccionar el servicio y la lectura a visualizar', 'info', 2500)
                                    } else {
                                        store_bitacora.load({
                                            params: {
                                                servicio: servicio,
                                                mes: Ext.getCmp('id_combo').getValue(),
                                                anno: App.selected_year
                                            }
                                        });
                                    }
                                }
                            },
                            {
                                xtype: 'button',
                                height: 28,
                                width: 100,
                                id: 'bitacora_print',
                                disabled: true,
                                text: 'Imprimir',
                                iconCls: 'fas fa-print text-primary',
                                handler: function (This, e) {
                                    let store = Ext.getCmp('grid_bitacora').getStore();
                                    let obj = {};
                                    let send = [];
                                    Ext.Array.each(store.data.items, function (valor) {
                                        send.push(valor.data);
                                    });
                                    obj.store = Ext.encode(send);
                                    App.request('POST', App.buildURL('/portadores/autolectura_tresescalas/printBitacora'), obj, null, null,
                                        function (response) {
                                            if (response && response.hasOwnProperty('success') && response.success) {
                                                let newWindow = window.open('', '', 'width=1020, height=800'),
                                                    document = newWindow.document.open();
                                                document.write(response.html);
                                                document.close();
                                                newWindow.print();
                                            }
                                        }, function (response) {
                                        });
                                }
                            },
                            {
                                xtype: 'button',
                                height: 28,
                                width: 120,
                                id: 'bitacora_autoinspeccion',
                                disabled: true,
                                text: 'Autoinspección',
                                iconCls: 'fa fa-eye',
                                handler: function (This, e) {
                                    let a = Ext.getCmp('id_combo').getValue();
                                    let mes = '';
                                    if (a === 1) {
                                        mes = 'Enero';
                                    }
                                    if (a === 2) {
                                        mes = 'Febrero';
                                    }
                                    if (a === 3) {
                                        mes = 'Marzo';
                                    }
                                    if (a === 4) {
                                        mes = 'Abril';
                                    }
                                    if (a === 5) {
                                        mes = 'Mayo';
                                    }
                                    if (a === 6) {
                                        mes = 'Junio';
                                    }
                                    if (a === 7) {
                                        mes = 'Julio';
                                    }
                                    if (a === 8) {
                                        mes = 'Agosto';
                                    }
                                    if (a === 9) {
                                        mes = 'Septiembre';
                                    }
                                    if (a === 10) {
                                        mes = 'Octubre';
                                    }
                                    if (a === 11) {
                                        mes = 'Noviembre';
                                    }
                                    if (a === 12) {
                                        mes = 'Diciembre';
                                    }
                                    Ext.create('Ext.window.Window', {
                                        title: 'Deficiencias o violaciones detectadas en la autoinspección, mes' + ' ' + mes,
                                        id: 'window_autoinspeccion_id',
                                        height: 500,
                                        width: 700,
                                        modal: true,
                                        scrollable: true,
                                        items: [
                                            {
                                                xtype: 'gridpanel',
                                                frame: true,
                                                id: 'grid_autoinspeccion',
                                                store: store_autoinspeccion,
                                                plugins: [cellEditing],
                                                columns: [
                                                    {
                                                        text: '<strong>Día</strong>',
                                                        dataIndex: 'dia',
                                                        width: 140, align: 'center',
                                                        filter: 'string',
                                                        sortable: false,
                                                        groupable: false,
                                                        xtype: 'datecolumn',
                                                        format: 'D,j M, Y '
                                                    },
                                                    {
                                                        text: '<strong>Deficiencias o violaciones detectadas en la autoinspección</strong>',
                                                        columns: [
                                                            {
                                                                text: '<strong>Durante el horario pico</strong>',
                                                                dataIndex: 'durante_horario_pico',
                                                                width: 200,
                                                                align: 'center',
                                                                filter: 'string',
                                                                sortable: false,
                                                                groupable: false,
                                                                editor: {
                                                                    xtype: 'textareafield'
                                                                }
                                                            },
                                                            {
                                                                text: '<strong>Fuera del horario pico</strong>',
                                                                dataIndex: 'fuera_horario_pico',
                                                                width: 200,
                                                                align: 'center',
                                                                filter: 'string',
                                                                sortable: false,
                                                                groupable: false,
                                                                editor: {
                                                                    xtype: 'textareafield'
                                                                }
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        text: '<strong>Responsable</strong>',
                                                        dataIndex: 'responsable',
                                                        flex: 3,
                                                        align: 'center',
                                                        editor: {
                                                            xtype: 'textfield'
                                                        }
                                                    }
                                                ]
                                            }
                                        ],
                                        dockedItems: [{
                                            xtype: 'toolbar',
                                            dock: 'top',
                                            items: [
                                                {
                                                    xtype: 'button',
                                                    height: 28,
                                                    width: 100,
                                                    id: 'autoinspeccion_print',
                                                    disabled: false,
                                                    text: 'Imprimir',
                                                    iconCls: 'fas fa-print text-primary',
                                                    handler: function (This, e) {
                                                        let store = Ext.getCmp('grid_autoinspeccion').getStore();
                                                        let obj = {};
                                                        let send = [];
                                                        Ext.Array.each(store.data.items, function (valor) {
                                                            send.push(valor.data);
                                                        });
                                                        obj.store = Ext.encode(send);
                                                        App.request('POST', App.buildURL('/portadores/autolectura_tresescalas/printBitacoraAutoinspeccion'), obj, null, null,
                                                            function (response) {
                                                                if (response && response.hasOwnProperty('success') && response.success) {
                                                                    let newWindow = window.open('', '', 'width=1020, height=800'),
                                                                        document = newWindow.document.open();
                                                                    document.write(response.html);
                                                                    document.close();
                                                                    newWindow.print();
                                                                }
                                                            }, function (response) {
                                                            });
                                                    }
                                                }
                                            ]
                                        }],
                                        listeners: {
                                            afterrender: function () {
                                                store_autoinspeccion.load({
                                                    params: {
                                                        mes: a
                                                    }
                                                });
                                            }
                                        },
                                        buttons: [
                                            {
                                                xtype: 'button',
                                                width: 70,
                                                text: 'Guardar',
                                                handler: function () {
                                                    let store = Ext.getCmp('grid_autoinspeccion').getStore();
                                                    let obj = {};
                                                    let send = [];
                                                    send.push(a);
                                                    Ext.Array.each(store.data.items, function (valor) {
                                                        send.push(valor.data);

                                                    });
                                                    obj.store = Ext.encode(send);
                                                    App.request('POST', App.buildURL('/portadores/autolectura_tresescalas/addAutoinspeccion'), obj, null, null,
                                                        function (response) {
                                                            if (response && response.hasOwnProperty('success') && response.success) {
                                                                store_autoinspeccion.load({
                                                                    params: {
                                                                        mes: a
                                                                    }
                                                                })
                                                            }
                                                        }, function (response) {
                                                        });
                                                }
                                            },
                                            {
                                                text: 'Cancelar',
                                                width: 70,
                                                handler: function () {
                                                    Ext.getCmp('window_autoinspeccion_id').close()
                                                }
                                            }
                                        ]
                                    }).show();
                                }
                            }
                        ]
                    }],
                    buttons: [
                        {
                            xtype: 'button',
                            height: 25,
                            id: 'bitacora_ver',
                            text: 'Consultar',
                            iconCls: 'fa fa-file-text-o',
                            width: 100,
                            handler: function (This, e) {
                                let html = '<p><h4>Introducción:</h4> </<br/><br/> ' +
                                    'La bitácora se implementará en los centros estatales con consumos mayores de 3 mil KWh mensuales,' +
                                    ' con el objetivo de que el centro se autoinspeccione y controle el uso racional y eficiente de la' +
                                    ' energía eléctrica. <br/><br/>' +
                                    'En la bitácora se reflejarán los resultados de la autoinspección diaria que realizará el energético ' +
                                    'del centro o la persona que se designe. Al cierre del mes se evaluará el cumplimiento de los' +
                                    'indicadores establecidos y se firmará por el energético y el director del centro en las tablas al final de la bitácora. <br/><br/>' +
                                    ' En la bitácora se incluirá el resultado de las inspecciones que recibirá el centro de su Empresa u Organismo superior y de las' +
                                    'inspecciones de la Oficina Nacional de Uso Racional de la Energía (ONURE). En estas inspecciones se verificará que el centro se esté' +
                                    'autoinspeccionando y se comprobará el llenado correcto de la bitácora por parte del centro. <br/><br/>' +

                                    '<h4>Lista de chequeo para la autoinspección: </h4><br/><br/> ' +
                                    'El incumplimiento por parte del centro de las medidas que se indican en la lista de chequeo siguiente,' +
                                    'serán las de deficiencias o violaciones que se reflejarán en esta bitácora. <br/><br/>' +
                                    'En el horario pico de 5 a 9 pm:<br/><br/>' +
                                    '1. Deberán paralizarse los hornos de fundición por arco eléctrico.<br/><br/>' +
                                    '2. Desconexión de los sistemas de clima no tecnológicos.<br/><br/>' +
                                    '3. Paralizar los bombeos de fluido (agua, combustibles, etc.)<br/><br/>' +
                                    'excepto en fuentes de abasto a la población y los expendios de combustible automotor<br/><br/>' +
                                    '4. Paralizar durante este horario los frigoríficos, equipos de refrigeración y cámaras frías.<br/><br/>' +
                                    '5. Reducir la iluminación al mínimo, solo para seguridad.<br/><br/>' +
                                    '+ 6. En los centros principales consumidores, los planes de producción deberán garantizar la utilización<br/><br/>' +
                                    'al máximo de las capacidades productivas evitando el trabajo durante este horario.<br/><br/>' +

                                    '<h4>Fuera del horario:</h4><br/><br/>' +
                                    '1. El aire acondicionado deberá estar ajustado para garantizar la temperatura de confort en el local de (24 ºC).<br/><br/>' +
                                    ' 2. El local deberá estar debidamente sellado y no deben existir fugas de aire.<br/><br/>' +
                                    ' 3. Los filtros, evaporadores y condensadores de los equipos de refrigeración y climatización, deben mantenerse siempre limpios.<br/><br/>' +
                                    '4. Los locales que tengan ventanas de cristal y que incida el sol directamente deberán tener quiebrasoles o papeles reflectores.<br/><br/>' +
                                    '5. El aislamiento térmico y estado de las cámaras frías y recintos refrigerados y de sus puertas, debe poseer la máxima' +
                                    'hermeticidad, para impedir la entrada de aire caliente a dichos locales.<br/><br/>' +
                                    '6. En el caso de los frigoríficos se cumplirán estrictamente los programas de apertura y cierre de las cámaras.<br/><br/>' +
                                    '7. Evitar la operación en vacío de los motores<br/><br/>' +
                                    '8. Evitar la explotación de motores con capacidad sobredimensionada respecto a las cargas que mueven los mismos.<br/><br/>' +
                                    '9. Evitar operar transformadores a baja carga (menor al 20%) respecto a su capacidad nominal, si es posible redistribuir las <br/><br/>' +
                                    '10. Revisar los filtros de las bombas. Limpiarlos con frecuencia para evitar que las obstrucciones ocasionen sobre' +
                                    'cargas que aumenten innecesariamente sus consumos de energía.<br/><br/>' +
                                    '11. Revisar toda la instalación de los conductos y tuberías para verificar que no existan fugas en especial en las uniones' +
                                    'de los tramos de tubería, tanques elevados, tanques de servicios sanitarios, cisternas y otros acumuladores de fluidos.<br/><br/>' +
                                    '12. Revisar la temperatura de operación de los conductores eléctricos. El calentamiento puede ser causado, entre otras' +
                                    'cosas por el calibre inadecuado de los conductores o por empalmes y conexiones mal efectuados.<br/><br/>' +
                                    ' 13. Limpiar periódicamente las luminarias, porque la suciedad disminuye el nivel de iluminación de una lámpara hasta en un 20%.<br/><br/>' +
                                    '14. Usar colores claros en las paredes, muros y techos,porque los colores oscuros absorben gran cantidad de luz y' +
                                    'obligan a utilizar más lámparas.<br/><br/>' +
                                    '15. Independizar y sectorizar los circuitos de iluminación, esto ayudará iluminar sólo los lugares que se necesitan.<br/><br/>' +
                                    '</p>';

                                let panell = Ext.create('Ext.panel.Panel', {
                                    title: 'Bitácora autoinspección consumo de energía eléctrica',
                                    width: 600,
                                    height: 500,
                                    margin: '5 5 5 5',
                                    scrollable: true,
                                    id: 'id_panel_bitacoraEx',
                                    html: html
                                });
                                Ext.create('Ext.window.Window', {
                                    id: 'window_com_id',
                                    plain: true,
                                    resizable: false,
                                    modal: true,
                                    items: [panell],
                                    buttons: [
                                        {
                                            text: 'Cerrar',
                                            width: 70,
                                            handler: function () {
                                                Ext.getCmp('window_com_id').close();
                                            }
                                        }
                                    ]
                                }).show();
                            }
                        },
                        {
                            text: 'Cerrar',
                            width: 70,
                            height: 25,
                            handler: function () {
                                Ext.getCmp('window_bitacora_id').close();
                                store_bitacora.removeAll();
                            }
                        }
                    ],
                    listeners: {
                        afterrender: function () {
                            _storeservicios.load();
                        }
                    }
                }).show();
            } else {
                App.showAlert('Seleccione el servisio', 'info', 2500);
            }

        }
    });

    let _btn_BorrarAutolecturas = Ext.create('Ext.button.MyButton', {
        id: 'autolecturan_btn_delete',
        text: 'Limpiar Lecturas',
        glyph: 0xf51a,
        disabled: true,
        width: 135,
        handler: function (This, e) {
            let grid_datos = Ext.getCmp('id_grid_autolectura').getSelectionModel().getLastSelected();
            let servicioid = Ext.getCmp('id_grid_autolecturaservicios').getSelectionModel().getLastSelected();
            if (grid_datos) {
                let datos = {};
                datos.servicio_id = servicioid.data.id;
                datos.fecha_lectura = grid_datos.data.fecha;
                Ext.Msg.show({
                    title: '¿Limpiar lecturas?',
                    message: Ext.String.format('Está seguro que desea limpiar las autolecturas posteriores a la fecha seleccionada. Esta Acción no se podrá deshacer?'),
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.QUESTION,
                    fn: function (btn) {
                        if (btn === 'yes') {
                            App.request('POST', App.buildURL('/portadores/autolectura_tresescalas/cleanautolecturas'), datos, null, null,
                                function (response) {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        Ext.getCmp('id_grid_autolectura').getStore().removeAll();
                                        Ext.getCmp('id_grid_autolectura').getStore().load({
                                            params: {
                                                idservicios: servicioid.data.id
                                            }
                                        });
                                        Ext.getCmp('id_grid_autolectura').getStore().sort('id', 'ASC');
                                        Ext.getCmp('id_grid_autolectura').getView().refresh();
                                    } else {
                                        App.showAlert('Error eliminando los datos. Contacte con el administrador', 'danger', 3500);
                                    }
                                }, function (response) {
                                });
                        }
                    }
                });

            } else {
                App.showAlert('Por favor selecccione a partir de que fecha desea limpiar las autolecturas', 'warning', 3500);
            }
        }

    });

    let _btn_Acumulados = Ext.create('Ext.button.MyButton', {
        id: '_btn_acumulados',
        text: 'Acumulados',
        glyph: 0xf62e,
        width: 120,
        handler: function (This, e) {
            if (Ext.getCmp('id_grid_autolectura').getStore().count() === 0) {
                App.showAlert('No Existen Autolecturas', 'warning', 2500);
            }
            else {
                let servicioid = Ext.getCmp('id_grid_autolecturaservicios').getSelectionModel().getLastSelected();
                if (servicioid) {
                    let idservicios = {};
                    idservicios.id = servicioid.data.id;
                    Ext.getCmp('id_acumulados').setHidden(false);
                    Ext.getCmp('id_acumulados').expand();

                    acumulados.load({
                        params: {
                            id: servicioid.data.id
                        }
                    });
                } else {
                    App.showAlert('Seleccione un servicio ', 'warning', 3500);
                }
            }

        }
    });

    let _tbar = Ext.getCmp('autolectura_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_BorrarAutolecturas);
    _tbar.add('-');
    _tbar.add(_btn_bitacora);
    _tbar.add('-');
    _tbar.add(_btn_Acumulados);
    _tbar.setHeight(36);
});


