/**
 * Created by yosley on 08/03/2016.
 */
Ext.onReady(function () {
    Ext.MessageBox.confirm('Confirmaci&oacute;n', 'Está usted seguro de cerrar el Período actual: ' + App.getMonthName(App.current_month) + ' del ' + App.current_year, function (btn) {
        if (btn === 'yes') {
            App.request('GET', App.buildURL('/portadores/cerrarperiodo/cierre'), null, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        App.showAlert('Período Cerrado Correctamente', 'success');
                        App.showWindowPeriodo();
                    } else {
                        if (response && response.hasOwnProperty('errors') && response.errors) {
                            App.showAlert(response.message, result.cls);
                        }
                    }
                }, function (response) { // failure_callback
                });
        }
    });
    // }
})
