/**
 * Created by yosley on 06/10/2015.
 */

Ext.onReady(function () {

    let groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Portador: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });

    let store_tipo_comb = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_combustible',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'codigo'},
            {name: 'monedaid'},
            {name: 'moneda'},
            {name: 'precio'},
            {name: 'precio_tiro_directo'},
            {name: 'maximo_tarjeta_dinero'},
            {name: 'maximo_tarjeta_litro'},
            {name: 'portador_nombre'}
        ],
        groupField: 'portador_nombre',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tipocombustible/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'nombre',
            direction: 'ASC',
            transform: function (siglas) {
                return siglas.toLowerCase();
            }
        }],
        autoLoad: true,
    });

    let find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_tipo_combustible').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            let value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value
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
                    let marked = field.marked;
                    field.setMarked(false);

                    if (marked) {
                        Ext.getCmp('id_grid_tipo_combustible').getStore().loadPage(1);
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
                let value = field.getValue();

                if (!Ext.isEmpty(Ext.String.trim(value)) && e.getKey() === e.ENTER) {
                    field.setMarked(true);
                    Ext.getCmp('id_grid_tipo_combustible').getStore().loadPage(1);
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
                    let value = this.getValue();
                    if (!Ext.isEmpty(Ext.String.trim(value))) {
                        this.setMarked(true);
                        if (Ext.getCmp('id_grid_tipo_combustible').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_tipo_combustible').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_tipo_combustible').getStore().loadPage(1);
                    }
                    // Ext.getCmp('id_grid_tiporam').setTitle('tiporam');
                    this.setMarked(false);
                }
            }
        },

        setMarked: function (marked) {
            let el = this.getEl(),
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

    let grid_tipo_combustible = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_tipo_combustible',
        store: store_tipo_comb,
        features: [groupingFeature],
        columns: [
            {
                text: '<strong>Tipo de combustible</strong>',
                dataIndex: 'nombre',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Código</strong>',
                dataIndex: 'codigo',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Fila</strong>',
                dataIndex: 'filaid',
                filter: 'string',
                flex: 1
            },
            // {
            //     text: '<strong>Moneda de Pago</strong>',
            //     dataIndex: 'moneda',
            //     filter: 'string',
            //     flex: 1
            // },
            {
                text: '<strong>Precio</strong>',
                dataIndex: 'precio',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Precio TD</strong>',
                dataIndex: 'precio_tiro_directo',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Máximo en Tarjeta(Dinero)</strong>',
                dataIndex: 'maximo_tarjeta_dinero',
                filter: 'string',
                flex: 1
            },
            {
                text: '<strong>Máximo en Tarjeta(Litro)</strong>',
                dataIndex: 'maximo_tarjeta_litro',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'tipo_combustible_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_tipo_combustible'),
            displayInfo: true,
            // plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('tipo_combustible_btn_mod'))
                Ext.getCmp('tipo_combustible_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('tipo_combustible_btn_del'))
                Ext.getCmp('tipo_combustible_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    let _panel_tipo_combustible = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_tipo_combustible',
        title: 'Tipos de combustibles',
        frame: true,
        closable:true,
        layout: 'fit',
        items: [grid_tipo_combustible]
    });

    App.render(_panel_tipo_combustible);
});