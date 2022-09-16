/**
 * Created by javier on 17/05/2016.
 */

Ext.onReady(function(){

    var textSearch=Ext.create('Ext.form.field.Text',{
        width:200,
        emptyText:'Tarjeta a buscar...',
        id:'buscar_consultar_tarjeta'
    });
    var btnSearch = Ext.create('Ext.button.MyButton',{
        width:30,
        height:28,
        tooltip:'Buscar',
        iconCls:'fas fa-search text-primary',
        handler: function(){
            grid_consultar_tarjeta.getStore().addFilter(
                [
                    {
                        "operator": "like",
                        "value": textSearch.getValue(),
                        "property": "nro_tarjeta"
                    }
                ]
            );
        }
    });
    var btnClearSearch = Ext.create('Ext.button.MyButton',{
        width : 30,
        height : 28,
        tooltip:'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function(){
            grid_consultar_tarjeta.getStore().clearFilter();
            textSearch.reset();
        }
    });


    var grid_consultar_tarjeta = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_consultar_tarjeta',
        store: Ext.create('Ext.data.JsonStore',{
            storeId: 'id_store_consultar_tarjeta',
            fields: [
                { name: 'id'},
                { name: 'tipo_combustibleid'},
                { name: 'tipo_combustible'},
                { name: 'nunidadid'},
                { name: 'nunidad'},
                { name: 'importe'},
                { name: 'nro_tarjeta'}
            ],
            proxy: {
                type: 'ajax',
                url: Routing.generate('loadConsultarTarjeta'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            groupField: 'nunidad',
            autoLoad: true
        }),
        columns: [
            { text: '<strong>No. tarjeta</strong>',
                dataIndex: 'nro_tarjeta',
                filter: 'string',
                flex: 1
            },
            { text: '<strong>Importe</strong>',
                dataIndex: 'importe',
                filter: 'string',
                flex: 1
            },
            { text: '<strong>Tipo de combustible</strong>',
                dataIndex: 'tipo_combustible',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'consultar_tarjeta_tbar',
            height: 36,
            items: [ textSearch, btnSearch, btnClearSearch, '-' ]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_consultar_tarjeta'),
            displayInfo: true,
            plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        features: [
            {
                ftype:'grouping',
                startCollapsed:true,
                groupHeaderTpl: [
                    '<div>{name:this.formatName}</div>',
                    {
                        formatName: function(name) {
                            return Ext.String.trim(name);
                        }
                    }
                ]
            }
        ],
        listeners: {
            selectionchange: function(This, selected, e){
                Ext.getCmp('consultar_tarjeta_tbar').items.each(
                    function(item, index, length){
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            }
        }
    });



    var panel_consultar_tarjeta = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_consultar_tarjeta',
        title: 'Consultar Tarjetas',
        frame : true,
        closable:true,
        layout: 'fit',
        items:[grid_consultar_tarjeta]
    });


    App.RenderMainPanel(panel_consultar_tarjeta);



});