/**
 * Created by orlando on 12/12/2016.
 */

Ext.onReady(function () {
    var store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta',
        fields: [
            {name: 'nro_tarjeta'},
            {name: 'id'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        sorters: [{property: 'nro_tarjeta', direction: 'ASC'}],
        listeners: {
            beforeload: function (store, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
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

    var store_libro_control = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_libro_control',
        fields: [
            {name: 'noOrden'},
            {name: 'matricula'},
            {name: 'marca'},
            {name: 'modelo'},
            {name: 'unidad'},
            {name: 'fechaEmision'},
            {name: 'horaEmision'},
            {name: 'fechaCierre'},
            {name: 'horaCierre'},
            {name: 'operacion'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/libro_control/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {

                if (fechaDesde.getRawValue() == '')
                    fechaDesde.validate();
                if (fechaHasta.getRawValue() == '')
                    fechaHasta.validate();

                if (fechaDesde.getRawValue() != '' &&
                    fechaHasta.getRawValue() != '') {
                    Ext.getCmp('id_grid_libro_control').getSelectionModel().deselectAll();
                    operation.setParams({
                        fechaDesde: fechaDesde.getRawValue(),
                        fechaHasta: fechaHasta.getRawValue(),
                        unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                    });
                }
            }
        }
    });

    var fechaDesde = Ext.create('Ext.form.field.Date', {
        labelWidth: 140,
        emptyText: 'Fecha desde...',
        selectOnFocus: true,
        editable: true,
        allowBlank: false,
        format: 'd/m/Y',
        listeners: {
            change: function (This, newValue) {
                fechaHasta.setMinValue(newValue);
            }
        }
    });
    var fechaHasta = Ext.create('Ext.form.DateField', {
        labelWidth: 140,
        emptyText: 'Fecha hasta...',
        selectOnFocus: true,
        editable: true,
        allowBlank: false,
        format: 'd/m/Y',
        listeners: {
            change: function (This, newValue) {

                fechaDesde.setMaxValue(newValue);

                if (newValue != null) {
                    btnSearch.enable();
                    btnClearSearch.enable();
                } else {
                    _btn_Print.disable();
                    _btn_Export.disable();
                    btnSearch.disable();
                    btnClearSearch.disable();
                }
            }
        }
    });
    var btnSearch = Ext.create('Ext.button.MyButton', {
        width: 25,
        height: 25,
        disabled: true,
        iconCls: 'fas fa-search text-primary',
        handler: function () {

            if (fechaDesde.getRawValue() == '')
                fechaDesde.validate();
            if (fechaHasta.getRawValue() == '')
                fechaHasta.validate();

            if (fechaDesde.getRawValue() != '' &&
                fechaHasta.getRawValue() != '') {
                grid.getStore().load();
                _btn_Print.enable();
                _btn_Export.enable();
            }
        }
    });
    var btnClearSearch = Ext.create('Ext.button.MyButton', {
        width: 25,
        height: 25,
        disabled: true,
        iconCls: 'fas fa-eraser text-primary',
        handler: function () {
            fechaDesde.reset();
            fechaHasta.reset();
            grid.getStore().load();
        }
    });

    var _btn_Print = Ext.create('Ext.button.MyButton', {
        id: 'libro_control_btn_print',
        text: 'Imprimir',
        iconCls: 'fas fa-print text-primary',
        disabled: true,
        handler: function (This, e) {

            if (fechaDesde.getRawValue() == '')
                fechaDesde.validate();
            if (fechaHasta.getRawValue() == '')
                fechaHasta.validate();

            if (fechaDesde.getRawValue() != '' &&
                fechaHasta.getRawValue() != '') {

                App.ShowWaitMsg();

                var _result = App.PerformSyncServerRequest(Routing.generate('printLibroControl'), {

                    fechaDesde: fechaDesde.getRawValue(),
                    fechaHasta: fechaHasta.getRawValue(),
                    start: (grid.store.currentPage - 1) * 25,
                    limit: (grid.store.currentPage - 1) * 25 + 25
                });
                App.HideWaitMsg();

                if (_result.success) {
                    var newWindow = window.open('', 'center', 'width=1300, height=600');
                    var documentP = newWindow.open('', 'center', 'width=1300, height=600').document.open();

                    documentP.write(_result.html);
                    documentP.close();
                    newWindow.print();
                }
            }
        }
    });
    // var _btn_Export = Ext.create('Ext.button.MyButton', {
    //     id: 'libro_control_btn_export',
    //     text: 'Exportar',
    //     iconCls: 'fa fa-share-square-o',
    //     disabled: true,
    //     handler: function (This, e) {
    //
    //         if (fechaDesde.getRawValue() == '')
    //             fechaDesde.validate();
    //         if (fechaHasta.getRawValue() == '')
    //             fechaHasta.validate();
    //
    //         if (fechaDesde.getRawValue() != '' &&
    //             fechaHasta.getRawValue() != '') {
    //
    //             App.ShowWaitMsg();
    //
    //             var _result = App.PerformSyncServerRequest(Routing.generate('printLibroControl'), {
    //                 fechaDesde: fechaDesde.getRawValue(),
    //                 fechaHasta: fechaHasta.getRawValue(),
    //                 start: (grid.store.currentPage - 1) * 25,
    //                 limit: (grid.store.currentPage - 1) * 25 + 25
    //             });
    //             App.HideWaitMsg();
    //
    //             if (_result.success) {
    //                 window.open('data:application/vnd.ms-excel,' + encodeURIComponent(_result.html));
    //             }
    //         }
    //     }
    // });

    var panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        id: 'arbolunidades',
        hideHeaders: true,
        border: true,
        rootVisible: false,
        collapsible: true,
        collapsed: false,
        collapseDirection: 'left',
        header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'},},
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
                grid.enable();
                store_tarjeta.load();
            }
        }


    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_libro_control',
        store: store_libro_control,
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        columns: [
            {text: '<strong>No. Orden</strong>', dataIndex: 'noOrden', filter: 'string'},
            {text: '<strong>Matrícula</strong>', dataIndex: 'matricula', filter: 'string'},
            {text: '<strong>Marca</strong>', dataIndex: 'marca', filter: 'string', flex: 0.4},
            {text: '<strong>Modelo</strong>', dataIndex: 'modelo', filter: 'string'},
            {text: '<strong>Unidad</strong>', dataIndex: 'unidad', filter: 'string', flex: 0.8},
            {text: '<strong>Fecha/Hora Emisión</strong>', dataIndex: 'fechaEmision', filter: 'string', flex: 0.4},
            //{text: '<strong>Hora Emisión</strong>', dataIndex: 'horaEmision', filter: 'string'},
            {text: '<strong>Fecha/Hora Cierre</strong>', dataIndex: 'fechaCierre', filter: 'string', flex: 0.4},
            //{text: '<strong>Hora Cierre</strong>', dataIndex: 'horaCierre', filter: 'string'},
            {text: '<strong>Operaciones</strong>', dataIndex: 'operacion', filter: 'string', flex: 1}
        ],
        tbar: {
            id: 'libro_control_tbar',
            height: 36,
            items: [fechaDesde, fechaHasta, btnSearch, btnClearSearch, '->', _btn_Print]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_libro_control'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('libro_control_btn_print').setDisabled(selected.length == 0);
            }
        }
    });

    var panel_libro_control = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_libro_control',
        title: 'Libro de Control',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });

    App.render(panel_libro_control);
});