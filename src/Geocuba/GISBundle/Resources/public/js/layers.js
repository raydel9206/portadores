Ext.onReady(function () {
    const map = window.Map;

    // -----------------------------------------------------------------------------------------------------------------

    Ext.define('GISRoute.Layer.Window', {
        extend: 'Ext.window.Window',

        animateTarget: 'list-layers',

        title: 'Control de capas',
        glyph: 0xf5fd,
        width: 375,
        height: 580,
        modal: false,
        // plain: true,
        resizable: false,
        // collapsible: true,

        defaultFocus: '[xtype=component]',

        layout: 'fit',
        items: [{
            // xtype: 'gridpanel',
            xtype: 'treepanel',

            // reserveScrollbar: true,
            scrollable: 'vertical',

            store: Ext.getStore('layers_store'),

            // lines: false,
            columnLines: true,
            rowLines: true,
            hideHeaders: true,

            rootVisible: false,
            root: {id: true, expanded: true, text: 'ROOT', 'children': []},

            viewConfig: {
                listeners: {
                    afteritemexpand: function (node, index, item, eOpts) {
                        this.refresh();
                    },

                    refresh: function (view) {
                        Ext.each(Ext.query(Ext.String.format('.x-grid-cell-{0} > .x-grid-cell-inner', view.ownerCt.down('widgetcolumn').getId()), false), function (cell_el) {
                            cell_el.applyStyles({padding: "0 10px"});
                        });
                    }
                }
            },

            columns: [{
                xtype: 'treecolumn',
                // text: 'Capa',
                dataIndex: 'title',

                flex: 1,
                iconCls: Ext.emptyString
            }, {
                xtype: 'templatecolumn',
                tpl: '<i class="{fa_icon} fa-lg" style="color: {fa_icon_color}"></i>',

                align: 'center',
                width: 40
            }, {
                xtype: 'widgetcolumn',
                dataIndex: 'active',

                align: 'center',
                width: 40,

                widget: {
                    xtype: 'checkbox',

                    listeners: {
                        change: function (self, newValue, oldValue, eOpts) {
                            const layer_index = self.getWidgetRecord().get('id'),
                                layer = map.getLayers().item(layer_index);

                            if (layer) {
                                layer.setVisible(newValue);
                            }
                        }
                    }
                },
            }]
        }],

        listeners: {
            boxready: function (win) {
                const panel = map.get('panel');
                panel.enableAction(win.getInitialConfig('action_el'), true);

                const treepanel = win.down('treepanel'),
                    data = {
                        text: 'ROOT',
                        expanded: true,
                        children: []
                    };

                map.getLayers().forEach(function (layer, index, array) {
                    data['children'].push({
                        id: index,
                        leaf: true,
                        title: layer.get('title'),
                        fa_icon: layer.get('fa_icon'),
                        fa_icon_color: layer.get('fa_icon_color'),
                        active: layer.getVisible()
                    });
                });

                treepanel.getStore().setRoot(data);
                treepanel.getView().refresh();

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
                map.get('panel').enableActions();
            }
        }
    });

    // -----------------------------------------------------------------------------------------------------------------

    $('#list-layers').click(function (evt) {
        if (!$(evt.currentTarget).hasClass('active')) {
            Ext.create('GISRoute.Layer.Window', {action_el: evt.currentTarget.id}).show();
        }
    }).parent().attr('hidden', false);
});