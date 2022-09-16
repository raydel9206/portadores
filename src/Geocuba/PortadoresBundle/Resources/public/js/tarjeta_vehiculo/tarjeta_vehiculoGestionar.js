/**
 * Created by javier on 17/05/2016.
 */

var store_vehiculo = Ext.create('Ext.data.JsonStore',{
    storeId: 'id_store_vehiculo_tarjeta_vehiculo',
    fields: [
        { name: 'id'},
        { name: 'nmarca_vehiculoid'},
        { name: 'nestado_tecnicoid'},
        { name: 'ndenominacion_vehiculoid'},
        { name: 'nunidadid'},
        { name: 'nunidad'},
        { name: 'nchoferid'},
        { name: 'ntipo_combustibleid'},
        { name: 'matricula'},
        { name: 'norma'},
        { name: 'nro_inventario'},
        { name: 'nro_serie_carreceria'},
        { name: 'nro_serie_motor'},
        { name: 'color'},
        { name: 'nro_circulacion'},
        { name: 'fecha_expiracion_circulacion'},
        { name: 'anno_fabricacion'}
    ],
    proxy: {
        type: 'ajax',
        url: Routing.generate('loadVehiculoTarjetaVehiculo'),
        reader: {
            rootProperty: 'rows'
        }
    },
    autoLoad: true
});

var store_tarjeta = Ext.create('Ext.data.JsonStore',{
    storeId: 'id_store_tarjeta_vehiculo_tarjeta',
    fields: [
        { name: 'id'},
        { name: 'ncajaid'},
        { name: 'ntipo_combustibleid'},
        { name: 'nombretipo_combustibleid'},
        { name: 'nmonedaid'},
        { name: 'nunidadid'},
        { name: 'nombreunidadid'},
        { name: 'nro_tarjeta'},
        { name: 'importe'},
        { name: 'fecha_registro'},
        { name: 'fecha_vencimieno'},
        { name: 'fecha_baja'},
        { name: 'causa_baja'},
        { name: 'reserva'},
        { name: 'exepcional'}
    ],
    groupField : 'nombreunidadid',

    proxy: {
        type: 'ajax',
//        url: Routing.generate('loadTarjetaTarjetaVehiculo'),
        reader: {
            rootProperty: 'rows'
        }
    },
    autoLoad: false
});

Ext.define('Portadores.tarjeta_vehiculo.Window',{
    extend: 'Ext.window.Window',
    width: 300,
    height: 155,
    initComponent: function(){
        this.items = [
            {
                xtype: 'form',
                frame: true,
                defaultType: 'textfield',
                bodyPadding: 10,
                width: 300,
                height: 155,
                items: [
                    {
                        xtype: 'combobox',
                        name: 'vehiculoid',
                        id: 'vehiculoid',
                        fieldLabel: 'Vehículo',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        store: store_vehiculo,
                        displayField: 'matricula',
                        valueField: 'id',
                        labelWidth:60,
                        width:'96%',
                        typeAhead: true,
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione el vehículo...',
                        selectOnFocus: true,
                        editable: true,
                        allowBlank: false,
                        listeners:{
                            select:function(This){
                                Ext.getCmp('tarjetaid').reset();
                                Ext.getCmp('tarjetaid').disable();
                                var result = App.PerformSyncServerRequest(Routing.generate('loadTarjetaTarjetaVehiculo'),{id:This.value});
                                Ext.getCmp('tarjetaid').getStore().loadData(result.rows)
                                Ext.getCmp('tarjetaid').enable();
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        name: 'tarjetaid',
                        id: 'tarjetaid',
                        disabled:true,
                        margin:'10 0 0 0',
                        fieldLabel: 'Tarjeta',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        store: store_tarjeta,
                        displayField: 'nro_tarjeta',
                        valueField: 'id',
                        labelWidth:60,
                        width:'96%',
                        typeAhead: true,
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione la tarjeta...',
                        selectOnFocus: true,
                        editable: true,
                        allowBlank: false
                    }
                ]
            }
        ];

        this.callParent();
    }
});

Ext.onReady(function() {
    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'tarjeta_vehiculo_btn_add',
        text: 'Adicionar',
        iconCls: 'fa fa-plus-square-o fa-1_4',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.tarjeta_vehiculo.Window', {
                title: 'Asignar tarjeta',
                id: 'window_tarjeta_vehiculo_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_tarjeta_vehiculo_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                App.ShowWaitMsg();
                                var _result = App.PerformSyncServerRequest(Routing.generate('asignarTarjeta'), form.getValues());
                                App.HideWaitMsg();
                                if (_result.success) {
                                    window.close();
                                    Ext.getCmp('id_grid_tarjeta_vehiculo').getStore().load();
                                }
                                App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_tarjeta_vehiculo_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton',{
        id: 'tarjeta_vehiculo_btn_mod',
        text: 'Modificar',
        iconCls: 'fa fa-pencil-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function(This, e){
            var selection = Ext.getCmp('id_grid_tarjeta_vehiculo').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.tarjeta_vehiculo.Window',{
                title: 'Modificar asignación',
                id: 'window_tarjeta_vehiculo_id',
                listeners:{
                    afterrender:function(){
//                        Ext.getCmp('window_tarjeta_vehiculo_id').disable();
                        var result = App.PerformSyncServerRequest(Routing.generate('loadTarjetaTarjetaVehiculo'),{id:selection.data.vehiculoid});
                        Ext.getCmp('tarjetaid').getStore().loadData(result.rows)
                        var store = Ext.getCmp('tarjetaid').getStore();
                        var find = store.findRecord('id', selection.data.tarjetaid);
                        Ext.getCmp('tarjetaid').select(find)
                        Ext.getCmp('tarjetaid').enable();
//                        Ext.getCmp('window_tarjeta_vehiculo_id').enable();
                    }
                },
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
                                var _result = App.PerformSyncServerRequest(Routing.generate('modTarjetaVehiculo'), obj);
                                App.HideWaitMsg();
                                if(_result.success){
                                    window.close();
                                    Ext.getCmp('id_grid_tarjeta_vehiculo').getStore().load();
                                }
                                App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function(){
                            Ext.getCmp('window_tarjeta_vehiculo_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton',{
        id: 'tarjeta_vehiculo_btn_del',
        text: 'Eliminar',
        iconCls: 'fa fa-minus-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function(This, e){
            App.ConfirmMessage(function(){
                var selection = Ext.getCmp('id_grid_tarjeta_vehiculo').getSelectionModel().getLastSelected();
                App.ShowWaitMsg();
                var _result = App.PerformSyncServerRequest(Routing.generate('delTarjetaVehiculo'), { id: selection.data.id});
                App.HideWaitMsg();
                App.InfoMessage('Información', _result.message, _result.cls);
                Ext.getCmp('id_grid_tarjeta_vehiculo').getStore().load();
            }, "Está seguro que desea eliminar la asignación seleccionada?");

        }
    });

    var _tbar = Ext.getCmp('tarjeta_vehiculo_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);


});
