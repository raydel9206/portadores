/**
 * Created by yosley on 13/10/2015.
 */



var _storem = Ext.create('Ext.data.JsonStore',{
    storeId: 'id_store_targeta_combustiK',
    fields: [
        { name: 'id'},
        { name: 'nro_tarjeta'}
    ],
    proxy: {
        type: 'ajax',
        url: Routing.generate('loadTarjeta'),
        reader: {
            rootProperty: 'rows'
        }
    },
    autoLoad: true
});

var _storem = Ext.create('Ext.data.JsonStore',{
    storeId: 'id_store_anexo_combustiK',
    fields: [
        { name: 'id'},
        { name: 'nro_tarjeta'}
    ],
    proxy: {
        type: 'ajax',
        url: Routing.generate('loadTarjeta'),
        reader: {
            rootProperty: 'rows'
        }
    },
    autoLoad: true
});






Ext.define('Portadores.tarjeta.Window',{
    extend: 'Ext.window.Window',
    //width: 700,
    //height: 390,
    modal:true,
    plain:true,
    resizable : false,

    initComponent: function(){
        this.items = [
            {
                xtype: 'form',
                frame: true,
                //width: 700,
                //height: 390,
                width: '100%',
                height: '100%',
                // defaultType: 'textfield',
                //  bodyPadding: 5,
                //  bodyPadding: 5,
                layout: 'column',
                bodyStyle: 'padding:5px 5px 0',
                items:[{

                    xtype:'fieldset',
                    columnWidth: 0.5,
                    // margin: '0 0 0 10',
                    margin: '10 10 10 10',
                    title: ' ',
                    collapsible: false,

                    defaults: {anchor: '100%'},
                    layout: 'anchor',
                    items:[

                        {
                            xtype : 'datefield',
                            // grow: true,
                            anchor: '100%',
                            name: 'fecha',
                            id:'fecha',
                            fieldLabel: 'Fecha',
                            allowBlank : true

                        },
                        {
                            xtype:'numberfield',
                            name: 'kilometraje',
                            id:'kilometraje',
                            fieldLabel: 'Kilometraje',
                            value:0,
                            minValue:0,
                            allowBlank: false // requires a non-empty value
                            //  maskRe: /[0-9]/
                        },

                        {
                            xtype:'numberfield',
                            name: 'combustible_abastecido',
                            id:'combustible_abastecido',
                            fieldLabel: 'Combustible Abastecido',
                            value:0,
                            minValue:0,
                            allowBlank: false // requires a non-empty value
                            //  maskRe: /[0-9]/
                        },
                        {
                            xtype:'numberfield',
                            name: 'combustible_estimado_tanque',
                            id:'combustible_estimado_tanque',
                            fieldLabel: 'Combustible Estimado Tanque ',
                            value:0,
                            minValue:0,
                            allowBlank: false // requires a non-empty value
                            //  maskRe: /[0-9]/
                        },


                        {
                            xtype: 'combobox',
                            name: 'ntarjetaid',
                            id: 'ntarjetaid',
                            fieldLabel: 'Seleccione la tarjeta',
                            labelWidth:140,
                            store: Ext.getStore('id_store_targeta_combustiK'),
                            displayField: 'nombre',
                            valueField: 'id',
                            typeAhead: true,
                            //queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione la tarjeta...',
                            selectOnFocus: true,
                            editable: false,
                            allowBlank: false
                        },
                        {
                            xtype: 'combobox',
                            name: 'anexo_unicoid',
                            id: 'anexo_unicoid',
                            fieldLabel: 'Seleccione el anexo',
                            labelWidth:140,
                            store: Ext.getStore('id_store_anexo_combustiK'),
                            displayField: 'nombre',
                            valueField: 'id',
                            typeAhead: true,
                            //queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione el anexo...',
                            selectOnFocus: true,
                            editable: false,
                            allowBlank: false
                        }


                    ]
                }

                   ]

            }
        ];

        this.callParent();
    }
});