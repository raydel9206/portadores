Ext.onReady(function () {
    var gridpanel = Ext.getCmp('gridpanel'),
        toolbar = gridpanel.down('toolbar');

    // Cargar los stores adicionales utilizados en el formulario.
    gridpanel.getStore().on('load', function () {
        var stores = [
                Ext.create('Ext.data.JsonStore', {
                    storeId: 'usuarios_store',
                    fields: ['id', 'usuario', 'nombre_completo'],
                    proxy: {
                        type: 'ajax',
                        url: App.buildURL('/admin/usuario/list'),
                        extraParams: {simple: true},
                        reader: {rootProperty: 'rows'}
                    },
                    pageSize: 0,
                    autoLoad: false
                }), Ext.create('Ext.data.JsonStore', {
                    storeId: 'grupos_store',
                    fields: ['id', 'nombre'],
                    proxy: {
                        type: 'ajax',
                        url: App.buildURL('/admin/grupo/list'),
                        extraParams: {simple: true},
                        reader: {rootProperty: 'rows'}
                    },
                    pageSize: 0,
                    autoLoad: false
                })
            ],
            stores_ids = Ext.Array.map(stores, function (store) {
                return store.getStoreId();
            });

        gridpanel.body.mask('Cargando...');

        toolbar.items.each(function (item) {
            item.disable();
        });

        stores.forEach(function (store) {
            if (Ext.getClassName(store) === Ext.data.JsonStore.$className) { // Omitir los stores estáticos
                store.on('load', function (_store, records, successful, eOpts) {
                    stores_ids = Ext.Array.remove(stores_ids, store.getStoreId());

                    if (Ext.isEmpty(Ext.Array.filter(stores_ids, function (__store_id) {
                        return !Ext.getStore(__store_id).isLoaded();
                    }))) {

                        gridpanel.body.unmask();
                        toolbar.items.first().enable();
                    }
                });

                store.load();
            }
        });
    }, this, {single: true});

    // Mostrar los components ocultos
    gridpanel.down('toolbar').show();

    // Iniciar/Detener la tarea del notifier
    gridpanel.getStore().on({
        beforeload: function (store, operation, eOpts) {
            App.stopNotifierTask();
        },

        load: function (store, records, successful, eOpts) {
            App.startNotifierTask();
        }
    });
});