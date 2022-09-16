/**
 * Created by yosley on 17/05/2016.
 */

Ext.onReady(function () {
    let mes_anno = Ext.create('Ext.form.field.Month', {
        format: 'm, Y',
        id: 'mes_anno',
        // fieldLabel: 'Date',
        width: 90,
        value: new Date(App.selected_month + '/1/' + App.selected_year),
        renderTo: Ext.getBody(),
        listeners: {
            boxready: function () {
                let me = this;
                me.selectMonth = new Date(App.selected_month + '/1/' + App.selected_year);

                let assignGridPromise = new Promise((resolve, reject) => {
                    let i = 0;
                    while(!Ext.getCmp('id_grid_registro_acta_resp') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_registro_acta_resp'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let store_tarjetas = Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_registro_acta_resp_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nro_tarjeta'},
        ],
        groupField: 'nombreunidadid',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });


    let cmbSearchTarjeta = Ext.create('Ext.form.ComboBox', {
        labelWidth: 140,
        store: store_tarjetas,
        displayField: 'nro_tarjeta',
        valueField: 'id',
        queryMode: 'local',
        forceSelection: true,
        emptyText: 'No. Tarjeta...',
        selectOnFocus: true,
        editable: true,
        afterLabelTextTpl: [
            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
        ],
        listeners: {
            change: function (This, newValue) {
                Ext.getCmp('id_grid_registro_acta_resp').getStore().currentPage = 1;
                Ext.getCmp('id_grid_registro_acta_resp').getStore().load();
            }
        }
    });

    let btnSearch = Ext.create('Ext.button.MyButton', {
        width: 30,
        height: 28,
        tooltip: 'Buscar',
        iconCls: 'fas fa-search text-primary',
        handler: function () {

            Ext.getCmp('id_grid_registro_acta_resp').getStore().currentPage = 1;
            Ext.getCmp('id_grid_registro_acta_resp').getStore().load();
        }
    });

    let btnClearSearch = Ext.create('Ext.button.MyButton', {
        width: 30,
        height: 28,
        tooltip: 'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function () {
            cmbSearchTarjeta.reset();
            // cmbSearchRecibe.reset();
            Ext.getCmp('id_grid_registro_acta_resp').getStore().load();
        }
    });

    let grid_registro_acta_resp = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_registro_acta_resp',
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'id_store_registro_acta_resp',
            fields: [
                {name: 'id'},
                {name: 'tarjetaid'},
                {name: 'tarjeta'},
                {name: 'nunidadid'},
                {name: 'nombreunidadid'},
                {name: 'entregaid'},
                {name: 'entrega'},
                {name: 'recibeid'},
                {name: 'recibe'},
                {name: 'nunidad'},
                {name: 'nunidad_name'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/registroActaResp/loadRegistroActaResp'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: false,
            groupField: 'nombreunidadid',
            listeners: {
                beforeload: function (This, operation, eOpts) {
                   Ext.getCmp('id_grid_registro_acta_resp').getSelectionModel().deselectAll();
                        operation.setParams({
                            tarjeta: cmbSearchTarjeta.getValue(),
                            mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                            anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                            unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                        });

                }
            }
        }),
        flex: 1,
        disabled: true,
        features: [{
            ftype: 'grouping',
            groupHeaderTpl: '<b>Unidad: {name} ' + ' ({rows.length})</b>',
            hideGroupedHeader: false,
            enableGroupingMenu: true,
        },],
        columns: [
            {
                text: '<strong>Fecha</strong>',
                dataIndex: 'fecha',
                filter: 'string',
                flex: 0.5
            },
            {
                text: '<strong>Entrega</strong>',
                dataIndex: 'entrega',
                filter: 'string',
                flex: 0.5
            },
            {
                text: '<strong>Recibe</strong>',
                dataIndex: 'recibe',
                filter: 'string',
                flex: 0.5
            },
            {
                text: '<strong>Tarjetas</strong>',
                dataIndex: 'tarjeta',
                filter: 'string',
                flex: 0.5
            }
        ],
        tbar: {
            id: 'registro_acta_resp_tbar',
            height: 36,
            items: [mes_anno,cmbSearchTarjeta, btnSearch, btnClearSearch, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_registro_acta_resp'),
            displayInfo: true,
            // plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('registro_acta_resp_btn_mod') != undefined)
                    Ext.getCmp('registro_acta_resp_btn_mod').setDisabled(selected.length == 0);
                if (Ext.getCmp('registro_acta_resp_btn_del') != undefined)
                    Ext.getCmp('registro_acta_resp_btn_del').setDisabled(selected.length == 0);
                if (Ext.getCmp('registro_acta_resp_btn_print') != undefined)
                    Ext.getCmp('registro_acta_resp_btn_print').setDisabled(selected.length == 0);
                if (Ext.getCmp('registro_acta_resp_btn_export') != undefined)
                    Ext.getCmp('registro_acta_resp_btn_export').setDisabled(selected.length == 0);
            }
        }
    });

    let panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: Ext.create('Ext.data.TreeStore', {
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
        }),
        id: 'arbolunidades',
        hideHeaders: true,
        width: 280,
        // height: App.GetDesktopHeigth() - 75,
        rootVisible: false,
        frame: true,
        collapsible: true,
        collapsed: false,
        region: 'west',
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
                if (record) {
                    grid_registro_acta_resp.enable();
                    Ext.getCmp('id_grid_registro_acta_resp').getStore().load();
                    Ext.getStore('id_store_registro_acta_resp_tarjeta').load({params:{unidadid : record.id}});
                    Ext.getStore('id_store_registro_acta_resp_persona').load({params:{unidadid : record.id}});
                    if(Ext.getCmp('registro_acta_resp_btn_add'))
                    Ext.getCmp('registro_acta_resp_btn_add').setDisabled(false);
                }
            },

        }


    });

    let panel_registro_acta_resp = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_registro_acta_resp',
        title: 'Actas de Responsabilidad Material',
        frame: true,
        closable:true,
        layout: {
            type: 'hbox',       // Arrange child items vertically
            align: 'stretch',    // Each takes up full width
            padding: 1
        },
        items: [panetree, grid_registro_acta_resp]
    });

    App.render(panel_registro_acta_resp);
});
