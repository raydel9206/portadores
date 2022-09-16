Ext.onReady(function () {
    const map = window.Map;

    // -----------------------------------------------------------------------------------------------------------------

    Ext.define('GISRoute.Table.Window', {
        extend: 'Ext.window.Window',

        animateTarget: 'list-tables',

        title: 'Tablas de distancia',
        glyph: 0xf5cb,
        width: 990,
        height: 580,
        modal: false,
        // plain: true,
        resizable: false,
        // resizeHandles: 'e',
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

            store: Ext.getStore('tables_store'),

            columns: [{
                text: 'Nombre',
                dataIndex: 'name',
                flex: 1
            }, {
                text: 'POI',
                dataIndex: 'count',
                xtype: 'numbercolumn',
                format: '0,000',
                cls: 'x-column-header-inner-centered',
                align: 'right',
                width: 105
            }],

            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',

                items: [{
                    text: 'Eliminar',
                    tooltip: 'Elimina la Tabla de distancia seleccionada',

                    glyph: 0xf2ed,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 90,
                    disabled: true,

                    handler: function (button) {
                        const gridpanel = button.up('gridpanel'),
                            selection = button.up('gridpanel').getSelection()[0];

                        Ext.Msg.show({
                            title: '¿Eliminar Tabla de distancia?',
                            message: Ext.String.format('¿Está seguro que desea eliminar <strong>{0}</strong>?', selection.get('name')),
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.Msg.QUESTION,
                            fn: function (btn) {
                                if (btn === 'yes') {
                                    const url = App.buildURL('/gis/table/del'),
                                        params = {id: selection.get('id')};

                                    App.request('DELETE', url, params, null, null, function (response) { // success_callback
                                        gridpanel.getStore().reload();
                                    });
                                }
                            }
                        });
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
                    tooltip: 'Exporta los detalles de la Tabla de distancia',

                    glyph: 0xf1c3,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 90,
                    disabled: true,

                    handler: function (button) {
                        const url = App.buildURL('/gis/table/export'),
                            params = {id: button.up('panel').previousSibling('gridpanel').getSelection()[0].get('id')};

                        App.request('GET', url, params, null, null,
                            function (response) { // success_callback
                                App.showDownloadWindow(response.getResponseHeader('Content-Type'), response.getResponseHeader('Content-Disposition'), response.responseBytes);
                            }, null, {binary: true}
                        );
                    }
                }],
            },

            scrollable: true,
            bodyPadding: 5,
            disabled: true,

            region: 'east',
            split: {size: 1},
            flex: 5,
            // width: 400,

            tpl: [
                '<tpl if="id">',
                '   <table class="table table-bordered table-hover text-monospace">',
                '       <thead>',
                '           <tpl if="Ext.isEmpty(points)">',
                '               <tr class="text-center bg-light text-danger"><th scope="row">No existen Puntos de interés (POI)</th></tr>',
                '           <tpl else>',
                '               <tr class="text-center">',
                '                   <th scope="col" colspan="2" class="align-middle">POI</th>',
                '                   <tpl foreach="points">',
                '                       <th scope="col" class="align-middle">{point_name}</th>',
                '                   </tpl>',
                '               </tr>',
                '           </tpl>',
                '       </thead>',
                '       <tbody>',
                '           <tpl if="!Ext.isEmpty(points)">',
                '               <tpl foreach="points">',
                '                   <tr>',
                '                       <th scope="col" class="text-center align-middle"><span class="badge badge-light">{[xindex]}</span></th>',
                '                       <th scope="col" class="text-center align-middle">{point_name}</th>',
                '                       <tpl foreach="nodes">',
                '                           <td class="text-right align-middle" scope="col">{[values === 0 ? "-" : Ext.util.Format.number(values, \'0,000.000\')]}</td>',
                '                       </tpl>',
                '                   </tr>',
                '               </tpl>',
                '           </tpl>',
                '       </tbody>',
                '   </table>',
                '</tpl>'
            ]
        }, {
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            store: Ext.getStore('tables_store'),
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

    $('#list-tables').click(function (evt) {
        if (!$(evt.currentTarget).hasClass('active')) {
            Ext.create('GISRoute.Table.Window', {action_el: evt.currentTarget.id}).show();
        }
    }).parent().attr('hidden', false);
});