/**
 * Created by yosley on 02/11/2015.
 */

Ext.onReady(function () {

    var grid_provincia_mun = Ext.create('Ext.grid.Panel', {
        id:'id_grid_provincia_mun',
        width:'98%',
        border:true,
        title:'Provincias',
        region:'center',
        store:Ext.create('Ext.data.JsonStore', {
            storeId:'id_store_provincia_munic',
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
            autoLoad:true,
            listeners:{
                beforeload:function (This, operation, eOpts) {
                    Ext.getCmp('id_grid_provincia_mun').getSelectionModel().deselectAll();
                }
            }
        }),
        columns:[
            { text:'<strong>Nombre</strong>',
                dataIndex:'nombre',
                filter:'string',
                flex:1
            },
            { text:'<strong>Código</strong>',
                dataIndex:'codigo',
                filter:'string',
                flex:1
            }
        ],
        tbar:{
            id:'provincia_tbar',
            height:36
        },
        plugins:'gridfilters',
        listeners:{
            selectionchange:function (This, selected) {

                grid_municipio.store.removeAll();
                if (selected.length > 0) {
                    grid_municipio.store.load({
                        params:{
                            id:selected[0].data.id
                        }
                    });
                    grid_municipio.expand();
                    if(Ext.getCmp('municipio_btn_add'))
                    Ext.getCmp('municipio_btn_add').setDisabled(false);
                }
                else {
                    if(Ext.getCmp('municipio_btn_add'))
                    Ext.getCmp('municipio_btn_add').disable();
                    grid_municipio.collapse();
                }

                if(Ext.getCmp('provincia_btn_mod'))
                Ext.getCmp('provincia_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('provincia_btn_del'))
                Ext.getCmp('provincia_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var grid_municipio = Ext.create('Ext.grid.Panel', {
        id:'id_grid_municipio',
        width:'60%',
        border:true,
        title:'Municipios',
        region:'east',
        collapsible:true,
        collapsed:true,
        store:Ext.create('Ext.data.JsonStore', {
            storeId:'id_store_municipio',
            fields:[
                { name:'id'},
                { name:'nombre'},
                { name:'codigo'},
                { name:'provinciaid'}
            ],
            proxy:{
                type:'ajax',
                url: App.buildURL('/portadores/municipio/listMunicipio'),
                reader:{
                    rootProperty:'rows'
                }
            },
            sorters:[
                {
                    property:'nombre',
                    direction:'ASC',
                    transform:function (siglas) {
                        return siglas.toLowerCase();
                    }
                }
            ],
            autoLoad:false,
            listeners:{
                beforeload:function (This, operation, eOpts) {
                    Ext.getCmp('id_grid_municipio').getSelectionModel().deselectAll();
                }
            }
        }),
        columns:[
            {
                text:'<strong>Nombre</strong>',
                dataIndex:'nombre',
                filter:'string',
                flex:1
            },
            {
                text:'<strong>Código</strong>',
                dataIndex:'codigo',
                filter:'string',
                flex:1
            }
        ],
        tbar:{
            id:'municipio_tbar',
            height:36
        },
        plugins:'gridfilters',
        listeners:{
            selectionchange:function (This, selected, e) {
                Ext.getCmp('municipio_tbar').items.each(
                    function(item, index, length){
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            }
        }
    });

    var panel_municipio = Ext.create('Ext.panel.Panel', {
        id:'id_panel_municipio',
//        title:'<div style="text-align: center">Gestionar Municipios</div>',
        title:'Gestionar División Política Administrativa',
        frame:true,
        closable:true,
        layout:'border',
        items:[grid_provincia_mun, grid_municipio]
    });

    App.render(panel_municipio);
});