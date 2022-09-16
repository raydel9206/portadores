/**
 * Created by javier on 16/05/16.
 */
Ext.onReady(function(){

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
        pageSize: 1000,
        autoLoad: true
    });

    // let store_persona_anticipo = Ext.create('Ext.data.JsonStore', {
    //     storeId: 'id_store_persona_anticipo',
    //     fields: [
    //         {name: 'id'},
    //         {name: 'nombre'}
    //     ],
    //     proxy: {
    //         type: 'ajax',
    //         url: App.buildURL('/portadores/anticipo/loadPersonaCombo'),
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
    //
    // });

    // let store_vehiculo_anticipo = Ext.create('Ext.data.JsonStore', {
    //     storeId: 'id_store_vehiculo_anticipo',
    //     fields: [
    //         {name: 'id'},
    //         {name: 'matricula'},
    //         {name: 'tipo_combustibleid'}
    //     ],
    //     proxy: {
    //         type: 'ajax',
    //         url: App.buildURL('/portadores/anticipo/loadVehiculoCombo'),
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

    Ext.define('Portadores.chip.Window', {
        id: 'window_chip_id',
        extend: 'Ext.window.Window',
        width: 280,
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
                        labelAlign: 'left',
                        allowBlank: false,
                        margin: '5 5',
                    },
                    items: [
                        {
                            xtype: 'combobox',
                            name: 'ntarjetaid',
                            id: 'tarjetaid',
                            fieldLabel: 'Tarjeta',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            store: Ext.getStore('id_store_tarjeta'),
                            displayField: 'ntarjetaidnro',
                            valueField: 'ntarjetaid',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione...',
                            selectOnFocus: true,
                            editable: true
                        },
                        {
                            xtype: 'combobox',
                            name: 'nservicentroid',
                            fieldLabel: 'Servicentro',
                            store: Ext.getStore('id_store_nservicentroidliquidacion'),
                            displayField: 'nombre',
                            displayTpl: Ext.create('Ext.XTemplate', '<tpl for = ".">', '{nombre}', '</tpl>'),
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Seleccione...',
                            selectOnFocus: true,
                            editable: true
                        },
                        {
                            xtype: 'textfield',
                            name: 'nro_vale',
                            id: 'nro_vale',
                            fieldLabel: 'No. Comp',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            maskRe: /[0-9]/
                        },
                        {
                            xtype: 'datefield',
                            name: 'fecha_registro',
                            id: 'fecha_registro',
                            editable: false,
                            value: new Date(),
                            // listeners: {
                            //     afterrender: function (This) {
                            //         let min = new Date(App.selected_month + '/' + 1 + '/' + App.current_year);
                            //         let max = new Date();
                            //         This.setMinValue(min);
                            //         This.setMaxValue(max);
                            //     }
                            // },
                            fieldLabel: 'Fecha Entrega:',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        },
                        {
                            xtype: 'datefield',
                            name: 'fecha_vale',
                            id: 'fecha_vale',
                            editable: false,
                            listeners: {

                                afterrender: function (This) {
                                    console.log(App.selected_month+' '+App.selected_year);
                                    let min = new Date(App.selected_month + '/' + 1 + '/' + App.selected_year);
                                    let max = new Date(App.selected_month + '/' + App.getDaysInMonth(App.selected_year,App.selected_month) + '/' + App.selected_year);
                                    This.setMinValue(min);
                                    This.setMaxValue(max);
                                }
                            },
                            fieldLabel: 'Fecha Servicio:',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        },
                        {
                            xtype: 'timefield',
                            increment: 5,
                            name: 'hora_vale',
                            id: 'hora',
                            fieldLabel: 'Hora Servicio',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
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
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'chip_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.chip.Window', {
                title: 'Adicionar Chip',
                id: 'window_chip_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_chip_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/chip/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_chip').getStore().loadPage(1);
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
                            Ext.getCmp('window_chip_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'chip_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_chip').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.chip.Window', {
                title: 'Modificar Chip',
                id: 'window_chip_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.liquidacionid;

                                App.request('POST', App.buildURL('/portadores/chip/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_chip').getStore().loadPage(1);
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
                            Ext.getCmp('window_chip_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'chip_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_chip').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar chip?' : '¿Eliminar chips?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el chip <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nro_vale')) :
                    '¿Está seguro que desea eliminar las chips seleccionadas?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/chip/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_chip').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    var _tbar = Ext.getCmp('chip_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);

});


