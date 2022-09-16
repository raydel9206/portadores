/**
 * Created by kireny on 3/10/16.
 */

Ext.onReady(function () {
    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('id_grid_responsabilidad_Acta_Material').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre: value,
                                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
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
                        Ext.getCmp('id_grid_responsabilidad_Acta_Material').getStore().loadPage(1);
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
                    Ext.getCmp('id_grid_responsabilidad_Acta_Material').getStore().loadPage(1);
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
                        if (Ext.getCmp('id_grid_responsabilidad_Acta_Material').getStore().getCount() > 0)
                            Ext.getCmp('id_grid_responsabilidad_Acta_Material').getStore().loadPage(1);
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
                        Ext.getCmp('id_grid_responsabilidad_Acta_Material').getStore().loadPage(1);
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

    var grid_responsabilidad_Acta_Material = Ext.create('Ext.grid.Panel', {
        id:'id_grid_responsabilidad_Acta_Material',
        width: '75%',
        region: 'center',
        store:Ext.create('Ext.data.JsonStore', {
            storeId:'id_store_responsabilidad_Acta_Material',
            fields:[
                { name:'id'},
                { name:'nombre'}
            ],
            proxy:{
                type:'ajax',
                url: App.buildURL('/portadores/responsabilidad/load'),
                reader:{
                    rootProperty:'rows'
                }
            },
            autoLoad:true,
            listeners:{
                beforeload:function (This, operation, eOpts) {
                    Ext.getCmp('id_grid_responsabilidad_Acta_Material').getSelectionModel().deselectAll();
                    operation.setParams({
                        nombre:find_button.getValue()
                    });
                }
            }
        }),
        columns:[
            { text:'<strong>Nombre</strong>', dataIndex:'nombre', filter:'string', flex:1}
        ],
        selModel: {
            mode: 'MULTI'
        },
        tbar:{
            id:'responsabilidad_Acta_Material_tbar',
            height:36,
            items:[ find_button, '-']
        },
        bbar:{
            xtype:'pagingtoolbar',
            pageSize:25,
            store:Ext.getStore('id_store_responsabilidad_Acta_Material'),
            displayInfo:true,
        },
        plugins: ['gridfilters',{
            ptype: 'rowexpander',
            rowBodyTpl: new Ext.XTemplate(
                '<div class="card p-1">',
                '   <div class="card">',
                '       <tpl>',
                '           <div class="card-header text-center">',
                '               <strong>Otros datos de interés</strong> <em class="text-muted"></em>',
                '           </div>',
                '       </tpl>',
                '       <table class="table table-bordered table-hover table-responsive-md mb-0">',
                '           <tpl if="Ext.isEmpty(id)">',
                '               <tr class="text-center">',
                '                   <td colspan="4"><span class="badge badge-secondary">No tiene mantenimientos asociados</span></td>',
                '                </tr>',
                '            <tpl else>',
                '            <thead class="text-center">',
                '               <tr>',
                '                   <th scope="col">Nombre:</th>',
                '               </tr>',
                '             </thead>',
                '             <tbody>',
                '               <tpl>',
                '                   <tr class="text-center">',
                '                       <td>{nombre}</td>',
                '                    </tr>',
                '                </tpl>',
                '              </tbody>',
                '           </tpl>',
                '       </table>',
                '   </div>',
                '</div>'
            )
        }],
        listeners:{
            selectionchange:function (This, selected, e) {
                if(Ext.getCmp('responsabilidadActaMaterial_btn_mod'))
                Ext.getCmp('responsabilidadActaMaterial_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('responsabilidadActaMaterial_btn_del'))
                Ext.getCmp('responsabilidadActaMaterial_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var panel_responsabilidad_Acta_Material = Ext.create('Ext.panel.Panel', {
        id:'id_panel_responsabilidad_Acta_Material',
        title:'Responsabilidades del Acta Material',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '2 0 0',
        items:[grid_responsabilidad_Acta_Material]
    });

    App.render(panel_responsabilidad_Acta_Material);
});