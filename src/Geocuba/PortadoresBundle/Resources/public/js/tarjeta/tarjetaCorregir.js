/**
 * Created by yosley on 10/01/2017.
 */



Ext.onReady(function () {


    // Ext.define('Portadores.corregirtarjeta.Window', {
    //     extend:'Ext.window.Window',
    //     width:300,
    //     modal:true,
    //     plain:true,
    //     resizable:false,
    //     initComponent:function () {
    //         this.items = [
    //             {
    //                 xtype:'form',
    //                 frame:true,
    //                 bodyPadding:10,
    //                 layout:{
    //                     type:'vbox',
    //                     align:'stretch'
    //                 },
    //                 fieldDefaults:{
    //                     msgTarget:'side',
    //                     labelAlign:'top',
    //                     allowBlank:false
    //                 },
    //                 items:[
    //                     {
    //                         xtype:'label',
    //                         text:'Esta acción eliminará todas las recargas y liquidaciones introducidas a la tarjeta seleccionada a partir de:',
    //                         style:{
    //                             color:'red',
    //                             textAlign:'center'
    //                         }
    //                     },
    //                     {
    //                         xtype:'fieldcontainer',
    //                         layout:{
    //                             type:'hbox',
    //                             align:'stretch'
    //                         },
    //                         items:[
    //                             {
    //                                 xtype:'datefield',
    //                                 name:'fecha',
    //                                 id:'fecha',
    //                                 flex:0.5,
    //                                 margin:'0 5 0 0',
    //                                 editable:false,
    //                                 value:new Date(),
    //                                 listeners:{
    //                                     afterrender:function (This) {
    //                                         var dias = App.getDaysInMonth(App.current_year, App.current_month);
    //                                         var anno = App.current_year;
    //                                         var min = new Date(App.current_month + '/' + 1 + '/' + anno);
    //                                         var max = new Date(App.current_month + '/' + dias + '/' + anno);
    //                                         This.setMinValue(min);
    //                                         This.setMaxValue(max);
    //                                     }
    //                                 },
    //                                 format:'d/m/Y',
    //                                 fieldLabel:'Fecha',
    //                                 afterLabelTextTpl:[
    //                                     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
    //                                 ]
    //                             },
    //                             {
    //                                 xtype:'timefield',
    //                                 increment:15,
    //                                 flex:0.5,
    //                                 margin:'0 0 0 5',
    //                                 name:'hora',
    //                                 id:'hora',
    //                                 fieldLabel:'Hora',
    //                                 afterLabelTextTpl:[
    //                                     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
    //                                 ]
    //                             }
    //                         ]
    //                     }
    //                 ]}
    //         ];
    //         this.callParent();
    //     }
    // });

    // var _btn_Corregir = Ext.create('Ext.button.MyButton', {
    //     id:'tarjeta_btn_corregir',
    //     text:'Revertir',
    //     iconCls:'fa  fa-eraser fa-1_4',
    //     disabled:true,
    //     width:100,
    //     handler:function (This, e) {
    //         Ext.create('Portadores.corregirtarjeta.Window', {
    //             title:'Corregir Tarjeta',
    //             id:'window_corregirtarjeta_id',
    //             buttons:[
    //                 {
    //                     text:'Aceptar',
    //                     width:70,
    //                     handler:function () {
    //                         var selection = Ext.getCmp('id_grid_tarjeta').getSelectionModel().getLastSelected();
    //                         var window = Ext.getCmp('window_corregirtarjeta_id');
    //                         var form = window.down('form').getForm();
    //                         if (form.isValid()) {
    //                             App.ShowWaitMsg();
    //                             window.hide();
    //                             var obj = form.getValues();
    //                             obj.id = selection.data.id;
    //                             obj.nunidadid = selection.data.nunidadid;
    //                             var _result = App.PerformSyncServerRequest(Routing.generate('corregirTarjetas'), obj);
    //                             App.HideWaitMsg();
    //                             if (_result.success) {
    //                                 window.close();
    //                                 Ext.getCmp('id_grid_tarjeta').getStore().load();
    //                             }
    //                             else {
    //                                 window.show();
    //                                 form.markInvalid(_result.message);
    //                             }
    //                             App.InfoMessage('Información', _result.message, _result.cls);
    //                         }
    //                     }
    //                 },
    //                 {
    //                     text:'Cancelar',
    //                     width:70,
    //                     handler:function () {
    //                         Ext.getCmp('window_corregirtarjeta_id').close()
    //                     }
    //                 }
    //             ]
    //         }).show();
    //     }
    // });

    // var _tbar = Ext.getCmp('tbar_historial');
    // _tbar.add(_btn_Corregir);
    // _tbar.add('-');
});
