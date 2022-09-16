Ext.onReady(function () {
    Ext.create('Ext.data.JsonStore', {
        storeId: 'points_store',
        fields: ['id', 'point_name'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/gis/poi/list'),
            reader: {rootProperty: 'rows'},
            listeners: {exception: exception_handler}
        },
        autoLoad: false,
        autoDestroy: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'routes_store',
        fields: ['gid', 'route_name', {
            name: 'metadata', type: 'auto'
        }, {
            name: 'distance', type: 'number'
        }],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/gis/route/list'),
            reader: {rootProperty: 'rows'},
            listeners: {exception: exception_handler}
        },
        autoLoad: false,
        autoDestroy: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'tables_store',
        fields: ['id', 'name', {
            name: 'points', type: 'auto'
        }, {
            name: 'count', calculate: function (data) {
                return Ext.Object.getKeys(data.points).length;
            }
        }],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/gis/table/list'),
            reader: {rootProperty: 'rows'},
            listeners: {exception: exception_handler}
        },
        autoLoad: false,
        autoDestroy: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'states_store',
        fields: ['id', 'nombre'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/gis/state/list'),
            reader: {rootProperty: 'rows'},
            listeners: {exception: exception_handler}
        },
        autoLoad: false,
        autoDestroy: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'localities_store',
        fields: ['id', 'nombre'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/gis/locality/list'),
            reader: {rootProperty: 'rows'},
            listeners: {exception: exception_handler}
        },
        autoLoad: false,
        autoDestroy: false
    });

    // Ext.create('Ext.data.JsonStore', {
    //     storeId: 'layers_store',
    //     fields: ['layer', 'active'],
    //     proxy: {
    //         type: 'memory',
    //         reader: {
    //             type: 'json',
    //             rootProperty: 'rows'
    //         }
    //     },
    //     autoLoad: false,
    //     autoDestroy: false
    // });

    Ext.create('Ext.data.TreeStore', {
        storeId: 'layers_store',
        fields: ['layer', 'active'],
        proxy: {
            type: 'memory',
        },
        autoLoad: false,
        autoDestroy: false
    });
});

function exception_handler(proxy, request, operation) {
    if (!request.aborted) {
        const message = request.hasOwnProperty('statusText') ? request.statusText : 'La solicitud no tiene respuesta disponible',
            error = request.hasOwnProperty('responseText') ? ':<br><em><small>' + App.parseError(request.responseText) + '</small></em>' : '';

        App.showAlert(message + error, 'danger', 10000);
    }
}