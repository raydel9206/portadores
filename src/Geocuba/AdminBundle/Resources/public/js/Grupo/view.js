Ext.onReady(function () {
    var gridpanel = Ext.create('Ext.grid.Panel', {
        title: 'Grupos',
        closable: true,

        selModel: {
            mode: 'MULTI'
        },

        viewConfig: {
            emptyText: '<div class="text-center">No existen grupos registrados</div>'
        },

        reserveScrollbar: true,
        scrollable: 'vertical',
        columnLines: true,

        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'grupos_store',
            fields: ['id', 'nombre', 'usuarios'],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/admin/grupo/list'),
                reader: {
                    rootProperty: 'rows'
                },
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
            autoLoad: true
        }),

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
                '            <thead class="text-center">',
                '               <tr>',
                '                   <th scope="col">Usuario</th>',
                '                   <th scope="col">Nombre completo</th>',
                '                   <th scope="col">Email</th>',
                '                   <th scope="col">Creado en</th>',
                '               </tr>',
                '             </thead>',
                '             <tbody>',
                '               <tpl for="usuarios">',
                '                   <tr class="text-center">',
                '                       <td>{usuario}</td>',
                '                       <td>{nombre_completo}</td>',
                '                       <td><a href="mailto:{email}">{email}</a></td>',
                '                       <td>{fecha_creacion}</td>',
                '                    </tr>',
                '                </tpl>',
                '              </tbody>',
                '           </tpl>',
                '       </table>',
                '   </div>',
                '</div>'
            )
        }],

        dockedItems: [{
            xtype: 'toolbar',
            id: 'toolbar',

            dock: 'top',
            hidden: true,

            items: [{
                text: 'Adicionar',
                id: 'btn_add',
                tooltip: 'Adiciona un nuevo grupo',

                glyph: 0xf0fe,
                cls: 'border-secondary',
                iconCls: 'text-dark',
                width: 95,
                disabled: false,

                handler: function (button) {
                    Ext.create('winform').show();
                }
            }, {
                text: 'Modificar',
                id: 'btn_edit',
                tooltip: 'Modifica el grupo seleccionado',

                glyph: 0xf044,
                cls: 'border-secondary',
                iconCls: 'text-dark',
                width: 95,
                disabled: true,

                handler: function (button) {
                    if (!gridpanel.getSelectionModel().hasSelection()) {
                        return;
                    }

                    Ext.create('winform', {record: gridpanel.getSelection()[0]}).show();
                },

                listeners: {
                    render: function (button) {
                        gridpanel.on('selectionchange', function (self, selected, eOpts) {
                            button.setDisabled(selected.length !== 1);
                        });
                    }
                }
            }, {
                text: 'Eliminar',
                id: 'btn_del',
                tooltip: 'Elimina el grupo seleccionado',

                glyph: 0xf2ed,
                cls: 'border-secondary',
                iconCls: 'text-dark',
                width: 95,
                disabled: true,

                handler: function (button) {
                    if (!gridpanel.getSelectionModel().hasSelection()) {
                        return;
                    }

                    var selection = gridpanel.getSelection(),
                        title = selection.length === 1 ? '¿Eliminar grupo?' : '¿Eliminar grupos?',
                        msg_part = selection.length === 1 ? Ext.String.format('el grupo <span class="font-italic font-weight-bold">{0}</span>', selection[0].get('nombre')) : 'los grupos seleccionados',
                        params = selection.length === {id: selection[0].get('id')} ? '' : {
                            ids: selection.map(function (r) {
                                return r.get('id');
                            })
                        };

                    Ext.Msg.show({
                        title: title,
                        message: Ext.String.format('¿Está seguro que desea eliminar {0}?', msg_part),
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                App.request('DELETE', App.buildURL('/admin/grupo/delete'), params, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        gridpanel.getStore().reload();
                                    }
                                });
                            }
                        }
                    });
                },

                listeners: {
                    render: function (button) {
                        gridpanel.on('selectionchange', function (self, selected, eOpts) {
                            button.setDisabled(selected.length === 0);
                        });
                    }
                }
            }],

            setBusy: function (busy) { //toolbar
                var self = this;

                self.items.each(function (item) {
                    item.setDisabled(busy || item.getInitialConfig('disabled') === true);
                });

                return self;
            }
        }, {
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            store: Ext.getStore('grupos_store'),
            displayInfo: true
        }],

        listeners: {
            boxready: function (self) {
                var toolbar = self.down('toolbar'),
                    selection_cache = [],
                    selection_model = self.getSelectionModel();

                self.getStore().on({
                    beforeload: function () {
                        selection_cache = selection_model.getSelection();
                        selection_model.deselectAll();

                        toolbar.setBusy(true);
                    },

                    load: function (store, records, successful, eOpts) {
                        toolbar.setBusy(false);

                        selection_cache = Ext.Array.clean(Ext.Array.map(selection_cache, function (record, index) { // update the selected record
                            return store.getById(record.getId());
                        }));
                        selection_model.select(selection_cache);
                    }
                });
            }
        }
    });

    App.render(gridpanel);
});