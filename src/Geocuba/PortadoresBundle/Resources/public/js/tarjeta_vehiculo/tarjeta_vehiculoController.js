/**
 * Created by javier on 17/05/2016.
 */

Ext.onReady(function(){
    var store_temp =  Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_tarjeta_vehiculo_temp',
        fields: [
            { name: 'id'},
            { name: 'vehiculoid'},
            { name: 'vehiculo'},
            { name: 'tarjetaid'},
            { name: 'nro_tarjeta'}
        ],
        proxy: {
            type: 'ajax',
//            url: Routing.generate('loadVehiculoTarjeta'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false
    });

    var store_tarjeta_vehiculo =  Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_tarjeta_vehiculo',
        fields: [
            { name: 'id'},
            { name: 'vehiculoid'},
            { name: 'vehiculo'},
            { name: 'tarjetaid'},
            { name: 'nro_tarjeta'}
        ],
        proxy: {
            type: 'ajax',
            url: Routing.generate('loadVehiculoTarjeta'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });

    var textSearch=Ext.create('Ext.form.field.Text',{
        width:200,
        emptyText:'Vehículo a buscar...',
        id:'buscar_tarjeta_vehiculo'
    });
    var btnSearch = Ext.create('Ext.button.MyButton',{
        width:30,
        height:28,
        tooltip:'Buscar',
        iconCls:'fas fa-search text-primary',
        handler: function(){
            store_temp.addFilter(
                [
                    {
                        "operator": "like",
                        "value": textSearch.getValue(),
                        "property": "vehiculo"
                    }
                ]
            );
            grid_tarjeta_vehiculo.setStore(store_temp);
        }
    });
    var btnClearSearch = Ext.create('Ext.button.MyButton',{
        width : 30,
        height : 28,
        tooltip:'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function(){
            store_temp.clearFilter();
            grid_tarjeta_vehiculo.setStore(store_tarjeta_vehiculo);
            textSearch.reset();
        }
    });


    var grid_tarjeta_vehiculo = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_tarjeta_vehiculo',
        store:store_tarjeta_vehiculo,
        columns: [
            { text: '<strong>Matrícula</strong>',
                dataIndex: 'vehiculo',
                filter: 'string',
                flex: 1
            },
            { text: '<strong>No. tarjeta</strong>',
                dataIndex: 'nro_tarjeta',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'tarjeta_vehiculo_tbar',
            height: 36,
            items: [ textSearch, btnSearch, btnClearSearch, '-' ]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_tarjeta_vehiculo'),
            displayInfo: true,
            plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function(This, selected, e){
                Ext.getCmp('tarjeta_vehiculo_tbar').items.each(
                    function(item, index, length){
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            },
            afterrender:function(){
                var _result = App.PerformSyncServerRequest(Routing.generate('loadVehiculoTarjeta'),{id:true});
                store_temp.loadData(_result.rows)
            }
        }
    });



    var panel_tarjeta_vehiculo = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_tarjeta_vehiculo',
        title: 'Asignación de Tarjetas a Vehículos',
        width : App.GetDesktopWidth(),
        height : App.GetDesktopHeigth() - 75,
        border : true,
        frame : true,
        layout: 'fit',
        items:[grid_tarjeta_vehiculo]
    });


    App.RenderMainPanel(panel_tarjeta_vehiculo);



});