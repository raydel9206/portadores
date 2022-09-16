2/**
 * Created by yosley on 07/10/2015.
 */

Ext.onReady(function () {

    var store = Ext.create('Ext.data.JsonStore', {
        frame: true,
        storeId: 'id_store_actividad',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'administrativa'},
            {name: 'um_actividad'},
            {name: 'um_actividad_nombre'},
            {name: 'inversion'},
            {name: 'trafico'},
            {name: 'id_portador'},
            {name: 'portadornombre'}
        ],
        groupField: 'portadornombre',
        viewConfig: {forceFit: true},
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/actividad/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'portadornombre',
            direction: 'ASC'
        }, {
            property: 'nombre',
            direction: 'ASC'
        }],
        autoLoad: true,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                if (Ext.getCmp('id_grid_actividad'))
                    Ext.getCmp('id_grid_actividad').getSelectionModel().deselectAll();
            }
        }
    });

    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_actividad').getStore().on({
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
                        Ext.getCmp('id_grid_actividad').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_actividad').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_actividad').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_actividad').getStore().loadPage(1, {params: {nombre: value}});
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
                        Ext.getCmp('id_grid_actividad').getStore().loadPage(1);
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
        groupHeaderTpl: '<b>Portador: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });

    var grid_actividad = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_actividad',
        store: store,
        features: [groupingFeature],
        columns: [
            {
                text: '<strong>Nombre</strong>',
                dataIndex: 'nombre',
                filter: 'string',
                flex: 2
            },
            {
                text: '<strong>Nombre Um Actividad</strong>',
                dataIndex: 'um_actividad_nombre',
                filter: 'string',
                flex: 2
            },
            {
                text: '<strong>Código GAE</strong>',
                dataIndex: 'codigogae',
                filter: 'string',
                flex: 1
            },
            {
                /*TODO porque se redondea esto no es un codigo*/
                text: '<strong>Código MEP</strong>',
                dataIndex: 'codigomep',
                filter: 'string',
                flex: 1,
                renderer: function (value) {
                    if (value) {
                        return Ext.util.Format.number(value, '0,0');
                    }
                }
            },
            {
                text: '<strong>Actividad no Productiva</strong>',
                dataIndex: 'administrativa',
                filter: 'string',
                flex: 1,
                renderer: function (value, met) {
                    if (value) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #04B431;';
                        return '<strong>Actividad no Productiva</strong>';
                    }
                }
            },
            {
                text: '<strong>Inversión</strong>',
                dataIndex: 'inversion',
                filter: 'string',
                flex: 1,
                renderer: function (value, met) {
                    if (value) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #2ECCFA;';
                        return '<strong>Inversión</strong>';

                    }
                }
            },
            {
                text: '<strong>Tráfico</strong>',
                dataIndex: 'trafico',
                filter: 'string',
                flex: 1,
                renderer: function (value, met) {
                    if (value) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #0080FF;';
                        return '<strong>Tráfico</strong>';
                    }
                }
            }
        ],
        tbar: {
            id: 'actividad_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_actividad'),
            displayInfo: true,
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('actividad_btn_mod'))
                    Ext.getCmp('actividad_btn_mod').setDisabled(selected.length == 0);
                if (Ext.getCmp('actividad_btn_del'))
                    Ext.getCmp('actividad_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var panel_actividad = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_actividad',
        title: 'Actividades',
        frame: true,
        closable: true,
        layout: 'fit',
        items: [grid_actividad]
    });

    App.render(panel_actividad);
});