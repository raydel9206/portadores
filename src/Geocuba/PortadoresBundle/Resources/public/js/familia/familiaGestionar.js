/**
 * Created by pfcadenas on 27/09/2016.
 */

Ext.define('Portadores.Familia.Window',{
    extend: 'Ext.window.Window',
    modal: true,
    plain: true,
    resizable: false,
    width: 320,
    height: 110,
    initComponent: function(){
        this.items = [
            {
                xtype: 'form',
                frame: true,
                width: '100%',
                height: '100%',
                defaultType: 'textfield',
                bodyPadding: 5,
                bodyPadding: 5,
                items: [
                    {
                        xtype: 'textfield',
                        name: 'nombre',
                        id:'nombre',
                        labelWidth:60,
                        fieldLabel: 'Nombre',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        allowBlank: false ,
//                        maskRe: /[a-z,.A-ZáéíóúñÁÉÍÓÚÑ ]/,
                        bodyPadding: 10,
                        width : '100%'

                    }
                ]
            }
        ];

        this.callParent();
    }
});
Ext.onReady(function(){
    var _btnAdd = Ext.create('Ext.button.MyButton',{
        id: 'Familia_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function(This, e){
            Ext.create('Portadores.Familia.Window',{
                title: 'Adicionar familia',
                id: 'window_Familia_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function(){
                            var window = Ext.getCmp('window_Familia_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/familia/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('storeFamiliaId').loadPage(1);
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
                        handler: function(){
                            Ext.getCmp('window_Familia_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton',{
        id: 'Familia_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function(This, e){
            var selection = Ext.getCmp('gridFamiliaId').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.Familia.Window',{
                title: 'Modificar familia',
                id: 'window_Familia_id',
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

                                App.request('POST', App.buildURL('/portadores/familia/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('gridFamiliaId').getStore().loadPage(1);

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
                            Ext.getCmp('window_Familia_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton',{
        id: 'Familia_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function(This, e){
            selection = Ext.getCmp('gridFamiliaId').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar familia?' : '¿Eliminar familias?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar la familia de productos <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar las familias de productos?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/familia/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('gridFamiliaId').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('Familia_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});