/**
 * Created by yosley on 06/10/2015.
 */
Ext.onReady(function () {
    let _storeportadores = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_portadores',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'unidad_medidaid'},
            {name: 'unidad_medida'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/portador/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });

    let _store_moneda = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_moneda',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/moneda/loadMoneda'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    Ext.define('Portadores.tipocombustible.Window', {
        extend: 'Ext.window.Window',
        width: 500,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
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
                            items: [{
                                xtype: 'textfield',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                name: ' nombre',
                                id: 'nombre',
                                fieldLabel: 'Tipo de combustible'
                                //maskRe: /[0-9-a-zA-ZáéíóúñÁÉÍÓÚÑ ]/

                            }, {
                                xtype: 'textfield',
                                name: 'codigo',
                                id: 'codigo',
                                fieldLabel: 'Código',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ]
                                //maskRe: /[0-9-a-zA-ZáéíóúñÁÉÍÓÚÑ ]/
                            }, {
                                xtype: 'combobox',
                                name: 'portador_id',
                                id: 'portador_id',
                                fieldLabel: ' Portador',
                                store: _storeportadores,
                                displayField: 'nombre',
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                triggerAction: 'all',
                                emptyText: 'Seleccione el Portador...',
                                selectOnFocus: true,
                                editable: true,
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ]
                            },
                                // {
                                //     xtype: 'combobox',
                                //     afterLabelTextTpl: [
                                //         '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                //     ],
                                //     name: 'monedaid',
                                //     id: 'moneda_id',
                                //     fieldLabel: 'Moneda',
                                //     labelWidth: 80,
                                //     store: _store_moneda,
                                //     displayField: 'nombre',
                                //     valueField: 'id',
                                //     typeAhead: true,
                                //     queryMode: 'local',
                                //     forceSelection: true,
                                //     triggerAction: 'all',
                                //     emptyText: 'Seleccione la Moneda...',
                                //     selectOnFocus: true,
                                //     editable: true
                                // },
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
                            items: [{
                                xtype: 'numberfield',
                                id: 'precio',
                                name: 'precio',
                                fieldLabel: 'Precio',
                                value: 0,
                                minValue: 0,
                                decimalSeparator: '.',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],

                            },{
                                xtype: 'numberfield',
                                id: 'precio_tiro_directo',
                                name: 'precio_tiro_directo',
                                fieldLabel: 'Precio Tiro Directo',
                                value: 0,
                                minValue: 0,
                                decimalSeparator: '.',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],

                            },
                                {
                                xtype: 'numberfield',
                                id: 'maximo_tarjeta_litro',
                                name: 'maximo_tarjeta_litro',
                                fieldLabel: 'Máximo en Tarjeta(Litros)',
                                value: 0,
                                minValue: 0,
                                decimalSeparator: '.',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                            },
                                {
                                xtype: 'numberfield',
                                id: 'maximo_tarjeta_dinero',
                                name: 'maximo_tarjeta_dinero',
                                fieldLabel: 'Máximo en Tarjeta(Dinero)',
                                value: 0,
                                minValue: 0,
                                readOnly: true,
                                decimalSeparator: '.',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ]
                            },
                                {
                                xtype: 'textfield',
                                id: 'filaid',
                                name: 'filaid',
                                fieldLabel: 'Fila',
                                value: 0,
                                minValue: 0,
                                decimalSeparator: '.',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ]
                            }],
                            bodyPadding: 5,
                            margin: '10 10 10 10',
                            collapsible: false
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    let _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'tipo_combustible_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        // iconCls: 'fa fa-plus-square fa-1_4',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.tipocombustible.Window', {
                title: 'Adicionar tipo de combustible',
                id: 'window_tipo_combustible_id',
                listeners: {
                    afterrender: function (This) {
                        Ext.getCmp('precio').setListeners(
                            {
                                change: function (This, newValue, oldValue, eOpts) {
                                    let maximo_tarjeta = Ext.getCmp('maximo_tarjeta_litro').getValue();
                                    if (maximo_tarjeta)
                                        Ext.getCmp('maximo_tarjeta_dinero').setValue(maximo_tarjeta * newValue);
                                }
                            }
                        ),
                            Ext.getCmp('maximo_tarjeta_litro').setListeners(
                                {
                                    change: function (This, newValue, oldValue, eOpts) {
                                        let precio = Ext.getCmp('precio').getValue();
                                        if (precio)
                                            Ext.getCmp('maximo_tarjeta_dinero').setValue(newValue * precio);
                                    }
                                }
                            )
                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let window = Ext.getCmp('window_tipo_combustible_id');
                            let form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/tipocombustible/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('id_store_tipo_combustible').loadPage(1);
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
                            Ext.getCmp('window_tipo_combustible_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'tipo_combustible_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        // iconCls: 'fa fa-edit fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_tipo_combustible').getSelectionModel().getLastSelected();
            let window = Ext.create('Portadores.tipocombustible.Window', {
                title: 'Modificar tipo de combustible',
                id: 'window_tipo_combustible_id',
                listeners: {
                    afterrender: function (This) {
                        Ext.getCmp('precio').setListeners(
                            {
                                change: function (This, newValue, oldValue, eOpts) {
                                    let maximo_tarjeta = Ext.getCmp('maximo_tarjeta_litro').getValue();
                                    if (maximo_tarjeta)
                                        Ext.getCmp('maximo_tarjeta_dinero').setValue(maximo_tarjeta * newValue);
                                }
                            }
                        ),
                            Ext.getCmp('maximo_tarjeta_litro').setListeners(
                                {
                                    change: function (This, newValue, oldValue, eOpts) {
                                        let precio = Ext.getCmp('precio').getValue();
                                        if (precio)
                                            Ext.getCmp('maximo_tarjeta_dinero').setValue(newValue * precio);
                                    }
                                }
                            )
                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                let obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_tipo_combustible').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/tipocombustible/upd'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getStore('id_store_tipo_combustible').loadPage(1);
                                            window.close();
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
                                // App.ShowWaitMsg();
                                // window.hide();
                                // let obj = form.getValues();
                                // obj.id = selection.data.id;
                                // let _result = App.PerformSyncServerRequest(Routing.generate('modTipoCombustible'), obj);
                                // App.HideWaitMsg();
                                // if (_result.success) {
                                //     window.close();
                                //     Ext.getCmp('id_grid_tipo_combustible').getStore().load();
                                // }
                                // else {
                                //     window.show();
                                //     form.markInvalid(_result.message);
                                // }
                                // App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_tipo_combustible_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    let _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'tipo_combustible_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        // iconCls: 'fa fa-minus-square fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_tipo_combustible').getSelectionModel().getLastSelected();
            let obj = {}
            obj.id = selection.data.id

            Ext.Msg.show({
                title: '¿Eliminar tipo de combustible?',
                message: Ext.String.format('¿Está seguro que desea eliminar el tipo de combustible <span class="font-italic font-weight-bold">{0}</span>?', selection.data.nombre),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/tipocombustible/del'), obj, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_tipo_combustible').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    let _tbar = Ext.getCmp('tipo_combustible_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
