/**
 * Created by kireny on 06/05/16.
 */
Ext.define('serviciosa', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'tipo',  type: 'string'}
    ]
});

var tipo= Ext.create('Ext.data.Store', {
    model: 'serviciosa',
    data : [
        {id: '1',    tipo: 'Monofásicos'},
        {id: '2',    tipo: 'Trifásicos'}
    ]
});

Ext.define('Portadores.bancotransformadores.Window',{
    extend: 'Ext.window.Window',
    width: 400,
    height: 200,
    initComponent: function(){
        this.items = [
            {
                xtype: 'form',
                frame: true,
                width: '100%',
                height: '100%',
                defaultType: 'textfield',
                bodyPadding: 5,
                items: [
                    {
                        fieldLabel: 'Capacidad',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weighst:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 70,
                        width:300,
                        name: 'capacidad',
                        allowBlank: false,
                        maskRe: /[0.000000-9.000000]/
                    },
                    {
                        xtype: 'combobox',
                        name: 'tipo',
                        id: 'tipo',
                        fieldLabel: 'Tipo',
                        labelWidth:70,
                        width:300,
                        store: tipo,
                        displayField: 'tipo',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        valueField: 'tipo',
                        typeAhead: true,
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione la tipo servicio...',
                        selectOnFocus: true,
                        editable: true,
                        allowBlank: false
                    },
                    {
                        fieldLabel: 'PFE',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weighst:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 70,
                        width:300,
                        name: 'pfe',
                        allowBlank: false,
                        maskRe: /[0.000000-9.000000]/
                    },
                    {
                        fieldLabel: 'PCU',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weighst:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 70,
                        width:300,
                        name: 'pcu',
                        allowBlank: false,
                        maskRe: /[0.000000-9.000000]/
                    }
                ]
            }
        ];
        this.callParent();
    }
});
Ext.onReady(function(){
    var _btnAdd = Ext.create('Ext.button.MyButton',{
        id: 'bancotransformadores_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function(This, e){
            Ext.create('Portadores.bancotransformadores.Window',{
                title: 'Adicionar capacidad de banco de transformadores',
                id: 'window_bancotransformadores_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function(){
                            var window = Ext.getCmp('window_bancotransformadores_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/banco_transformadores/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('gridbanco_transformadoresId').getStore().loadPage(1);
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
                        handler: function(){
                            Ext.getCmp('window_bancotransformadores_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton',{
        id: 'bancotransformadores_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function(This, e){
            var selection = Ext.getCmp('gridbanco_transformadoresId').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.bancotransformadores.Window',{
                title: 'Modificar capacidad de banco de transformadores',
                id: 'window_bancotransformadores_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function(){
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/banco_transformadores/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('gridbanco_transformadoresId').getStore().loadPage(1);

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
                        handler: function(){
                            Ext.getCmp('window_bancotransformadores_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton',{
        id: 'bancotransformadores_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function(This, e){
            selection = Ext.getCmp('gridbanco_transformadoresId').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar factor?',
                message: '¿Está seguro que desea eliminar el banco transformador seleccionado?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/banco_transformadores/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_factor').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('bancotransformadores_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});