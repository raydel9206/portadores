/**
 * Created by yosley on 03/11/2015.
 */

Ext.onReady(function () {

    var store_umnivel = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_umnivel',
        fields: [
            {name: 'id'},
            {name: 'nivel_actividad'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/um_nivel_actividad/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true,

    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_umnivel').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
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
                    var marked = field.marked;
                    field.setMarked(false);

                    if (marked) {
                        Ext.getCmp('id_grid_umnivel').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_umnivel').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_umnivel').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_umnivel').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_umnivel').getStore().loadPage(1);
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

    var grid_umnivel = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_umnivel',
        store: store_umnivel,
        columns: [
            {
                text: '<strong>Nombre</strong>',
                dataIndex: 'nivel_actividad',
                filter: 'string',
                flex: 1
            }

        ],
        selModel: {
            mode: 'MULTI'
        },
        tbar: {
            id: 'umnivel_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_umnivel'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('umnivel_btn_mod'))
                Ext.getCmp('umnivel_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('umnivel_btn_del'))
                Ext.getCmp('umnivel_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var panel_umnivel = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_umnivel',
        title: 'UMs del Nivel de Actividad',
        frame: true,
        closable:true,
        layout: 'fit',
        items: [grid_umnivel]
    });

    App.render(panel_umnivel);
});