
Ext.onReady(function () {

    let storeMotores = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeMotores',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/grupos_electrogenos_motores/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true,
    });

    let find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('gridMotores').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            let value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    no_serie: value
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
                        Ext.getCmp('gridMotores').getStore().loadPage(1);
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
                    Ext.getCmp('gridMotores').getStore().loadPage(1);
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
                        if (Ext.getCmp('gridMotores').getStore().getCount() > 0)
                            Ext.getCmp('gridMotores').getStore().loadPage(1, {params: {no_serie: value}});
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
                        Ext.getCmp('gridMotores').getStore().loadPage(1);
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


    let grid = Ext.create('Ext.grid.Panel', {
        id: 'gridMotores',
        reference: 'gridMotores',
        store: storeMotores,
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen motores registrados</div>'},
        columns: [
            {text: '<strong>No. Serie</strong>', dataIndex: 'no_serie', filter: 'string', flex: 1},
            {text: '<strong>Modelo</strong>', dataIndex: 'modelo_nombre', filter: 'string', flex: 1},
            {text: '<strong>Marca</strong>', dataIndex: 'marca_nombre', filter: 'string', flex: 1},
            {text: '<strong>Hp</strong>', dataIndex: 'hp', filter: 'string', flex: 1},
            {text: '<strong>Rpm</strong>', dataIndex: 'rpm', filter: 'string', flex: 1},
            ],
        tbar: {
            id: 'motores_tbar',
            height: 36,
            items: [find_button, '-']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeMotores'),
            displayInfo: true
        },
        plugins: 'gridfilters'
    });

    let _panel = Ext.create('Ext.panel.Panel', {
        title: 'Grupos Electr??genos - Motores',
        frame: true,
        closable:true,
        layout: 'fit',
        items: [grid]
    });

    App.render(_panel);
});