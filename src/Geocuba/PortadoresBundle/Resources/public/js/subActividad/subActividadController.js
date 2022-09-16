/**
 * Created by kireny on 4/11/15.
 */

Ext.onReady(function () {

    var store_sub_activ = Ext.create('Ext.data.JsonStore', {
        frame: true,
        storeId: 'id_store_subActividad',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'nactividadid'},
            {name: 'nactividadnombre'}
        ],
        groupField: 'nactividadnombre',
        sorters: ['nombreunidadid' , 'nombre' ] ,
        viewConfig: {forceFit: true},
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/subactividad/load'),
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
                Ext.getCmp('id_grid_subActividad').getStore().on({
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
                        Ext.getCmp('id_grid_subActividad').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_subActividad').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_subActividad').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_subActividad').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_subActividad').getStore().loadPage(1);
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
    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Actividad: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });
    var grid_subActividad = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_subActividad',
        features: [groupingFeature],
        store: store_sub_activ,
        columns: [
            {
                text: '<strong>Nombre</strong>',
                dataIndex: 'nombre',
                filter: 'string',
                flex: 1
            }
        ],
        tbar: {
            id: 'subActividad_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_subActividad'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(Ext.getCmp('subActividad_btn_mod'))
                Ext.getCmp('subActividad_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('subActividad_btn_del'))
                Ext.getCmp('subActividad_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var _panel_subActividad = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_subActividades',
        title: 'Subactividades',
        frame: true,
        layout: 'fit',
        items: [grid_subActividad]
    });
    App.render(_panel_subActividad);
});