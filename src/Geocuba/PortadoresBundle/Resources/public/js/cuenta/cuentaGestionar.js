Ext.onReady(function () {

    var storeClas = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_storeClas',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'codigo'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/clasificador/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true,
    });

    Ext.define('Portadores.cuenta.Window', {
        extend: 'Ext.window.Window',
        width: 330,
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
                            xtype: 'textfield',
                            fieldLabel: 'Nro. Cuenta',
                            id: 'no_cuenta',
                            name: 'no_cuenta',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            maxLength: 3,
                            enforceMaxLength: true,
                            maskRe: /[0-9]/,
                            regex: /^[0-9]{3}$/,
                        }, {
                            xtype: 'combo',
                            fieldLabel: 'Denominacón',
                            id: 'clasificador',
                            name: 'clasificador',
                            store: storeClas,
                            queryMode: 'local',
                            displayField: 'nombre',
                            valueField: 'id',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                        }, {
                            xtype: 'textarea',
                            fieldLabel: 'Descripción',
                            allowBlank: true,
                            name: 'descripcion',
                            id: 'descripcion'
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var monedaStore = Ext.create('Ext.data.JsonStore', {
        storeId: 'monedaStore',
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
        autoLoad: true
    });

    Ext.define('Portadores.subCuenta.Window', {
        extend: 'Ext.window.Window',
        width: 330,
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
                            xtype: 'textfield',
                            fieldLabel: 'Subcuenta',
                            id: 'no_cuenta',
                            name: 'no_cuenta',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            maxLength: 5,
                            enforceMaxLength: true,
                            maskRe: /[0-9]/,
                            regex: /^[0-9]{5}$/,
                        }, {
                            xtype: 'combobox',
                            name: 'moneda_id',
                            id: 'moneda_id',
                            fieldLabel: 'Moneda',
                            store: monedaStore,
                            displayField: 'nombre',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione la moneda...',
                            selectOnFocus: true,
                            editable: true
                        },
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'cuenta_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.cuenta.Window', {
                title: 'Adicionar Cuenta',
                id: 'window_cuenta_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_cuenta_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                let obj = form.getValues();
                                obj.unidad = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                console.log(obj.unidad);
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/cuenta/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_cuenta').getStore().loadPage(1);
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
                            Ext.getCmp('window_cuenta_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnAddSub = Ext.create('Ext.button.MyButton', {
        id: 'subcuenta_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.subCuenta.Window', {
                title: 'Adicionar subcuenta',
                id: 'window_subcuenta_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_subcuenta_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                let obj = form.getValues();
                                obj.cuenta = Ext.getCmp('id_grid_cuenta').getSelectionModel().getLastSelected().data.id;
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/cuenta/addSub'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_subcuenta').getStore().loadPage(1);
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
                            Ext.getCmp('window_subcuenta_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'cuenta_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_cuenta').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.cuenta.Window', {
                title: 'Modificar Cuenta',
                id: 'window_cuenta_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_cuenta').getSelectionModel().getLastSelected().data.id;
                                obj.unidad = Ext.getCmp('id_grid_cuenta').getSelectionModel().getLastSelected().data.unidad;
                                App.request('POST', App.buildURL('/portadores/cuenta/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_cuenta').getStore().loadPage(1);
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
                            Ext.getCmp('window_cuenta_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btnModSub = Ext.create('Ext.button.MyButton', {
        id: 'subcuenta_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_subcuenta').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.subCuenta.Window', {
                title: 'Modificar subcuenta',
                id: 'window_cuenta_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_subcuenta').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/cuenta/modSub'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_subcuenta').getStore().loadPage(1);
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
                            Ext.getCmp('window_cuenta_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'cuenta_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_cuenta').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar Cuenta?',
                message: Ext.String.format('¿Está seguro que desea eliminar la cuenta <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nro_cuenta')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/cuenta/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_cuenta').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _btn_DelSub = Ext.create('Ext.button.MyButton', {
        id: 'subcuenta_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_subcuenta').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar subcuenta?',
                message: Ext.String.format('¿Está seguro que desea eliminar la subcuenta <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nro_cuenta')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/cuenta/delSub'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_subcuenta').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('cuenta_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);

    var tbar_subcuenta = Ext.getCmp('grid_subcuenta_tbar');
    tbar_subcuenta.add(_btnAddSub);
    tbar_subcuenta.add('-');
    tbar_subcuenta.add(_btnModSub);
    tbar_subcuenta.add('-');
    tbar_subcuenta.add(_btn_DelSub);
    tbar_subcuenta.add('-');


});