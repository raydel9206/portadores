Ext.onReady(function () {

    Ext.define('Portadores.distribucion_combustible.Window', {
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
                        labelAlign: 'top',
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
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            listeners: {
                                afterrender: function (This) {
                                    var dias = App.getDaysInMonth (App.selected_year, App.selected_month);
                                    var anno = App.selected_year;
                                    var min = new Date(App.selected_month + '/' + 1 + '/' + anno);
                                    var max = new Date(App.selected_month + '/' + dias + '/' + anno);
                                     This.setMinValue(min);
                                    This.setMaxValue(max);
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            name: 'tipo_combustible_id',
                            id: 'tipo_combustible_id',
                            fieldLabel: 'Tipo de combustible',
                            store: Ext.getStore('storeTipoCombustible'),
                            displayField: 'nombre',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione tipo de combustible...',
                            selectOnFocus: true,
                            editable: true
                        },
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Para Mes:',
                            format: 'm/Y',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'para_mes',
                            id: 'para_mes',
                            listeners: {
                                afterrender: function (This) {
                                    var date = new Date();
                                    This.setValue(date);
                                }
                            }
                        },
                        {
                            xtype: 'numberfield',
                            name: 'cantidad',
                            id: 'cantidad',
                            decimalSeparator: '.',
                            decimalPrecision: 2,
                            fieldLabel: 'Cantidad',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            value: 0,
                            minValue: 0
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'asignacion_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.distribucion_combustible.Window', {
                title: 'Adicionar asignación de combustible',
                id: 'window_distribucion_combustible_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_distribucion_combustible_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.unidadid =  Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/asignacion/add'),obj , null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_asignacion').getStore().load();
                                            Ext.getCmp('id_grid_disponible').getStore().load();
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                window.down('form').getForm().markInvalid(response.errors);
                                            }
                                        }
                                        window.close();
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
                            Ext.getCmp('window_distribucion_combustible_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'asignacion_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_asignacion').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.distribucion_combustible.Window', {
                title: 'Modificar asignación de combustible',
                id: 'window_distribucion_id',
                listeners:{
                    afterrender:function(){
                        Ext.getCmp('tipo_combustible_id').setReadOnly(true);
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
                                obj.unidadid =  Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/asignacion/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_asignacion').getStore().load();
                                            Ext.getCmp('id_grid_disponible').getStore().reload();
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
                            Ext.getCmp('window_distribucion_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'asignacion_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_asignacion').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Asignación?',
                message: Ext.String.format('¿Está seguro que desea eliminar la asignación <span class="font-italic font-weight-bold">{0}</span>?', selection.data.denominacion),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var obj = {};
                        obj.id = selection.data.id;
                        obj.unidadid =  Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                        obj.tipo_combustible_id = selection.data.tipo_combustible_id
                        App.request('DELETE', App.buildURL('/portadores/asignacion/del'), obj, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_asignacion').getStore().reload();
                                Ext.getCmp('id_grid_disponible').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('Area_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});