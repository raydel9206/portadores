/**
 * Created by orlando on 06/10/2015.
 */

Ext.onReady(function () {

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'No. Cheque...',
        width: 115,
        listeners: {
            change: function (field, newValue, oldValue, eOpt) {
                field.getTrigger('clear').setVisible(newValue);
                if (Ext.isEmpty(Ext.String.trim(field.getValue()))) {
                    var marked = field.marked;
                    field.setMarked(false);

                    if (marked) {
                        grid_chequefincimex.getStore().loadPage(1);
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
                    grid_chequefincimex.getStore().loadPage(1);
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
                        if (grid_chequefincimex.getStore().getCount() > 0)
                            grid_chequefincimex.getStore().loadPage(1);
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
                        grid_chequefincimex.getStore().loadPage(1);
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

    let store = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_chequefincimex',
        fields: [
            {name: 'id'},
            {name: 'no_cheque'},
            {name: 'moneda_id'},
            {name: 'moneda_nombre'},
            {name: 'monto_gasolina'},
            {name: 'monto_diesel'},
            {name: 'monto_total'},
            {name: 'fecha_registro'},
            {name: 'fecha_deposito'}
        ],
        // groupField: 'moneda_nombre',
        // sorters: 'nombreunidadid',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/chequeFincimex/loadChequeFincimex'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidad: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    no_cheque: find_button.getValue(),
                });
            }
        }
    });

    // let groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
    //     groupHeaderTpl: '<b>Moneda: {name}' + '({rows.length})</b>',
    //     hideGroupedHeader: true,
    //     startCollapsed: false,
    //     ftype: 'grouping'
    // });

    let grid_chequefincimex = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_chequefincimex',
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        store: store,
        columns: [
            {text: '<strong>No. Cheque</strong>', dataIndex: 'no_cheque', align: 'center', filter: 'string', flex: .1},
            {
                text: '<strong>Moneda</strong>',
                dataIndex: 'moneda_nombre',
                align: 'center',
                filter: 'string',
                width: 100
            },
            {
                text: '<strong>Total</strong>',
                dataIndex: 'monto_total',
                formatter: "number('0.00')",
                align: 'right',
                flex: .1
            },
            {text: '<strong>Fecha de Registro</strong>', dataIndex: 'fecha_registro', align: 'center', flex: .1},
            {
                text: '<strong>Depositado</strong>', dataIndex: 'fecha_deposito', align: 'center', flex: .1,
                renderer: function (value) {
                    if (value == '')
                        return "<div class='badge-false'>No</div>";
                    else
                        return "<div class='badge-true'>Si</div>";
                }
            },
            {text: '<strong>Fecha de Deposito</strong>', dataIndex: 'fecha_deposito', align: 'center', flex: .1},

        ],
        tbar: {
            id: 'chequefincimex_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_chequefincimex'),
            displayInfo: true,
            // plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('chequefincimex_btn_mod'))
                    Ext.getCmp('chequefincimex_btn_mod').setDisabled(selected.length == 0 || selected[0].data.fecha_deposito != '');
                if (Ext.getCmp('chequefincimex_btn_del'))
                    Ext.getCmp('chequefincimex_btn_del').setDisabled(selected.length == 0 || selected[0].data.fecha_deposito != '');
                if (Ext.getCmp('chequefincimex_btn_depositar'))
                    Ext.getCmp('chequefincimex_btn_depositar').setDisabled(selected.length == 0 || selected[0].data.fecha_deposito != '');
                // if(Ext.getCmp('_btn_cheques_export'))
                // Ext.getCmp('_btn_cheques_export').setDisabled(selected.length == 0 || selected[0].data.fecha_deposito != '');
                if (Ext.getCmp('_btn_cheques_comprobante'))
                    Ext.getCmp('_btn_cheques_comprobante').setDisabled(selected.length == 0);

                // store_desglose.load();
            }
        }
    });

    let tree_store = Ext.create('Ext.data.TreeStore', {
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

    let panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        region: 'west',
        width: 280,
        border: true,
        store: tree_store,
        id: 'arbolunidades',
        hideHeaders: true,
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
            selectionchange: function (This, record, tr, rowIndex, e, eOpts) {
                grid_chequefincimex.setDisabled(record.length === 0);
                grid_chequefincimex.getStore().load();

                // grid_distribucion.enable();
                // grid_distribucion.getStore().loadPage(1);
            }
        }


    });

    let panel_chequefincimex = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_chequefincimex',
        title: 'Gestión de cheques para pago a FINCIMEX',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_chequefincimex]
    });

    App.render(panel_chequefincimex);
});