/**
 * Created by yosley on 06/10/2015.
 */
Ext.onReady(function () {

    Ext.define('Portadores.centrocosto.Window', {
        extend: 'Ext.window.Window',
        width: 380,
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
                            fieldLabel: 'Nombre',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 60,
                            name: 'nombre',
                            maskRe: /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ+. ]/,
                            regex: /^[A-Za-z0-9áéíóúñÁÉÍÓÚÑ+.]*\s?([A-Za-z0-9áéíóúñÁÉÍÓÚÑ+.]+\s?)+[A-Za-z0-9áéíóúñÁÉÍÓÚÑ+.]$/,
                            regexText: 'El nombre no es válido'
                        },
                        {
                            fieldLabel: 'Código',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 60,
                            name: 'codigo',
                            minLength: 1,
                            enforceMaxLength: true,
                            maskRe: /[0-9]/,
                            regex: /^[0-9]{1,50}$/,
                            regexText: 'El código no es válido',
                        },
                        {
                            xtype: 'combobox',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'unidadid',
                            id: 'unidadid',
                            fieldLabel: 'Unidad',
                            labelWidth:60,
                            displayField: 'nombre',
                            store: 'store_unidades',
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione la unidad...',
                            selectOnFocus: true,
                            editable: true
                        }
                    ]
                }

            ];
            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'centrocosto_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.centrocosto.Window', {
                title: 'Adicionar centro de costo',
                id: 'window_centrocosto_id',
                listeners:{
                    afterrender:function(){
                        var selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                        var _record_ini = Ext.getCmp('unidadid').store;
                        var _record_i = _record_ini.findRecord('id', selected.id);
                        Ext.getCmp('unidadid').select(_record_i);
                        Ext.getCmp('unidadid').setReadOnly(true);
                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_centrocosto_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/centrocosto/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            var selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                                            var _record_ini = Ext.getCmp('unidadid').store;
                                            var _record_i = _record_ini.findRecord('id', selected.id);
                                            Ext.getCmp('unidadid').select(_record_i);
                                            Ext.getCmp('unidadid').setReadOnly(true);
                                            Ext.getCmp('id_grid_centrocosto').getStore().loadPage(1);
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
                            Ext.getCmp('window_centrocosto_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'centrocosto_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        // iconCls: 'fa fa-edit fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_centrocosto').getSelection()[0];
            var window = Ext.create('Portadores.centrocosto.Window', {
                title: 'Modificar centro de costo',
                id: 'window_centrocosto_id',
                listeners: {
                    afterrender:function(){
                        Ext.getCmp('unidadid').setReadOnly(true);
                    },
                    boxready: function (self) {
                        self.down('form').loadRecord(selection);
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
                                obj.id = Ext.getCmp('id_grid_centrocosto').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/centrocosto/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getStore('id_store_centrocosto').loadPage(1);
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
                            Ext.getCmp('window_centrocosto_id').close();
                        }
                    }
                ]
            });
            window.show();


        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'centrocosto_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_centrocosto').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar Centro de Costo?' : '¿Eliminar centros de Costo?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el centro de costo <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar los centros de costo seleccionados?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/centrocosto/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_centrocosto').getStore().reload();
                            }
                        });
                    }
                }
            });


        }
    });

    var _tbar = Ext.getCmp('centrocosto_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);


});