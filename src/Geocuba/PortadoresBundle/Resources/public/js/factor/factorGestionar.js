/**
 * Created by kireny on 4/11/15.
 */
Ext.onReady(function(){

Ext.define('portador', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'portador',  type: 'string'}
    ]
});
var _portador= Ext.create('Ext.data.Store', {
    model: 'portador',
    data : [
        {id: '1',    portador: 'DIESEL'},
        {id: '2',    portador: 'Fuel Oil'},
        {id: '3',    portador: 'GASOLINA'},
        {id: '4',    portador: 'GLP'}
    ]
});


var _storeum = Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_unidadmedida_factor',
    fields: [
        {name: 'id'},
        {name: 'nombre'}
    ],
    proxy: {
        type: 'ajax',
        url: App.buildURL('/portadores/unidad_medida/load'),
        reader: {
            rootProperty: 'rows'
        }
    },
    autoLoad: true
});





Ext.define('Portadores.factor.Window',{
    extend: 'Ext.window.Window',
    modal:true,
    plain:true,
    resizable:false,

    initComponent:function (){
        this.items= [
            {
                xtype: 'form',
                frame: true,
                width: '100%',
                height: '100%',
                layout: 'anchor',
                bodyStyle: 'padding:5px 5px 0',
                items: [
                    {
                        xtype: 'combobox',
                        width: 350,
                        name: 'portador',
                        id: 'portador',
                        fieldLabel: 'Portador',
                        store: _portador,
                        displayField: 'portador',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth:140,
                        valueField: 'portador',
                        typeAhead: true,
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione el portador',
                        selectOnFocus: true,
                        editable: true,
                        allowBlank: false

                    },
                            {
                                xtype: 'combobox',
                                width: 350,
                                name: 'unidad_medida_id1',
                                id: 'unidad_medida_id1',
                                fieldLabel: 'UM a convertir',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                labelWidth:140,
                                store: Ext.getStore('id_store_unidadmedida_factor'),
                                displayField: 'nombre',
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                triggerAction: 'all',
                                emptyText: 'Seleccione la unidad de medida',
                                selectOnFocus: true,
                                editable: true,
                                allowBlank: false

                            },
                            {
                                xtype: 'textfield',
                                name: 'factor_id1',
                                id:'factor_id1',
                                width: 350,
                                labelWidth:140,
                                fieldLabel: 'Factor de conversión ',
                                afterLabelTextTpl: [
                                     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                                allowBlank: false , // requires a non-empty value
                                maskRe: /[0,0000-9,0000 ]/,
                                bodyPadding: 10
                            },
                            {
                                xtype: 'combobox',
                                width: 350,
                                name: 'unidad_medida_id2',
                                id: 'unidad_medida_id2',
                                fieldLabel: 'UM convertida',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                labelWidth:140,
                                store: Ext.getStore('id_store_unidadmedida_factor'),
                                displayField: 'nombre',
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                triggerAction: 'all',
                                emptyText: 'Seleccione la unidad de medida',
                                selectOnFocus: true,
                                editable: true,
                                allowBlank: false
                            },
                            {
                                xtype: 'textfield',
                                name: 'factor_id2',
                                id:'factor_id2',
                                width: 350,
                                labelWidth:140,
                                fieldLabel: 'Factor de conversión',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                allowBlank: false , // requires a non-empty value
                                maskRe: /[0,0000-9,0000 ]/,
                                bodyPadding: 10

                            }
                ]
            }
        ];

        this.callParent();
    }

});


    var _btnAdd = Ext.create('Ext.button.MyButton',{
        id: 'factor_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.factor.Window',{
                title: 'Adicionar factor de conversión',
                id: 'window_factor_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width:70,
                        handler: function() {
                            var window = Ext.getCmp('window_factor_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/factor/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_factor').getStore().loadPage(1);
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
                            Ext.getCmp('window_factor_id').close()
                        }
                    }
                ]

            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton',{
        id: 'factor_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection= Ext.getCmp('id_grid_factor').getSelectionModel().getLastSelected();
            var window= Ext.create('Portadores.factor.Window',{
                title: 'Modificar factor de conversión',
                id: 'window_factor_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function() {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;

                                App.request('POST', App.buildURL('/portadores/factor/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_factor').getStore().loadPage(1);

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
                            Ext.getCmp('window_factor_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton',{
        id: 'factor_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_factor').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar factor?',
                message: Ext.String.format('¿Está seguro que desea eliminar el factor de conversión <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/factor/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_factor').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('factor_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);

});