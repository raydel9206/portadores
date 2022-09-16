Ext.onReady(function () {
    const map = window.Map;

    // -----------------------------------------------------------------------------------------------------------------

    Ext.define('GISRoute.Route.Window', {
        extend: 'Ext.window.Window',

        animateTarget: 'list-routes',

        title: 'Rutas',
        glyph: 0xf5ee,
        width: 990,
        height: 580,
        modal: false,
        // plain: true,
        resizable: false,
        collapsible: true,

        layout: 'border',
        items: [{
            xtype: 'gridpanel',

            reserveScrollbar: true,
            scrollable: 'vertical',
            columnLines: true,

            region: 'center',
            flex: 3,
            // width: 600,

            store: Ext.getStore('routes_store'),

            columns: [{
                xtype: 'rownumberer'
            }, {
                text: 'Nombre',
                dataIndex: 'route_name',
                flex: 1
            }, {
                text: 'Distancia (km)',
                dataIndex: 'distance',
                xtype: 'numbercolumn',
                format: '0,000.000',
                cls: 'x-column-header-inner-centered',
                align: 'right',
                width: 135
            }],

            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',

                items: [{
                    text: 'Localizar',
                    tooltip: 'Localiza en el mapa la Ruta seleccionada',

                    glyph: 0xf689,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 90,
                    disabled: true,

                    handler: function (button) {
                        button.up('window').collapse();

                        const feature = map.getFeature(1, 'ROUTES.' + button.up('gridpanel').getSelection()[0].get('gid'));
                        if (feature) {
                            map.locateFeature(feature, false);
                        }
                    }
                }],
            }],

            listeners: {
                boxready: function (self) {
                    const panel = self.nextSibling('panel'),
                        toolbar = self.down('toolbar'),
                        listener = self.getStore().on('beforeload', function (store) {
                            store.removeAll();

                            panel.disable().setData();
                            toolbar.items.each(function (i) {
                                i.disable();
                            });
                        }, this, {destroyable: true});

                    self.on('selectionchange', function (model, selected, eOpts) {
                        toolbar.items.each(function (i) {
                            i.setDisabled(selected.length === 0);
                        });

                        panel.setDisabled(selected.length === 0).setData(selected.length !== 0 ? selected[0].getData() : null);
                    });

                    self.up('window').on('close', function () {
                        listener.destroy();
                    });

                    self.getStore().loadPage(1);
                }
            }
        }, {
            xtype: 'panel',

            header: {
                title: 'Detalles',
                style: {
                    backgroundColor: 'white',
                    borderBottom: '1px solid #c1c1c1 !important',
                    padding: '6px !important'
                },

                listeners: {
                    boxready: function (self, eOpts) {
                        self.el.down('[data-ref=textEl]').setStyle({color: 'black'});

                        Ext.each(self.query('button[xtype=button]'), function (button) { // Remove blue style from each button
                            var el = Ext.get(button.getId());
                            el.removeCls('x-btn x-unselectable x-box-item x-btn-default-small');
                            el.addCls('x-btn x-unselectable x-box-item x-toolbar-item x-btn-default-toolbar-small');

                            el = Ext.get(button.getId() + '-btnWrap');
                            el.removeCls('x-btn-wrap x-btn-wrap-default-small');
                            el.addCls('x-btn-wrap x-btn-wrap-default-toolbar-small');

                            el = Ext.get(button.getId() + '-btnEl');
                            el.removeCls('x-btn-button x-btn-button-default-small x-btn-text x-btn-button-center');
                            el.addCls('x-btn-button x-btn-button-default-toolbar-small x-btn-text x-btn-icon x-btn-icon-left x-btn-button-center');

                            el = Ext.get(button.getId() + '-btnIconEl');
                            el.removeCls('x-btn-icon-el x-btn-icon-el-default-small');
                            el.addCls('x-btn-icon-el x-btn-icon-el-default-toolbar-small');

                            el = Ext.get(button.getId() + '-btnInnerEl');
                            el.removeCls('x-btn-inner x-btn-inner-default-small');
                            el.addCls('x-btn-inner x-btn-inner-default-toolbar-small');
                        });
                    }
                },
                items: [{
                    xtype: 'button',
                    text: 'Exportar',
                    tooltip: 'Exporta los detalles de la Ruta',

                    glyph: 0xf1c3,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 90,
                    disabled: true,

                    handler: function (button) {
                        const url = App.buildURL('/gis/route/export'),
                            params = {gid: button.up('panel').previousSibling('gridpanel').getSelection()[0].get('gid')};

                        App.request('GET', url, params, null, null,
                            function (response) { // success_callback
                                App.showDownloadWindow(response.getResponseHeader('Content-Type'), response.getResponseHeader('Content-Disposition'), response.responseBytes);
                            }, null, {binary: true}
                        );
                    }
                }],
            },

            scrollable: 'vertical',
            bodyPadding: 5,
            disabled: true,

            region: 'east',
            split: {size: 1},
            flex: 5,
            // width: 400,

            tpl: [
                '<tpl if="gid">',
                '   <div class="list-group list-group-flush text-monospace">',
                '       <div class="list-group-item"><strong>Origen:</strong> {metadata.from}</div>',
                '       <div class="list-group-item"><strong>Destino:</strong> {metadata.to}</div>',
                '       <div class="list-group-item px-0">',
                '           <table class="table table-bordered table-hover">',
                '               <thead>',
                '                   <tr class="text-center">',
                '                       <th scope="col">#</th>',
                '                       <th scope="col">Origen</th>',
                '                       <th scope="col">Destino</th>',
                '                       <th scope="col" colspan="2">Distancia (km)</th>',
                '                   </tr>',
                '               </thead>',
                '               <tbody>',
                '                   <tpl if="Ext.isEmpty(metadata.segments)">',
                '                       <tr class="text-center bg-light text-danger"><th scope="row" colspan="4">No existen segmentos en la Ruta</th></tr>',
                '                   <tpl else>',
                '                       <tpl foreach="metadata.segments">',
                '                           <tr class="text-center">',
                '                               <th scope="col" class="text-center align-middle"><span class="badge badge-light">{#}</span></th>',
                '                               <td>{source}</td>',
                '                               <td>{target}</td>',
                '                               <td class="text-right">{[Ext.util.Format.number(values.distance, \'0,000.000\')]}</td>',
                '                               <td class="text-right">{[Ext.util.Format.number(values.cumul_distance, \'0,000.000\')]}</td>',
                '                           </tr>',
                '                       </tpl>',
                '                       <tr class="">',
                '                           <th scope="row" colspan="3">Total</th>',
                '                           <th scope="row" colspan="2" class="text-right">{[Ext.util.Format.number(values.distance, \'0,000.000\')]}</th>',
                '                       </tr>',
                '                   </tpl>',
                '               </tbody>',
                '           </table>',
                '       </div>',
                '   </div>',
                '</tpl>',
            ]
        }, {
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            store: Ext.getStore('routes_store'),
            displayInfo: true,

            region: 'south'
        }],

        listeners: {
            boxready: function (win) {
                const panel = map.get('panel');
                panel.enableAction(win.getInitialConfig('action_el'), true);

                const position = panel.getPosition();
                win.setPosition(position[0] + 60, position[1] + 80);
                win.focus();

                // -----------------------------------------------------------------------------------------------------

                const listener_key = panel.on('destroy', function () {
                    win.close();
                }, this, {destroyable: true});

                win.on('destroy', function () {
                    listener_key.destroy();
                });
            },

            close: function (win) {
                const store = win.down('gridpanel').getStore();
                if (store.isLoading()) {
                    try {
                        store.getProxy().lastRequest.getOperation().abort();
                        console.info('Request canceled');
                    } catch (e) {
                        console.warn(e);
                    }
                }

                map.get('panel').enableActions();
            }
        }
    });

    // -----------------------------------------------------------------------------------------------------------------

    $('#list-routes').click(function (evt) {
        if (!$(evt.currentTarget).hasClass('active')) {
            Ext.create('GISRoute.Route.Window', {action_el: evt.currentTarget.id}).show();
        }
    }).parent().attr('hidden', false);
});