/**
 * Created by pfcadenas on 11/11/2016.
 */


Ext.onReady(function () {

    var cmbSearch = Ext.create('Ext.form.field.ComboBox', {
        width: 100,
        id: 'opcion',
        name: 'opcion',
        emptyText: 'Mes...',
        store: {
            xtype: 'Ext.data.Store',
            storeId: 'id_store_unidad',
            fields: [
                {name: 'id'},
                {name: 'nombre'}
            ],
            data: [
                {"id": "0", "nombre": "Anual"},
                {"id": "1", "nombre": "Enero"},
                {"id": "2", "nombre": "Febrero"},
                {"id": "3", "nombre": "Marzo"},
                {"id": "4", "nombre": "Abril"},
                {"id": "5", "nombre": "Mayo"},
                {"id": "6", "nombre": "Junio"},
                {"id": "7", "nombre": "Julio"},
                {"id": "8", "nombre": "Agosto"},
                {"id": "9", "nombre": "Septiembre"},
                {"id": "10", "nombre": "Octubre"},
                {"id": "11", "nombre": "Noviembre"},
                {"id": "12", "nombre": "Diciembre"},
            ]
        },
        displayField: 'nombre',
        valueField: 'id',
        typeAhead: true,
        queryMode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        selectOnFocus: true,
        editable: true,
        value: '0'
    });

    var _btnPrint = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_combustible_btn_print',
        text: 'Imprimir',
        iconCls: 'fas fa-print text-primary',
        // disabled: true,
        handler: function (This, e) {
            console.log()
            if (!Ext.getCmp('nTipoCombustibleId').getValue()){
                App.showAlert('Seleccione un tipo de combustible', 'warning');
            }else if(!Ext.getCmp('fieldMesId').getValue()){
                App.showAlert('Seleccione un Mes', 'warning');
            }
            else {
                var obj = {};
                obj.opcion = Ext.getCmp('fieldMesId').getStore().findRecord('min', Ext.getCmp('fieldMesId').getValue()).data.id;
                obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                obj.tipoCombustible = Ext.getCmp('nTipoCombustibleId').getValue();
                obj.anno = Ext.getCmp('fieldAnnoId').getValue();
                App.request('GET', App.buildURL('/portadores/plan_combustible/print'), obj, null, null,
                    function (response) { // success_callback
                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                            var newWindow = window.open('', '', 'width=1200, height=700'),
                                document = newWindow.document.open();
                            document.write(response.html);
                            setTimeout(() => {
                                newWindow.print();
                            }, 500);
                            document.close();
                        }
                    },
                    function (response) { // failure_callback
                    }
                );
            }

        }
    });

    var _btnExport = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_combustible_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        // disabled: true,
        handler: function (This, e) {
            var obj = {};
            obj.opcion = Ext.getCmp('fieldMesId').getStore().findRecord('min', Ext.getCmp('fieldMesId').getValue()).data.id;
            obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
            obj.tipoCombustible = Ext.getCmp('nTipoCombustibleId').getValue();
            obj.anno = Ext.getCmp('fieldAnnoId').getValue();
            App.request('GET', App.buildURL('/portadores/plan_combustible/print'), obj, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                    }
                },
                function (response) { // failure_callback
                }
            );
        }
    });

    var _tbar = Ext.getCmp('planificacion_combustible_tbar');
    _tbar.add('->');
    _tbar.add(_btnPrint);
    _tbar.add(_btnExport);
});
