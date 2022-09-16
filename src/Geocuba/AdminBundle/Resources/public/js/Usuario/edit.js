Ext.onReady(function () {
    var stores_ids = ['grupos_store'],
        gridpanel = Ext.getCmp('gridpanel'),
        toolbar = gridpanel.down('toolbar');

    gridpanel.getStore().on('load', function (store, records, successful, eOpts) {
        gridpanel.body.mask('Cargando...');

        toolbar.items.each(function (item) {
            item.disable();
        });

        stores_ids.forEach(function (store_id) {
            var store = Ext.getStore(store_id);

            if (Ext.getClassName(store) === Ext.data.JsonStore.$className) { // Omitir los stores estáticos
                store.on('load', function (_store, records, successful, eOpts) {
                    stores_ids = Ext.Array.remove(stores_ids, store.getStoreId());

                    if (Ext.isEmpty(Ext.Array.filter(stores_ids, function (__store_id) {
                            return !Ext.getStore(__store_id).isLoaded();
                        }))) {

                        gridpanel.body.unmask();
                        toolbar.items.last().enable();
                        toolbar.items.first().enable();
                    }
                });

                store.load();
            }
        });
    }, this, {single: true});

    // Mostrar los components ocultos
    Ext.each(['btn_add', 'btn_upd', 'btn_del', 'btn_pwd'], function (field_id) {
        Ext.getCmp(field_id).setVisible(true);
    });
});