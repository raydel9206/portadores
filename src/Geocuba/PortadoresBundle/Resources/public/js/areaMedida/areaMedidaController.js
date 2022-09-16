/**
 * Created by kireny on 5/11/15.
 */

Ext.onReady(function () {

    var store = Ext.create('Ext.data.JsonStore', {
        frame:true,
        storeId:'id_store_areamedida',
        fields:[
            {name:'id'},
            {name:'nombre'},
            {name:'nlista_areaid'},
            {name:'nlista_areanombre'},
            {name:'invalidante'}
        ],
        groupField:'nlista_areanombre',
        sorters:'nombreunidadid',
        viewConfig:{forceFit:true},
        proxy:{
            type:'ajax',
            url: App.buildURL('/portadores/accion/load'),
            reader:{
                rootProperty:'rows'
            }
        },
        autoLoad:false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_areamedida').getSelectionModel().deselectAll();
                operation.setParams({
                    area: find_button.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });
    var tree_store = Ext.create('Ext.data.TreeStore', {
        id: 'store_unidades',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'nombre', type: 'string'},
            {name: 'siglas', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'municipio', type: 'string'},
            {name: 'municipio_nombre', type: 'string'},
            {name: 'provincia', type: 'string'},
            {name: 'provincia_nombre', type: 'string'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad/loadTree'),
            reader: {
                type: 'json',
                rootProperty: 'children'
            }
        },
        sorters: 'nombre',
        listeners: {
            beforeload: function () {
                if (Ext.getCmp('arbolunidades') !== undefined)
                    Ext.getCmp('arbolunidades').getSelectionModel().deselectAll();
            }
        }
    });

    var panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        border:true,
        id: 'arbolunidades',
        hideHeaders: true,
        rootVisible: false,
        collapsible: true,
        collapsed: false,

        collapseDirection: 'left',
        header: {             style: {                 backgroundColor: 'white',                 borderBottom: '1px solid #c1c1c1 !important'             },         },
        layout: 'fit',

        columns: [
            {xtype: 'treecolumn', iconCls: Ext.emptyString, width: 450, dataIndex: 'nombre'}
        ],
        root: {
            text: 'root',
            expanded: true
        },
        listeners: {
            select: function (This, record, tr, rowIndex, e, eOpts) {
                grid_areamedida.enable();
                Ext.getCmp('id_grid_areamedida').getStore().loadPage(1);
                if(Ext.getStore('id_store_area_areamedida'))
                    Ext.getStore('id_store_area_areamedida').load();

            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Área a buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_areamedida').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value,
                                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
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
                        Ext.getCmp('id_grid_areamedida').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_areamedida').getStore().loadPage(1);
                } else if (e.getKey() === e.BACKSPACE && e.getKey() === e.DELETE && (e.ctrlKey && e.getKey() === e.V)) {
                    field.setMarked(false);
                }
            }
        },
        triggers: {
            search: {
                cls: Ext.baseCSSPrefix + 'form-search-trigger',
                hidden: true,
                handler: function () {
                    var value = this.getValue();
                    if (!Ext.isEmpty(Ext.String.trim(value))) {
                        this.setMarked(true);
                        if (Ext.getCmp('id_grid_areamedida').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_areamedida').getStore().loadPage(1, {params: {area: value}});
                    }
                }
            },
            clear: {
                cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                hidden: true,
                handler: function () {
                    this.setValue(null);
                    this.updateLayout();

                    if (this.marked) {
                        Ext.getCmp('id_grid_areamedida').getStore().loadPage(1);
                    }
                    // Ext.getCmp('id_grid_tiporam').setTitle('tiporam');
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

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl:'<b>Área: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader:true,
        startCollapsed:false,
        ftype:'grouping'
    });
    var grid_areamedida = Ext.create('Ext.grid.Panel', {
        id:'id_grid_areamedida',
        region:'center',
        width:'75%',
        disabled: true,
        selModel: {
            mode: 'MULTI'
        },
        store:store,
        features:[groupingFeature],
        columns:[
            {
                text:'<strong>Nombre</strong>',
                dataIndex:'nombre',
                filter:'string',
                flex:3
            },
            {
                text:'<strong>Invalidante</strong>',
                dataIndex:'invalidante',
                filter:'string',
                flex:1,
                renderer:function (value) {
                    if (value) {
                        return '<span class="label label-danger"><strong>Invalidante</strong></span>';
                    }
                }
            }
        ],
        tbar:{
            id:'areamedida_tbar',
            height:36,
            items:[find_button, '-']
        },
        bbar:{
            xtype:'pagingtoolbar',
            pageSize:25,
            store:Ext.getStore('id_store_areamedida'),
            displayInfo:true,
        },
        plugins:['gridfilters', {
            ptype:'rowexpander',
            rowBodyTpl:new Ext.XTemplate(
                '<br>',
                '<p><b>Nombre:</b> {nombre}</p>'
            )
        }],
        listeners:{
            selectionchange:function (This, selected, e) {
                if(Ext.getCmp('areamedida_btn_mod'))
                Ext.getCmp('areamedida_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('areamedida_btn_del'))
                Ext.getCmp('areamedida_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var _panel_areamedida = Ext.create('Ext.panel.Panel', {
        id:'id_panel_areamedida',
        title:'Acciones',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '1 0 0',
        items:[panetree, grid_areamedida]
    });
    App.render(_panel_areamedida);
});