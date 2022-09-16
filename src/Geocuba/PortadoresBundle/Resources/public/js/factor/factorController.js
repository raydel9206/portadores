/**
 * Created by kireny on 4/11/15.
 */

Ext.onReady(function(){
    var store_temp = Ext.create('Ext.data.JsonStore',{
        frame: true,
        storeId:'id_store_factor_temp',
        fields:[
            {name:'id'},
            {name:'portador'},
            {name:'unidad_medida_id1'},
            {name: 'unidad_medida_nombre1'},
            {name: 'factor_id1'},
            {name:'unidad_medida_id2'},
            {name: 'unidad_medida_nombre2'},
            {name:'factor_id2'}
        ],
        viewConfig: {forceFit: true},
        proxy:{
            type:'ajax',
//            url:Routing.generate('loadFactor'),
            reader:{
                rootProperty:'rows'
            }
        },
        autoLoad:false
    });
    var store_factor = Ext.create('Ext.data.JsonStore',{
        frame: true,
        storeId:'id_store_factor',
        fields:[
            {name:'id'},
            {name:'portador'},
            {name:'unidad_medida_id1'},
            {name: 'unidad_medida_nombre1'},
            {name: 'factor_id1'},
            {name:'unidad_medida_id2'},
            {name: 'unidad_medida_nombre2'},
            {name:'factor_id2'}
        ],
        viewConfig: {forceFit: true},
        proxy:{
            type:'ajax',
            url: App.buildURL('/portadores/factor/load'),
            reader:{
                rootProperty:'rows'
            }
        },
        autoLoad:true
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_factor').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value
                                });
                            }
                        }
                    },
                    load: function () {
                        field.enable();
                    }
                });
            },
            change: function (field, newValue, oldValue, eOpt) {
                field.getTrigger('clear').setVisible(newValue);
                if (Ext.isEmpty(Ext.String.trim(field.getValue()))) {
                    var marked = field.marked;
                    field.setMarked(false);

                    if (marked) {
                        Ext.getCmp('id_grid_factor').getStore().loadPage(1);
                    }

                    field.getTrigger('search').hide();
                } else {
                    field.getTrigger('search').show();

                    if (field.marked) {
                        field.setMarked(true);
                    }
                }
            },
            specialkey: function (field, e) {
                var value = field.getValue();

                if (!Ext.isEmpty(Ext.String.trim(value)) && e.getKey() === e.ENTER) {
                    field.setMarked(true);
                    Ext.getCmp('id_grid_factor').getStore().loadPage(1);
                } else if (e.getKey() === e.BACKSPACE && e.getKey() === e.DELETE && (e.ctrlKey && e.getKey() === e.V)) {
                    field.setMarked(false);
                }
            }
        },
        triggers: {
            search: {
                cls: Ext.baseCSSPrefix + 'form-search-trigger',
                hidden: true,
                handler: function(){
                    store_temp.addFilter(
                        [
                            {
                                "operator": "like",
                                "value": this.getValue(),
                                "property": "portador"
                            }
                        ]
                    );
                    grid_factor.setStore(store_temp);
                }
            },
            clear: {
                cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                hidden: true,
                handler: function () {
                    this.setValue(null);
                    this.updateLayout();

                        grid_factor.setStore(store_factor);
                    this.setMarked(false);
                }
            }
        },

        setMarked: function (marked) {
            var el = this.getEl(),
                id = '#' + this.getId();

            this.marked = marked;

            if (marked) {
                el.down(id + '-inputEl').addCls('x-form-invalid-field x-form-invalid-field-default');
                el.down(id + '-inputWrap').addCls('form-text-wrap-invalid');
                el.down(id + '-triggerWrap').addCls('x-form-trigger-wrap-invalid');
            } else {
                el.down(id + '-inputEl').removeCls('x-form-invalid-field x-form-invalid-field-default');
                el.down(id + '-inputWrap').removeCls('form-text-wrap-invalid');
                el.down(id + '-triggerWrap').removeCls('x-form-trigger-wrap-invalid');
            }
        }
    });

    var  grid_factor= Ext.create('Ext.grid.Panel',{
        id:'id_grid_factor',
        store: store_factor,
        columns:[
            {
                text: '<strong>PORTADOR</strong>',
                dataIndex: 'portador',
                width: 230, align: 'left',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Tonelada</strong>',
                columns: [{
                    text: '<strong>Factor</strong>',
                    dataIndex:'factor_id1',
                    width: 230, align: 'center',
                    flex: 1
                },
                    {
                        text: '<strong>UM</strong>',
                        dataIndex:'unidad_medida_nombre1',
                        width: 230, align: 'center',
                        flex: 1
                    }
                ]
            },
            {
                text: '<strong>m³</strong>',
                columns: [{
                    text: '<strong>Factor</strong>',
                    dataIndex:'factor_id2',
                    width: 230, align: 'center',
                    flex: 1
                },
                    {
                        text: '<strong>UM</strong>',
                        dataIndex:'unidad_medida_nombre2',
                        width: 230, align: 'center',
                        flex: 1
                    }
                ]
            }
        ],
        tbar:{
            id:'factor_tbar',
            height:36,
            items:[find_button,'-']
        },
        bbar:{
            xtype:'pagingtoolbar',
            pageSize:25,
            store:Ext.getStore('id_store_factor'),
            displayInfo: true,
        },
        plugins:'gridfilters',
        listeners:{
            selectionchange:function(This, selected, e){
                Ext.getCmp('factor_tbar').items.each(
                    function(item, index, length){
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            },
            // afterrender:function(){
            //     App.request('GET',App.buildURL('/portadores/factor/load'),{id:true},null, null, function (response){
            //         if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
            //             store_temp.loadData(response.rows);
            //         }
            //     });
            // }
        }
    });

    var _panel_factor=Ext.create('Ext.panel.Panel',{
        id:'id_panel_factor',
        title: 'Factores de Conversión',
        frame : true,
        closable:true,
        layout: 'fit',
        items:[grid_factor]
    });
    App.render(_panel_factor);
});