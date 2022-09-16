/**
 * Created by kireny on 5/11/15.
 */
Ext.onReady(function(){

var _storem = Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_area_areamedida',
    fields: [
        {name: 'id'},
        {name: 'nombre'}
    ],
    proxy: {
        type: 'ajax',
        url: App.buildURL('/portadores/area/loadAreaCombo'),
        reader: {
            rootProperty: 'rows'
        }
    },
    autoLoad: false,
    listeners: {
        beforeload: function (This, operation, eOpts) {
            Ext.getCmp('id_grid_areamedida').getSelectionModel().deselectAll();
            operation.setParams({
                unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
            });
        }
    }
});

Ext.define('Portadores.areamedida.Window',{
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
                bodyPadding: 10,
                fieldDefaults:{
                    msgTarget:'side',
                    labelWidth:80,
                    labelAlign: 'top',
                    allowBlank:false
                },
                items: [
                    {
                        xtype: 'combobox',
                        width: 500,
                        name: 'nlista_areaid',
                        id: 'nlista_areaid',
                        fieldLabel: 'Área',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        store: _storem,
                        displayField: 'nombre',
                        valueField: 'id',
                        typeAhead: true,
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione el Área',
                        selectOnFocus: true,
                        editable: true
                    },
                    {
                        xtype: 'textareafield',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        width: 500,
                        height:100,
                        bodyPadding: 10,
                        name: 'nombre',
                        id: 'nombre',
                        fieldLabel: 'Nombre'
                    },
                    {
                        xtype: 'checkboxfield',
                        bodyPadding: 10,
                        name: 'invalidante',
                        id: 'invalidante',
                        fieldLabel: 'Invalidante',
                        labelAlign: 'left',
                        inputValue: 'true'
                    }
                ]
            }
        ];

        this.callParent();
    },
    // listeners: {
    //     afterrender: function (This, operation, eOpts) {
    //         Ext.getStore('id_store_area_areamedida').load();
    //         // Ext.getStore('id_store_destino').load();
    //     }
    // }

});



    var _btnAdd = Ext.create('Ext.button.MyButton',{
        id: 'areamedida_btn_add',
        text: 'Adicionar',
        // disabled: true,
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.areamedida.Window',{
                title: 'Adicionar acción',
                id: 'window_areamedida_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width:70,
                        handler: function() {
                            var window = Ext.getCmp('window_areamedida_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/accion/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_areamedida').getStore().loadPage(1);
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
                            Ext.getCmp('window_areamedida_id').close()
                        }
                    }
                ]

            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton',{
        id: 'areamedida_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection= Ext.getCmp('id_grid_areamedida').getSelectionModel().getLastSelected();
            var window= Ext.create('Portadores.areamedida.Window',{
                title: 'Modificar acción',
                id: 'window_areamedida_id',
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

                                App.request('POST', App.buildURL('/portadores/accion/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_areamedida').getStore().loadPage(1);

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
                            Ext.getCmp('window_areamedida_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton',{
        id: 'areamedida_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_areamedida').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar medida?' : '¿Eliminar medidas?',
                message: selection.length === 1 ?
                    '¿Está seguro que desea eliminar la medida?':
                    '¿Está seguro que desea eliminar las medidas?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/accion/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_areamedida').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('areamedida_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);

});