/**
 * Created by javier on 20/05/2016.
 */
Ext.define('Portadores.submayor.Window',{
    extend: 'Ext.window.Window',
    width: 250,
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
                items: [
                    {
                        fieldLabel: 'Nombre',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 55,
                        name: 'nombre',
                        allowBlank: false,
                        maskRe: /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]/,
                        regex: /^[A-Za-z0-9áéíóúñÁÉÍÓÚÑ]*\s?([A-Za-z0-9áéíóúñÁÉÍÓÚÑ]+\s?)+[A-Za-z0-9áéíóúñÁÉÍÓÚÑ]$/,
                        regexText: 'El nombre no es válido'
                    }
                ]
            }
        ];

        this.callParent();
    }
});
Ext.onReady(function(){
    var _btnAdd = Ext.create('Ext.button.MyButton',{
        id: 'submayor_btn_add',
        text: 'Adicionar',
        iconCls: 'fa fa-plus-square-o fa-1_4',
        width: 100,
        handler: function(This, e){
            Ext.create('Portadores.submayor.Window',{
                title: 'Adicionar submayor',
                id: 'window_submayor_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function(){
                            var window = Ext.getCmp('window_submayor_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                App.ShowWaitMsg();
                                var _result = App.PerformSyncServerRequest(Routing.generate('addCargo'), form.getValues());
                                App.HideWaitMsg();
                                if(_result.success){
                                    window.close();
                                    Ext.getCmp('id_grid_submayor').getStore().load();
                                }
                                App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function(){
                            Ext.getCmp('window_submayor_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton',{
        id: 'submayor_btn_mod',
        text: 'Modificar',
        iconCls: 'fa fa-pencil-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function(This, e){
            var selection = Ext.getCmp('id_grid_submayor').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.submayor.Window',{
                title: 'Modificar submayor',
                id: 'window_submayor_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function(){
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                App.ShowWaitMsg();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                var _result = App.PerformSyncServerRequest(Routing.generate('modCargo'), obj);
                                App.HideWaitMsg();
                                if(_result.success){
                                    window.close();
                                    Ext.getCmp('id_grid_submayor').getStore().load();
                                }
                                App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function(){
                            Ext.getCmp('window_submayor_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton',{
        id: 'submayor_btn_del',
        text: 'Eliminar',
        iconCls: 'fa fa-minus-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function(This, e){
            App.ConfirmMessage(function(){
                var selection = Ext.getCmp('id_grid_submayor').getSelectionModel().getLastSelected();
                App.ShowWaitMsg();
                var _result = App.PerformSyncServerRequest(Routing.generate('delCargo'), { id: selection.data.id});
                App.HideWaitMsg();
                App.InfoMessage('Información', _result.message, _result.cls);
                Ext.getCmp('id_grid_submayor').getStore().load();
            }, "Está seguro que desea eliminar el submayor seleccionado?");

        }
    });

    var _btn_print_submayor = Ext.create('Ext.button.MyButton',{
        id: '_btn_submayor',
        text: 'Imprimir',
        //disabled:true,
        iconCls: 'fas fa-print text-primary',
        handler: function(This, e){
            var store = Ext.getCmp('id_grid_submayor').getStore();
            var obj = {};
            var send = [];
            Ext.Array.each(store.data.items,function(valor){
                send.push(valor.data);
            });
            obj.store = Ext.encode(send);
            obj.group = Ext.getCmp('id_grid_submayor').getStore().groupField;
            obj.unidadid = Ext.getCmp('arbolunidades').getSelection()[0].data.id;
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

            App.request('POST', App.buildURL('/portadores/submayor/print'), obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        var newWindow = window.open('', '', 'width=1200, height=700'),
                            document = newWindow.document.open();
                        document.write(response.html);
                        setTimeout(() => {
                            newWindow.print();
                        }, 500);
                        document.close();
                    }

                }, null, null, true
            );
        }
    });

    var _btn_export_submayor = Ext.create('Ext.button.MyButton',{
        id: '_btn_sunmayor_export',
        text: 'Exportar',
       // disabled:true,
        iconCls: 'fas fa-file-excel text-primary',
        handler: function(This, e){
            var store = Ext.getCmp('id_grid_submayor').getStore();
            var send = [];
            Ext.Array.each(store.data.items,function(valor){
                send.push(valor.data);
            });

            var obj = {};
            obj.store = Ext.encode(send);
            obj.group = Ext.getCmp('id_grid_submayor').getStore().groupField;
            obj.unidadid = Ext.getCmp('arbolunidades').getSelection()[0].data.id;
            obj.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

            App.request('POST', App.buildURL('/portadores/submayor/print'), obj, null, null,
                function (response) { // success_callback
                    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                }
            );

        }
    });
    var _tbar = Ext.getCmp('submayor_tbar');
    _tbar.add('->');
    _tbar.add(_btn_print_submayor);
    _tbar.add(_btn_export_submayor);
//    _tbar.add(_btnAdd);
//    _tbar.add('-');
//    _tbar.add(_btnMod);
//    _tbar.add('-');
//    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
