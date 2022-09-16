/**
 * Created by yosley on 08/03/2016.
 */
Ext.onReady(function () {
console.log(App.current_year)
    // var cerrarPeriodo= function(){
        Ext.MessageBox.confirm('Confirmaci&oacute;n', 'Está usted seguro de cerrar el Período actual: ' + App.getMonthName(App.current_month) + ' del ' + App.current_year, function (btn) {
            if (btn == 'yes'){
                var result = App.request('GET',App.buildURL('/portadores/cerrarperiodo/cierre'));
                if(result.success){
                    console.log('si')
                    App.showAlert('Período Cerrado Correctamente','success');
                    App.showWindowPeriodo();
                }
                else
                {
                    console.log('no')
                    App.showAlert(result.message, result.cls);
                }

            }
        });
    // }
})
