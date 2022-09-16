/**
 * Created by kireny on 02/11/2015.
 */
Ext.onReady(function () {
//

    var store_tarifa = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarifa',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'costopico'},
            {name: 'costomadrugada'},
            {name: 'costodia'},
            {name: 'costofijo'},
            {name: 'PrecioxKW'},
            {name: 'grupo'},
            {name: 'costocualquierhorario'}

        ],
        groupField: 'grupo',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarifa/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });

    // var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
    //     groupHeaderTpl: '<b>Grupo: {name} ' + ' ({rows.length}) </b>',
    //     hideGroupedHeader: true,
    //     startCollapsed: false,
    //     ftype: 'grouping'
    // });

    var grid_tarifa = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_tarifa',
        store: store_tarifa,
        columns: [
            {
                text: '<strong>Nombre</strong>',
                dataIndex: 'nombre',
                filter: 'string',
                flex: 1
            }
        ],
        // features: [groupingFeature],
        plugins: ['gridfilters',{
            ptype: 'rowexpander',
            rowBodyTpl: new Ext.XTemplate(
                '<table width: 100%,border: 1px solid black,vertical-align: bottom>',
                '<tpl if="costopico">',
                '<tr>',
                '<td>', '<p><b>Costo Variable Pico:</b> {costopico} $kw/h</p>', '</td>',
                '</tr>',
                '</tpl>',
                '<tpl if="costodia">',
                '<tr>',
                '<td>', '<p><b>Costo Variable Dia:</b> {costodia} $kw/h</p>', '</td>',
                '</tr>',
                '</tpl>',
                '<tpl if="costomadrugada">',
                '<tr>',
                '<td>', '<p><b>Costo Variable Madrugada</b> {costomadrugada} $kw/h</p>', '</td>',
                '</tr>',
                '</tpl>',
                '<tpl if="costofijo">',
                '<tr>',
                '<td>', '<p><b>Costo Fijo:</b> {costofijo} $kw/h</p>', '</td>',
                '</tr>',
                '</tpl>',
                '</table>'
            )
        }],
        // tbar: {
        //     id: 'tarifa_tbar',
        //     height: 36
        //     //items: [ textSearch, btnSearch, btnClearSearch, '-']
        // },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_tarifa'),
            displayInfo: true,
        },
        enableLocking: true,
        width: 600,
        height: 300,
        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('tarifa_tbar').items.each(
                    function (item, index, length) {
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            }
        }
    });
    var panel_tarifa = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_tarifa',
        title: 'Tarifas',
        frame: true,
        closable:true,
        layout: 'fit',
        items: [grid_tarifa]
    });
    App.render(panel_tarifa);


});