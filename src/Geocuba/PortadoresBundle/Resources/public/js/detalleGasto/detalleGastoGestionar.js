/**
 * Created by orlando on 06/10/2015.
 */

Ext.onReady(function () {

    let _store_tipocombustible = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_combustible',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/tipocombustible/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
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
        autoLoad: true
    });

    Ext.define('Portadores.detallegasto.Window', {
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
                            xtype: 'combobox',
                            // afterLabelTextTpl: [
                            //     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            // ],
                            name: 'tipocombustibleid',
                            id: 'tipo_combustible_id',
                            fieldLabel: 'Combustible',
                            labelWidth: 80,
                            store: _store_tipocombustible,
                            displayField: 'nombre',
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            allowBlank: true,
                            // forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione el combustible...',
                            selectOnFocus: true,
                            editable: true
                        },
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
        id: 'detallegasto_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.detallegasto.Window', {
                title: 'Adicionar detalle del gasto',
                id: 'window_detallegasto_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_detallegasto_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/detallegasto/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_detallegasto').getStore().loadPage(1);
                                            window.show();
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
                            Ext.getCmp('window_detallegasto_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'detallegasto_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_detallegasto').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.detallegasto.Window', {
                title: 'Modificar detalle del gasto',
                id: 'window_detallegasto_id',
                // listeners: {
                //     afterrender: function () {
                //         Ext.getCmp('unidadid').setReadOnly(true);
                //     },
                //     boxready: function (self) {
                //         self.down('form').loadRecord(selection);
                //     }
                // },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_detallegasto').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/detallegasto/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_detallegasto').getStore().loadPage(1);
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
                            Ext.getCmp('window_detallegasto_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'detallegasto_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_detallegasto').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar detalle del gasto?' : '¿Eliminar detalles de los gastos?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el detalle del gasto <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('codigo')) :
                    '¿Está seguro que desea eliminar los detalles del gasto seleccionados?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/detallegasto/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_detallegasto').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('detallegasto_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);


});