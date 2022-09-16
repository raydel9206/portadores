/**
 * Created by yosley on 07/10/2015.
 */
Ext.onReady(function(){
    Ext.define('Portadores.servicio_agua.Window',{
    extend: 'Ext.window.Window',
    id:'win_id',
    width: 300,
    height: 240,
    modal:true,
    plain:true,
    resizable : false,
    initComponent: function(){
        this.items = [
            {
                xtype: 'form',
                //frame: true,
                width: '100%',
                height: '100%',
                defaultType: 'textfield',
                bodyPadding: 5,
                items: [
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Unidad',
                        margin:'5 0 0 0',
                        store: 'store_unidades',
                        emptyText: 'Seleccione la unidad...',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name: 'unidad_id',
                        id: 'unidad_id',
                        displayField: 'nombre',
                        valueField: 'id',
                        queryMode: 'local',
                        forceSelection: true,
                        allowBlank: false,
                        editable: false
                    },
                    {
                        fieldLabel: 'Nombre',
                        margin:'5 0 0 0',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name: 'nombre',
                        allowBlank: false,
                        maskRe: /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ() ]/,
                        regex: /^[A-Za-z0-9áéíóúñÁÉÍÓÚÑ()]*\s?([A-Za-z0-9áéíóúñÁÉÍÓÚÑ()]+\s?)+[A-Za-z0-9áéíóúñÁÉÍÓÚÑ()]$/,
                        regexText: 'El nombre no es válido'
                    },
                    {
                        xtype:'textarea',
                        fieldLabel:'Dirección',
                        margin:'7 0 0 0',
                        allowBlank: false,
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name:'direccion',
                        id:'direccion_id',
                        height:40
                    },
                    {
                        xtype: 'checkboxgroup',
//                        margin:'5 0 0 0',
                        columns: 2,
                        width: '98%',
                        vertical: true,
                        items: [
                            {
                                boxLabel: 'Metrado',
                                name: 'metrado',
                                id: 'metrado',
                                inputValue: '1',
                                listeners:{
                                    change:function(This){
                                        if(This.value){
                                            Ext.getCmp('codigo_id').show();
                                            Ext.getCmp('codigo_id').allowBlank = false;

                                            Ext.getCmp('lectura_inicial_id').show();
                                            Ext.getCmp('lectura_inicial_id').allowBlank = false;
                                            Ext.getCmp('window_servicio_agua_id').setHeight(295);
                                        }else{
                                            Ext.getCmp('codigo_id').hide();
                                            Ext.getCmp('codigo_id').reset();
                                            Ext.getCmp('codigo_id').allowBlank = true;

                                            Ext.getCmp('lectura_inicial_id').hide();
                                            Ext.getCmp('lectura_inicial_id').reset();
                                            Ext.getCmp('lectura_inicial_id').allowBlank = true;
                                            Ext.getCmp('window_servicio_agua_id').setHeight(240);
                                        }
                                    }
                                }
                            }
                        ]
                    },
                    {
                        xtype:'textfield',
                        fieldLabel:'Código',
                        hidden:true,
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name:'codigo',
                        id:'codigo_id',
                        maskRe:/^[a-zA-Z0-9. ]/
                    },
                    {
                        xtype:'numberfield',
                        fieldLabel:'Lectura Inicial',
                        hideTrigger:true,
                        decimalSeparator:'.',
                        decimalPrecision:'2',
                        margin:'7 0 0 0',
                        hidden:true,
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name:'lectura_inicial',
                        id:'lectura_inicial_id'
                    },
                    {
                        xtype:'hidden',
                        name:'id',
                        id:'id'
                    }
                ]
            },
        ];

        this.callParent();
    }
});

    var _btnAdd = Ext.create('Ext.button.MyButton',{
        id: 'servicio_agua_btn_add',
        text: 'Adicionar',
        // disabled: true,
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function(This, e){
            Ext.create('Portadores.servicio_agua.Window',{
                title: 'Adicionar servicio de agua',
                id: 'window_servicio_agua_id',
                listeners:{
                    afterrender:function(){
                        var selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                        var _record_ini = Ext.getCmp('unidad_id').store;
                        var _record_i = _record_ini.findRecord('id', selected.id);
                        Ext.getCmp('unidad_id').select(_record_i);
                        Ext.getCmp('unidad_id').setReadOnly(true);
                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function(){
                            var window = Ext.getCmp('window_servicio_agua_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/servicio_agua/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            var selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                                            var _record_ini = Ext.getCmp('unidad_id').store;
                                            var _record_i = _record_ini.findRecord('id', selected.id);
                                            Ext.getCmp('unidad_id').select(_record_i);
                                            Ext.getCmp('unidad_id').setReadOnly(true);
                                            var selection = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                                            Ext.getCmp('id_grid_servicio_agua').getStore().load({id: selection.id});
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
                            Ext.getCmp('window_servicio_agua_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton',{
        id: 'servicio_agua_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function(This, e){
            var selection = Ext.getCmp('id_grid_servicio_agua').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.servicio_agua.Window',{
                title: 'Modificar servicio de agua',
                id: 'window_servicio_agua_id',
                listeners:{
                    afterrender:function(){
                        var selected = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                        Ext.getCmp('unidad_id').setReadOnly(true);
                        if(selected.metrado)
                            Ext.getCmp('window_servicio_agua_id').setHeight(295);
                        else
                            Ext.getCmp('window_servicio_agua_id').setHeight(240);
                    }
                },
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

                                App.request('POST', App.buildURL('/portadores/servicio_agua/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            var selection = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                                            Ext.getCmp('id_grid_servicio_agua').getStore().load({id: selection.id});
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
                            Ext.getCmp('window_servicio_agua_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton',{
        id: 'servicio_agua_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function(This, e){
            selection = Ext.getCmp('id_grid_servicio_agua').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar servicio?',
                message: Ext.String.format('¿Está seguro que desea eliminar el servicio de agua <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/servicio_agua/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                var selection = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data;
                                Ext.getCmp('id_grid_servicio_agua').getStore().load({id: selection.id});
                            }
                        });
                    }
                }
            });

        }
    });

    var _tbar = Ext.getCmp('servicio_agua_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
