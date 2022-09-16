Ext.onReady(function () {
    var gridpanel = Ext.create('Ext.grid.Panel', {
            title: 'Grupos',

            region: 'west',
            width: 325,
            minWidth: 325,

            reserveScrollbar: true,
            collapsible: true,
            split: {size: 2},

            viewConfig: {
                emptyText: '<div class="text-center">No existen grupos registrados</div>'
            },

            hideHeaders: true,
            store: Ext.create('Ext.data.JsonStore', {
                fields: ['id', 'nombre'],
                proxy: {
                    type: 'ajax',
                    url: App.buildURL('/admin/grupo/list'),
                    reader: {rootProperty: 'rows'}
                    // extraParams: {simple: true}
                },
                pageSize: 0,
                autoLoad: true
            }),

            tools: [{
                type: 'refresh',
                tooltip: 'Actualiza el listado de grupos',
                callback: function (gridpanel, tool, event) {
                    if (!treepanel.getStore().isLoading() && !gridpanel.getStore().isLoading()) {
                        treepanel.setBusy(true);

                        gridpanel.getSelectionModel().deselectAll();
                        gridpanel.getStore().reload({
                            callback: function () {
                                treepanel.setBusy(false);
                            }
                        });
                    }
                }
            }],

            columns: [{
                text: 'Nombre',
                dataIndex: 'nombre',
                flex: 1
            }],

            plugins: [{
                ptype: 'rowexpander',
                rowBodyTpl: new Ext.XTemplate(
                    '<div class="card p-1">',
                    '   <div class="card">',
                    '       <tpl if="!Ext.isEmpty(usuarios)">',
                    '           <div class="card-header text-center">',
                    '               <strong>Usuarios</strong> <em class="text-muted">({usuarios.length})</em>',
                    '           </div>',
                    '       </tpl>',
                    '       <table class="table table-bordered table-hover table-responsive-md mb-0">',
                    '           <tpl if="Ext.isEmpty(usuarios)">',
                    '               <tr class="text-center">',
                    '                   <td colspan="4"><span class="badge badge-secondary">No tiene usuarios asociados</span></td>',
                    '                </tr>',
                    '            <tpl else>',
                    // '            <thead class="text-center">',
                    // '               <tr>',
                    // '                   <th scope="col">Usuario</th>',
                    // '                   <th scope="col">Email</th>',
                    // '               </tr>',
                    // '             </thead>',
                    '             <tbody>',
                    '               <tpl for="usuarios">',
                    '                   <tr class="text-center">',
                    '                       <td>{usuario}</td>',
                    '                       <td>{nombre_completo}</td>',
                    '                    </tr>',
                    '                </tpl>',
                    '              </tbody>',
                    '           </tpl>',
                    '       </table>',
                    '   </div>',
                    '</div>'
                )
            }],

            listeners: {
                selectionchange: function (model, selected, eOpts) {
                    if (selected.length !== 0) {
                        gridpanel.getView().disable();

                        treepanel.getStore().reload({
                            params: {grupo_id: selected[0].get('id')},
                            callback: function () {
                                gridpanel.getView().enable();
                            }
                        });
                    } else {
                        treepanel.getRootNode().removeAll();
                        treepanel.getView().refresh();
                        treepanel.down('toolbar').items.each(function (item) {
                            item.disable();
                        });
                    }
                }
            }
        }),
        treepanel = Ext.create('Ext.tree.TreePanel', {
            xtype: 'treepanel',
            title: 'Recursos',
            id: 'treepanel',
            closable: true,

            region: 'center',
            flex: 2,
            scrollable: 'vertical',

            reserveScrollbar: true,

            // viewConfig: {
            //     stripeRows: true,
            //     emptyText: '<div class="text-center">No existen recursos registrados</div>'
            // },

            rootVisible: false,
            root: {expanded: true, text: 'ROOT', 'children': []},
            store: Ext.create('Ext.data.TreeStore', {
                fields: ['id', 'ruta', 'nombre', 'dependent_of', 'descripcion', 'dropdown', 'archivos'],
                proxy: {
                    type: 'ajax',
                    url: App.buildURL('/admin/recurso/list'),
                    listeners: {
                        exception: function (proxy, request, operation) {
                            if (!request.aborted) {
                                var message = request.hasOwnProperty('statusText') ? request.statusText : 'La solicitud no tiene respuesta disponible',
                                    error = request.hasOwnProperty('responseText') ? ':<br><em><small>' + App.parseError(request.responseText) + '</small></em>' : '';

                                App.showAlert(message + error, 'danger', 10000);
                            }
                        }
                    }
                },
                pageSize: 0,
                autoLoad: false,
                listeners: {
                    datachanged: function (store, eOpts) {
                        if (store.isLoaded()) {
                            var empty = store.getCount() === 0;

                            Ext.each(treepanel.down('toolbar').query('button'), function (btn) {
                                btn.setDisabled(empty);
                            });
                        }
                    }
                }
            }),

            columnLines: true,
            rowLines: true,
            columns: [{
                xtype: 'treecolumn',
                text: 'Nombre',
                dataIndex: 'nombre',
                // style: 'text-align: center',
                iconCls: Ext.emptyString,
                flex: 4
            }, {
                text: 'Descripción',
                dataIndex: 'descripcion',
                style: 'text-align: center',
                flex: 5
            }, {
                text: 'Menú',
                dataIndex: 'dropdown',
                style: 'text-align: center',
                align: 'center',
                flex: 2
            }, {
                text: 'Archivos <em class="text-muted">(sólo dev)</em>',
                dataIndex: 'archivos',
                hidden: !App.verbose,
                style: 'text-align: center',
                flex: 3
            }],

            listeners: {
                checkchange: function (node, checked, eOpts) {
                    switch (node.getDepth()) {
                        case 1:
                            node.eachChild(function (route_node) {
                                route_node.set('checked', checked);
                                route_node.eachChild(function (resource_node) {
                                    resource_node.set('checked', checked);
                                });
                            });

                            break;
                        case 2:
                            node.eachChild(function (resource_node) {
                                resource_node.set('checked', checked);
                            });

                            if (checked) {
                                node.parentNode.set('checked', true);
                            } else {
                                node.parentNode.set('checked', !Ext.isEmpty(node.parentNode.findChild('checked', true, true)));
                            }

                            break;
                        case 3:
                            if (checked) {
                                node.parentNode.set('checked', true);
                                node.parentNode.parentNode.set('checked', true);
                            } else {
                                node.parentNode.set('checked', !Ext.isEmpty(node.parentNode.findChild('checked', true, true)));
                                node.parentNode.parentNode.set('checked', !Ext.isEmpty(node.parentNode.parentNode.findChild('checked', true, true)));
                            }
                            break;
                    }

                    if (checked) {
                        node.expand(true);
                    } else {
                        if (node.getDepth() === 1) {
                            node.collapseChildren(true);
                        } else {
                            node.collapse(true);
                        }
                    }
                },

                close: function (treepanel, eOpts) {
                    treepanel.up().close();
                },

                render: function (treepanel) {
                    treepanel.previousSibling('gridpanel').on('selectionchange', function (gridpanel, selected, eOpts) {
                        treepanel.setTitle(Ext.String.format('Recursos {0}', selected.length > 0 ? ' del grupo ' + selected[0].get('nombre') : null));
                    });
                }
            },

            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                id: 'toolbar',

                items: [{
                    text: 'Registrar',
                    id: 'btn_register',
                    tooltip: 'Registra los recursos del grupo seleccionado',
                    disabled: true,
                    hidden: true,
                    glyph: 0xf0c7,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 95,
                    handler: function (button, event) {
                        var orphan_node = null,
                            dependent_of_node = null;

                        treepanel.getStore().each(function (node) {
                            if (node.getDepth() === 1) {
                                node.eachChild(function (route_node) {
                                    route_node.eachChild(function (resource_node) {
                                        if (resource_node.get('checked') && resource_node.get('dependent_of')) {
                                            dependent_of_node = route_node.findChild('nombre', resource_node.get('dependent_of'));
                                            if (dependent_of_node && !dependent_of_node.get('checked')) {
                                                return !(orphan_node = resource_node);
                                            }
                                        }
                                    });
                                    return !orphan_node;
                                });
                            }
                            return !orphan_node;
                        });

                        if (orphan_node) {
                            treepanel.selectPath(dependent_of_node.getPath());
                            App.showAlert(Ext.String.format('El recurso <span class="font-italic font-weight-bold">{0}</span> depende de <span class="font-italic font-weight-bold">{1}</span>.', orphan_node.getPath('nombre').replace('//', ''), dependent_of_node.getPath('nombre').replace('//', '')), 'danger', 5000);
                            return;
                        }

                        var selection = gridpanel.getSelection()[0];

                        Ext.Msg.show({
                            title: '¿Registrar recursos?',
                            message: Ext.String.format('¿Está seguro que desea registrar los recursos del grupo <span class="font-italic font-weight-bold">{0}</span>?', selection.get('nombre')),
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.Msg.QUESTION,
                            minWidth: 200,
                            fn: function (btn) {
                                if (btn === 'yes') {
                                    var url = App.buildURL('admin/recurso/register'),
                                        data = [];

                                    treepanel.getStore().each(function (node) {
                                        if (node.getDepth() === 1) {
                                            node.eachChild(function (route_node) {
                                                if (!route_node.get('checked')) {
                                                    return true;
                                                }

                                                var resources = [];

                                                route_node.eachChild(function (resource_node) {
                                                    if (resource_node.get('checked')) {
                                                        resources.push(Ext.copyTo({}, resource_node.data, ['nombre']));
                                                    }
                                                });

                                                data.push({
                                                    ruta: route_node.get('ruta'),
                                                    recursos: resources
                                                });
                                            });
                                        }
                                    });

                                    App.request('POST', url, {
                                        grupo_id: selection.get('id'),
                                        datos: Ext.encode(data)
                                    });
                                }
                            }
                        });
                    }
                }, '->', {
                    text: 'Seleccionar',
                    tooltip: 'Selecciona todos los recursos',
                    disabled: true,
                    glyph: 0xf14a,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 110,
                    handler: function (button, event) {
                        treepanel.getRootNode().cascadeBy({
                            before: function (node) {
                                if (node.getDepth() !== 0) { // root
                                    node.set('checked', true);
                                    if (node.getDepth() <= 1) {
                                        node.expand();
                                    } else {
                                        node.collapse();
                                    }
                                }
                            }
                        });
                    }
                }, {
                    text: 'Deseleccionar',
                    tooltip: 'Deselecciona todos los recursos',
                    disabled: true,
                    glyph: 0xf0c8,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 120,
                    handler: function (button, event) {
                        treepanel.getRootNode().cascadeBy({
                            before: function (node) {
                                if (node.getDepth() !== 0) { // root
                                    node.set('checked', false);
                                    if (node.getDepth() <= 1) {
                                        node.expand();
                                    } else {
                                        node.collapse();
                                    }
                                }
                            }
                        });
                    }
                }, '-', {
                    text: 'Expandir',
                    tooltip: 'Expande todos los recursos',
                    disabled: true,
                    glyph: 0xf065,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 95,
                    handler: function (button, event) {
                        treepanel.expandAll();
                    }
                }, {
                    text: 'Contraer',
                    tooltip: 'Contrae todos los recursos',
                    disabled: true,
                    glyph: 0xf066,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 95,
                    handler: function (button, event) {
                        treepanel.collapseAll();
                    }
                }, '-', {
                    text: 'Actualizar',
                    tooltip: 'Actualiza el árbol de recursos',
                    disabled: true,
                    glyph: 0xf021,
                    width: 95,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    handler: function (button) {
                        if (!treepanel.getStore().isLoading()) {
                            treepanel.getStore().reload();
                        }
                    }
                }],
                listeners: {
                    render: function (toolbar) {
                        treepanel.getStore().on({
                            beforeload: function () {
                                toolbar.items.each(function (item) {
                                    item.disable();
                                });
                            },
                            load: function (store, records, successful, eOpts) {
                                if (successful) {
                                    toolbar.items.each(function (item) {
                                        item.enable();
                                    });
                                }
                            }
                        });
                    }
                }
            }],

            setBusy: function (busy) {
                var toolbar = this.down('toolbar'),
                    view = this.getView();

                if (busy) {
                    toolbar.mask();
                    view.mask();
                } else {
                    toolbar.unmask();
                    view.unmask();
                }

                return this;
            }
        });

    App.render(Ext.create('Ext.panel.Panel', {
        layout: 'border',
        items: [gridpanel, treepanel]
    }));
});