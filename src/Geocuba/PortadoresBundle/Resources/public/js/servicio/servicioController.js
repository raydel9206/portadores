/**
 * Created by yosley on 02/11/2015.
 */

Ext.onReady(function(){
    var tree_store = Ext.create('Ext.data.TreeStore', {
        id: 'store_unidades',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'nombre', type: 'string'},
            {name: 'siglas', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'municipio', type: 'string'},
            {name: 'municipio_nombre', type: 'string'},
            {name: 'provincia', type: 'string'},
            {name: 'provincia_nombre', type: 'string'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad/loadTree'),
            reader: {
                type: 'json',
                rootProperty: 'children'
            }
        },
        sorters: 'nombre',
        listeners: {
            beforeload: function () {
                if (Ext.getCmp('arbolunidades') !== undefined)
                    Ext.getCmp('arbolunidades').getSelectionModel().deselectAll();
            }
        }
    });

    var panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        id: 'arbolunidades',
        hideHeaders: true,
        border: true,
        rootVisible: false,
        collapsible: true,
        collapsed: false,
        collapseDirection: 'left',
        header: {             style: {                 backgroundColor: 'white',                 borderBottom: '1px solid #c1c1c1 !important'             },         },
        layout: 'fit',

        columns: [
            {xtype: 'treecolumn', iconCls: Ext.emptyString, width: 450, dataIndex: 'nombre'}
        ],
        root: {
            text: 'root',
            expanded: true
        },
        listeners: {
            select: function (This, record, tr, rowIndex, e, eOpts) {
                grid_servicios.enable();
                grid_servicios.getStore().loadPage(1);
            }
        }
    });
    var store_servicios = Ext.create('Ext.data.JsonStore',{
        storeId: 'storeserviciosId',
        fields: [
            { name: 'id'},
            { name: 'nombre_servicio'},
            { name: 'codigo_cliente'},
            { name: 'factor_metrocontador'},
            { name: 'MaximaDemandaContratada'},
            { name: 'control'},
            { name: 'ruta'},
            { name: 'folio'},
            { name: 'direccion'},
            { name: 'factor_combustible'},
            { name: 'capac_banco_transf'},
            { name: 'capac_banco_transf_capac'},
            { name: 'tipo_servicio'},
            { name: 'turno_trabajo'},
            { name: 'id_turno_trabajo'},
            { name: 'nunidadid'},
            { name: 'nombreunidadid'},
            { name: 'provicianid'},
            { name: 'nombreprovicianid'},
            { name: 'tarifaid'},
            { name: 'nombretarifaid'},
            { name: 'nactividadid'},
            { name: 'nombrenactividadid'},
            { name: 'num_nilvel_actividadid'},
            { name: 'nombreum_nilvel_actividadid'},
            { name: 'numero'},
            { name: 'servicio_mayor'},
            { name: 'servicio_prepago'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/servicio/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                grid_servicios.getSelectionModel().deselectAll();
                operation.setParams({
                    nombre: find_button.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });
    var find_button = Ext.create('Ext.form.field.Text', {
        emptyText: 'Buscar...',
        width: 250,
        listeners: {
            render: function (field) {
                Ext.getCmp('gridserviciosId').getStore().on({
                    beforeload: function (store, operation, eOpts) {
                        if (field.marked) {
                            var value = field.getValue();
                            if (!Ext.isEmpty(Ext.String.trim(value))) {
                                operation.setParams({
                                    nombre_servicio: value
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
                        Ext.getCmp('gridserviciosId').getStore().loadPage(1);
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
                    Ext.getCmp('gridserviciosId').getStore().loadPage(1);
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
                        if (Ext.getCmp('gridserviciosId').getStore().getCount() > 0)
                            Ext.getCmp('gridserviciosId').getStore().loadPage(1, {params: {nombre_servicio: value}});
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
                        Ext.getCmp('gridserviciosId').getStore().loadPage(1);
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

    var grid_servicios = Ext.create('Ext.grid.Panel',{
        id: 'gridserviciosId',
        region:'center',
        width:'75%',
        disabled:true,
        store:store_servicios ,
        columns: [
            { text: '<strong>Nombre servicio</strong>',
                dataIndex: 'nombre_servicio',
                filter: 'string',
                flex: 1},
            { text: '<strong>Tipo servicio</strong>',
                dataIndex: 'tipo_servicio',
                filter: 'string',
                flex: 1,
                renderer: function (value) {
                    if(value === '1')
                        return 'Monofásicos';
                    else
                        return 'Trifásicos';
                }
            },
            { text: '<strong>Número de servicio</strong>',
                dataIndex: 'numero',
                filter: 'string',
                flex: 1},
            { text: '<strong>Maxima Demanda Contratada</strong>',
                dataIndex: 'MaximaDemandaContratada',
                filter: 'string',
                flex: 1},
            { text: '<strong>Servicio Mayor</strong>',
                dataIndex: 'servicio_mayor',
                filter: 'bolean',
                flex: 1,
                renderer: function(value){
                    if (value == true)
                        return '<div class="badge-true">SI</div>';
                    else
                        return '<div class="badge-false">NO</div>';
                }
            } ,{ text: '<strong>Servicio Prepago</strong>',
                dataIndex: 'servicio_prepago',
                filter: 'bolean',
                flex: 1,
                renderer: function(value){
                    if (value == true)
                        return '<div class="badge-true">SI</div>';
                    else
                        return '<div class="badge-false">NO</div>';
                }
            }
        ],
        tbar: {
            id: 'servicios_tbar',
            height: 36,
            items: [ find_button, '-' ]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeserviciosId'),
            displayInfo: true,
        },


        enableLocking: true,
        plugins: [{
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
                '                   <th scope="col">Codigo Cliente:</th>',
                '                   <th scope="col">Control:</th>',
                '                   <th scope="col">Ruta:</th>',
                '                   <th scope="col">Folio:</th>',
                '                   <th scope="col">Direccion:</th>',
                '                   <th scope="col">Factor combustible:</th>',
                '                   <th scope="col">Capacidad banco:</th>',
                '                   <th scope="col">Turno Trabajo:</th>',
                '                   <th scope="col">Tarifa:</th>',
                '               </tr>',
                '             </thead>',
                '             <tbody>',
                '               <tpl>',
                '                   <tr class="text-center">',
                '                       <td>{codigo_cliente}</td>',
                '                       <td>{control}</td>',
                '                       <td>{ruta}</td>',
                '                       <td>{folio}</td>',
                '                       <td>{direccion}</td>',
                '                       <td>{factor_combustible}</td>',
                '                       <td>{capac_banco_transf_capac}</td>',
                '                       <td>{id_turno_trabajo}</td>',
                '                       <td>{nombretarifaid}</td>',
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
            selectionchange: function(This, selected, e){
                Ext.getCmp('servicios_tbar').items.each(
                    function(item, index, length){
                        item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            },
        }
    });
    var _panelservicios = Ext.create('Ext.panel.Panel',{
        id: 'manage_servicios_panel_id',
        title: 'Servicios',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '1 0 0',
        items:[panetree, grid_servicios]
    });
    App.render(_panelservicios);
});