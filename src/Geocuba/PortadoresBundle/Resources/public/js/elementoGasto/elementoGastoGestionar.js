/**
 * Created by orlando on 06/10/2015.
 */

Ext.onReady(function () {

    let _store_portador = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_portador',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/elementogasto/loadPortadores'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
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
        autoLoad: true
    });

    Ext.define('Portadores.elementogasto.Window', {
        extend: 'Ext.window.Window',
        width: 300,
        modal: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
                    bodyPadding: 5,
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
                            fieldLabel: 'Código',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            maxLength: 5,
                            enforceMaxLength: true,
                            maskRe: /[0-9]/,
                            regex: /^[0-9]{5}$/,
                            regexText: 'El código no es válido',
                            labelWidth: 80,
                            name: 'codigo'
                        },
                        {
                            xtype: 'combobox',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'monedaid',
                            id: 'moneda_id',
                            fieldLabel: 'Moneda',
                            labelWidth: 80,
                            store: _store_moneda,
                            displayField: 'nombre',
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione la Moneda...',
                            selectOnFocus: true,
                            editable: true
                        },
                        {
                            xtype: 'checkbox',
                            name: 'combustible',
                            id: 'combustible',
                            fieldLabel: 'Combustible',
                            inputValue: true,
                            // listeners: {
                            //     change: function (This) {
                            //         if (!This.value) {
                            //             let store = _storec;
                            //             let find = store.findRecord('id', Ext.getCmp('ntipo_combustibleid').getValue());
                            //             if (find != null) {
                            //                 Ext.getCmp('importe').maxValue = parseFloat(find.data.maximo_tarjeta);
                            //                 if (Ext.getCmp('importe').getValue() > parseFloat(find.data.maximo_tarjeta))
                            //                     Ext.getCmp('importe').reset()
                            //             }
                            //         }
                            //     }
                            // },
                            labelWidth: 80,
                            labelAlign: 'left',
                            allowBlank: true
                        },
                        // {
                        //     xtype: 'tagfield',
                        //     name: 'portadores_ids',
                        //     fieldLabel: '<strong>Portadores</strong>',
                        //     // afterLabelTextTpl: [
                        //     //     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        //     // ],
                        //     labelWidth: 80,
                        //     // margin: {top: 5, left: 0},
                        //     store: _store_portador,
                        //     displayField: 'nombre',
                        //     valueField: 'id',
                        //     queryMode: 'local',
                        //     forceSelection: true,
                        //     allowBlank: true,
                        //     filterPickList: true,
                        //     // listeners: {
                        //     //     // change: function () {
                        //     //     //     if (action === 'add')
                        //     //     //         return null;
                        //     //     //     else
                        //     //     //         Ext.getCmp('groups_change').setHidden(false);
                        //     //     // }
                        //     // }
                        // },
                        // action === 'add' ? null : {
                        //     xtype: 'container',
                        //     id: 'groups_change',
                        //     hidden: true,
                        //     margin: 5,
                        //     html: '<div class="text-center"><small class="text-dark"><i class="fa fa-info-circle"></i> <strong>Los cambios en los grupos se aplicarán cuando el usuario reinicie la sesión.</strong></small></div>'
                        // },
                        {
                            fieldLabel: 'Descripción',
                            xtype: 'textarea',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 80,
                            name: 'descripcion'
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'elementogasto_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.elementogasto.Window', {
                title: 'Adicionar Elemento de Gasto',
                id: 'window_elementogasto_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_elementogasto_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/elementogasto/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            window.show();
                                            Ext.getCmp('id_grid_elementogasto').getStore().loadPage(1);
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
                            Ext.getCmp('window_elementogasto_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'elementogasto_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_elementogasto').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.elementogasto.Window', {
                title: 'Modificar Elemento de Gasto',
                id: 'window_elementogasto_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_elementogasto').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/elementogasto/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_elementogasto').getStore().loadPage(1);
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
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_elementogasto_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'elementogasto_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_elementogasto').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar elemento de gasto?' : '¿Eliminar elementos de gastos?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el elemento de gasto <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('codigo')) :
                    '¿Está seguro que desea eliminar los elementos de gastos seleccionados?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/elementogasto/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_elementogasto').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('elementogasto_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);


});