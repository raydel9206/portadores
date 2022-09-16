/**
 * Created by kireny on 3/10/16.
 */

Ext.define('Portadores.responsabilidad_Acta_Material.Window', {
    extend:'Ext.window.Window',
    width:500,
    modal:true,
    resizable:false,
    initComponent:function () {
        this.items = [
            {
                xtype:'form',
                frame:true,
                defaultType:'textareafield',
                bodyPadding:5,
                layout:{
                    type:'vbox',
                    align:'stretch'
                },
                fieldDefaults:{
                    msgTarget:'side',
                    labelAlign:'top',
                    allowBlank:false
                },
                items:[
                    {
                        fieldLabel:'Nombre',
                        afterLabelTextTpl:[
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth:55,
                        height:200,
                        name:'nombre'
                    }
                ]
            }
        ];

        this.callParent();
    }
});
Ext.onReady(function () {
    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id:'responsabilidadActaMaterial_btn_add',
        text:'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width:100,
        handler:function (This, e) {
            Ext.create('Portadores.responsabilidad_Acta_Material.Window', {
                title:'Adicionar responsabilidad',
                id:'window_responsabilidadActaMaterial_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var window = Ext.getCmp('window_responsabilidadActaMaterial_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                let obj = {};
                                obj = form.getValues();
                                App.request('POST', App.buildURL('/portadores/responsabilidad/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            window.show();
                                            Ext.getCmp('id_grid_responsabilidad_Acta_Material').getStore().loadPage(1);
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
                        text:'Cancelar',
                        width:70,
                        handler:function () {
                            Ext.getCmp('window_responsabilidadActaMaterial_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id:'responsabilidadActaMaterial_btn_mod',
        text:'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            var selection = Ext.getCmp('id_grid_responsabilidad_Acta_Material').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.responsabilidad_Acta_Material.Window', {
                title:'Modificar responsabilidad',
                id:'window_responsabilidadActaMaterial_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/responsabilidad/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_responsabilidad_Acta_Material').getStore().loadPage(1);

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
                        text:'Cancelar',
                        width:70,
                        handler:function () {
                            Ext.getCmp('window_responsabilidadActaMaterial_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id:'responsabilidadActaMaterial_btn_del',
        text:'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            selection = Ext.getCmp('id_grid_responsabilidad_Acta_Material').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar responsabilidad?' : '¿Eliminar responsabilidades?',
                message: selection.length === 1 ?
                    '¿Está seguro que desea eliminar la responsabilidad de acta material?':
                    '¿Está seguro que desea eliminar las responsabilidades de acta material?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/responsabilidad/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_responsabilidad_Acta_Material').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('responsabilidad_Acta_Material_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
