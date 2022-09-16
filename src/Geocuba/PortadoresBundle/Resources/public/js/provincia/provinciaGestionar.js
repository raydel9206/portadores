/**
 * Created by yosley on 02/11/2015.
 */
Ext.onReady(function () {
var store_provincias = Ext.create('Ext.data.JsonStore', {
    storeId:'id_store_provincia_munic_combo',
    fields:[
        { name:'id'},
        { name:'nombre'},
        { name:'codigo'}
    ],
    proxy:{
        type:'ajax',
        url: App.buildURL('/portadores/provincia/list'),
        reader:{
            rootProperty:'rows'
        }
    },
    autoLoad:true
});


Ext.define('Portadores.provincia.Window', {
    extend:'Ext.window.Window',
    modal:true,
    width:340,
    height:155,

    initComponent:function () {
        this.items = [
            {
                xtype:'form',
                frame:true,
                width:'200%',
                height:'200%',
                defaultType:'textfield',
                bodyPadding:5,
                fieldDefaults:{
                    msgTarget:'side',
                    allowBlank:false
                },
                items:[
                    {
                        fieldLabel:'Nombre',
                        afterLabelTextTpl:[
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth:60,
                        width:300,
                        name:'nombre',
                        maxLength:255
                    },
                    {
                        xtype:'numberfield',
                        hideTrigger:true,
                        fieldLabel:'Código',
                        afterLabelTextTpl:[
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth:60,
                        width:300,
                        enforceMaxLength: true,
                        maxLength: 6,
                        name:'codigo',
                        minValue:0
                    }
                ]
            }
        ];

        this.callParent();
    }
});

Ext.define('Portadores.municipio.Window', {
    extend:'Ext.window.Window',
    modal:true,
    width:340,
    height:180,

    initComponent:function () {
        this.items = [
            {
                xtype:'form',
                id:'form_munic_id',
                frame:true,
                width:'200%',
                height:'200%',
                defaultType:'textfield',
                bodyPadding:5,
                fieldDefaults:{
                    msgTarget:'side',
                    allowBlank:false
                },
                items:[
                    {
                        fieldLabel:'Nombre',
                        afterLabelTextTpl:[
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth:60,
                        width:300,
                        name:'nombre'
                    },
                    {
                        xtype:'numberfield',
                        hideTrigger:true,
                        fieldLabel:'Código',
                        afterLabelTextTpl:[
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth:60,
                        width:300,
                        enforceMaxLength: true,
                        maxLength: 6,
                        name:'codigo'
                    },
                    {
                        xtype:'combobox',
                        name:'provinciaid',
                        id:'provinciaid',
                        fieldLabel:'Provincia',
                        afterLabelTextTpl:[
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth:60,
                        width:300,
                        // hidden:false,
                        store:store_provincias,
                        displayField:'nombre',
                        valueField:'id',
                        queryMode:'local',
                        forceSelection:true,
                        triggerAction:'all',
                        emptyText:'Seleccione la provincia...',
                        editable:false,
                        allowBlank:true,
                        listeners:{
                            beforerender:function (This) {
                                selection=Ext.getCmp('id_grid_provincia_mun').getSelectionModel().getLastSelected();
                                This.setValue(selection.data.id);
                            }
                        }
                    },
                ]
            }
        ];

        this.callParent();
    }
});




    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id:'provincia_btn_add',
        text:'Adicionar',
        // iconCls:'fa fa-plus-square-o fa-1_4',
        iconCls: 'fas fa-plus-square text-primary',
        width:100,
        handler:function (This, e) {
            Ext.create('Portadores.provincia.Window', {
                title:'Adicionar una Provincia',
                id:'window_provincia_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var window = Ext.getCmp('window_provincia_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/provincia/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('id_store_provincia_munic').loadPage(1);
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                Ext.getCmp('window_provincia_id').down('form').getForm().markInvalid(response.errors);
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
                        text:'Cancelar',
                        width:70,
                        handler:function () {
                            window.close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id:'provincia_btn_mod',
        text:'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            var selection = Ext.getCmp('id_grid_provincia_mun').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.provincia.Window', {
                title:'Modificar provincia',
                id:'window_provincia_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_provincia_mun').getSelectionModel().getLastSelected().data.id;
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/provincia/edit'),obj , null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_provincia_mun').getStore().load();
                                            window.close();
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                Ext.getCmp('window_provincia_id').down('form').getForm().markInvalid(response.errors);
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
                            Ext.getCmp('window_provincia_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id:'provincia_btn_del',
        text:'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            selection = Ext.getCmp('id_grid_provincia_mun').getSelection();

            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar provincia?' : '¿Eliminar provincias?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar la provincia <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar las provincias seleccionadas?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });

                        App.request('DELETE', App.buildURL('/portadores/provincia/delete'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_provincia_mun').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    var _tbar = Ext.getCmp('provincia_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);


    var _btnAdd_munic = Ext.create('Ext.button.MyButton', {
        id:'municipio_btn_add',
        text:'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width:100,
        disabled:true,
        handler:function (This, e) {
            Ext.create('Portadores.municipio.Window', {
                title:'Adicionar Municipio',
                id:'window_municipio_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var prov_id = Ext.getCmp('id_grid_provincia_mun').getSelectionModel().getLastSelected().data.id;
                            var window = Ext.getCmp('window_municipio_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.provinciaid = prov_id;
                                App.request('POST', App.buildURL('/portadores/municipio/addMunicipio'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('provinciaid').setValue(Ext.getCmp('id_grid_provincia_mun').getSelectionModel().getLastSelected());
                                            Ext.getCmp('id_grid_municipio').getStore().load({
                                                params:{
                                                    id:prov_id
                                                }
                                            });
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                Ext.getCmp('window_municipio_id').down('form').getForm().markInvalid(response.errors);
                                            }
                                            Ext.getCmp('window_municipio_id').show();
                                        }
                                        window.show();
                                    },
                                    function (response) { // failure_callback
                                        Ext.getCmp('window_municipio_id').show();
                                    }
                                );
                            }
                        }
                    },
                    {
                        text:'Cancelar',
                        width:70,
                        handler:function () {
                            Ext.getCmp('window_municipio_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod_munic = Ext.create('Ext.button.MyButton', {
        id:'municipio_btn_mod',
        text:'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            var selection = Ext.getCmp('id_grid_municipio').getSelectionModel().getLastSelected();
            var prov_id = Ext.getCmp('id_grid_provincia_mun').getSelectionModel().getLastSelected().data.id;

            var window = Ext.create('Portadores.municipio.Window', {
                title:'Modificar municipio',
                id:'window_municipio_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_municipio').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/municipio/editMunicipio'),obj , null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('id_grid_municipio').getStore().load({
                                                params:{
                                                    id:prov_id
                                                }
                                            });
                                            window.close();
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                Ext.getCmp('window_municipio_id').down('form').getForm().markInvalid(response.errors);
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
                            Ext.getCmp('window_municipio_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del_munic = Ext.create('Ext.button.MyButton', {
        id:'municipio_btn_del',
        text:'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            selection = Ext.getCmp('id_grid_municipio').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar municipio?' : '¿Eliminar municipios?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el municipio <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar los municipios seleccionados?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });

                        App.request('DELETE', App.buildURL('/portadores/municipio/deleteMunicipio'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_municipio').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    var _tbar_munic = Ext.getCmp('municipio_tbar');
    _tbar_munic.add(_btnAdd_munic);
    _tbar_munic.add('-');
    _tbar_munic.add(_btnMod_munic);
    _tbar_munic.add('-');
    _tbar_munic.add(_btn_Del_munic);
    _tbar_munic.setHeight(36);


});