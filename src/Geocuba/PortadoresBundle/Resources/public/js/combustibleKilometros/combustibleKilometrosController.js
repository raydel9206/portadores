/**
 * Created by yosley on 13/10/2015.
 */


Ext.onReady(function(){
    var textSearch = Ext.create('Ext.form.field.Text',{
        width: 200,
        id:'buscar_combus'

    });
    var btnSearch = Ext.create('Ext.button.MyButton',{
        width : 30,
        height : 28,
        tooltip:'Buscar',
        iconCls: 'fas fa-search text-primary',
        handler: function(){
            Ext.getCmp('id_grid_combustibleK').getStore().filter('nro_tarjeta',textSearch.getValue())

        }
    });
    var btnClearSearch = Ext.create('Ext.button.MyButton',{
        width : 30,
        height : 28,
        tooltip:'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function(){
            grid_combustibleK.getStore().clearFilter();
            textSearch.reset();
        }
    });
    var grid_combustibleK = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_combustibleK',
        store: Ext.create('Ext.data.JsonStore',{
            storeId: 'id_store_combustibleK',
            fields: [
                { name: 'id'},
                { name: 'anexo_unicoid'},
                { name: 'fecha'},
                { name: 'ntarjetaid'},
                { name: 'kilometraje'},
                { name: 'combustible_abastecido'},
                { name: 'combustible_estimado_tanque'}

            ],
            proxy: {
                type: 'ajax',
                url: Routing.generate('loadCombustibleKilometros'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: true
        }),
        columns: [
            { text: '<strong>Fecha</strong>',
                dataIndex: 'fecha',
                filter: 'string',
                flex: 1
            },
            { text: '<strong>Combustible Abastecido</strong>',
                dataIndex: 'combustible_abastecido',
                filter: 'string',
                flex: 1
            },
            { text: '<strong>Combustible Estimado Tanque</strong>',
                dataIndex: 'combustible_estimado_tanque',
                filter: 'string',
                flex: 1
            },
            { text: '<strong>Fecha Registro</strong>',
                dataIndex: 'fecha_registro',
                filter: 'string',
                flex: 1
            },
            { text: '<strong>Kilometraje</strong>',
                dataIndex: 'kilometraje',
                filter: 'string',
                flex: 1
            },
            { text: '<strong>Nro Targeta</strong>',
                dataIndex: 'ntarjetaid',
                filter: 'string',
                flex: 1
            }



        ],
        tbar: {
            id: 'combustibleK_tbar',
            height: 36,
            items: [ textSearch, btnSearch, btnClearSearch, '-' ]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_combustibleK'),
            displayInfo: true,
            plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function(This, selected, e){
                Ext.getCmp('combustibleK_tbar').items.each(
                    function(item, index, length){
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            }
        }
    });
    var _panel_combustibleK = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_combustibleK',
        title: 'Gestión de Combustible por Kilómetros',
        frame : true,
        closable:true,
        layout: 'fit',
        items:[grid_combustibleK]
    });
    App.RenderMainPanel(_panel_combustibleK);
});