/**
 * Created by yosley on 06/10/2015.
 */
Ext.onReady(function () {

Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_cargo_persona',
    fields: [
        {name: 'id'},
        {name: 'nombre'}
    ],
    proxy: {
        type: 'ajax',
        url: App.buildURL('/portadores/cargo/load'),
        reader: {
            rootProperty: 'rows'
        }
    },
    pageSize: 1000,
    autoLoad: true
});

Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_operario_taller',
    fields: [
        {name: 'id'},
        {name: 'nombre'}
    ],
    data: [
        [-1, 'No es Operario...'],
        [1,'Jefe de Taller'],
        [2,'Emisor'],
        [3,'Mecánico']
    ]
});

Ext.define('Portadores.persona.Window', {
    extend: 'Ext.window.Window',
    width: 580,
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
                    labelAlign: 'top',
                    allowBlank: false
                },
                items: [
                    {
                        xtype: 'fieldcontainer',
                        layout: {
                            type: 'hbox',
                            align: 'stretch'
                        },
                        items: [
                            {
                                xtype: 'fieldcontainer',
                                flex: 1,
                                bodyPadding: 5,
                                margin: '10 10 0 10',
                                layout: {
                                    type: 'vbox',
                                    align: 'stretch'
                                },
                                items: [{
                                    xtype: 'textfield',
                                    name: 'nombre',
                                    id: 'nombre',
                                    fieldLabel: 'Nombre(s) y apellidos',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ]
                                },
                                    {
                                        xtype: 'textfield',
                                        name: 'ci',
                                        id: 'ci',
                                        fieldLabel: 'CI',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        maxLength: 11,
                                        enforceMaxLength: true,
                                        maskRe: /[0-9]/,
                                        regex: /^[0-9]{11}$/,
                                        regexText: 'Este campo debe contener 11 valores numéricos'
                                    },
                                    {
                                        xtype: 'fieldcontainer',
                                        fieldLabel: 'Teléfono',
//                                        combineErrors: true,
                                        layout: {
                                            type: 'hbox',
                                            defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
                                        },
                                        msgTarget: 'side',
                                        defaults: {
                                            hideLabel: true
                                        },
                                        items: [
                                            {xtype: 'displayfield', value: '('},
                                            {
                                                xtype: 'textfield',
                                                name: 'codigo-1',
                                                flex: .3,
                                                maskRe: /[0-9]/,
                                                regex: /^[0-9]*$/,
                                                regexText: 'Este campo debe contener solo valores numéricos',
                                                maxLength: 3,
                                                enforceMaxLength: true,
                                                allowBlank: true
                                            },
                                            {xtype: 'displayfield', value: ')'},

                                            {xtype: 'displayfield', value: '-'},
                                            {
                                                xtype: 'textfield',
                                                name: 'numero-2',
                                                flex: .7,
                                                maskRe: /[0-9]/,
                                                regex: /^[0-9]*$/,
                                                regexText: 'Este campo debe contener solo valores numéricos',
                                                maxLength: 10,
                                                enforceMaxLength: true,
                                                allowBlank: true
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                xtype: 'fieldcontainer',
                                flex: 1,
                                bodyPadding: 5,
                                margin: '10 10 0 10',
                                layout: {
                                    type: 'vbox',
                                    align: 'stretch'
                                },
                                items: [{
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
                                }, {
                                    xtype: 'combobox',
                                    name: 'cargoid',
                                    id: 'cargoid',
                                    fieldLabel: 'Cargo',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    store: Ext.getStore('id_store_cargo_persona'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione el cargo...',
                                    selectOnFocus: true,
                                    editable: true
                                },{
                                        xtype: 'combobox',
                                        name: 'operarioTaller',
                                        id: 'operarioTaller',
                                        fieldLabel: 'Operario de taller',
                                        store: Ext.getStore('id_store_operario_taller'),
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        forceSelection: true,
                                        value: -1,
                                        emptyText: 'Seleccione la unidad...',
                                        editable: false,
                                        allowBlank: true
                                    }]
                            }]
                    },
                    {
                        xtype: 'textareafield',
                        bodyPadding: 5,
                        margin: '0 10 10 10',
                        name: 'direccion',
                        id: 'direccion',
                        fieldLabel: 'Dirección particular',
                        allowBlank: true
                    }
                ]
            }
        ];

        this.callParent();
    }
});

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'persona_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.persona.Window', {
                title: 'Adicionar persona',
                id: 'window_persona_id',
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
                            var window = Ext.getCmp('window_persona_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/persona/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            var selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                                            var _record_ini = Ext.getCmp('unidadid').store;
                                            var _record_i = _record_ini.findRecord('id', selected.id);
                                            Ext.getCmp('unidadid').select(_record_i);
                                            Ext.getCmp('unidadid').setReadOnly(true);
                                            Ext.getCmp('id_grid_persona').getStore().load();
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
                            Ext.getCmp('window_persona_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'persona_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_persona').getSelection()[0];
            var window = Ext.create('Portadores.persona.Window', {
                title: 'Modificar persona',
                id: 'window_persona_id',
                listeners: {
                    afterrender:function(){
                        Ext.getCmp('unidadid').setReadOnly(true);
                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_persona_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/persona/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_persona').getStore().loadPage(1);

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
                            Ext.getCmp('window_persona_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'persona_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_persona').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar persona?',
                message: Ext.String.format('¿Está seguro que desea eliminar la persona <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/persona/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_persona').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('persona_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
