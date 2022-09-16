/**
 * Created by pfcadenas on 11/11/2016.
 */

Ext.onReady(function () {
    let store_moneda = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_moneda_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/moneda/loadMoneda'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    Ext.define('Portadores.solicitud_compra.Window', {
        extend: 'Ext.window.Window',
        width: 250,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: 250,
                    defaultType: 'textfield',
                    bodyPadding: 10,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        labelAlign: 'left',
                        msgTarget: 'side',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'fecha',
                            id: 'fecha',
                            flex: 0.5,
                            fieldLabel: 'Fecha',
                            labelWidth: 80,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            listeners: {
                                afterrender: function (This) {
                                    var dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                                    var anno = App.selected_year;
                                    var min = new Date((App.selected_month) + '/' + 1 + '/' + anno);
                                    var max = new Date((App.selected_month) + '/' + dias + '/' + anno);
                                    This.setMinValue(min);
                                    This.setMaxValue(max);
                                }
                            }
                        },
                        // {
                        //     xtype: 'combobox',
                        //     name: 'moneda_id',
                        //     id: 'moneda_id',
                        //     fieldLabel: 'Moneda',
                        //     labelWidth: 80,
                        //     width: 200,
                        //     margin: '5 0',
                        //     store: store_moneda,
                        //     displayField: 'nombre',
                        //     valueField: 'id',
                        //     typeAhead: true,
                        //     queryMode: 'local',
                        //     forceSelection: true,
                        //     triggerAction: 'all',
                        //     emptyText: 'Moneda...',
                        //     selectOnFocus: true,
                        //     editable: true,
                        //     afterLabelTextTpl: [
                        //         '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        //     ]
                        // }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'solicitud_compra_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.solicitud_compra.Window', {
                title: 'Adicionar Solicitud de Compra',
                id: 'window_solicitud_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_solicitud_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                let obj = form.getValues();
                                obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/solicitud_compra/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_solicitud').getStore().loadPage(1);
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
                            Ext.getCmp('window_cuentagasto_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'solicitud_compra_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_solicitud').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.solicitud_compra.Window', {
                title: 'Modificar Solicitud de Compra',
                id: 'window_solicitud_id',
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
                                App.request('POST', App.buildURL('/portadores/solicitud_compra/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_solicitud').getStore().loadPage(1);
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
                            Ext.getCmp('window_cuentagasto_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'solicitud_compra_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_solicitud').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar Solicitud de Compra?',
                message: '¿Está seguro que desea eliminar la solicitud de compra seleccionada?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/solicitud_compra/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_solicitud').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _btn_Aprob = Ext.create('Ext.button.MyButton', {
        id: 'solicitud_compra_btn_aprob',
        text: 'Aprobar',
        iconCls: 'fas fa-check-circle text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var obj = {};
            obj.id = Ext.getCmp('id_grid_solicitud').getSelectionModel().getLastSelected().data.id;
            App.request('POST', App.buildURL('/portadores/solicitud_compra/aprobar'), obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        Ext.getCmp('id_grid_solicitud').getStore().load();
                    } else {
                        if (response && response.hasOwnProperty('errors') && response.errors) {
                            window.down('form').getForm().markInvalid(response.errors);
                        }
                    }
                }
            );
        }
    });

    var _btn_Desaprob = Ext.create('Ext.button.MyButton', {
        id: 'solicitud_compra_btn_desaprob',
        text: 'Desaprobar',
        iconCls: 'fas fa-times-circle text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var obj = {};
            obj.id = Ext.getCmp('id_grid_solicitud').getSelectionModel().getLastSelected().data.id;
            App.request('POST', App.buildURL('/portadores/solicitud_compra/desaprobar'), obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        Ext.getCmp('id_grid_solicitud').getStore().load();
                    } else {
                        if (response && response.hasOwnProperty('errors') && response.errors) {
                            window.down('form').getForm().markInvalid(response.errors);
                        }
                    }
                }
            );
        }
    });

    var _btnAct = Ext.create('Ext.button.MyButton', {
        id: 'demanda_combustible_btn_act',
        iconCls: 'fas fas fa-check-square text-primary',
        text: 'Guardar',
        width: 100,
        disabled: true,
        handler: function (This, e) {
            _btnAtras.setDisabled(true);

            var store = Ext.getCmp('id_grid_solicitud_compra_desglose').getStore();
            var send = [];

            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });

            var obj = {};
            obj.solicitud_id = Ext.getCmp('id_grid_solicitud').getSelectionModel().getLastSelected().data.id;
            obj.desgloses = Ext.encode(send);
            App.request('POST', App.buildURL('/portadores/solicitud_compra/desglose/guardar'), obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        _btnAct.setDisabled(true);
                        Ext.getCmp('id_grid_solicitud_compra_desglose').getStore().load();
                        Ext.getCmp('id_grid_solicitud').getStore().load();
                    } else {
                        if (response && response.hasOwnProperty('errors') && response.errors) {
                            window.down('form').getForm().markInvalid(response.errors);
                        }
                    }
                }
            );
        }
    });

    var _btnAtras = Ext.create('Ext.button.MyButton', {
        id: 'demanda_combustible_btn_back',
        text: 'Deshacer',
        iconCls: 'fas fas fa-undo-alt text-primary',
        width: 100,
        disabled: true,
        handler: function (This, e) {
            This.setDisabled(true);
            _btnAct.setDisabled(true);
            Ext.getCmp('id_grid_demanda_combustible').getStore().reload();

        }
    });

    var _btnReinicar = Ext.create('Ext.button.MyButton', {
        id: 'demanda_combustible_btn_reinciar',
        text: 'Reiniciar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_solicitud').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Reinciar Desglose?',
                message: 'Esta acci&oacute;n elimininará el desglose de compra de combustible.<br>¿Está seguro que desea realizar esta acci&oacute;n?.',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var obj = {};
                        obj.solicitud_id = selection.data.id;
                        App.request('DELETE', App.buildURL('/portadores/solicitud_compra/desglose/del'), obj, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_solicitud_compra_desglose').getStore().reload();
                                Ext.getCmp('id_grid_solicitud').getStore().load();
                            }
                        });
                    }
                }
            });


        }
    });


    var _tbar = Ext.getCmp('solicitud_compra_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.add('-');
    _tbar.add(_btn_Aprob);
    _tbar.add('-');
    _tbar.add(_btn_Desaprob);

    var _tbar = Ext.getCmp('solicitud_compra_desglose_tbar');
    _tbar.add(_btnAct);
    _tbar.add('-');
    _tbar.add(_btnAtras);
    _tbar.add('-');
    _tbar.add(_btnReinicar);


});


