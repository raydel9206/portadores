/**
 * Created by kireny on 02/11/2015.
 */


Ext.define('Portadores.tarifa.Window',{
    extend: 'Ext.window.Window',
    width: 1100,
    height: 250,

    initComponent: function(){
        this.items = [
            {
                xtype: 'form',
                frame: true,
                width: '200%',
                height: '200%',
                layout:'anchor',
                bodyStyle:'padding:5px 5px 0',
                items: [
                    {
                        xtype:'fieldcontainer',
                        //flex: 1,
                        layout: 'hbox',
                        border: false,
                        collapsible: false,
                        items:[
                            {
                                xtype:'fieldcontainer',
                                //flex:2,
                                layout:'anchor',
                                border:false,
                                collapsible:false,
                                items:[
                                    {
                                        xtype:'field',
                                        fieldLabel: 'Nombre Tarifa',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 100,
                                        width:600,
                                        name: 'nombre',
                                        allowBlank: false
                                    },

                                    {
                                        xtype:'field',
                                        fieldLabel: 'Cualquier momento del dia',
                                        labelWidth: 100,
                                        width:620,
                                        name: 'momento_dia',
                                        id:'momento_dia',
                                        disabled:true
                                    },
                                    {
                                        xtype:'field',
                                        fieldLabel: 'Horario Pico',
                                        labelWidth: 100,
                                        width:600,
                                        name: 'horario_pico',
                                        id:'horario_pico',
                                        disabled:true
                                    },
                                    {
                                        xtype:'field',
                                        fieldLabel: 'Horario del Día',
                                        labelWidth: 100,
                                        width:600,
                                        name: 'horario_dia',
                                        id:'horario_dia',
                                        disabled:true
                                    },
                                    {
                                        xtype:'field',
                                        fieldLabel: 'Horario de la Madrugada',
                                        labelWidth: 100,
                                        width:600,
                                        name: 'horario_madrugada',
                                        id:'horario_madrugada',
                                        disabled:true
                                    }
                                ]
                            },
                            {
                                xtype:'fieldcontainer',
                                //flex:1,
                                layout:'anchor',
                                border:false,
                                margin:'40 0 0 0',
                                collapsible:false,
                                items:[
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'K',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 30,
                                        width:130,
                                        name: 'k_md',
                                        id:'k_md',
                                        allowBlank: false,
                                        disabled:true
                                    },
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'K',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 30,
                                        width:130,
                                        name: 'k_hp',
                                        id:'k_hp',
                                        allowBlank: false,
                                        disabled:true
                                    },
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'K',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 30,
                                        width:130,
                                        name: 'k_hd',
                                        id:'k_hd',
                                        allowBlank: false,
                                        disabled:true
                                    },
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'K',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 30,
                                        width:130,
                                        name: 'k_hm',
                                        id:'k_hm',
                                        allowBlank: false,
                                        disabled:true
                                    }
                                ]
                            },
                            {
                                xtype:'fieldcontainer',
                                layout:'anchor',
                                border:false,
                                margin:'40 10 10 10',
                                collapsible:false,
                                items:[
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'Consumo del día en KW/h',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 200,
                                        width:300,
                                        name: 'consumo_pico_md',
                                        id:'consumo_pico_md',
                                        allowBlank: false,
                                        disabled:true
                                    },
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'Consumo pico en KW/h',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 200,
                                        width:300,
                                        name: 'consumo_pico_hp',
                                        id:'consumo_pico_hp',
                                        allowBlank: false,
                                        disabled:true
                                    },
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'Consumo día en KW/h',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 200,
                                        width:300,
                                        name: 'consumo_pico_hd',
                                        id:'consumo_pico_hd',
                                        allowBlank: false,
                                        disabled:true
                                    },
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'Consumo madrugada en KW/h',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 200,
                                        width:300,
                                        name: 'consumo_pico_hm',
                                        id:'consumo_pico_hm',
                                        allowBlank: false,
                                        disabled:true
                                    },
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

Ext.define('Portadores.c2.Window',{
    extend: 'Ext.window.Window',
    width: 770,
    height: 220,

    initComponent: function(){
        this.items = [
            {
                xtype: 'form',
                frame: true,
                width: '200%',
                height: '200%',
                layout:'anchor',
                bodyStyle:'padding:5px 5px 0',
                items: [
                    {
                        xtype:'fieldcontainer',
                        //flex: 1,
                        layout: 'hbox',
                        border: false,
                        collapsible: false,
                        items:[
                            {
                                xtype:'fieldcontainer',
                                //flex:2,
                                layout:'anchor',
                                border:false,
                                collapsible:false,
                                items:[
                                    {
                                        xtype:'field',
                                        fieldLabel: 'Nombre Tarifa',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 150,
                                        width:600,
                                        name: 'nombre',
                                        allowBlank: false
                                    },

                                    {
                                        xtype:'field',
                                        fieldLabel: 'Cualquier horario del dia',
                                        labelWidth: 150,
                                        width:650,
                                        name: 'ecuacion',
                                        id:'ecuacion',
                                        disabled:true
                                    },
                                    {
                                        xtype:'field',
                                        fieldLabel: 'Donde:',
                                        labelWidth: 150,
                                        width:750,
                                        name: 'kgee',
                                        id:'kgee',
                                        disabled:true
                                    },
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'Entrega del día',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 150,
                                        width:300,
                                        name: 'consumo_pico_md',
                                        id:'consumo_pico_md',
                                        allowBlank: false,
                                        disabled:true
                                    },
                                    {
                                        xtype:'textfield',
                                        fieldLabel: 'Precio de la tonelada',
                                        afterLabelTextTpl: [
                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        ],
                                        labelWidth: 150,
                                        width:300,
                                        name: 'k_md',
                                        id:'k_md',
                                        allowBlank: false,
                                        disabled:true
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

Ext.onReady(function() {
    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'tarifa_btn_add',
        text: 'Adicionar',
        iconCls: 'fa fa-plus-square-o fa-1_4',
        width: 100,
        disabled: true,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_tarifa').getSelectionModel().getLastSelected();
            var c2 =selection.data.ecuacion;
            if(c2){
                var window=Ext.create('Portadores.c2.Window',{
                    title:'Adicionar Tarifa',
                    id:'window_c2_id',
                    listeners:{
                        afterrender:function(){
                            if(selection.data.ecuacion !=""){
                                Ext.getCmp('ecuacion').enable();
                                Ext.getCmp('kgee').enable();
                                Ext.getCmp('consumo_pico_md').enable();
                                Ext.getCmp('k_md').enable();
                            }
                        }
                    },
                    buttons:[
                        {
                            text:'Aceptar',
                            width:70,
                            handler:function(){
                                var form = window.down('form').getForm();
                                if(form.isValid()){
                                    App.ShowWaitMsg();
                                    window.hide();
                                    var obj = form.getValues();
                                    obj.id = selection.data.id;
                                    var _result = App.PerformSyncServerRequest(Routing.generate('addTarifa'),obj);
                                    App.HideWaitMsg();
                                    if(_result.success){
                                        window.close();
                                        Ext.getCmp('id_grid_tarifa').getStore().load();
                                    }
                                    else{
                                        window.show();
                                        form.markInvalid(_result.message);
                                    }
                                    App.InfoMessage('Información',_result.message,_result.cls);
                                }
                            }
                        },
                        {
                            text:'Cancelar',
                            width:70,
                            handler: function () {
                                Ext.getCmp('window_c2_id').close();
                            }
                        }

                    ]
                });
                window.show();
                window.down('form').loadRecord(selection);
            }
            else{
                var window=Ext.create('Portadores.tarifa.Window',{
                    title:'Adicionar Tarifa',
                    id:'window_tarifaadd_id',
                    listeners:{
                        afterrender:function(){
                            if(selection.data.momento_dia !=""){
                                Ext.getCmp('momento_dia').enable();
                                Ext.getCmp('k_md').enable();
                                Ext.getCmp('consumo_pico_md').enable();
                            }
                            else if(selection.data.horario_pico !="" && selection.data.horario_dia !="" && selection.data.horario_madrugada !=""){
                                Ext.getCmp('horario_pico').enable();
                                Ext.getCmp('k_hp').enable();
                                Ext.getCmp('consumo_pico_hp').enable();

                                Ext.getCmp('horario_dia').enable();
                                Ext.getCmp('k_hd').enable();
                                Ext.getCmp('consumo_pico_hd').enable();

                                Ext.getCmp('horario_madrugada').enable();
                                Ext.getCmp('k_hm').enable();
                                Ext.getCmp('consumo_pico_hm').enable();
                            }
                        }
                    },
                    buttons:[
                        {
                            text:'Aceptar',
                            width:70,
                            handler:function(){
                                var form = window.down('form').getForm();
                                if(form.isValid()){
                                    App.ShowWaitMsg();
                                    window.hide();
                                    var obj = form.getValues();
                                    obj.id = selection.data.id;
                                    var _result = App.PerformSyncServerRequest(Routing.generate('addTarifa'),obj);
                                    App.HideWaitMsg();
                                    if(_result.success){
                                        window.close();
                                        Ext.getCmp('id_grid_tarifa').getStore().load();
                                    }
                                    else{
                                        window.show();
                                        form.markInvalid(_result.message);
                                    }
                                    App.InfoMessage('Información',_result.message,_result.cls);
                                }
                            }
                        },
                        {
                            text:'Cancelar',
                            width:70,
                            handler: function () {
                                Ext.getCmp('window_tarifaadd_id').close();
                            }
                        }

                    ]
                });
                window.show();
                window.down('form').loadRecord(selection);
            }
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton',{
        id: 'tarifa_btn_mod',
        text: 'Modificar',
        iconCls: 'fa fa-pencil-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function(This, e){
            var selection = Ext.getCmp('id_grid_tarifa').getSelectionModel().getLastSelected();
            //console.log(selection)
            var window = Ext.create('Portadores.tarifa.Window',{
                title: 'Modificar tarifa',
                id: 'window_tarifa_id',
                listeners:{
                    afterrender:function(){
                        if(selection.data.momento_dia != ""){
                            Ext.getCmp('momento_dia').enable();
                            // Ext.getCmp('diario').setValue(true);
                        }else if(selection.data.horario_pico !="" && selection.data.horario_dia !="" && selection.data.horario_madrugada !=""){
                            Ext.getCmp('horario_pico').enable();
                            Ext.getCmp('horario_dia').enable();
                            Ext.getCmp('horario_madrugada').enable();
                            Ext.getCmp('divido').setValue(true);
                        }
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
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                var _result = App.PerformSyncServerRequest(Routing.generate('modTarifa'), obj);
                                App.HideWaitMsg();
                                if(_result.success){
                                    window.close();
                                    Ext.getCmp('id_grid_tarifa').getStore().load();
                                }
                                else{
                                    window.show();
                                    form.markInvalid(_result.message);
                                }
                                App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function(){
                            Ext.getCmp('window_tarifa_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton',{
       id: 'tarifa_btn_del',
       text: 'Eliminar',
       iconCls: 'fa fa-minus-square-o fa-1_4',
       disabled: true,
       width: 100,
       handler: function(This, e){
           App.ConfirmMessage(function(){
               var selection = Ext.getCmp('id_grid_tarifa').getSelectionModel().getLastSelected();
               App.ShowWaitMsg();
               var _result = App.PerformSyncServerRequest(Routing.generate('delTarifa'), { id: selection.data.id});
               App.HideWaitMsg();
               App.InfoMessage('Información', _result.message, _result.cls);
               Ext.getCmp('id_grid_tarifa').getStore().load();
           }, "Está seguro que desea eliminar la tarifa  seleccionada?");
       }
    });

    var _tbar = Ext.getCmp('tarifa_tbar');
    // _tbar.add(_btnAdd);
    // _tbar.add('-');
    // _tbar.add(_btnMod);
    // _tbar.add('-');
    // _tbar.add(_btn_Del);
    //_tbar.setHeight(36);
});