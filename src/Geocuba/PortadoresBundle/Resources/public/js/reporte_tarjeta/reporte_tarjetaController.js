/**
 * Created by javier on 17/05/2016.
 */

Ext.onReady(function(){

    var textSearch=Ext.create('Ext.form.field.Text',{
        width:200,
        emptyText:'Tarjeta a buscar...',
        id:'buscar_reporte_tarjeta'
    });
    var btnSearch = Ext.create('Ext.button.MyButton',{
        width:30,
        height:28,
        tooltip:'Buscar',
        iconCls:'fas fa-search text-primary',
        handler: function(){
            grid_reporte_tarjeta.getStore().addFilter(
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
            grid_reporte_tarjeta.getStore().clearFilter();
            textSearch.reset();
        }
    });


    var grid_reporte_tarjeta = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_reporte_tarjeta',
        store: Ext.create('Ext.data.JsonStore',{
            storeId: 'id_store_reporte_tarjeta',
            fields: [
                { name: 'id'},
                { name: 'responsable'},
                { name: 'nunidadid'},
                { name: 'nunidad'},
                { name: 'vehiculoid'},
                { name: 'nro_tarjeta'},
                { name: 'matricula'}
            ],
            proxy: {
                type: 'ajax',
                url: Routing.generate('loadReporteTarjeta'),
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
            { text: '<strong>Matrícula</strong>',
                dataIndex: 'matricula',
                filter: 'string',
                flex: 1
            },
            { text: '<strong>Responsable</strong>',
                dataIndex: 'responsable',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'reporte_tarjeta_tbar',
            height: 36,
            items: [ textSearch, btnSearch, btnClearSearch, '-' ]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_reporte_tarjeta'),
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
                Ext.getCmp('reporte_tarjeta_tbar').items.each(
                    function(item, index, length){
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            }
        }
    });



    var panel_reporte_tarjeta = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_reporte_tarjeta',
        title: 'Reportes de Tarjetas',
        width : App.GetDesktopWidth(),
        height : App.GetDesktopHeigth() - 75,
        border : true,
        frame : true,
        layout: 'fit',
        items:[grid_reporte_tarjeta]
    });


    App.RenderMainPanel(panel_reporte_tarjeta);



});