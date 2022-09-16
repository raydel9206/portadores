/**
 * Created by kireny on 06/05/16.
 */

Ext.onReady(function(){
    var store_temp = Ext.create('Ext.data.JsonStore',{
        storeId: 'storeBanco_TransformadoresId_temp',
        fields: [
            { name: 'id'},
            { name: 'capacidad'},
            {name:'tipo'}
        ],
        proxy: {
            type: 'ajax',
//            url: Routing.generate('loadBancoTransformadores'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false
    });

    var store_banco = Ext.create('Ext.data.JsonStore',{
        storeId: 'storeBanco_TransformadoresId',
        fields: [
            { name: 'id'},
            { name: 'capacidad'},
            { name: 'tipo'},
            { name: 'pfe'},
            { name: 'pcu'}
        ],
        groupField : 'tipo',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/banco_transformadores/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });
    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('gridbanco_transformadoresId').getStore().on({
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
                        Ext.getCmp('gridbanco_transformadoresId').getStore().loadPage(1);
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
                    Ext.getCmp('gridbanco_transformadoresId').getStore().loadPage(1);
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
                    grid.setStore(store_temp);
                }
            },
            clear: {
                cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                hidden: true,
                handler: function () {
                    this.setValue(null);
                    this.updateLayout();

                    grid.setStore(store_factor);
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
    var groupingFeature = Ext.create('Ext.grid.feature.Grouping',{
        groupHeaderTpl: '{name} ' + ' ({rows.length})',
        hideGroupedHeader: true,
        startCollapsed: true,
        ftype: 'grouping'
    });
    var grid = Ext.create('Ext.grid.Panel',{
        id: 'gridbanco_transformadoresId',
        store: store_banco,
        features: [groupingFeature],
        columns: [
            {
                text: '<strong>Capacidad</strong>', dataIndex: 'capacidad', filter: 'string', flex: 1
                //renderer:function(val){
                //    var a =  val.explode('.');
                //    if(a.length > 1)
                //        return a[0]+','+a[1];
                //    else
                //        return a[0]
                //}},
            },
            //{
            //    text:'<strong>Tipo</strong>', dataIndex: 'tipo', filter: 'string', flex: 1
            //},
            {
                text:'<strong>PFE</strong>', dataIndex: 'pfe', filter: 'string', flex: 1
            },
            {
                text:'<strong>PCU</strong>', dataIndex: 'pcu', filter: 'string', flex: 1
            }

        ],
        tbar: {
            id: 'bancotransformadores_tbar',
            height: 36,
            items: [ find_button, '-' ]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeBanco_TransformadoresId'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function(This, selected, e){
                Ext.getCmp('bancotransformadores_tbar').items.each(
                    function(item, index, length){
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            },
            afterrender:function(){
                App.request('GET',App.buildURL('/portadores/banco_transformadores/load'),{id:true},null, null, function (response){
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        store_temp.loadData(response.rows);
                    }
                });
                // var _result = App.PerformSyncServerRequest(Routing.generate('loadBancoTransformadores'),{id:true});
                // store_temp.loadData(_result.rows)
            }
        }
    });
    var _panel = Ext.create('Ext.panel.Panel',{
        id: 'manage_bancotransformadores_panel_id',
        title: 'Capacidad Banco de Transformadores',
        frame : true,
        closable:true,
        layout: 'fit',
        items:[grid]
    });
    App.render(_panel);
});