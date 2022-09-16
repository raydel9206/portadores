/**
 * Created by javier on 16/05/16.
 */
Ext.onReady(function(){

    var store_liquidaciones = Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_liquidacion_anticipo',
        fields: [
            { name: 'id'},
            { name: 'nvehiculoid'},
            { name: 'ntarjetaid'},
            { name: 'npersonaid'},
            { name: 'nsubactividadid'},
            { name: 'ntarjetaidnro'},
            { name: 'nservicentroid'},
            { name: 'nro_vale'},
            { name: 'importe'},
            { name: 'importe_inicial'},
            { name: 'importe_final'},
            { name: 'cant_litros'},
            { name: 'fecha_vale'}

        ],
        groupField : 'ntarjetaidnro',
        proxy: {
            type: 'ajax',
//        url: Routing.generate('loadLiquidacion'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false
    });

    var store_liquidaciones_controller = Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_liquidacion_controller',
        fields: [
            { name: 'id'},
            { name: 'nvehiculoid'},
            { name: 'ntarjetaid'},
            { name: 'npersonaid'},
            { name: 'nactividadid'},
            { name: 'ntarjetaidnro'},
            { name: 'nservicentroid'},
            { name: 'nro_vale'},
            { name: 'importe'},
            { name: 'importe_inicial'},
            { name: 'importe_final'},
            { name: 'cant_litros'},
            { name: 'fecha_vale'}

        ],
        groupField : 'ntarjetaidnro',
        proxy: {
            type: 'ajax',
//        url: Routing.generate('loadLiquidacion'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false
    });


    Ext.define('Portadores.anticipo_liquidaciones.Window',{
        extend: 'Ext.window.Window',
        width: 550,
        height: 350,
        layout: {
            type: 'fit'
        },
        initComponent: function(){
            this.items = [
                {
                    xtype:'gridpanel',
                    id: 'id_grid_anticipo_liquidaciones',
                    store: store_liquidaciones,
                    columns: [
                        { text: '<strong>No.Vale</strong>', dataIndex: 'nro_vale', filter: 'string', flex: 1},
                        { text: '<strong>Importe</strong>', dataIndex: 'importe', filter: 'string', flex: 1},
                        { text: '<strong>Cant.Litros</strong>', dataIndex: 'cant_litros', filter: 'string', flex: 1},
                        { text: '<strong>Fecha</strong>', dataIndex: 'fecha_vale', filter: 'string', flex: 1}
                    ],
                    plugins: 'gridfilters',
                    listeners: {
                        afterrender:function(This){
                            var selection = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected().data;
                            var _result = App.PerformSyncServerRequest(Routing.generate('loadLiquidaciones'),{
                                vehiculo:selection.vehiculoid,
                                tarjeta:selection.tarjetaid
                            });
                            This.getStore().loadData(_result.rows)
                        }
                    }
                }
            ];
            this.callParent();
        }
    });

    Ext.define('Portadores.liquidaciones.Window',{
        extend: 'Ext.window.Window',
        width: 600,
        height: 400,
        layout: {
            type: 'fit'
        },
        initComponent: function(){
            this.items = [
                {
                    xtype:'gridpanel',
                    id: 'id_grid_liquidaciones',
                    store: store_liquidaciones_controller,
                    columns: [
                        { text: '<strong>No.Vale</strong>', dataIndex: 'nro_vale', filter: 'string', flex: 1},
                        { text: '<strong>Importe</strong>', dataIndex: 'importe', filter: 'string', flex: 1},
                        { text: '<strong>Cant.Litros</strong>', dataIndex: 'cant_litros', filter: 'string', flex: 1},
                        { text: '<strong>Fecha</strong>', dataIndex: 'fecha_vale', filter: 'string', flex: 1}
                    ],
                    tbar: {
                        id: 'liquidaciones_tbar',
                        height: 36
                    },
//                bbar: {
//                    xtype: 'pagingtoolbar',
//                    pageSize: 25,
//                    store: Ext.getStore('id_store_liquidacion_controller'),
//                    displayInfo: true,
//                    plugins: new Ext.ux.ProgressBarPager()
//                },
                    listeners: {
                        'selectionchange': {
                            fn: function(View, selections, options) {
                                if(selections.length == 0){
                                    Ext.getCmp('liquidacion_btn_del').disable();
                                }
                                else
                                {
                                    Ext.getCmp('liquidacion_btn_del').enable();
                                }
                            },
                            scope: this
                        }
                    }
                }
            ];
            this.callParent();
        }
    });


    var _btnLiquidaciones = Ext.create('Ext.button.MyButton',{
        id: 'anticipo_liquidaciones_btn',
        // disabled:true,
        text: 'Liquidaciones',
        iconCls: 'fa fa-plus-square-o fa-1_4',
        width: 125,
        handler: function(This, e){
            Ext.create('Portadores.liquidaciones.Window',{
                title: 'Gestionar liquidaciones',
                id: 'window_liquidaciones_id',
                listeners:{
                    afterrender:function(){
                        var selected = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                        var result = App.PerformSyncServerRequest(Routing.generate('loadAnticipoLiquidaciones'),{id:selected.data.id});
                        Ext.getCmp('id_grid_liquidaciones').getStore().loadData(result.rows)

                        var _btnAddLiquidaciones = Ext.create('Ext.button.MyButton',{
                            id: 'anticipo_liquidaciones_btn_add',
                            text: 'Adicionar',
                            iconCls: 'fa fa-plus-square-o fa-1_4',
                            width: 100,
                            handler: function(This, e){
                                Ext.create('Portadores.anticipo_liquidaciones.Window',{
                                    title: 'Listado de liquidaciones',
                                    id: 'window_anticipo_liquidaciones_id',
                                    buttons: [
                                        {
                                            text: 'Aceptar',
                                            width: 70,
                                            handler: function(){
                                                var store = Ext.getCmp('id_grid_anticipo_liquidaciones').getSelection();
                                                var selection = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                                                if (store.length > 0) {
                                                    var obj = {};
                                                    var send = [];
                                                    Ext.Array.each(store,function(valor){
                                                        send.push(valor.data);
                                                    });
                                                    obj.store = Ext.encode(send);
                                                    obj.anticipoid = selection.data.id;
                                                    App.ShowWaitMsg();
                                                    var _result = App.PerformSyncServerRequest(Routing.generate('addLiquidaciones'), obj);

                                                    App.HideWaitMsg();
                                                    if(_result.success){
                                                        window.close();
                                                        Ext.getCmp('liquidacion_btn_del').disable();
                                                        var selected = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                                                        var result = App.PerformSyncServerRequest(Routing.generate('loadAnticipoLiquidaciones'),{id:selected.data.id});
                                                        Ext.getCmp('id_grid_liquidaciones').getStore().loadData(result.rows)
                                                        Ext.getCmp('window_anticipo_liquidaciones_id').close();
                                                    }
                                                    App.InfoMessage('Información', _result.message, _result.cls);
                                                }
                                            }
                                        },
                                        {
                                            text: 'Cancelar',
                                            width: 70,
                                            handler: function(){
                                                Ext.getCmp('window_anticipo_liquidaciones_id').close()
                                            }
                                        }
                                    ]
                                }).show();
                            }
                        });
                        var _btn_Del_Liquidaciones = Ext.create('Ext.button.MyButton',{
                            id: 'liquidacion_btn_del',
                            text: 'Eliminar',
                            iconCls: 'fa fa-minus-square-o fa-1_4',
                            disabled: true,
                            width: 100,
                            handler: function(This, e){
                                App.ConfirmMessage(function(){
                                    var selection = Ext.getCmp('id_grid_liquidaciones').getSelectionModel().getLastSelected();
                                    App.ShowWaitMsg();
                                    var _result = App.PerformSyncServerRequest(Routing.generate('delLiquidacion'), { id: selection.data.id});
                                    App.HideWaitMsg();
                                    App.InfoMessage('Información', _result.message, _result.cls);
                                    Ext.getCmp('liquidacion_btn_del').disable();
                                    var selected = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                                    var result = App.PerformSyncServerRequest(Routing.generate('loadAnticipoLiquidaciones'),{id:selected.data.id});
                                    Ext.getCmp('id_grid_liquidaciones').getStore().loadData(result.rows)

                                },"¿Está seguro que desea eliminar la liquidación seleccionada'?");
                            }
                        });

                        Ext.getCmp('liquidaciones_tbar').add(_btnAddLiquidaciones);
                        Ext.getCmp('liquidaciones_tbar').add('-');
                        Ext.getCmp('liquidaciones_tbar').add(_btn_Del_Liquidaciones);
                    }
                },
                buttons: [
                    {
                        text: 'Cerrar',
                        width: 70,
                        handler: function(){
                            Ext.getCmp('window_liquidaciones_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

   //  var _tbar1 = Ext.getCmp('anticipo_tbar');
   // _tbar1.add(_btnLiquidaciones);

});


