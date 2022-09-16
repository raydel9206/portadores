/**
 * Created by yosley on 06/10/2015.
 */

Ext.onReady(function () {


    let store_producto = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_producto',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'fila'},
            {name: 'enblanco'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/producto/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'fila',
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
                Ext.getCmp('id_grid_producto').getStore().on({
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
                        Ext.getCmp('id_grid_producto').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_producto').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_producto').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_producto').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_producto').getStore().loadPage(1);
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

    let grid_producto = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_producto',
        store: store_producto,
        columns: [
            {
                text: '<strong>Fila</strong>',
                dataIndex: 'fila',
                filter: 'string',
                width: 100,
                align: 'center'
            },
            {
                text: '<strong>Nombre</strong>',
                dataIndex: 'nombre',
                filter: 'string',
                width: 400,
            },
            {
                text: '<strong>U/M</strong>',
                dataIndex: 'um',
                filter: 'string',
                width: 100,
                align: 'center'
            },
            {
                text: '<strong>En Blanco</strong>',
                align: 'center',
                dataIndex: 'enblanco',
                xtype: 'booleancolumn',
                width: 100,
                align: 'center',
                trueText: '<span class="badge-true">Sí</span>',
                falseText: '<span class="badge-false">No</span>'
            }
        ],
        tbar: {
            id: 'producto_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_producto'),
            displayInfo: true,
            // plugins: new Ext.ux.ProgressBarPager()
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('producto_btn_mod'))
                Ext.getCmp('producto_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('producto_btn_del'))
                Ext.getCmp('producto_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    let _panel_producto = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_producto',
        title: 'Productos',
        frame: true,
        closable:true,
        layout: 'fit',
        items: [grid_producto]
    });

    App.render(_panel_producto);
});