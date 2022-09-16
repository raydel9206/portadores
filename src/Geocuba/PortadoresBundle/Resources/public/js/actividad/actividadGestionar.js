/**
 * Created by yosley on 07/10/2015.
 */

Ext.onReady(function () {
var _store_um_actividad = Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_um_ctividad',
    fields: [
        {name: 'id'},
        {name: 'nivel_actividad'}
    ],
    proxy: {
        type: 'ajax',
        url: App.buildURL('/portadores/um_nivel_actividad/load'),
        reader: {
            rootProperty: 'rows'
        }
    },
    pageSize: 1000,
    autoLoad: true
});

var _store_portador = Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_portadores',
    fields: [
        {name: 'id'},
        {name: 'nombre'}
    ],
    proxy: {
        type: 'ajax',
        url: App.buildURL('/portadores/portador/load'),
        reader: {
            rootProperty: 'rows'
        }
    },
    pageSize: 1000,
    autoLoad: true
});

Ext.define('Portadores.actividad.Window', {
    extend: 'Ext.window.Window',
    width: 480,
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
                items: [{
                    xtype: 'fieldcontainer',
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    border: false,
                    items: [{
                        xtype: 'fieldcontainer',
                        flex: 1,
                        layout: {
                            type: 'vbox',
                            align: 'stretch'
                        },
                        border: false,
                        bodyPadding: 5,
                        margin: '10 10 0 10',
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: 'Código GAE',
                            name: 'codigogae',
                            id: 'codigogae',
                            allowBlank: true
                            //maskRe: /^[a-zA-Z0-9. ]/
                        }, {
                            xtype: 'textfield',
                            fieldLabel: 'Código MEP',
                            name: 'codigomep',
                            id: 'codigomep',
                            allowBlank: true
                            //maskRe: /^[a-zA-Z0-9. ]/
                        }]
                    }, {
                        xtype: 'fieldcontainer',
                        flex: 1,
                        layout: {
                            type: 'vbox',
                            align: 'stretch'
                        },
                        border: false,
                        bodyPadding: 5,
                        margin: '10 10 0 10',
                        items: [{
                            xtype: 'combobox',
                            name: 'um_actividad',
                            id: 'um_actividad',
                            fieldLabel: 'UM Actividad',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            store: _store_um_actividad,
                            displayField: 'nivel_actividad',
                            valueField: 'id',
                            queryMode: 'local',
                            typeAhead: true,
                            editable: true,
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione ...'
                        }, {
                            xtype: 'combobox',
                            name: 'id_portador',
                            id: 'id_portador',
                            fieldLabel: 'Portador Energético',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            store: _store_portador,
                            displayField: 'nombre',
                            valueField: 'id',
                            queryMode: 'local',
                            typeAhead: true,
                            editable: true,
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione ...'

                        }]
                    }]
                }, {
                    xtype: 'textareafield',
                    fieldLabel: 'Nombre de la Actividad',
                    afterLabelTextTpl: [
                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                    ],
                    name: 'nombre',
                    bodyPadding: 5,
                    margin: '0 10 0 10'
                    //maskRe: /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ() ]/,
                    //regex: /^[A-Za-z0-9áéíóúñÁÉÍÓÚÑ()]*\s?([A-Za-z0-9áéíóúñÁÉÍÓÚÑ()]+\s?)+[A-Za-z0-9áéíóúñÁÉÍÓÚÑ()]$/,
                    //regexText: 'El nombre no es válido'
                }, {
                    xtype: 'fieldcontainer',
                    layout: 'vbox',
                    bodyPadding: 5,
                    margin: '10 10 10 10',
                    fieldDefaults: {
                        labelAlign: 'left'
                    },
                    items: [
                        {
                            xtype: 'fieldcontainer',
                            fieldLabel: 'Actividad no Productiva',
                            flex: 1,
                            labelWidth: 150,
                            defaultType: 'radiofield',
                            layout: 'hbox',
                            items: [
                                {
                                    boxLabel: 'Si',
                                    name: 'administrativa',
                                    inputValue: '1',
                                    id: 'radio1',
                                    margin: '0 10 0 0'
                                },
                                {
                                    boxLabel: 'No',
                                    name: 'administrativa',
                                    inputValue: '0',
                                    id: 'radio2',
                                    margin: '0 10 0 0'
                                }
                            ]
                        },
                        {
                            xtype: 'fieldcontainer',
                            fieldLabel: 'Inversión',
                            defaultType: 'radiofield',
                            layout: 'hbox',
                            flex: 1,
                            labelWidth: 150,
                            items: [
                                {
                                    boxLabel: 'Si',
                                    name: 'inversion',
                                    inputValue: '1',
                                    id: 'radio1_inversiones',
                                    margin: '0 10 0 0'
                                },
                                {
                                    boxLabel: 'No',
                                    name: 'inversion',
                                    inputValue: '0',
                                    id: 'radio2_inversiones',
                                    margin: '0 10 0 0'
                                }
                            ]
                        },
                        {
                            xtype: 'fieldcontainer',
                            fieldLabel: 'Tráfico',
                            defaultType: 'radiofield',
                            layout: 'hbox',
                            flex: 1,
                            labelWidth: 150,
                            items: [
                                {
                                    boxLabel: 'Si',
                                    name: 'trafico',
                                    inputValue: '1',
                                    id: 'radio1_trafico',
                                    margin: '0 10 0 0'
                                },
                                {
                                    boxLabel: 'No',
                                    name: 'trafico',
                                    inputValue: '0',
                                    id: 'radio2_trafico',
                                    margin: '0 10 0 0'
                                }
                            ]
                        }
                    ]
                }
                ]
            }
        ];
        this.callParent();
    }
});


    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'actividad_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.actividad.Window', {
                title: 'Adicionar actividad',
                id: 'window_actividad_id',
                floating: true,
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_actividad_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/actividad/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_actividad').getStore().loadPage(1);
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
                            Ext.getCmp('window_actividad_id').close()
                        }
                    }
                ]
            }).show('slideUp');
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'actividad_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_actividad').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.actividad.Window', {
                title: 'Modificar actividad',
                id: 'window_actividad_id',
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

                                App.request('POST', App.buildURL('/portadores/actividad/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_actividad').getStore().loadPage(1);

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
                            Ext.getCmp('window_actividad_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'actividad_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_actividad').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar actividad?',
                message: Ext.String.format('¿Está seguro que desea eliminar la actividad <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/actividad/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_actividad').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('actividad_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
