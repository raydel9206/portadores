Ext.onReady(function () {

    let mes_habil = 0,
        anno_habil = 0;

    Ext.define('Ext.button.MyButton', {
        extend: 'Ext.button.Button',
        // override: 'Ext.button.Button',

        enable: function (silent, fromParent) {
            if (!fromParent) {
                this.callParent(arguments);
            }
        },


        disable: function (silent, fromParent) {
            if (!fromParent) {
                this.callParent(arguments);
            }
        }
    });

    Ext.define('Ext.form.field.Month', {
        extend: 'Ext.form.field.Date',
        alias: 'widget.monthfield',
        requires: ['Ext.picker.Month'],
        alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
        selectMonth: null,
        grid: null,
        createPicker: function () {
            let me = this,
                format = Ext.String.format,
                pickerConfig;

            pickerConfig = {
                pickerField: me,
                ownerCmp: me,
                renderTo: document.body,
                floating: true,
                hidden: true,
                focusOnShow: true,
                minDate: me.minValue,
                maxDate: me.maxValue,
                disabledDatesRE: me.disabledDatesRE,
                disabledDatesText: me.disabledDatesText,
                disabledDays: me.disabledDays,
                disabledDaysText: me.disabledDaysText,
                format: me.format,
                showToday: me.showToday,
                startDay: me.startDay,
                minText: format(me.minText, me.formatDate(me.minValue)),
                maxText: format(me.maxText, me.formatDate(me.maxValue)),
                listeners: {
                    select: {scope: me, fn: me.onSelect},
                    monthdblclick: {scope: me, fn: me.onOKClick},
                    yeardblclick: {scope: me, fn: me.onOKClick},
                    OkClick: {scope: me, fn: me.onOKClick},
                    CancelClick: {scope: me, fn: me.onCancelClick}
                },
                keyNavConfig: {
                    esc: function () {
                        me.collapse();
                    }
                }
            };

            if (Ext.isChrome) {
                me.originalCollapse = me.collapse;
                pickerConfig.listeners.boxready = {
                    fn: function () {
                        this.picker.el.on({
                            mousedown: function () {
                                this.collapse = Ext.emptyFn;
                            },
                            mouseup: function () {
                                this.collapse = this.originalCollapse;
                            },
                            scope: this
                        });
                    },
                    scope: me,
                    single: true
                }
            }

            return Ext.create('Ext.picker.Month', pickerConfig);
        },
        onCancelClick: function () {
            let me = this;
            me.selectMonth = null;
            me.collapse();
        },
        onOKClick: function () {
            let me = this;
            if (me.selectMonth) {
                me.setValue(me.selectMonth);
                me.fireEvent('select', me, me.selectMonth);
            }
            me.collapse();
            me.grid.getSelectionModel().deselectAll();
            me.grid.getStore().load();
        },
        onSelect: function (m, d) {
            let me = this;
            me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
        }
    });

    Ext.define('Ext.form.field.SearchText', {
        extend: 'Ext.form.field.Text',
        emptyText: 'Buscar...',
        width: 150,
        grid: null,
        listeners: {
            change: function (field, newValue) {
                field.getTrigger('clear').setVisible(newValue);
                if (Ext.isEmpty(Ext.String.trim(field.getValue()))) {
                    let marked = field.marked;
                    field.setMarked(false);

                    if (marked) {
                        this.grid.getStore().loadPage(1);
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
                let value = field.getValue();

                if (!Ext.isEmpty(Ext.String.trim(value)) && e.getKey() === e.ENTER) {
                    field.setMarked(true);
                    this.grid.getStore().loadPage(1);
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
                    let value = this.getValue();
                    if (!Ext.isEmpty(Ext.String.trim(value))) {
                        this.setMarked(true);
                        // if (this.grid.getStore().getCount() > 0)
                        let params = {};
                        params[this.nameValue] = value;
                        this.grid.getStore().loadPage(1, {params});
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
                        this.grid.getStore().loadPage(1);
                    }
                    this.setMarked(false);
                }
            }
        },

        setMarked: function (marked) {
            let el = this.getEl(),
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

    const CONSTANTS = {
        DATE_FORMAT: 'd/m/Y',
        DATETIME_FORMAT: 'd/m/Y H:i',

        /***************************************************************************************************************
         * Notificaciones
         **************************************************************************************************************/
        NOTIFICACION_GLOBAL: 1,
        NOTIFICACION_GRUPO: 2,
        NOTIFICACION_USUARIO: 3,

        NOTIFICACIONES: [
            'GLOBAL',
            'GRUPO',
            'USUARIO'
        ],

        /*******************************************************************************************************************
         * Eventos
         ******************************************************************************************************************/
        EVENTO_INSERT: 1,
        EVENTO_UPDATE: 2,
        EVENTO_DELETE: 3,

        EVENTOS: {
            1: 'INSERT',
            2: 'UPDATE',
            3: 'DELETE'
        },

        /*******************************************************************************************************************
         * FORMATOS
         ******************************************************************************************************************/
        FORMATO_PDF: 1,
        FORMATO_EXCEL: 2,
        FORMATO_WORD: 3,
        FORMATO_PNG: 4,
    };

    const App = {
        initialize: function (params) {
            let start = performance.now();

            Object.assign(this, this, params);

            this.current_year = params.current_year;
            this.current_month = params.current_month;

            this.selected_year = params.selected_year;
            this.selected_month = params.selected_month;
            this.min_month = params.min_month;
            this.min_year = params.min_year;

            this.interval_handler_key = -1;
            this.timeout_handler_key = -1;
            this.templates = [];
            this.container = Ext.get('container');

            this
                .debug('You are in dev mode.')
                .debug(' > User:', '"' + this.user.username + '"')
                .debug(' > Route:', this.route ? '"' + this.route + '".' : null)
                .debug(' > Module:', (this.module ? '"' + this.module + '".' : null))
                .debug(' > Base path:', '"' + this.base_url + '".')
                .debug(' > Notifications:', this.notifications, '(query interval is', this.notifications_interval, 'sec).')
                .debug(' > Libraries:', ['ExtJS@' + Ext.getVersion('extjs').version + (Ext.getVersion('extjs').major > 5 ? ('(' + Ext.theme.name + ')') : ''), 'JQuery@' + $.fn.jquery, 'Bootstrap@' + $.fn.tooltip.Constructor.VERSION] + '.')
                .debug(' > Año:', this.current_year)
                .debug(' > Mes:', this.current_month)
                .debug(' > Año Seleccionado:', this.selected_year)
                .debug(' > Mes Seleccionado:', this.selected_month)
                .debug(' > Año Min:', this.min_month)
                .debug(' > Mes Max:', this.min_year);

            this
                .buildTemplates()
                .configureAlerts()
                .buildNavbar()
                .buildUserInfo()
                .buildNotifier()
                .startNotifierTask()
                .buildReloader()
                .buildHelper()
                .configureExtJS();

            if (!this.verbose && this.session_timeout) {
                let self = this;
                $.idleTimer(this.session_timeout * 1000).on("idle.idleTimer", function (event, elem, obj, triggerevent) {
                    window.location.href = self.buildURL('logout?expired=true&timeout=' + self.session_timeout);
                });
            }

            if (this.module === 'Portadores') {
                if (this.selected_year === null || this.selected_year === 0) {
                    this.selected_year = this.current_year;
                    this.selected_month = this.current_month;
                    this.showWindowPeriodo();
                }
            }

            this.debug('App was initialized in', (performance.now() - start).toFixed(2), 'ms.');
        },

        showWindowPeriodo: function () {

            var _window = Ext.create('Ext.window.Window', {
                modal: true,
                onEsc: Ext.emptyFn,
                closable: false,
                title: 'Seleccionar per&iacute;odo',
                items: [
                    {
                        xtype: 'form',
                        layout: 'column',
                        id: 'form_periodo_id',
                        bodyPadding: 10,
                        standardSubmit: true,
                        items: [
                            {
                                xtype: 'monthpicker',
                                showButtons: false,
                                id: 'periodo',
                                name: 'periodo',
                                editable: false,
                                minDate: new Date(1 + '/' + 12 + '/' + 2018),
                                listeners: {
                                    select: function (This, value, eOpts) {
                                        console.log(value[1]);
                                        let flag = false;
                                        // if (value[1] < anno_habil) {
                                        //     App.showAlert('No puede seleccionar períodos vencidos', 'warning');
                                        //     Ext.getCmp('btn_acept').setDisabled(true);
                                        //     flag = true;
                                        // } else {
                                        //     Ext.getCmp('btn_acept').setDisabled(false);
                                        // }

                                        if (value[0] + 1 > mes_habil && value[1] === anno_habil) {
                                            App.showAlert('No puede seleccionar períodos posteriores', 'warning');
                                            Ext.getCmp('btn_acept').setDisabled(true);
                                            flag = true;
                                        } else {
                                            Ext.getCmp('btn_acept').setDisabled(false);
                                        }
                                        Ext.getCmp('btn_acept').setDisabled(flag);
                                    }
                                }
                            }
                        ]
                    }
                ],
                listeners: {
                    afterrender: function (This) {
                        App.request('GET', App.buildURL('/portadores/utiles/getCurrentPeriodo'), [], null, null,
                            function (response) { // success_callback
                                mes_habil = response.mes;
                                anno_habil = response.anno;
                                Ext.getCmp('periodo').setValue([response.mes - 1, response.anno]);
                            },
                            function (response) { // failure_callback
                                _window.show();
                            }, null, false, _window);


                    },
                    scope: this
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        id: 'btn_acept',
                        handler: function () {
                            var params = {};
                            params.mes_seleccionado = Ext.getCmp('periodo').getValue()[0];
                            params.anno_seleccionado = Ext.getCmp('periodo').getValue()[1];
                            var url = App.buildURL('/portadores/utiles/cambiarPeriodo');
                            _window.hide();

                            App.request('POST', url, params, null, null,
                                function (response) { // success_callback
                                    _window.close();
                                    if (App.selected_year !== 0) {
                                        App.reset();
                                    }
                                },
                                function (response) { // failure_callback
                                    _window.show();
                                }, null, true);
                        }
                    },
                ]
            });
            _window.show();
        },

        // cerrarPeriodo: function () {
        //     var tree_store = Ext.create('Ext.data.TreeStore', {
        //         id: 'store_unidad',
        //         fields: [
        //             {name: 'id', type: 'string'},
        //             {name: 'nombre', type: 'string'},
        //             {name: 'siglas', type: 'string'}
        //         ],
        //         proxy: {
        //             type: 'ajax',
        //             url: App.buildURL('/portadores/utiles/loadTree'),
        //             reader: {
        //                 type: 'json',
        //             }
        //         },
        //         root: {
        //             expanded: true,
        //             children: []
        //         },
        //         autoLoad: false,
        //         listeners: {
        //             beforeload: function (This, operation) {
        //                 operation.setParams({
        //                     unidad_id: App.user.unidad,
        //                     checked: true
        //                 });
        //             }
        //         }
        //     });
        //
        //     var params = {};
        //     Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Está usted seguro de cerrar el Período actual: ' + App.getMonthName(App.selected_month) + ' del ' + App.selected_year + '? <br> Una ves realizada la acción, no podrá modificar los datos del mismo',
        //         function (btn) {
        //             if (btn === 'yes') {
        //                 App.request('GET', App.buildURL('/portadores/utiles/cerrarPeriodo'), params, null, null,
        //                     function (response) { //
        //                         if(response.success()){
        //                             App.showAlert('Período Cerrado Correctamente', 'success');
        //                             App.showWindowPeriodo();
        //                         }else{
        //                             App.showAlert(response.message, 'error');
        //                         }
        //                     },
        //                     function (response) { // failure_callback
        //                         App.showAlert(response.message, 'error');
        //                     }, null);
        //             }
        //         });
        // },

        FilterStore: function (DataStore, Params) {
            var _url = DataStore.getProxy().url;
            var _index = _url.indexOf('?', 0);
            _url = (_index !== -1) ? _url.substr(0, _index) : _url;
            var _params = Ext.Object.toQueryString(Params);
            _url += '?' + _params;

            DataStore.getProxy().url = _url;
        },

        round: function (value, precision) {
            var result = Number(value);
            if (typeof precision === 'number') {
                precision = Math.pow(10, precision);
                result = Math.round(value * precision) / precision;
            }
            return result;
        },

        getMonthName: function (monthNumber) {
            if (monthNumber >= 0 && monthNumber <= 12) {
                var arr = new Array('Año', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
                return arr[monthNumber];
            }
            return 'Error, parámetro incorrecto';
        },

        getDaysInMonth: function (num_anno, num_mes) {
            var temp = null;
            if (num_mes === 1 || num_mes === 3 || num_mes === 5 || num_mes === 7 || num_mes === 8 || num_mes === 10 || num_mes === 12)
                temp = 31;
            else if (num_mes === 4 || num_mes === 6 || num_mes === 9 || num_mes === 11)
                temp = 30;
            else {
                if ((num_anno % 100 !== 0 || num_anno % 400 === 0) && num_anno % 4 === 0)
                    temp = 29;
                else
                    temp = 28;
            }

            return temp;
        },

        getDateInWeek: function (_dateI) {
            var temp = _dateI.split('/');
            var _year = temp[2];
            var _month = temp[1];
            var _day = temp[0];

            var jsDate = new Date(_year, _month - 1, _day);

            var dia = (7 - (parseFloat(jsDate.getDay()) + 1) + parseFloat(jsDate.getDate())).toString();
            var fecha = '';
            if (parseFloat(dia) > App.getDaysInMonth(_year, _month))
                fecha = App.getDaysInMonth(_year, _month) + '/' + _month + '/' + _year;
            else
                fecha = ((dia < 10) ? ('0' + dia) : dia) + '/' + _month + '/' + _year;

            return fecha;
        },

        configureExtJS: function () {
            var self = this;

            Ext.Ajax.addListener('beforerequest', function (connection) {     // Ext.Ajax.setExtraParams({view_id: App.route});
                // App.closeAlert();
                if (self.route) {
                    connection.setExtraParams({view_id: self.route});
                }
            });

            Ext.Ajax.setTimeout(this.ajax_timeout * 1000); // ExtJS defaults is 30000 ms (30 sec).

            Ext.setGlyphFontFamily('Font Awesome\\ 5 Free'); // http://extjs.eu/using-font-icons-in-ext-fontawesome/ TODO: Font Awesome 5 Solid

            Ext.tip.QuickTipManager.init();

            Ext.util.Format.decimalSeparator = '.';
            Ext.util.Format.thousandSeparator = ',';

            Ext.define('', {
                    override: 'Ext.grid.RowEditor',
                    initComponent: function () {
                        this.callParent();
                    },
                    saveBtnText: 'Aceptar',
                    cancelBtnText: 'Cancelar'
                }
            );
        },

        render: function (component) {
            $('#container').children().hide(); // $('#logo').hide();

            let self = this;
            component.on('destroy', function () {
                // console.log(component.propagate);
                if (component.propagate === true || component.propagate === undefined) {
                    self.reset();
                }
            });

            if (this.container) {
                component.setSize(this.container.getWidth(), this.container.getHeight());
                component.render(this.container.getId());
            }

            return this;
        },

        isBusy: function () {
            return !Ext.isEmpty(Ext.data.StoreManager.findBy(function (store, store_id) {
                return (!store.isEmptyStore && store_id !== 'notifications') ? store.isLoading() : false;
            }));
        },

        request: function (method, url, params, start_callback, complete_callback, success_callback, failure_callback, options, silent, component) {
            if (component) {
                component.mask('Loading...');
            }
            else if (typeof component !== 'boolean') {
                this.mask();
            }

            if (typeof start_callback === 'function') {
                start_callback.call(this);
            }

            let _params = {};
            Ext.Object.each(params, function (key, value) {
                if (Ext.isArray(value)) {
                    Ext.each(value, function (item, index) {
                        _params[key + '[' + index + ']'] = item;
                    });
                } else {
                    _params[key] = value;
                }
            });
            params = _params;

            let self = this,
                opts = Ext.Object.merge(options || {}, {
                    url: url,
                    params: params,
                    method: method,
                    callback: function (request, success, response) {
                        if (component) {
                            component.unmask();
                        } else {
                            self.unmask();
                        }

                        if (typeof complete_callback === 'function') {
                            complete_callback.call(this, response);
                        }

                        let message, error, type;

                        if (success) {
                            response = request.hasOwnProperty('binary') ? response : Ext.decode(response.responseText, true); // decode in safe mode

                            if (typeof success_callback === 'function') {
                                success_callback.call(this, response);
                            }

                            if (!silent) {
                                message = response && response.hasOwnProperty('message') ? response.message : 'La solicitud ha sido procesada correctamente';
                                error = response && response.hasOwnProperty('error') ? '<br><em><small>' + response.error + '</small></em>' : '';
                                type = response && response.hasOwnProperty('success') ? (response.success === true ? 'success' : 'danger') : 'success';

                                self.showAlert(message + error, type, type !== 'success' ? 10000 : undefined);
                            }
                        } else {
                            if (typeof failure_callback === 'function') {
                                failure_callback.call(this, response);
                            }

                            message = response && response.hasOwnProperty('statusText') ? response.statusText : 'La solicitud no pudo ser procesada';
                            error = '';

                            if (self.verbose && response && response.hasOwnProperty('responseText')) {
                                let parsed_error = self.parseError(response.responseText);

                                error = '<br><em><small>' + (parsed_error ? parsed_error : response.responseText) + '</small></em>';
                            }

                            self.showAlert(message + error, 'danger', 10000);
                        }
                    }
                });

            Ext.Ajax.request(opts);
        },

        submit: function (form, url, success_callback, failure_callback, options, silent) {
            this.mask();

            let self = this,
                opts = Ext.Object.merge(options || {}, {
                    url: url,
                    success: function (form, action) {
                        console.log('success');

                        let response = Ext.decode(action.response.responseText, true); // decode in safe mode

                        self.unmask();

                        if (typeof success_callback === 'function') {
                            success_callback.call(this, response);
                        }

                        if (!silent) {
                            let message = response && response.hasOwnProperty('message') ? response.message : 'La solicitud ha sido procesada correctamente',
                                type = response && response.hasOwnProperty('success') ? (response.success === true ? 'success' : 'danger') : 'success';

                            self.showAlert(message, type, type !== 'success' ? 10000 : undefined);
                        }
                    },

                    failure: function (form, action) {
                        console.log('failure');

                        let response = Ext.decode(action.response.responseText, true); // decode in safe mode

                        self.unmask();

                        if (typeof failure_callback === 'function') {
                            failure_callback.call(this, response);
                        }

                        let message = response && response.hasOwnProperty('message') ? response.message : 'La solicitud no pudo ser procesada';

                        self.showAlert(message, 'danger', 10000);
                    }
                });

            form.submit(opts);
        },

        parseError: function (responseText) {
            let parser = new DOMParser(),
                text = parser.parseFromString(responseText, "text/html"),
                titles = text.getElementsByTagName('title');

            return titles.length !== 0 ? titles[0].innerHTML : '';
        },

        mask: function () {
            if (!this.spot) {
                this.spot = Ext.create('Ext.ux.Spotlight', {animate: false, easing: 'easeOut', duration: 300});
            }

            this.spot.show($('#spot').removeAttr('hidden').attr('id'));

            return this;
        },

        unmask: function () {
            if (this.spot) {
                $('#spot').attr('hidden', 'hidden');
                this.spot.hide();
                delete this.spot;
            }

            return this;
        },

        showAlert: function (message, type, delay) {
            if (Ext.isEmpty(message.trim())) {
                return this;
            }

            let self = this,
                alert_el = $('.alert-notifier'),
                callback = function () {
                    alert_el.removeClass('alert-info alert-warning alert-danger alert-success alert-secondary alert-dark')
                        .addClass('alert-' + (type ? type : 'info'))
                        .html(self.templates['alert_msg_tpl'].apply({
                            message: message,
                            type: type ? type : 'info'
                        }))
                        .fadeIn();

                    alert_el.find('button').click(function () {
                        self.closeAlert();
                    });

                    let progress_el = $('.progress-bar'),
                        increment = (delay > 1000 ? 1000 : delay) / delay * 100;

                    self.interval_handler_key = setInterval(function () {
                        let width = progress_el.width() / progress_el.parent().width() * 100;

                        if (Math.round(width) === 100) {
                            width = 100;
                        }

                        // console.log(width, increment, width + increment);

                        progress_el.width(width + increment + '%');
                    }, 1000);

                    self.timeout_handler_key = setTimeout(function () {
                        alert_el.fadeOut(1000, function () {
                            clearInterval(self.interval_handler_key);
                        });
                    }, delay);
                };

            clearInterval(this.interval_handler_key);
            clearTimeout(this.timeout_handler_key);

            delay = delay || 5000; // 5 seconds if delay is undefined

            if (alert_el.css('display') === 'block') {
                alert_el.fadeOut(150, function () {
                    callback();
                });
            } else {
                callback();
            }

            return this;
        },

        closeAlert: function () {
            $('.alert-notifier').hide();

            clearInterval(this.interval_handler_key);
            clearTimeout(this.timeout_handler_key);

            return this;
        },

        debug: function () {
            let _arguments = Array.prototype.slice.call(arguments);
            _arguments[0] = '%c' + _arguments[0];

            if (this.verbose) {
                console.log.apply(console, [_arguments.join(' '), "color: #6c757d"]); // #dc3545, #17a2b8, #28a745, #117a8b
            }

            return this;
        },

        buildTemplates: function () {
            this.templates['nav_item_tpl'] = new Ext.Template(
                '<li class="nav-item">',
                '   <a class="nav-link" href="{url}">{text}</a>',
                '</li>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['submenu_item_tpl'] = new Ext.Template(
                '<li class="nav-item">',
                '   <a class="dropdown-item pl-2 pr-2" href="{url}">{text}</a>',
                '</li>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['dropdown_tpl'] = new Ext.Template(
                '<li class="nav-item dropdown" id="{id}">',
                '   <a class="nav-link dropdown-toggle pl-3 pr-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{text}</a>',
                '   <div class="dropdown-menu pt-1 pb-1" id="{div_id}"></div>', // dropdown-menu-right
                '</li>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );


            this.templates['dropdown_submenu_tpl'] = new Ext.Template(
                '<li class="dropdown dropdown-submenu" id="{id}">',
                '   <a href="#" class="dropdown-item dropdown-toggle pl-3 pr-3" data-toggle="dropdown"><span class="mr-2">{text}</span></a>',
                '   <ul id="{ul_id}" class="dropdown-menu pt-1 pb-1"></ul>',
                '</li>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['dropdown_header_tpl'] = new Ext.Template(
                '<h5 class="dropdown-header pl-3 pr-3 small-caps text-danger" style="font-size: 1rem;">{text}</h5>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['dropdown_item_tpl'] = new Ext.Template(
                '<a class="dropdown-item pl-3 pr-3" href="{url}">{text}</a>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['dropdown_item_help_tpl'] = new Ext.Template(
                '<a class="dropdown-item mr-4 pl-3 pr-3" target="_blank" href="{url}"><i class="fas fa-question-circle float-right text-primary fa_inherit"></i>Ayuda</a>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );


            this.templates['dropdown_item_about_tpl'] = new Ext.Template(
                '<a class="dropdown-item mr-4 pl-3 pr-3" href="#" data-toggle="modal" data-target="#about"><i class="fas fa-info-circle float-right text-primary fa_inherit"></i>Acerca de</a>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );


            this.templates['dropdown_item_logout_tpl'] = new Ext.Template(
                '<a class="dropdown-item mr-4 pl-3 pr-3" href="{url}"><i class="fas fa-sign-out-alt float-right text-secondary fa_inherit"></i>Cerrar sesi&oacute;n</a>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['dropdown_item_periodo_tpl'] = new Ext.Template(
                '<a class="dropdown-item mr-4 pl-3 pr-3" href="#" onclick="App.showWindowPeriodo()"><i class="fas fa-calendar-alt float-right text-primary fa_inherit"></i>Seleccionar Período</a>',
                {compiled: true, disableFormats: true});

            // this.templates['dropdown_item_close_periodo_tpl'] = new Ext.Template(
            //     '<a class="dropdown-item mr-4 pl-3 pr-3" href="#" onclick="App.cerrarPeriodo()"><i class="fas fa-lock float-right text-primary fa_inherit"></i>Cerrar Período</a>',
            //     {compiled: true, disableFormats: true});


            this.templates['nav_item_logout_tpl'] = new Ext.Template(
                '<a class="nav-link" href="{url}">Cerrar sesión&nbsp;<i class="fas fa-sign-out-alt mt-1"></i></a>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['divider_tpl'] = new Ext.Template(
                '<div class="dropdown-divider mb-1 mt-1"></div>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            // Equal-width multi-row (http://getbootstrap.com/docs/4.0/layout/grid/#equal-width-multi-row)
            this.templates['userinfo_popover_tpl'] = new Ext.XTemplate(
                "<div class='row no-gutters'>",
                "   <div class='col-md-2'>",
                "       <i class='fas fa-user fa-fw'></i>",
                "   </div>",
                "   <div class='col-md-10'>{username}</div>",
                "   <div class='w-100 mb-1'></div>",
                "   <div class='col-md-2'>",
                "       <i class='fas fa-clock fa-fw'></i>",
                "   </div>",
                "   <div class='col-md-10'>{created_at}</div>",
                "   <tpl if='!Ext.isEmpty(email)'>",
                "       <div class='w-100 mb-1'></div>",
                "       <div class='col-md-2'>",
                "           <i class='fas fa-envelope fa-fw'></i>",
                "       </div>",
                "       <div class='col-md-10'>{email}</div>",
                "   </tpl>",
                "</div>",
                {compiled: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['notifier_popover_tpl'] = new Ext.Template(
                "<div class='text-center'>{message}</div>",
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['helper_popover_tpl'] = new Ext.Template(
                '<div class="text-danger text-uppercase text-center">{text}</div>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            this.templates['alert_msg_tpl'] = new Ext.Template(
                '<div class="p-0 pr-4 pl-1 d-flex">',
                '   <div class="p-2 w-100">{message}</div>',
                '   <button type="button" class="close p-0 pt-1 pr-2 flex-shrink-1" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
                '</div>',
                '<div class="progress" style="height: 5px;">',
                '   <div class="progress-bar progress-bar-striped progress-bar-animated bg-{type}" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0"></div>',
                '</div>',
                {compiled: true, disableFormats: true}  // compile immediately, setting disableFormats to true will reduce apply time.
            );

            return this;
        },

        configureAlerts: function () {
            let notifier = document.getElementById('notifier');

            if (!notifier) {
                return this;
            }

            if ($._data(notifier, 'events')) {
                return this;
            }

            $('#notifier').click(this.showNotifications.bind(this));

            return this;
        },

        startNotifierTask: function () {
            if (!this.notifierTask) {
                let self = this,
                    store = Ext.create('Ext.data.JsonStore', {
                        storeId: 'notifications',
                        fields: ['id', 'mensaje', 'tipo', {
                            name: 'fecha_creacion',
                            type: 'date',
                            dateFormat: CONSTANTS.DATETIME_FORMAT
                        }],
                        proxy: {
                            type: 'ajax',
                            url: self.buildURL('/notificacion/list'),
                            reader: {
                                rootProperty: 'rows'
                            }
                        },
                        pageSize: 0,
                        autoLoad: false,
                        autoDestroy: false,
                        listeners: {
                            beforeLoad: function (store, operation, eOpts) {
                                self.currentOperation = operation;
                            },
                            load: function (store, records, successful, eOpts) {
                                self.notifications = successful ? records.length : 0;
                                self.currentOperation = null;
                                self.buildNotifier();
                            }
                        }
                    });

                this.notifierTask = (new Ext.util.TaskRunner()).newTask({
                    run: function () {
                        if (!store.isLoading()) {
                            store.reload();
                        }
                    },
                    interval: self.notifications_interval * 1000
                });
            }

            this.notifierTask.start();

            return this;
        },

        stopNotifierTask: function () {
            this.notifierTask.stop();

            if (this.currentOperation) {
                this.currentOperation.abort();
            }

            return this;
        },

        initTooltips: function () {
            $('[data-toggle="tooltip"]').tooltip();
        },

        reset: function (module, propagate) { // TODO: los stores creados deben tener auto destroy
            this.route = undefined;
            this.module = module;

            document.cookie = this.user.username + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            window.history.pushState('', '', this.base_url);
            document.title = this.app_name;

            // $('.nav-link.active[role!="tab"]').removeClass('active').parents('.active').removeClass('active'); // excluir los elementos del Modal (about.html.twig)
            $('#navbar-left').find('.active').removeClass('active');
            $('#container').children().show();

            if (this.container) {
                let child = this.container.child('.x-panel');
                if (child && child.component) {
                    child.component.propagate = false;
                    child.component.destroy();
                }
            }

            return this.closeAlert().debug('The app was reset.');
        },

        buildURL: function (path) {
            return this.base_url + (path.startsWith('/') ? path.replace('/', '') : path);
        },

        buildDropdown: function (id, text) {
            return $(this.templates['dropdown_tpl'].apply({id: id, text: text, div_id: id + '_dropdown-menu'}));
        },

        generateName: function (str) {
            return str.trim().toLowerCase().replace(/ |\(|\)|\./g, '_');
        },

        buildLeftNavbar: function () {
            let navbar_left_el = $('#navbar-left').empty(),
                self = this;

            $.each(this.routes, function (name, route) {
                if (route.hasOwnProperty('module') && route['module'] === self.module) {
                    let route_url = self.buildURL(route['path']),
                        text = route['text'];

                    if
                    (route.hasOwnProperty('dropdown')) {
                        let dropdown_data = route['dropdown'],
                            module = self.generateName(self.module),
                            dropdown_id, dropdown_menu_el, dropdown_header_el, dropdown_item_el;

                        if (typeof dropdown_data === 'string') {
                            // LEVEL 1
                            dropdown_id = module + '_' + self.generateName(dropdown_data) + '_A';

                            if ($('#' + dropdown_id).length === 0) {
                                navbar_left_el.append(self.buildDropdown(dropdown_id, dropdown_data));
                            }

                            dropdown_menu_el = $('#' + dropdown_id + '_dropdown-menu');

                            if (route.hasOwnProperty('dropdown_header')) {
                                let dropdown_header = route['dropdown_header'];
                                dropdown_header_el = $('#' + dropdown_id + '_dropdown-menu > .dropdown-header:contains("' + dropdown_header + '")').first();

                                if (dropdown_header_el.length === 0) {
                                    dropdown_header_el = $(self.templates['dropdown_header_tpl'].apply({text: dropdown_header}));
                                    dropdown_menu_el.append(dropdown_header_el);
                                }
                            }

                            dropdown_item_el = $(self.templates['dropdown_item_tpl'].apply({
                                url: route_url,
                                text: text
                            }));
                            if (dropdown_header_el && dropdown_header_el.length !== 0) {
                                let last_dropdown_item_el = dropdown_header_el.nextUntil('h5').last(); // nextUntil('a:last').last();
                                if (last_dropdown_item_el.length !== 0) {
                                    last_dropdown_item_el.after(dropdown_item_el);
                                } else {
                                    dropdown_header_el.after(dropdown_item_el);
                                }
                            } else {
                                dropdown_menu_el.append(dropdown_item_el);
                            }

                            if (route.hasOwnProperty('divider') && route['divider'] === true) {
                                dropdown_menu_el.append($(self.templates['divider_tpl'].apply()));
                            }
                        } else if (typeof dropdown_data === 'object' && Array.isArray(dropdown_data) && dropdown_data.length === 2) {
                            // LEVEL 1
                            dropdown_id = module + '_' + self.generateName(dropdown_data[0]) + '_A';

                            if ($('#' + dropdown_id).length === 0) {
                                navbar_left_el.append(self.buildDropdown(dropdown_id, dropdown_data[0]));
                            }

                            dropdown_menu_el = $('#' + dropdown_id + '_dropdown-menu');

                            // LEVEL 2
                            dropdown_id = module + '_' + self.generateName(dropdown_data[0]) + '_submenu_' + self.generateName(dropdown_data[1]) + '_B';

                            if ($('#' + dropdown_id).length === 0) {
                                dropdown_menu_el.append($(self.templates['dropdown_submenu_tpl'].apply({
                                    id: dropdown_id,
                                    text: dropdown_data[1],
                                    ul_id: dropdown_id + '_dropdown-submenu'
                                })));
                            }

                            if (route.hasOwnProperty('dropdown_header')) {
                                let dropdown_header = route['dropdown_header'];
                                dropdown_header_el = $('#' + dropdown_id + '_dropdown-menu > .dropdown-header:contains("' + dropdown_header + '")').first();

                                if (dropdown_header_el.length === 0) {
                                    dropdown_header_el = $(self.templates['dropdown_header_tpl'].apply({text: dropdown_header}));
                                    $('#' + dropdown_id + '_dropdown-submenu').append(dropdown_header_el);
                                }
                            }

                            // LEVEL 3
                            $('#' + dropdown_id + '_dropdown-submenu').append($(self.templates['submenu_item_tpl'].apply({
                                url: route_url,
                                text: text
                            })));

                            // Divider
                            if (route.hasOwnProperty('divider') && route['divider'] === true) {
                                dropdown_menu_el.append($(self.templates['divider_tpl'].apply()));
                            }
                        } else {
                            return true; // Ignore route
                        }
                    } else {
                        navbar_left_el.append($(self.templates['nav_item_tpl'].apply({url: route_url, text: text})));
                    }

                    if (self.route === name) {// activate the current route
                        $('a[href="' + route_url + '"]').addClass('active').parents('li').children('a').addClass('active');
                    }
                }
            });

            navbar_left_el.removeAttr('hidden').find('a').click(function (e) {
                let a = $(this);
                if (!a.hasClass('dropdown-toggle') && a.hasClass('active') && a.parents('li').hasClass('active')) {
                    self.debug('Click event canceled: the link is active.');
                    e.preventDefault();
                }
            });

            // https://stackoverflow.com/questions/44467377/bootstrap-4-multilevel-dropdown-inside-navigation
            $('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
                if (!$(this).next().hasClass('show')) {
                    $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                }
                let $subMenu = $(this).next(".dropdown-menu");
                $subMenu.toggleClass('show');

                $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
                    $('.dropdown-submenu .show').removeClass("show");
                });

                return false;
            });

            // Dropdown menus badly displayed when the button is on the right of the screen: https://github.com/twbs/bootstrap/issues/16968
            $('.dropdown').on('shown.bs.dropdown', function () {
                let menu = $(this).find('.dropdown-menu'),
                    menu_left = menu.offset().left,
                    menu_width = menu.outerWidth(),
                    document_width = $('body').outerWidth();

                if (menu_left + menu_width > document_width) {
                    menu.offset({'left': document_width - menu_width});
                }
            });

            // Dividers are rendered AFTER each item.
            $('.dropdown-menu').find('.dropdown-divider:last').each(function (idx, item) {
                let divider_el = $(item);
                if (divider_el.is(':last-child')) {
                    divider_el.remove();
                }
            });

            return this;
        },

        buildRightNavbar: function () {
            let navbar_right_el = $('#navbar-right');

            navbar_right_el.children().last().remove();

            if (this.module) {
                let self = this,
                    dropdown_container_el,
                    module = this.generateName(this.module);

                navbar_right_el
                    .append(self.buildDropdown(module + '_dropdown', this.module))
                    .find('.dropdown-menu').addClass('dropdown-menu-right');

                dropdown_container_el = $('#' + module + '_dropdown_dropdown-menu');

                this.modules.forEach(function (module) {
                    if (module === self.module) {
                        return;
                    }

                    let dropdown_item_el = $(self.templates['dropdown_item_tpl'].apply({
                        url: '#',
                        text: module
                    })).click(function () {
                        if (self.isBusy()) {
                            self.debug(':( the action could not be executed because the application is busy.');
                            return;
                        }
                        self.reset(module, false);
                        self.buildNavbar();
                    });

                    dropdown_container_el.append(dropdown_item_el);
                });

                if (this.modules.length > 1) {
                    dropdown_container_el.append($(this.templates['divider_tpl'].apply()));
                }

                dropdown_container_el
                    .append($(this.templates['dropdown_item_help_tpl'].apply({url: this.buildURL('help')})))
                    .append($(this.templates['dropdown_item_about_tpl'].apply()))
                    .append($(this.templates['divider_tpl'].apply()))
                    .append($(this.templates['dropdown_item_periodo_tpl'].apply()))
                    // .append($(this.templates['dropdown_item_close_periodo_tpl'].apply()))
                    .append($(this.templates['dropdown_item_logout_tpl'].apply({url: this.buildURL('logout')})));
            } else {
                navbar_right_el.append($(this.templates['nav_item_logout_tpl'].apply({url: this.buildURL('logout')})));
            }

            navbar_right_el.removeAttr('hidden');

            return this;
        },

        buildUserInfo: function () {
            let userdata_el = $('#userdata'),
                content = this.templates['userinfo_popover_tpl'].apply({
                    username: this.user.username,
                    fullname: this.user.fullname,
                    created_at: this.user.created_at,
                    email: this.user.email
                });

            if (userdata_el.data('bs.popover')) {
                userdata_el.data('bs.popover').config.content = content;
            } else {
                userdata_el.popover({
                    trigger: 'hover', // manual
                    placement: 'bottom',
                    html: true,
                    content: content,
                    container: 'body'
                });

                // userdata_el.on('show.bs.popover', function () {
                //     $('.arrow').css('left', parseInt($('.arrow').css('left')) + 3); // workaround for wrong arrow placement (no related issues were found)
                // });
            }

            userdata_el.removeAttr('hidden');

            // userdata_el.popover('show'); // ONLY FROM DEV!!!

            return this;
        },

        buildNotifier: function () {
            let notifier_el = $('#notifier'),
                content = this.templates['notifier_popover_tpl'].apply({
                    message: this.notifications === undefined || this.notifications === 0
                        ? 'No tiene notificaciones pendientes'
                        : (this.notifications === 1 ? 'Tiene 1 notificación pendiente' : 'Tiene ' + this.notifications + ' notificaciones pendientes')
                });

            if (notifier_el.data('bs.popover')) {
                notifier_el.data('bs.popover').config.content = content;
            } else {
                notifier_el.popover({
                    trigger: 'hover', // manual
                    placement: 'bottom',
                    html: true,
                    content: content,
                    container: 'body'
                });
            }

            if (this.notifications > 0) {
                notifier_el.find('i').addClass('text-danger faa-horizontal animated effect'); // faa-horizontal animated effect
            } else {
                notifier_el.find('i').removeClass('text-danger faa-horizontal animated effect'); // faa-horizontal animated effect
            }

            // if (this.notifications > 0) {
            //     notifier_el.find('i').removeClass('fa-bell-slash').addClass('fa-bell faa-horizontal animated effect text-danger');
            // } else {
            //     notifier_el.find('i').removeClass('fa-bell faa-horizontal animated effect text-danger').addClass('fa-bell-slash');
            // }

            notifier_el.removeAttr('hidden');

            // notifier_el.popover('show'); // ONLY FROM DEV!!!

            return this;
        },

        buildNavbar: function () {
            let start = 0;
            if (this.verbose) {
                start = performance.now();
            }

            this.buildLeftNavbar().buildRightNavbar();

            if (this.verbose) {
                this.debug(' > Building menu:', (performance.now() - start).toFixed(2), 'ms.');
            }

            // BS3 Navbar Click Dropdown-Submenu
            // https://www.bootply.com/nZaxpxfiXz
            $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).parent().siblings().removeClass('open');
                $(this).parent().toggleClass('open');
            });

            return this;
        },

        buildReloader: function () {
            if (!this.verbose) {
                return this; // ONLY FROM DEV!!!
            }

            let reloader = document.getElementById('reloader');

            if ($._data(reloader, 'events')) {
                return this;
            }

            let reloader_el = $('#reloader'),
                content = this.templates['helper_popover_tpl'].apply({text: 'Recargar rutas (ONLY FROM DEV!!!)'}),
                self = this;

            reloader_el.click(function () {
                if (self.isBusy()) {
                    self.debug(':( the action could not be executed: the app is busy!.');
                    return;
                }

                self.closeAlert();

                let icon_el = $(this).find('i');

                self.request('POST', self.buildURL('/routes/reload'), null, function () {
                    icon_el.removeClass('text-danger');
                }, null, function (response) {
                    // console.log(response);

                    let data = response.data;
                    self.reset(data.module || data.modules[0], false).initialize(data);
                    icon_el.addClass('text-danger');
                });
            });

            if (reloader_el.data('bs.popover')) {
                reloader_el.data('bs.popover').config.content = content;
            } else {
                reloader_el.popover({
                    trigger: 'hover', // manual
                    placement: 'bottom',
                    html: true,
                    content: content,
                    container: 'body'
                });
            }

            reloader_el.removeAttr('hidden');

            // reloader_el.popover('show'); // ONLY FROM DEV!!!

            return this;
        },

        showNotifications: function () {
            let self = this.stopNotifierTask(),
                container = $('#container'),
                store = Ext.getStore('notifications'),
                listeners;

            Ext.create('Ext.window.Window', {
                title: 'Notificaciones',

                height: container.height() * .75,
                width: container.width() * .75,
                layout: 'fit',
                modal: true,
                glyph: 0xf0e0,
                // closable: false,

                tools: [{
                    type: 'refresh',
                    tooltip: 'Actualiza el listado de notificaciones',
                    callback: function (owner, tool, event) {
                        owner.down('gridpanel').getStore().reload();
                    }
                }],

                items: {
                    xtype: 'gridpanel',
                    selType: 'checkboxmodel',
                    viewConfig: {
                        emptyText: '<div class="text-center">No tiene notificaciones pendientes</div>'
                    },
                    reserveScrollbar: true,
                    scrollable: 'vertical',
                    store: store,
                    columns: [{
                        xtype: 'rownumberer', align: 'center'
                    }, {
                        text: 'Mensaje',
                        dataIndex: 'mensaje',
                        // style: 'text-align: center',
                        flex: 3
                    }, {
                        text: 'Tipo',
                        dataIndex: 'tipo',
                        align: 'center',
                        // flex: 1,
                        width: 175,
                        renderer: function (v) {
                            return '<div class="font-weight-bold">' + v + '</div>';
                        }
                    }, {
                        text: 'Creado en',
                        dataIndex: 'fecha_creacion',
                        xtype: 'datecolumn',
                        format: CONSTANTS.DATETIME_FORMAT,
                        align: 'center',
                        width: 200
                        // flex: 2
                    }],

                    dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'bottom',
                        ui: 'footer',
                        layout: {
                            pack: 'center'
                        },
                        items: [{
                            text: 'Descartar',
                            width: 80,
                            handler: function (button, event) {
                                let gridpanel = button.up('gridpanel'),
                                    ids = Ext.Array.map(gridpanel.getSelection(), function (item) {
                                        return item.get('id');
                                    }),
                                    url = self.buildURL('/notification/read');

                                self.request('POST', url, {ids: ids}, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            gridpanel.getStore().reload();
                                        }
                                    });
                            },
                            listeners: {
                                render: function (button) {
                                    button.up('gridpanel').on('selectionchange', function (self, selected, eOpts) {
                                        button.setDisabled(selected.length === 0);
                                    });
                                }
                            }
                        }, {
                            text: 'Cerrar',
                            width: 80,
                            hidden: true,
                            handler: function (button, event) {
                                button.up('window').close();
                            }
                        }],

                        listeners: {
                            render: function (toolbar) {
                                let gridpanel = toolbar.up('gridpanel'),
                                    sel_model = gridpanel.getSelectionModel(),
                                    selection = [];

                                listeners = gridpanel.getStore().on({
                                    beforeload: function (store, operation, eOpts) {
                                        selection = sel_model.getSelection();
                                        sel_model.deselectAll();

                                        toolbar.items.each(function (item) {
                                            item.disable();
                                        });
                                    },
                                    load: function (store, records, successful, eOpts) {
                                        selection = Ext.Array.map(selection, function (record, index) { // update the selected record
                                            return store.getById(record.getId());
                                        });
                                        selection = Ext.Array.clean(selection);
                                        sel_model.select(selection);

                                        // toolbar.child('[text=Cerrar]').enable();
                                    },
                                    destroyable: true
                                });
                            }
                        }
                    }]
                },

                listeners: {
                    show: function () {
                        store.removeAll();
                        store.reload();
                    },
                    close: function () {
                        listeners.destroy();
                        self.closeAlert().startNotifierTask();
                    }
                }
            }).show();
        },

        showDownloadWindow: function (type, disposition, data) { // http://stackoverflow.com/questions/19327749/javascript-blob-filename-without-link
            let filename = 'default';

            if (disposition && disposition.indexOf('attachment') !== -1) {
                let filename_regex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/,
                    matches = filename_regex.exec(disposition);

                if (matches !== null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }

            this
                .debug('Downloading file:')
                .debug(' > Filename: "' + filename + '"')
                .debug(' > Type: "' + type + '"')
                .debug(' > Data length: ' + Ext.util.Format.round(data.length / 1024, 2) + ' kB (aprox.)');

            if (data !== null && navigator.msSaveBlob) { // IE
                return navigator.msSaveBlob(new Blob([data], {type: type}), filename);
            }

            let a = $("<a style='display: none;'/>"),
                url = window.URL.createObjectURL(new Blob([data], {type: type}));

            $("body").append(a.attr("href", url).attr("download", filename));

            a[0].click();
            window.URL.revokeObjectURL(url);
            a.remove();
        },

        buildHelper: function () {
            if (!this.verbose) {
                return this; // ONLY FROM DEV!!!
            }

            let helper = document.getElementById('helper');

            if ($._data(helper, 'events')) {
                return this;
            }

            let helper_el = $(helper),
                content = this.templates['helper_popover_tpl'].apply({text: 'Font Awesome 5 Free\'s Cheatsheet (ONLY FROM DEV!!!)'}),
                container = $('#container'),
                self = this;

            helper_el.click(function () {
                if (self.isBusy()) {
                    self.debug(':( the action could not be executed: the app is busy!.');
                    return;
                }

                self.closeAlert();

                Ext.create('Ext.window.Window', {
                    title: 'Font Awesome 5 Free\'s Cheatsheet',

                    height: container.height() * .75,
                    width: container.width() * .75,
                    layout: 'fit',
                    modal: true,
                    // glyph: 0xf2b4, // FIXME: not working

                    defaultFocus: '[name=search]',

                    dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'top',
                        id: 'cheatsheet_toolbar',

                        style: {
                            backgroundColor: 'white',
                            borderBottom: '1px solid #c7bebe !important'
                        },

                        items: [{
                            xtype: 'label',
                        }, '->', {
                            xtype: 'combobox',
                            name: 'size',
                            width: 140,
                            store: Ext.create('Ext.data.Store', {
                                fields: ['value'],
                                data: [{value: 'fa-2x'}, {value: 'fa-3x'}, {value: 'fa-4x'}, {value: 'fa-5x'}, {value: 'fa-6x'}, {value: 'fa-7x'}, {value: 'fa-8x'}, {value: 'fa-9x'}, {value: 'fa-10x'}]
                            }),
                            valueField: 'value',
                            displayField: 'value',
                            queryMode: 'local',
                            editable: false,
                            value: 'fa-5x',
                            listeners: {
                                change: function (self, newValue, oldValue, eOpts) {
                                    var i_el = $('i', '#fa-container');

                                    self.getStore().each(function (r) {
                                        i_el.removeClass(r.get('value'));
                                    });

                                    if (newValue) {
                                        i_el.addClass(newValue);
                                    } else {
                                        i_el.addClass('fa-5x');
                                    }
                                }
                            }
                        }, {
                            xtype: 'combobox',
                            name: 'text',
                            width: 140,
                            store: Ext.create('Ext.data.Store', {
                                fields: ['value'],
                                data: [{value: 'text-dark'}, {value: 'text-danger'}, {value: 'text-warning'}, {value: 'text-info'}, {value: 'text-primary'}, {value: 'text-success'}]
                            }),
                            valueField: 'value',
                            displayField: 'value',
                            value: 'text-info',
                            queryMode: 'local',
                            editable: false,
                            listeners: {
                                change: function (self, newValue, oldValue, eOpts) {
                                    var trigger_clear = self.getTrigger('clear'),
                                        i_el = $('i', '#fa-container');

                                    self.getStore().each(function (r) {
                                        i_el.removeClass(r.get('value'));
                                    });

                                    if (newValue) {
                                        trigger_clear.show();

                                        i_el.addClass(newValue);
                                    } else {
                                        trigger_clear.hide();
                                    }
                                }
                            },
                            triggers: {
                                clear: {
                                    weight: -1,
                                    cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                                    // hidden: true,
                                    handler: function () {
                                        this.setValue(null);
                                        this.updateLayout();
                                    }
                                }
                            }
                        }, {
                            xtype: 'textfield',
                            name: 'search',
                            emptyText: 'Search...',
                            width: 225,

                            listeners: {
                                change: function (field, newValue, oldValue, eOpt) {
                                    field.getTrigger('clear').setVisible(newValue);

                                    let panel = field.up('window').down('panel');

                                    if (!Ext.isEmpty(Ext.String.trim(field.getValue()))) {
                                        panel.filter(newValue);
                                    } else {
                                        panel.apply(panel.process(panel._data));
                                    }
                                }
                            },
                            triggers: {
                                clear: {
                                    cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                                    hidden: true,
                                    handler: function () {
                                        this.setValue(null);
                                        this.updateLayout();

                                        this.up('window').down('panel').apply(null);
                                    }
                                }
                            },
                        }]
                    }],

                    items: {
                        xtype: 'panel',
                        scrollable: true,

                        tpl: new Ext.XTemplate(
                            '<tpl if="data">',
                            '   <div class="list-group" id="fa-container">',
                            '       <tpl for="data">',
                            '           <tpl if="!Ext.isEmpty(values)">',
                            '               <div class="list-group-item d-flex justify-content-between">',
                            '                   <tpl for=".">',
                            '                       <div class="d-flex w-100 align-items-center">',
                            '                           <tpl if="clazz && unicode">',
                            '                               <i class="{clazz} {[Ext.getCmp(\'cheatsheet_toolbar\').down(\'[name=size]\').getValue()]} {[Ext.getCmp(\'cheatsheet_toolbar\').down(\'[name=text]\').getValue()]}"></i>',
                            '                               <div class="d-flex flex-column pl-1">',
                            '                                   <code>&lt;i class="{clazz}"&gt;&lt;/i&gt;</code>',
                            '                                   <code class="pt-1 text-muted">{unicode}</code>',
                            '                               </div>',
                            '                           </tpl>',
                            '                       </div>',
                            '                   </tpl>',
                            '               </div>',
                            '           </tpl>',
                            '       </tpl>',
                            '   </div>',
                            '<tpl else>',
                            '   <div class="w-100 h-100 d-flex justify-content-center align-items-center">',
                            '       <tpl if="error">',
                            '           <div class="text-danger text-uppercase font-weight-bold">{error}</div>',
                            '       <tpl else>',
                            '           <i class="fas fa-circle-notch fa-spin text-info fa-5x"></i>',
                            // '           <small class="position-absolute text-muted font-weight-bold" style="font-size: .6rem">Loading...</small>',
                            '       </tpl>',
                            '   </div>',
                            '</tpl>',
                        ),

                        filter: function (pattern) {
                            this.apply(this.process(this._data, pattern), pattern);
                        },

                        apply: function (data, pattern) {
                            this.update({data: data || this.process(this._data)});

                            let message = pattern
                                ? Ext.String.format('{0} icon{2} found for "{1}".', this.count, pattern, this.count > 1 ? 's' : null)
                                : Ext.String.format('{0} icons.', this.count);

                            this.up('window').down('toolbar').down('label').setText(message);
                        },

                        process: function (data, pattern) {
                            let _data = Ext.Array.clone(data);

                            if (pattern) {
                                try {
                                    let reg = new RegExp(pattern.toLowerCase(), 'i');

                                    _data = _data.filter(function (v, i) {
                                        return reg.test(v['clazz']) || reg.test(v['unicode']);
                                    });
                                } catch (e) {
                                    this.count = 0;
                                    return [];
                                }
                            }

                            this.count = _data.length;
                            let grid_size = 3, _grouped_data = [];

                            if (_data.length <= grid_size) {
                                _grouped_data[0] = _data;
                            } else {
                                let n = 0;
                                while (_data.length > 0) {
                                    if (_data.length < grid_size) {
                                        for (let i = _data.length; i < grid_size; i++) {
                                            _data.push({clazz: null, unicode: null});
                                        }
                                    }

                                    _grouped_data[n++] = _data.splice(0, grid_size);
                                }
                            }

                            return _grouped_data;
                        }
                    },

                    listeners: {
                        show: function (_window) {
                            let panel = _window.down('panel'),
                                toolbar = _window.down('toolbar');

                            panel.update(null);
                            toolbar.items.each(function (i) {
                                i.disable();
                            });

                            Ext.Ajax.request({
                                url: self.buildURL('/_docs/fontawesome'),
                                method: 'GET',
                                callback: function (request, success, response) {
                                    if (success) {
                                        let _response = Ext.decode(response.responseText, true),
                                            _data = _response.data;

                                        try {
                                            panel._data = _data;
                                            panel.apply(panel.process(_data));

                                            _window.setTitle(Ext.String.format("Font Awesome 5 Free's Cheatsheet - {0}", _response.version));
                                        } catch (e) {
                                        }
                                    } else {
                                        panel.update({error: response.statusText});
                                    }

                                    try {
                                        toolbar.items.each(function (i) {
                                            i.enable();
                                        });
                                    } catch (e) {
                                    }
                                }
                            });
                        }
                    }
                }).show();
            });

            if (helper_el.data('bs.popover')) {
                helper_el.data('bs.popover').config.content = content;
            } else {
                helper_el.popover({
                    trigger: 'hover', // manual
                    placement: 'bottom',
                    html: true,
                    content: content,
                    container: 'body'
                });
            }

            helper_el.removeAttr('hidden');

            // reloader_el.popover('show'); // ONLY FROM DEV!!!

            return this;
        }
    };

    window.onresize = function onresize() {
        if (App.container) {
            let child = App.container.child('div:last-child');
            if (child && child.component) {
                child.component.setSize(App.container.getWidth(), App.container.getHeight());
                child.component.updateLayout();
            }
        }
    };

    window.App = App;
    window.CONSTANTS = CONSTANTS;
});

Ext.define('Ext.form.field.ComboBoxMes', {
    extend: 'Ext.form.field.ComboBox',
    alternateClassName: 'Ext.form.ComboBoxMes',
    alias: ['widget.comboboxmes', 'widget.combomes'],
    store: Ext.create('Ext.data.Store', {
        fields: ['id', 'min', 'nombre'],
        data: [
            {id: 13, min: 'Anno', nombre: 'Anual'},
            {id: 1, min: 'Ene', nombre: 'Enero'},
            {id: 2, min: 'Feb', nombre: 'Febrero'},
            {id: 3, min: 'Mar', nombre: 'Marzo'},
            {id: 4, min: 'Abr', nombre: 'Abril'},
            {id: 5, min: 'May', nombre: 'Mayo'},
            {id: 6, min: 'Jun', nombre: 'Junio'},
            {id: 7, min: 'Jul', nombre: 'Julio'},
            {id: 8, min: 'Ago', nombre: 'Agosto'},
            {id: 9, min: 'Sep', nombre: 'Septiembre'},
            {id: 10, min: 'Oct', nombre: 'Octubre'},
            {id: 11, min: 'Nov', nombre: 'Noviembre'},
            {id: 12, min: 'Dic', nombre: 'Diciembre'},
        ]
    }),
    displayField: 'nombre',
    valueField: 'id'
});
