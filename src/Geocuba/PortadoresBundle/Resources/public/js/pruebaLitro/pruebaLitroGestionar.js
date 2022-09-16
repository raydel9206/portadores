/**
 * Created by Yosley on 5/11/15.
 */
Ext.onReady(function () {

    var _storev = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo_unidad',
        fields: [
            {name: 'id'},
            {name: 'matricula'},
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
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            },
        }
    });

    Ext.define('Portadores.pruebalitro.Window', {
        extend: 'Ext.window.Window',
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
                            flex: 1,
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            border: false,
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
                                    margin: '10 10 0 10',
                                    items: [
                                        {
                                            xtype: 'combobox',
                                            name: 'nvehiculoid',
                                            id: 'nvehiculoid',
                                            fieldLabel: 'Vehículo',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            store: _storev,
                                            displayField: 'matricula',
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Seleccione el Vehículo',
                                            selectOnFocus: true,
                                            editable: true
                                        },

                                        {
                                            xtype: 'checkboxfield',
                                            boxLabel: 'Actualizar Norma',
                                            name: 'actualizar',
                                            inputValue: '1',
                                            id: 'checkbox1'
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
                                    margin: '10 10 0 10',
                                    items: [
                                        {
                                            xtype: 'datefield',
                                            name: 'fecha_prueba',
                                            id: 'fecha_prueba',
                                            fieldLabel: 'Fecha de prueba',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ]
                                        },
                                        {
                                            xtype: 'numberfield',
                                            name: 'indice',
                                            id: 'indice',
                                            fieldLabel: 'Indice Far',
                                            decimalSeparator: '.',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            value: 0,
                                            minValue: 0
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            xtype: 'textfield',
                            width: 450,
                            name: 'responsable',
                            id: 'responsable',
                            fieldLabel: 'Responsable',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            maskRe: /[a-zA-ZáéíóúñÁÉÍÓÚÑ ]/,
                            margin: '0 10 10 10'
                        }
                    ]
                }
            ];

            this.callParent();

        },
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'pruebalitro_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        // disabled:true,
        handler: function (This, e) {
            Ext.create('Portadores.pruebalitro.Window', {
                title: 'Adicionar prueba de litro',
                id: 'window_pruebalitro_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_pruebalitro_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/prueba_litro/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_pruebalitro').getStore().load();
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
                            Ext.getCmp('window_pruebalitro_id').close()
                        }
                    }
                ]

            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'pruebalitro_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_pruebalitro').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.pruebalitro.Window', {
                title: 'Modificar prueba litro',
                id: 'window_pruebalitro_id',
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
                                App.request('POST', App.buildURL('/portadores/prueba_litro/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_pruebalitro').getStore().load();
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
                            Ext.getCmp('window_pruebalitro_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'pruebalitro_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_pruebalitro').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Prueba del Litro?',
                message: '¿Está seguro que desea eliminar la prueba de litro seleccionada?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/prueba_litro/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_pruebalitro').getStore().load();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('pruebalitro_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);

});