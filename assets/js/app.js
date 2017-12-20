









/*          This is a generated filed do not edit.          */









/*          From static/js/ run "grunt concat"          */










// ============================================================
// modules/base.js
// ============================================================

//Modification: Modal skin problem fix
$(function () {
    $('div.alert').each(function() {
        var div = $(this),
            match = div.attr('class').match(/(\W|^)alert-([a-z]+)/),
            type = match ? match[2] : 'success';
        
        div.find('[data-dismiss]').remove();

        if (div.text()) {
            bootstrap_alert(type, div.text());
            div.remove();
        }
    })
})

function enable(selector) {
    $(selector).prop('disabled', false);
}

function bootstrap_alert(type, html) {
    var options = {
        message: html
    };
    var settings = {
        animate: {
            enter: 'animated bounceInDown',
        },
        placement: {
            from: 'top',
            align: 'center'
        },
        type: type,
        z_index: 1100
    };

    if (type == 'danger') {
        options.icon = 'glyphicon glyphicon-exclamation-sign';
    }
    else if (type == 'success') {
        options.icon = 'glyphicon glyphicon-ok-circle';
    }
    else if (type == 'warning') {
        options.icon = 'glyphicon glyphicon-warning-sign';
    }

    $.notify(options, settings);
}

function show_backdrop() {
    $('body').addClass('modal-open').append('<div class="widget-box-layer" style="width:100%"><div class="loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i></div></div>');
}

function remove_backdrop() {
    $('.widget-box-layer').remove();
    $('body').removeClass('modal-open');
}
;
// ============================================================
// modules/pow_message.js
// ============================================================


var POW = {};

POW.msg = {
	run: function(messages) {
		if (typeof messages == 'object') {
			$.each(messages, function(i, message) {
				switch (message.action) {
					case 'ok':
						break;

					case 'reload':
						window.location.reload();
						break;

					case 'redirect':
						window.location = message.url;
						break;

					case 'exec':
						if (window[message.command]) {
							window[message.command].apply(null, message.arguments);
						}
						else {
							message.command.apply(null, message.arguments);
						}
						break;

					case 'html':
						$(message.target).html(message.html);
						break;

					case 'close_modal':
						// hacky but leaves no artifacts
						$('.modal:visible .close').click();
						break;
				}
			});
		}
	},
	rc: function(type, url, data, callback) {
		$.ajax({
			url: url,
			type: type,
			data: data,
			error: function(xhr, text, errorString) {
				console.log(xhr.status+' '+text+' '+errorString);
				console.log(xhr.status+' '+xhr.responseText.substr(0, 500));
			},
			success: function(data) {
				if (!callback) {
					POW.msg.run(data);
				}
				else {
					callback.call(null, data);
				}
			}
		});
	}
};
;
// ============================================================
// modules/ajax.js
// ============================================================

POW.ajax = {};

$(function() {
    $(document).on('click', '[data-ajax]', POW.ajax.run_data_ajax);
    $(document).on('change', 'select[data-ajax]', POW.ajax.run_select_data_ajax);
});

POW.ajax.run_data_ajax = function(e) {
    var el = $(this),
        url = -1 < el.data('ajax').indexOf('http') ? el.data('ajax') : appPath + el.data('ajax');

    if ($(e.target).is('a')) {
        e.preventDefault();
    }

    if (el.is('select')) return;

    POW.msg.rc('get', url);
}

POW.ajax.run_select_data_ajax = function() {
    var el = $(this),
        url = -1 < el.data('ajax').indexOf('http') ? el.data('ajax') : appPath + el.data('ajax');


    POW.msg.rc('get', url + '/' + el.val());
}
;
// ============================================================
// modules/forms.js
// ============================================================

jQuery.fn.reverse = Array.prototype.reverse;

POW.forms = {};

$(function() {

    $(document).on('submit', 'form', POW.forms.submit);
    $(document).on('click', '[data-submit]', POW.forms.button_submit);
    $(document).on('submit', 'form[get-html]', form_get_html);
    $(document).on('change', 'form[interactive][get-html] select', form_get_html);
    $(document).on('click', '[data-post]', form_submit_target);
    $(document).on('change', 'input[type=checkbox]', POW.forms.process_hide_show);
    $(document).on('click', 'input[type=checkbox][data-select-all]', POW.forms.check_all);
    $(document).on('change', 'input[type=checkbox][data-checked-show],input[type=checkbox][data-checked-hide],input[type=checkbox][data-not-checked-show],input[type=checkbox][data-not-checked-hide]', POW.forms.process_hide_show);

    //plugin bootstrap minus and plus
    //http://jsfiddle.net/laelitenetwork/puJ6G/
    $('.btn-number').click(function(e){
        e.preventDefault();

        var type      = $(this).data('type');
        var input =  $(this).parent().parent().find('input.controls');
        var max = +input.attr("max");
        var currentVal = parseInt(input.val());

        if (!isNaN(currentVal)) {
            if (type == 'minus') {
                if(currentVal>0)
                    input.val(currentVal - 1);
            }
            else if(type == 'plus') {
                if (!max || currentVal < max)
                    input.val(currentVal + 1);
            }
        } else {
            input.val(0);
        }
    });

    POW.forms.process_hide_show();
    update_multiselect('form.new select[multiple]', 175);
    update_multiselect('form select[multiple]');

    $(document).on('modal-shown', function(e, id) {
        var context = id ? $('#' + id) : null;

        POW.forms.process_hide_show(null, context);
        update_multiselect('form select[multiple]', null, context);
    });

    $(document).on('change', 'select:not([multiple])', function() {
        var select = $(this),
            option = $('option:selected', select);

        if (option.data('show')) {
            $(option.data('show')).show();
        }
        if (option.data('hide')) {
            $(option.data('hide')).hide();
        }
    });
});

function submit_filter()
{
    $('form.filter').submit();
}

POW.forms.process_hide_show = function(e, context)
{
    $('select [data-show]:selected', context).each(function() {
        $($(this).data('show')).show();
    });
    $('select [data-hide]:selected', context).each(function() {
        $($(this).data('hide')).hide();
    });
    $('input[type=checkbox][data-checked-show]:checked', context).each(function() {
        $($(this).data('checked-show')).show();
    })
    $('input[type=checkbox][data-checked-hide]:checked', context).each(function() {
        $($(this).data('checked-hide')).hide();
    })
    $('input[type=checkbox][data-not-checked-show]:not(:checked)', context).each(function() {
        $($(this).data('not-checked-show')).show();
    })
    $('input[type=checkbox][data-not-checked-hide]:not(:checked)', context).each(function() {
        $($(this).data('not-checked-hide')).hide();
    })
}

function update_multiselect(selector, width, context)
{
    $(selector, context)
        .multiselect({
            maxHeight: 300,
            numberDisplayed: 0,
            buttonWidth: width ? width : null,
            includeSelectAllOption: true,
            enableFilter: true,
        })
        .multiselect('refresh');
}

POW.forms.button_submit = function(e)
{
    e.preventDefault();

    var button = $(this);

    // disable buttons only when allowed to submit
    if (POW.forms.submit.apply(this, [e]))
    {
        button.prop('disabled', true);

        if (button.is('input[type=submit]')) {
            button.val('Processing...');
        }
        else if (button.is('button')) {
            button.text('Processing...');
        }
    }
}

POW.forms.submit = function(e, data)
{
    var form = $(this).closest('form');
    var allow = true;

    form.find('input[required]:not([disabled]):not([type=checkbox]):visible,select[required]:not([disabled]):visible').each(function() {
        var el = $(this);
        if (el.val()) {
            el.closest('.form-group').removeClass('has-error');
        }
        else {
            el.closest('.form-group').addClass('has-error');
            allow = false;
        }
    });

    form.find('input[type=checkbox]:not([disabled]):visible').each(function() {
        var el = $(this),
            selector = 'input[name=%s]'.replace('%s', el.attr('name').replace(/[\[]/g, '\\[').replace(/[\]]/g, '\\]')),
            object = $(selector+':checked');

        if (object.val()) {
            object.closest('.form-group').removeClass('has-error');
        }
        else {
            $(selector).closest('.form-group').addClass('has-error');
            allow = false;
        }
    });

    if (!allow) {
        e.preventDefault();
        bootstrap_alert('danger', 'Please fill out required fields');
        return false;
    }

    if (form.attr('enctype'))
    {
        form.find('input[type=submit].reactive')
            .val('Processing...')
            .prop('disabled', true);
    }
    else if (form.is('[ajax]')) {
        e.preventDefault();
        POW.msg.rc(form.attr('method'), form.attr('action'), data ? data : form.serialize());
    }

    return true;
}

POW.forms.check_all = function(){
    var context = $(this).closest('table,form');

    if($(this).parents().eq(3).hasClass('fixedHeader-floating'))
        context = context.add($('table.dataTable'));//used when dataTable fixedHeader is enabled

    $('input[type=checkbox]',context).prop('checked', $(this).prop('checked'));
}

function form_get_html(e)
{
    var el = $(this);
    var form = el.closest('form');

    if (form.is(el)) {
        e.preventDefault();
    }

    POW.msg.rc(form.attr('method'), form.attr('action'), form.serialize(), function(data) {
        var selector = form.data('target');
        if (selector) {
            var target = $(selector);
            target.html(data);

            $(document).trigger('html-update');
        }
    });
}

function form_submit_target(e, callback)
{
    var el = $(this);
    var target = el.data('post') ? $(el.data('post')) : $(el.data('target'));
    var html = el.html();

    e.preventDefault();

    el.prop('disabled', true);
    if (el.is(':not(input[type=submit])')) {
        el.html('<i class="fa fa-spin fa-cog"></i>&nbsp;Processing...');
    }

    $.ajax({
        url: el.data('url') ? el.data('url') : target.attr('action'),
        method: target.attr('method') ? target.attr('method') : 'post',
        data: target.serialize(),
        success: function(data) {
            el.prop('disabled', false);
            if (el.is(':not(input[type=submit])'))
                el.html(html);

            if (callback) {
                callback.call(el, data);
            }
        }
    });
}

function rewrite_filter_select(selector, html)
{
    var select = $(selector),
        parent = select.parent(),
        select_clone;

    select_clone = $(select.get(0).cloneNode())
        .insertBefore(parent)
        .html(html)
        .prop('disabled', html.length ? false : true);
    
    parent
        .remove();

    if (select.is('[multiple]')) {
        update_multiselect(selector);
    }
}
;
// ============================================================
// modules/modals.js
// ============================================================

POW.modals = {};

$(function() {
    $(document).on('click', '[data-modal]:not(select)', POW.modals.click_show);
    $(document).on('click', '[data-modal-close]', POW.modals.button_close);
    $(document).on('change', 'select[data-modal]', POW.modals.select_show);
});

POW.modals.cached = [];
POW.modals.container = '<div class="modal fade" id="{{id}}" role="dialog">';

POW.modals.button_close = function(e) {
    $(this).closest('.modal').find('.close').click();
}

POW.modals.show = function(id, url) {
    if (id && POW.modals.cached[id]) {
        return $('#'+id)
            .html(POW.modals.cached[id])
            .modal('show');
    }

    POW.msg.rc('get', (url.indexOf('http') > -1 ? url : appPath + url), null, function(html) {
        if (id) {
            POW.modals.cached[id] = html;
        }

        var handle = $(POW.modals.container.replace('{{id}}', id))
            .html(html)
            .appendTo('body')
            .modal('show')
            .on('shown.bs.modal', function() { 
                $(document).trigger('modal-shown', [id]); 
            })
            .on('hidden.bs.modal', function() {
                if (!id) handle.remove();
            })
    });
}

POW.modals.click_show = function(e) {
    e.preventDefault();

    var handle = $(this),
        id = handle.data('modal'),
        url = handle.data('url') ? handle.data('url') : handle.attr('href');

    POW.modals.show(id, url);
}

POW.modals.select_show = function(e) {
    e.preventDefault();

    var handle = $(this),
        first = handle.find(':selected').first(),
        id = first.data('id'),
        url = first.data('url');

    if (!id && !url) return;

    handle
        .find('option:nth-child(1)')
        .prop('selected', true);

    POW.modals.show(id, url);
}
;
// ============================================================
// modules/tables.js
// ============================================================

POW.tables = {};

$(function() {
    prep_datatables();

    $(document).on('html-update', prep_datatables);
    $(window).on('resize', POW.tables.pow_responsive);

    POW.tables.pow_responsive();
});

function prep_datatables()
{
    $('div.datatable,div.datatable-json').each(function() {
        var target = $(this);
        var options = {
            pageLength: 100,
            fixedHeader: true
        };
        if (target.is('.datatable-json')) {
            options.aaData = table_data;
        }
        if (target.data('hide-length')) {
            options.bLengthChange = false;
        }
        if (target.is('[data-sort-column]')) {
            options.order = [[+target.data('sort-column'), target.data('sort-direction') ? target.data('sort-direction') : 'asc']];
        }
        if (target.is('[data-no-filter-column]')) {
            options.columnDefs = [{"searchable": false, "targets": JSON.parse("[" + target.data('no-filter-column') + "]")}];
        }
        target.find('table').dataTable(options);
    });
}

POW.tables.pow_responsive = function() 
{
    var table = $('table.pow-responsive');

    if (window.innerWidth < 960 && table.is('table') && table.is(':not(.pow-processed)')) {
        var ths = table.find('thead th');

        if (table.is('.table-bordered')) {
            table
                .removeClass('table-bordered')
                .addClass('was-bordered');
        }

        table.addClass('pow-processed');

        table.find('tbody tr').each(function() {
            var tr = $(this);

            tr.find('td').reverse().each(function(i) {
                var td = $(this);
                if (!td.html().trim()) {
                    td.html('&nbsp;');
                }
                td.before('<th class="mobile-only">' + ths.eq(ths.length - i - 1).text() + '</th>');
            })
        })
    }
    else if (window.innerWidth >= 960 && table.is('table') && table.is('.pow-processed')) {
        $('tbody tr th.mobile-only').remove();

        if (table.is('.was-bordered')) {
            table.addClass('table-bordered');
        }

        table.removeClass('pow-processed');
    }
};
;
// ============================================================
// modules/monolith.js
// ============================================================

    /* Simple Javasctipt/JQuery MVC Framework
     * Copyright (C) <2013>  Shamim Ahmed
     * CodeRangers LLC
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     
     * You should have received a copy of the GNU General Public License
     * along with this program.  If not, see <http://www.gnu.org/licenses/>
     */

    //Author: Shamim Ahmed
    //shamim@coderangers.com
    //08/06/2013

    /*global jQuery */
    var App, Utils = null;
     
    String.prototype.contains = function(it) { return this.indexOf(it) != -1; };
    String.prototype.trim = String.prototype.trim || function() {
        return this.replace(/^\s+/, '').replace(/\s+$/, '');
    };

    function alert(m){
      swal('Alert!',m)
    }


    jQuery(function ($) {
        'use strict';

     
        Utils = {
     
            uuid: function () {
                /*jshint bitwise:false */
                var i, random;
                var uuid = '';

                for (i = 0; i < 32; i++) {
                    random = Math.random() * 16 | 0;
                    if (i === 8 || i === 12 || i === 16 || i === 20) {
                        uuid += '-';
                    }
                    uuid += (i === 12 ? 4 : (i === 16 ? (random & 3 | 8) : random)).toString(16);
                }

                return uuid;
            },
            pluralize: function (count, word) {
                return count === 1 ? word : word + 's';
            },
            Log: function (data) {
                //console.log("Log: " + data);
            },
            Strip: function (html) {
                var tmp = document.createElement("DIV");
                tmp.innerHTML = html;
                return tmp.textContent || tmp.innerText || "";
            },
            Alert: function (data) {
                alert(data);
            }, Round_up: function (value, places)
            {
                var mult = Math.pow(10, Math.abs(places));
                return places < 0 ?
                        Math.ceil(value / mult) * mult :
                        Math.ceil(value * mult) / mult;
            },
            store: function (namespace, data) {
                if (arguments.length > 1) {
                    return localStorage.setItem(namespace, JSON.stringify(data));
                } else {
                    var store = localStorage.getItem(namespace);
                    return (store && JSON.parse(store)) || [];
                }
            },
            isOnline: function () {
              return  navigator.onLine;
            },   
            animatedSkillBar: function () {
                $('.progress-skills').each(function () {
                    var t = $(this),
                            label = t.attr('data-label');
                    percent = t.attr('data-percent') + '%';
                    t.find('.bar').text(label + ' ' + '(' + percent + ')').animate({
                        width: percent
                    });
                });
            },
            _GET: function (field, url) {
                    var href = url ? url : window.location.href;
                    var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
                    var string = reg.exec(href);
                    return string ? string[1] : null;
            },
            responsiveVideoPlayer: function () {

            },
            scrollEffect: function () {
                scrollPos = $(this).scrollTop();
                $('#landingSlide').css({
                    'background-position': 'center ' + (200 + (scrollPos / 4)) + "px"
                });
            },
            scrollEffectInit: function () {
                $(window).scroll(function () {
                    this.scrollEffect;
                });
            },
            getPageName: function () {
                return window.location.pathname.split('/')[1];
            },
            getFullPageName: function () {
                return window.location.pathname.split('/')[2];
            },
            getPageAnchor: function () {
                return window.location.hash;
            },
            getHashOnly: function (key) {
                var a = window.location.hash;
                if (a.indexOf(key) > -1) {
                    return true;
                } else {
                    return false;
                }
            },
            getQueryParam: function () {

                var vars = [], hash;
                var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                for (var i = 0; i < hashes.length; i++)
                {
                    hash = hashes[i].split('=');
                    vars.push(hash[0]);
                    vars[hash[0]] = hash[1];
                }
                return vars;

            },
            addValidation: function (form, callbacks) {

                $('form[name="' + form + '"]').find('input, textarea').not("[type=submit]").not("[type=hidden]").jqBootstrapValidation({
                    submitSuccess: function ($form, event) {
                        event.preventDefault();

                        App[callbacks].apply(this);
                    }
                });

            },
            RoundUp: function (value, places) {

                var mult = Math.pow(10, Math.abs(places));
                return places < 0 ?
                        Math.ceil(value / mult) * mult :
                        Math.ceil(value * mult) / mult;

            },


            dateTime: function (value, places) {
                var today = new Date(); var date = today.getFullYear()+'-'+(today.getMonth()+1).toString().padStart(2, '0')+'-'+today.getDate().toString().padStart(2, '0');
                var time = today.getHours().toString().padStart(2, '0') + ":" + today.getMinutes().toString().padStart(2, '0') + ":" + today.getSeconds().toString().padStart(2, '0');
                var dateTime = date+' '+time;

                return dateTime;
            },           

            getLocale: function (str) {
                var newstr = '';
                if ($.isNumeric(str)) {

                    for (var x = 0; x < str.length; x++)
                    {
                        var c = $.i18n._(str.charAt(x));
                        newstr = newstr + c;
                    }
                    return newstr;
                } else {
                    //  console.log(str);
                    return $.i18n._(str);
                }
            },
            Localize: function () {
                /*
                 $.each($('.i18n'), function() {
                 $(this)._t($(this).html());
                 });
                 
                 
                 $.each($('.i18n-date'), function() {
                 //$(this)._t($(this).html());
                 var date = $(this).html().split(' ');;
                 var month = date[0];
                 var day = date[1];
                 day = day.replace(/,+$/, '');
                 var year = date[2];
                 console.log(month);
                 date = Utils.getLocale(month)+ ' '+Utils.getLocale(day)+', '+Utils.getLocale(year);
                 $(this).html(date);
                 });
                 $.each($('.i18n-n'), function() {  
                 $(this).html(Utils.getLocale($(this).html()));
                 });
                 */
            },
            Confirm: function (heading, question, cancelButtonTxt, okButtonTxt, callback) {
                var confirmModal =
                        $('<div class="modal hide fade">' +
                                '<div class="modal-header">' +
                                '<a class="close" data-dismiss="modal" >&times;</a>' +
                                '<h3>' + heading + '</h3>' +
                                '</div>' +
                                '<div class="modal-body">' +
                                '<p>' + question + '</p>' +
                                '</div>' +
                                '<div class="modal-footer">' +
                                '<a href="#" class="btn" data-dismiss="modal">' +
                                cancelButtonTxt +
                                '</a>' +
                                '<a href="#" id="okButton" class="btn btn-primary">' +
                                okButtonTxt +
                                '</a>' +
                                '</div>' +
                                '</div>');

                confirmModal.find('#okButton').click(function (event) {
                    callback();
                    confirmModal.modal('hide');
                });

                confirmModal.modal('show');
            },
            LiveEdit: function (el) {

            
                var type = el.data('type');


                var tmpl1 = '<span class="editable-container editable-inline" style=""><div><div class="editableform-loading" style="display: none;"></div><form id="' + el.attr('rel') + '" class="form-inline editableform" style="">' +
                        '<div class="control-group"><div>';

                var tmpl3 = '<span class="editable-clear-x"></span></div><div class="editable-buttons"><button class="btn btn-primary editable-submit" type="submit"><i         class="icon-ok icon-white"></i></button><button class="btn editable-cancel" type="button"><i class="icon-remove"></i></button></div></div><div class="editable-error-block help-block" style="display: none;"></div></div></form></div></span>';

                if (type == 'text') {
                    var tmpl2 = '<div class="editable-input" style="position: relative;"><input type="text" style="padding-right: 24px;" value="' + $.trim(el.html()) + '" class="input-mini">';
                    el.hide().before(tmpl1 + tmpl2 + tmpl3);
                }

                if (type == 'textarea') {
                    var tmpl2 = '<div class="editable-input" style="position: relative;"><textarea class="input-large" placeholder="' + el.html() + '" rows="4">' + el.html() + '</textarea>';
                    el.hide().before(tmpl1 + tmpl2 + tmpl3);
                }

                if (type == 'tags') {
                    var tmpl2 = '<div class="editable-input" style="position: relative;width:165px;"><select data-placeholder="Select Tags" user-input="true" multiple class="chzn-select-width input-medium live-select" tabindex="16"> ';
                    var option = '';
                    $.each(el.attr('data-source').split(','), function (key, data) {

                        option += '<option selected="selected" value="' + data + '">' + data + '</option>';
                    });

                    tmpl2 += option + '</select>';
                    el.hide().before(tmpl1 + tmpl2 + tmpl3);
                    Utils.setChosen();


                }
                if (type == 'select')
                {
                    var tmpl2 = '<div class="editable-input" style="position: relative;"><select class="input-medium live-select"> ';
                    var option = '';

                    //   var option = '<option value="">Select</option>';
                    //$.getJSON('/util/' + el.data('source'), function(data) {
                    $.each(el.attr('data-source').split(','), function (key, data) {

                        var value = data.split('=');
                        if (el.html() == value[1])
                            option += '<option value="' + value[1] + '" selected>' + value[0] + '</option>';
                        else
                            option += '<option value="' + value[1] + '">' + value[0] + '</option>';
                    });
                    tmpl2 += option + '</select>';
                    el.hide().before(tmpl1 + tmpl2 + tmpl3);


                }
            },
            ContainerResize: function (id) {
                var ration = .3;
                var containerWidth = $(id).width();
                var containerHeight = containerWidth * ration;
                $(id).height(containerHeight + "px");
                console.log("Width:" + containerWidth);
                console.log("Height:" + containerHeight);
            } 
        };




         App = {
            url: 'error',
            placeholdervalue: '',
            type: 'GET',
            data: 'Jones=1',
            dataType: 'JSON',
            idleTime: 0,
            //idleLimit: 900000,
            idleLimit: 7200,  //15 mins
            VideoDataTable: null,
            UrlPattern: /(^(https|http)?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/,
            init: function () {
                this.ENTER_KEY = 13;
                //this.todos = Utils.store('Coderangers');
                this.cacheElements();
                this.bindEvents();
                //this.render();
                Utils.animatedSkillBar();
                Utils.responsiveVideoPlayer();
                Utils.scrollEffectInit();
                // Utils.Localize();
            },
            cacheElements: function () {
                this.$configform = $("#config_form");
              
                this.$body = $("#body");
                 
            },
            bindEvents: function () {
            


                App.idleTime = 0;
                //Increment the idle time counter every minute.
                var idleInterval = setInterval(App.idleChecker, 1000); // 1 minute

                //Zero the idle timer on mouse movement.
                $(document.body).on("mousemove", function () {
                    App.idleTime = 0;
                });
                $(document.body).on("keypress", function () {
                    App.idleTime = 0;
                });
                $(document.body).on("click", function (e) {
                    App.idleTime = 0;
                });  

                $('.quickfill-remove').on("click",function(){
                            $(this).prev().show();
                            $(this).prev().prev().hide();
                            $(this).hide(); 
                            $(this).next().show();
                });
          

                $('.quickfill-reset').on("click",function(){
                            $(this).prev().prev().prev().show();
                            $(this).prev().show();
                            $(this).prev().prev().hide();
                            $(this).hide(); 
                           $(this).prev().prev().prev().prev().hide();
                            $(this).parent().find('.picked-label').hide();
                });

                            $('form[name="add-position-form"]').on("submit", function (event) { 

                                event.preventDefault(); 


                               var par = parseInt($('#add_par').val());
                                var capacity = parseInt($('#add_capacity').val());
                                var soh = parseInt($('#add_soh').val());

                                

                                if(par > capacity){
                                    alert("PAR should be less than or equal to Capacity");
                                    return ;
                                }

                                else if(soh > capacity){
                                    alert("On Hand should be less than or equal to Capacity");
                                    return ;
                                }


                                var selected_ids = [];
                                  $('.kiosk-numbers').each(function(){
                                      if($(this).prop('checked')){      
                                          selected_ids.push( $(this).data('id'));                    
                                      }
                                })

                                if(selected_ids.length==0)
                                 {
                                        alert("please select kiosk")   
                                 }
                                 else
                                 {
                                        App.AddPositionQueue($('form[name="add-position-form"]'),selected_ids);
                                 }     
                        });

                            
                            $("form[name=batch-modify-position-form]").on("submit", function (event) {    
                                event.preventDefault(); 
                                var isValidateCap = true;

                                var isValidate = true;

                                var par = parseInt($('#batch_par').val());
                                var capacity = parseInt($('#batch_capacity').val());
                                var soh = parseInt($('#batch_soh').val());

                                var coil = $('#batch_coil').val();

                                var label = $('#batch_label').val();

                                var product = $("#batch_product_filter").val();
                                

                                var fillcount = $('form[name=batch-modify-position-form] input[type="text"],form[name=batch-modify-position-form] .select-value').filter(function () {
                                                                                return !!this.value;
                                                                            }).length;
                                console.log(product)

                                 console.log(product)
                              
                                  console.log(label)

                                
                                if(fillcount==0)
                                {
                                     alert("Please select at least one attribute");
                                     return ;
                                }else if(par > capacity){
                                    alert("PAR should be less than or equal to Capacity");
                                    return ;
                                }

                                else if(soh > capacity){
                                    alert("On Hand should be less than or equal to Capacity");
                                    return ;
                                }

                                else if( !isNaN(capacity)   && coil==""){
                                                
                                                 swal({
                                                title: "You've queued a Capacity change but no Coil change. ",
                                                text: "Are you sure you want to proceed?",
                                                type: "info",
                                                showCancelButton: true,
                                                confirmButtonColor: "#DD6B55",
                                                confirmButtonText: "Continue",
                                                cancelButtonText: "Cancel!",
                                                closeOnConfirm: true,
                                                closeOnCancel: true
                                              },
                                              function(isConfirm){
                                                if (isConfirm) {
                                                 
                                                   var selected_ids = [];
                                                     $('.offering-checkboxes').each(function(){

                                                          if($(this).prop('checked')){
                                                              // console.log(  $(this).data('id') );
                                                              selected_ids.push( [$(this).data('id'),$(this).data('position')]);        
                                                          }

                                                     })

                                                     if(selected_ids.length==0)
                                                     {
                                                            alert("please select kiosk")   
                                                     }
                                                     else
                                                     {
                                                        
                                                                if(isValidate && isValidateCap)
                                                                App.BatchModifyQueue($('form[name="batch-modify-position-form"]'),selected_ids);
                                                           

                                                     } 
                                           
                                                } else {
                                                   
                                                    isValidateCap = false;

                                                }
                                              });
                                }                               
                                else if( isNaN(capacity)   && coil!=""){
                                               //  isValidateCap = false;
                                                 swal({
                                                title: "You've queued a Coil change but no capacity change. ",
                                                text: "Are you sure you want to proceed?",
                                                type: "info",
                                                showCancelButton: true,
                                                confirmButtonColor: "#DD6B55",
                                                confirmButtonText: "Continue",
                                                cancelButtonText: "Cancel!",
                                                closeOnConfirm: true,
                                                closeOnCancel: true
                                              },
                                              function(isConfirm){
                                                if (isConfirm) {
                                                 
                                                   var selected_ids = [];
                                                     $('.offering-checkboxes').each(function(){

                                                          if($(this).prop('checked')){
                                                              // console.log(  $(this).data('id') );
                                                              selected_ids.push( [$(this).data('id'),$(this).data('position')]);        
                                                          }

                                                     })

                                                     if(selected_ids.length==0)
                                                     {
                                                            alert("please select kiosk")   
                                                     }
                                                     else
                                                     {
                                                        
                                                                if(isValidate && isValidateCap)
                                                                App.BatchModifyQueue($('form[name="batch-modify-position-form"]'),selected_ids);
                                                           

                                                     } 
                                           
                                                } else {
                                                   
                                                    isValidateCap = false;

                                                }
                                              });
                                }  
                                else if(  (   product !=""  && label!=1)  ){
                                              isValidate = false;   
                                              swal({
                                                title: "You've queued a Product change but no Label change. ",
                                                text: "Are you sure you want to proceed?",
                                                type: "info",
                                                showCancelButton: true,
                                                confirmButtonColor: "#DD6B55",
                                                confirmButtonText: "Continue",
                                                cancelButtonText: "Cancel!",
                                                closeOnConfirm: true,
                                                closeOnCancel: true
                                              },
                                              function(isConfirm){
                                                if (isConfirm) {
                                                 
                                                    isValidate = true;

                                                        var selected_ids = [];
                                                     $('.offering-checkboxes').each(function(){

                                                          if($(this).prop('checked')){
                                                              // console.log(  $(this).data('id') );
                                                              selected_ids.push( [$(this).data('id'),$(this).data('position')]);        
                                                          }

                                                     })

                                                     if(selected_ids.length==0)
                                                     {
                                                            alert("please select kiosk")   
                                                     }
                                                     else
                                                     {
                                                          var fillcount = $('form[name=batch-modify-position-form] input[type="text"],form[name=batch-modify-position-form] .select-value').filter(function () {
                                                                                return !!this.value;
                                                                            }).length;

                                                          console.log(fillcount)
                                                          if(fillcount==0)
                                                          {
                                                                alert("Please select at least one attribute")   
                                                          }else
                                                          {     
                                                                if(isValidate && isValidateCap)
                                                                App.BatchModifyQueue($('form[name="batch-modify-position-form"]'),selected_ids);
                                                          }

                                                     } 


                                           
                                                } else {
                                                   
                                                    isValidate = false;

                                                }
                                              });


                                }else if(  (   product ==""  && label!="")  ){
                                              isValidate = false;   
                                              swal({
                                                title: "You've queued a Label change but no Product change. ",
                                                text: "Are you sure you want to proceed?",
                                                type: "info",
                                                showCancelButton: true,
                                                confirmButtonColor: "#DD6B55",
                                                confirmButtonText: "Continue",
                                                cancelButtonText: "Cancel!",
                                                closeOnConfirm: true,
                                                closeOnCancel: true
                                              },
                                              function(isConfirm){
                                                if (isConfirm) {
                                                 
                                                    isValidate = true;

                                                        var selected_ids = [];
                                                     $('.offering-checkboxes').each(function(){

                                                          if($(this).prop('checked')){
                                                              // console.log(  $(this).data('id') );
                                                              selected_ids.push( [$(this).data('id'),$(this).data('position')]);        
                                                          }

                                                     })

                                                     if(selected_ids.length==0)
                                                     {
                                                            alert("please select kiosk")   
                                                     }
                                                     else
                                                     {
                                                          var fillcount = $('form[name=batch-modify-position-form] input[type="text"],form[name=batch-modify-position-form] .select-value').filter(function () {
                                                                                return !!this.value;
                                                                            }).length;

                                                          console.log(fillcount)
                                                          if(fillcount==0)
                                                          {
                                                                alert("Please select at least one attribute")   
                                                          }else
                                                          {     
                                                                if(isValidate && isValidateCap)
                                                                App.BatchModifyQueue($('form[name="batch-modify-position-form"]'),selected_ids);
                                                          }

                                                     } 


                                           
                                                } else {
                                                   
                                                    isValidate = false;

                                                }
                                              });


                                }else {    
                                          var selected_ids = [];
                                         $('.offering-checkboxes').each(function(){

                                              if($(this).prop('checked')){
                                                  // console.log(  $(this).data('id') );
                                                  selected_ids.push( [$(this).data('id'),$(this).data('position')]);        
                                              }

                                         })

                                         if(selected_ids.length==0)
                                         {
                                                alert("please select kiosk")   
                                         }
                                         else
                                         {
                                              var fillcount = $('form[name=batch-modify-position-form] input[type="text"],form[name=batch-modify-position-form] .select-value').filter(function () {
                                                                    return !!this.value;
                                                                }).length;

                                              console.log(fillcount)
                                              if(fillcount==0)
                                              {
                                                    alert("Please select at least one attribute")   
                                              }else
                                              {     
                                                    if(isValidate && isValidateCap)
                                                    App.BatchModifyQueue($('form[name="batch-modify-position-form"]'),selected_ids);
                                              }

                                         } 


                                }   
    


                        });             
               

            },
            CreateFileUploader: function (el, ext, url, responseHandler, btnText, class1, class2) {
                var uploader = new qq.FineUploader({
                    element: document.getElementById(el),
                    validation: {
                        allowedExtensions: ext,
                        sizeLimit: 5000000,
                        minSizeLimit: 0,
                        stopOnFirstInvalidFile: true,
                        acceptFiles: null
                    },
                    editFilename: {
                        enabled: true
                    },
                    multiple: false,
                    request: {
                        endpoint: url
                    },
                    deleteFile: {
                        enabled: true,
                        endpoint: '/user/upload/deletefile',
                        forceConfirm: true
                    },
                    text: {
                        uploadButton: '<div><span class="i18n ' + class1 + '">' + btnText + '</div></div>'
                    },
                    template: '<div class="qq-uploader">' +
                            '<pre class="qq-upload-drop-area"><span>{dragZoneText}</span></pre>' +
                            '<div class="' + class2 + '" style="width: auto;">{uploadButtonText}</div>' +
                            '<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
                            '<ul class="qq-upload-list" style="margin-top: 10px; text-align: center;"></ul>' +
                            '</div>',
                    classes: {
                        success: 'alert alert-success',
                        fail: 'alert alert-error'
                    },
                    callbacks: {
                        onSubmit: function (id, fileName) {
                            $(".loading-image").removeClass("hidden");

                        },
                        onComplete: function (id, fileName, responseJSON) {
                            $(".loading-image").addClass("hidden");

                            if (responseJSON.success) {
                                $('.contact-form-submit').removeAttr('disabled');
                                App[responseHandler].apply(this, [{
                                        "response": responseJSON
                                    }]);
                            }
                        },
                        onCancel: function (id, fileName) {

                        },
                        onDelete: function (id) {
                            $(".img-holder").html('');
                            $('#org-logo').val('');
                            $('#mtpicture').val('');
                            $('#brpicture').val('');
                            //$(".settings a,.security a").removeClass("disablepointerevent");
                        }
                    }
                });

                return uploader;
            },
            render: function (el, data, template) {

                $('#' + el).html(tmpl(template, data));
                //  Utils.Localize();
            },
            Append: function (el, data, template) {

                $('#' + el).append(tmpl(template, data));
                //  Utils.Localize();
            },
            addPlaceHolder: function (el) {
                $(el).prev().attr("placeholder", $(el).html());
                $(el).remove();
            },
            RenderDataGrid: function () {

                App.Table = $('#table_report').dataTable({
                    "aoColumns": [
                        {
                            "bSortable": false
                        },
                        null, null, null, null, null,
                        {
                            "bSortable": false
                        }
                    ]
                });

                $('table th input:checkbox').on('click', function () {
                    var that = this;
                    $(this).closest('table').find('tr > td:first-child input:checkbox')
                            .each(function () {
                                this.checked = that.checked;
                                $(this).closest('tr').toggleClass('selected');
                            });

                });

                $('[data-rel=tooltip]').tooltip();

            },
            processingButton: function() {
                $('.sweet-alert.showSweetAlert.visible').find('button.confirm')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spin fa-cog"></i>&nbsp;Processing...');
            },
            enableButton: function() {
                $('.sweet-alert.showSweetAlert.visible').find('button.confirm')
                    .prop('disabled', false);
            },
            PickNPack: function (form,datetime) {

                App.url = appPath+'/picks/dopick';
                App.type = 'POST';
                App.Ajax(this.$body,  form.serialize()+"&datetime="+datetime, "renderPickNPack");    

            },
            FetchAttributes: function (kid) {

                App.url = appPath+'/configitem/getSingleConfigsByKioskId';
                App.type = 'GET';
                App.Ajax(this.$body, 'kiosk_select='+kid, "renderAttribute");    

            },
            getattribute: function (kid) {

                App.url = appPath+'/Productcategory/getattribute';
                App.type = 'GET';
                App.Ajax(this.$body, 'id='+kid, "getAttributeRender");    

            },
            CopyAttributes: function (kid) {

                App.url = appPath+'/configitem/getSingleConfigsByKioskId';
                App.type = 'GET';
                App.Ajax(this.$body, 'kiosk_select='+kid, "copyAttributeRender");    

            },
            PickDelete: function (transfer_id) {

                App.url = appPath+'/picks/deletetransfer';
                App.type = 'POST';
                App.Ajax(this.$body, 'transfer_id='+transfer_id, "deletePickRender");    

            },
            PickNPackCommit: function (queued_id,current_id,datetime) {

                App.url = appPath+'/kiosk/singlecommit';
                App.type = 'POST';
                App.Ajax(this.$body, 'queued_id='+queued_id+'&current_id='+current_id+'&datetime='+datetime, "commitPickRender");    

            },
            QueueAttributes: function (form) {
                var today = new Date(); var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                var dateTime = date+' '+time;

                App.url = appPath+'/configitem/processqueue';
                App.type = 'POST';
                App.Ajax(form, form.serialize()+'&datetime='+dateTime, "queueAttributeRender");    

            },
            deleteOfferingQueue: function (seleted_ids) {

                App.url = appPath+'/batchofferingchange/deleteOfferingQueue';
                App.type = 'POST';
                App.Ajax(this.$body, 'json='+JSON.stringify(seleted_ids), "deleteOfferingQueueRender");    

            },
             AddPositionQueue: function (form,seleted_ids) {

                App.url = appPath+'/batchofferingchange/addpositionqueue';
                App.type = 'POST';
                App.Ajax($('form[name="add-position-form"]'), form.serialize()+'&json='+JSON.stringify(seleted_ids), "AddPositionQueueRender");

            },
            BatchModifyQueue: function (form,seleted_ids) {
                App.url = appPath+'/batchofferingchange/batchmodifyqueue';
                App.type = 'POST';
                App.processingButton();
                App.Ajax($('form[name="batch-modify-position-form"]'), form.serialize()+'&json='+JSON.stringify(seleted_ids), "BatchModifyQueueRender");    

            },
            deleteQueuedOfferingItem: function (kiosk_id,position) {
                App.url = appPath+'/batchofferingchange/deletefromqueue';
                App.type = 'POST';
                App.Ajax(this.$body,  'kiosk_id='+kiosk_id+'&position='+position, "DeleteFromQueueRender");    

            },
            CommitQueuedOfferingItem: function (kiosk_id,position) {
                App.url = appPath+'/batchofferingchange/commitfromqueue';
                App.type = 'POST';
                App.Ajax(this.$body,  'kiosk_id='+kiosk_id+'&position='+position, "CommitFromQueueRender");    

            },
            BatchUnqueuPosition: function (seleted_ids) {
                App.url = appPath+'/batchofferingchange/batchunqueue';
                App.type = 'POST';
                App.Ajax(this.$body,  'json='+JSON.stringify(seleted_ids), "CommitFromQueueRender");    

            },
            CommitSelectPosition: function (seleted_ids) {
                App.url = appPath+'/batchofferingchange/commitbatchqueue';
                App.type = 'POST';
                App.Ajax(this.$body,  'json='+JSON.stringify(seleted_ids), "CommitFromQueueRender");
            },
            getPositionKiosk: function (position) {
                App.url = appPath+'/batchofferingchange/getkioskbyposition';
                App.type = 'POST';
                App.Ajax(this.$body,  'position='+position, "getPositionRender");    

            },
            Ajax: function (el, data, responseHandler) {
                
                App.initLoader(el);
                var request = $.ajax({
                    url: App.url,
                    async: true,  
                    type: App.type,
                    data: data,
                    dataType: App.dataType
                });
                request.done(function (response) {
                    //Utils.Log(response.TestResponse);
                    App.destroy(".widget-box-layer");
                    App[responseHandler].apply(this, [{
                            "element": el.selector,
                            "response": response
                        }]);
                });
                request.fail(function (jqXHR, textStatus) {
                    App.destroy(".widget-box-layer");
                    Utils.Log("Request failed: " + textStatus);
                });

            },
            completeYearData: function (data) {

                if (data.response.result == true) {          

      

                }

            },
            
            renderAttribute: function (data) {
             //   console.log(data.response.info)
                var html ='';    

                 console.log(data.response.info.configItems)
                $.each(data.response.info.configItems, function(i, item) {
                    item.value='';
                    var field = '';
                    var fieldValues = item.value_options.split(';');
                   

                    console.log(item)

                    if(data.response.info.kiosk_config_items.length>0){
                    $.each(data.response.info.kiosk_config_items, function(j, v) {
                        if(item.name == v.name){
                            console.log(item.name +' -- ' +v.name)
                            item.value =v.value;
                        }
                        var options = '<option value=""></option>';
                        $.each(fieldValues, function(m, option) {
                           options +='<option value="'+option.trim()+'">'+option.trim()+'</option>';
                        })
                        if(item.field_type == "Dropdown – Single Select"){
                            field = '<select class="select-atttribute single reset-value" id="attribute'+item.id+'" name="attribute[new]['+item.id+']" >'+options+'</select>';
                        }else  if(item.field_type == "Dropdown – Single Select"){
                            field = '<select class="select-atttribute multiple reset-value" name="attribute[new]['+item.id+']" id="attribute'+item.id+'" multiple>'+options+'</select>';
                        }else
                        {
                            field = '<input class="select-atttribute numeric reset-value"  name="attribute[new]['+item.id+']" id="attribute'+item.id+'" />';
                        }
                    })
                   }else
                   {
                         var options = '<option value=""></option>';
                        $.each(fieldValues, function(m, option) {
                           options +='<option value="'+option.trim()+'">'+option.trim()+'</option>';
                        })
                        if(item.field_type == "Dropdown – Single Select"){
                            field = '<select class="select-atttribute single reset-value" id="attribute'+item.id+'" name="attribute[new]['+item.id+']" >'+options+'</select>';
                        }else  if(item.field_type == "Dropdown – Single Select"){
                            field = '<select class="select-atttribute multiple reset-value" name="attribute[new]['+item.id+']" id="attribute'+item.id+'" multiple>'+options+'</select>';
                        }else
                        {
                            field = '<input class="select-atttribute numeric reset-value"  name="attribute[new]['+item.id+']" id="attribute'+item.id+'" />';
                        }

                   }

                    html += '<tr class="value_rows" role="row">'+
                            '<td>'+item.name+'</td>'+

                            '<td><input type="hidden" name="attribute[current]['+item.id+']" value="'+item.value+'" />'+item.value+'</td>'+
                            '<td ><input id="id_queued_attribute_'+item.id+'"" type="hidden" name="attribute[queued]['+item.id+']" value="" /><span class="queued_attribute_'+item.id+'"> </span></td>'+

                            '<td>'+field+'</td>'+   
                            '</tr>';
                })
                $("#attribute-table-content").html(html);
                $.each(data.response.info.kiosk_config_items_queued, function(j, item) {
                    console.log(item.config_item_id+" "+item.value)
                    $('.queued_attribute_'+item.config_item_id).html(item.value);
                    $('#id_queued_attribute_'+item.config_item_id).val(item.value);
                 })
                $('.single-kiost-copy').show();
            },
            copyAttributeRender: function (data) {
                console.log(data.response.info)
                  //   $('#single_config_form')[0].reset();
                    $('.reset-value').val("")
                    $.each(data.response.info.kiosk_config_items, function(j, config) {
                        $('#attribute'+config.config_item_id+'.numeric').val(config.value);
                        $('#attribute'+config.config_item_id+'.single option[value="' + config.value + '"').prop('selected','selected');  
                    })
               // $("#attribute-table-content").html(html);

            },

            getAttributeRender: function (data) {
                     console.log(data.response)
                 $.each(data.response, function(j, value) {
                    console.log(value.id)

                    var attribute = {};
                    attribute.id = value.id;
                    attribute.name= value.name;
                    attribute.unit = value.unit_of_measure_id;
                    console.log($('.class_attr_'+value.id).length);
                    if($('.class_attr_'+value.id).length==0){
                        new AddProductAttribute(attribute);
                    }


                 });
                  
               
            },
            AddPositionQueueRender: function (data) {
                console.log(data.response)
                if (data.response.result == true) {
                    $('#AddPositionModal').modal('hide')
                    App.reset('#add-position-form');
                    $("#planagram .filter:visible").submit();
                    swal("Great!", "Request successfully completed.", "success");
                }else
                {
                    swal("Oops", "Nothing to change", "error");
                }
            }
            ,BatchModifyQueueRender: function (data) {
                console.log(data.response)
                App.enableButton();
                $('#planagram form.filter button[type=submit]:visible').click();
                if (data.response.result == true) {      
                    App.reset('#batch-modify-position-form');
                    $('#BatchModifyModal').modal('hide');
                }
                else {
                    swal("Oops", "No transfer created.", "error");
                }
            },
            deletePickRender: function (data) {
                console.log(data.response)
                 window.location= appPath+'/picks/all';

            },
            deleteOfferingQueueRender: function (data) {
                console.log(data.response)
                if (data.response.result == true) {
                    $('#planagram form.filter button[type=submit]:visible').click();
                }else
                {
                    swal("Oops", "An error occured.", "error");
                }
               // $("#attribute-table-content").html(html);

            },
             commitPickRender: function (data) {
                console.log(data.response)
                if (data.response.result == true) {          
                   

                }else
                {
                    swal("Oops", "An error occured.", "error");
                }
               // $("#attribute-table-content").html(html);

            },
             renderPickNPack: function (data) {
                console.log(data.response)
                if (data.response.result == true) {          
                    window.location= appPath+'/stockmovement/jobs';
                }else
                {
                    swal("Oops", "Transfer not created. No missing stock in target or insufficient stock in source.", "error");
                }
            },
            CommitFromQueueRender: function (data) {
                console.log(data.response)
                App.enableButton();
                if (data.response.result == true) {          
                    console.log('a');
                    $('#planagram form.filter button[type=submit]:visible').click();
                }
                else
                {
                    setTimeout(function() {
                        swal("Oops", "An error occured.", "error");
                    }, $('.sweet-alert.visible').length ? 500 : 0);
                }
               // $("#attribute-table-content").html(html);

            },
            DeleteFromQueueRender: function (data) {
                console.log(data.response)
                if (data.response.result == true) {          
                    swal({
                      title: "Great!",
                      text: "Your Request successfully completed.",
                      type: "success",
                     
                     
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            $('#apply_filters_button').click();
                            $('.apply_filters_button:visible').click();
                        } else {
                        }

                        });

                }else
                {
                    swal("Oops", "An error occured.", "error");
                }
               // $("#attribute-table-content").html(html);

            },
             getPositionRender: function (data) {
              
                    var html = '';

                     
                    $('.blocks').show();

                    $('.kiosk-numbers').prop('checked','');

                    $.each(data.response.info, function(key, kiosk) {
                       
                      $("#block"+kiosk.kiosk_id).hide();

                    })

                
            },
            queueAttributeRender: function (data) {
                console.log(data.response)

                if (data.response.result == true) {          
            
                    if(data.response.refresh)
                       {
                                  $('#change_kiosk').change();
                                //   location.href =appPath + data.response.redirect;  
                      }else
                      {
                            swal({
                              title: "Great!",
                              text: "Configuration have been successfully queued...",
                              type: "success",
                             
                             
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                      
                                         location.href =appPath + data.response.redirect;

                                         
                                } else {
                                }

                                });
                                           
                    }

                }else
                {
                    swal("Oops", data.response.message, "error");
                }
 
            
               

               // $("#attribute-table-content").html(html);

            },
            noops: function () {

                

            },
            initLoader: function (el) {
                //  el.before('<div class="progress progress-success progress-striped"><div class="bar" style="width: 100%"></div></div>')
                el.parent().parent().css('position', 'relative');

                el.before('<div class="widget-box-layer" style="width:100%"><div class="loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i></div></div>')

                //el.before('<div class="widget-box-layer" style="width:100%"><div class="loader"><i class="fa fa-cog fa-spin fa-3x"></i></div></div>');
                // el.before('<div class="widget-box-layer" style="width:100%"><div class="loader gif-loader"></div></div>')


            },
            setNewToken: function (token) {
                $("input[name='token']").val(token);
            },
            destroy: function (el) {
                $(el).remove();
            },
            reset: function (el) {
                $(el)[0].reset();
            },
            hideModal: function (modal, time) {
                setTimeout(function () {
                    $('#' + modal).modal('hide');
                }, time);
            },
            hideModalShowanother: function (modal, time, newone) {
                setTimeout(function () {
                    $('#' + modal).modal('hide');
                    if (newone == true) {
                        $("#launchsecurityquestion").trigger("click");
                    }
                }, time);
            },
            showModal: function (modal) {
                setTimeout(function () {
                    $('#' + modal).modal('show');
                }, time);
            },
            idleChecker: function () {
                App.idleTime = App.idleTime + 1;

             
                if (App.idleTime == App.idleLimit) { // 20 minutes
                //    console.log(22)
                    location.href = appPath+'/auth/logout';
                    // $('.logoutbtn').trigger("click");
                }
            },
            patternCheck: function (value, pattern) {
                var Pattern = pattern
                var Idvalue = value;
                if (Pattern.test(Idvalue)) {
                    return true;
                } else {
                    return false;
                }

            }
     
        };

        App.init();


    });;;
// ============================================================
// modules/shadow_filter.js
// ============================================================

/**
 * Prerequisite shows child options in the postrequisite
 */

$(function() {
    $('select[data-prerequisite]').each(function() {
        var el = $(this);
        var prereq = $(el.data('prerequisite'));

        if (!prereq.val() || !prereq.val().length) {
            el.prop('disabled', true);
        }
        else {
            el.prop('disabled', false);
        }
    });

    $(document).on('change', 'select[data-postrequisite]', function() {
        update_select_postrequisite($(this));
    });

    $('select[data-postrequisite]').each(function() {
        update_select_postrequisite($(this));
    });
});

function update_select_postrequisite(prereq)
{
    var postreq = $(prereq.data('postrequisite'));
    var shadow = $('#' + postreq.attr('id') + '_shadow');
    var selected = false;

    postreq.find('option').each(function() {
        var option = $(this);
        if (option.attr('value')) option.remove();
    });

    if (!prereq.val() || prereq.val().length) {
        var filter = new RegExp(prereq.val(), 'i');

        shadow.find('option').each(function() {
            var option = $(this);
            if (option.data('filter').match(filter)) {
                var clone = option.clone();
                if (clone.attr('value') == postreq.data('value')) {
                    clone.prop('selected', true);
                    selected = true;
                }
                clone.appendTo(postreq);
            }
        });
    }
    else {
        shadow.find('option').each(function() {
            var option = $(this);
            var clone = option.clone();
            
            if (clone.attr('value') == postreq.data('value')) {
                clone.prop('selected', true);
                selected = true;
            }
            clone.appendTo(postreq);
        });
    }

    // set to default option
    if (!selected) {
        postreq.find('option:nth-child(1)').prop('selected', true);
    }

    postreq.prop('disabled', false);
}
;
// ============================================================
// modules/btn_bucket.js
// ============================================================

$(function() {
    $('.btn-bucket').click(function(e){
        e.preventDefault();

        var group = $(this).closest('.input-group')
        var type = $(this).data('type');
        var input = group.find('input.controls');
        var max = input.attr("max");
        var currentVal = parseInt(input.val());
        var available = 0;
        var selector = input.is('.bucket-destination') ? '.bucket-source' : '.bucket-destination';

        $(selector + '[data-skuid='+input.data('skuid')+']').each(function() {
            available += +$(this).val();
        });

        if (!isNaN(currentVal)) {
            if (type == 'minus') {
                if (currentVal>0) {
                    input.val(currentVal - 1);

                    var dec = false;
                    $(selector + '[data-skuid='+input.data('skuid')+']').each(function() {
                        if (dec) return;

                        var el = $(this);
                        if (+el.val() < +el.attr('max')) {
                            el.val(+el.val() + 1);
                            dec = true;
                        }
                    });
                }
            }
            else if (type == 'plus') {
                if (currentVal < max && available > 0) {
                    input.val(currentVal + 1);

                    var inc = false;
                    $(selector + '[data-skuid='+input.data('skuid')+']').reverse().each(function() {
                        if (inc) return;

                        var el = $(this);
                        if (+el.val() > 0) {
                            el.val(el.val() - 1);
                            inc = true;
                        }
                    });
                }
            }
        } else {
            input.val(0);
        }
    });    
});
    ;
// ============================================================
// modules/swal.presets.js
// ============================================================

function destructive_swal(title, text, button_text, callback) {
    swal(
        {
            title: title,
            text: text,
            type: "warning",
            confirmButtonColor: "#E64942", // this doesn't work
            confirmButtonText: button_text,
            cancelButtonText: "Cancel",
            showCancelButton: true,
            dangerMode: true,
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(confirm) {
            if (confirm) {
                callback.call()
            }
        }
    );
}

function info_swal(title, text, button_text, callback) {
    swal(
        {
            title: title,
            text: text,
            type: "info",
            confirmButtonText: button_text,
            cancelButtonText: "Cancel",
            showCancelButton: true,
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(confirm) {
            if (confirm) {
                callback.call()
            }
        }
    );
}
;
// ============================================================
// apps/application.js
// ============================================================

$(function() {

	// download buttons
	$(document).on('click', '[data-post-download]', function(e) {
		form_submit_target.call($(this), e, function(data) {
			window.location = data.download;
		});
	});

});
;
// ============================================================
// apps/atkiosk.js
// ============================================================

var AT_KIOSK_DT_REMOVE =null;
var AT_KIOSK_DT_CHECK_PRICE = null;
var AT_KIOSK_DT_STOCK = null;

$(document).ready(function(){
    $('#atKioskList #dodelete').on("click",function(){
        var transfer_id = $(this).data('id');
        destructive_swal('Are you sure?', 'This action can not be undone', 'Yes, Delete', function() {
            App.PickDelete(transfer_id);
        })
    });

    $('#atKioskList #check-price').click(function(){
        var kiosk_id = $('#atKioskId').val();
        $.ajax({
            url : appPath + '/picks/getpricetable',
            type : 'POST',
            data : {'kiosk_id' : kiosk_id},
            success : function(response){
                $('#check-price-data').html(response);
                console.log(response)
                if(!response){
                    $("#table-check-price").hide();
                    alert("No price issues found!");
                }
                else{
                    alert("Price issue found!");
                    $("#table-check-price").show();
                }
            }
        });
    });

    $(document).on("click", '#atKioskList .quickfill-commit', function(){
        App.PickNPackCommit($(this).data('id'), $(this).data('currentid'),  Utils.dateTime());
        $(this).prev().show();
        $(this).next().next().hide();
        $(this).hide(); 
    });

    $(document).on("click",'#atKioskList .quickfill-commit-3',function(){
        $(this).parent().find('.quickfill-reset').show();
        $(this).prev().show();
        $(this).next().next().hide();
        $(this).hide(); 
    });

    $(document).on("click", '#atKioskList .quickfill-commit-queued-no', function(){
        $(this).parent().find('.quickfill-reset').show();
        $(this).prev().show();
        $(this).next().next().hide();
        $(this).hide(); 
    });

    $("#atKioskList .quickfill").on("click",function(){
        $(this).hide();
        var value = $(this).data('value');
        $(this).parent().next().show();
        var input =  $(this).parent().parent().find('input.controls');
        input.val(value);
    });

    $("#atKioskList form#atkiosk-pick .dosubmit").on("click",function(){
        var hidden = $('div.soh-boxes:hidden').length;
        var planagram_not_done = $('.planagram .label-success:hidden').length;
        var button = $(this);

        if(hidden > 0 || planagram_not_done > 0)
        {
            alert("Please tick all the items.");
        }
        else
        {
            var price_changes = $('#atKioskPriceChanges').val();
            if(Utils.isOnline())
            {
                if(price_changes>0)
                {
                    console.log(price_changes)
                    swal({
                        title: "Price issue detected",
                        text: "Continue to press ignore or fix the issue.",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ignore",
                        cancelButtonText: "Fix Issue",
                        closeOnConfirm: false,
                        closeOnCancel: true
                    },
                    function(isConfirm){
                        if (isConfirm) {
                            destructive_swal('Are you sure?', 'This can not be undone', 'Yes', function() {
                                button.prop('disabled', true).val('Processing...');
                                POW.msg.rc('post', button.data('url'), $("form#atkiosk-pick").serialize());
                            })
                        } else {
                            $(window).scrollTop(4000);
                        }
                    });
                }else{
                    button.prop('disabled', true).val('Processing...');

                    var transfer_id = $('input[name=transfer_id]').val();

                    // clear drafts
                    localStorage.setItem('atkiosk-replenish-open'+transfer_id, '');
                    localStorage.setItem('atkiosk-replenish-soh'+transfer_id, '');
                    localStorage.setItem('atkiosk-planagram-open'+transfer_id, '');
                    localStorage.setItem('atkiosk-planagram-closed'+transfer_id, '');

                    button.prop('disabled', true).val('Processing...');
                    POW.msg.rc('post', button.data('url'), $("form#atkiosk-pick").serialize());
                }    
            }
            else
            {
                swal({
                    title: "Price issue detected",
                    text: "Continue to press ignore or fix the issue.....",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Ignore",
                    cancelButtonText: "Fix issue",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if (isConfirm) {
                        swal("You are offline!", "Please click on Save Draft button", "warning");
                    } else {
                        swal("Cancelled", "", "error");
                    }
                });
            }
        }
    });

    $("#atKioskList .quickfill-stock").on("click",function(){
        var button = $(this);
        var tr = button.closest('tr');
        var new_soh = +button.data('value') + +button.data('pick');
        var id = tr.data('id');

        if (tr.find('.swap-stock').is('input')) {
            new_soh += +tr.find('.swap-stock').val();
        }

        button.hide();
        tr.find('.soh-boxes').show();
        tr.find('input.soh-item').val(new_soh);
    });

    AT_KIOSK_DT_REMOVE = $('#atKioskList #table-remove-items').DataTable({
        responsive:false,
        "ordering": true,
        "searching": false,
        "info": false,
        "dom": '<"top"lf>t<"bottom"pi><"clear">',
        "autoWidth": false,  "paging": false
    });

    AT_KIOSK_DT_STOCK = $('#atKioskList #table-stock-kiosk').DataTable({
        responsive:false,
        "ordering": true,
        "searching": false,
        "info": false,
        fixedHeader: true,
        "dom": '<"top"lf>t<"bottom"pi><"clear">',
        "autoWidth": false,  "paging": false,
        "columnDefs": [{"orderable": false, "targets": 5 }]
    });

    $('#atKioskList #sorting_stock_kiosk').change( function() {
        var col = $(this).val();
        var item =  col.split("|");
        col = item[0];
        var dir = item[1];

        AT_KIOSK_DT_STOCK
        .column( col )
        .order( dir )
        .draw();
    });

    $('#atKioskList #sorting_remove_items').change( function() {
        var col = $(this).val();
        var item =  col.split("|");
        col = item[0];
        var dir = item[1];

        DT_REMOVE
        .column( col )
        .order( dir )
        .draw();
    });

    $('#atKioskList form#atkiosk-pick').submit(function(){
        alert($(this["options"]).val());
        return false;
    });

    atkiosk_tick_items();

    $('.dosave').on("click", atkiosk_local_draft);
});

function atkiosk_tick_items()
{
    var transfer_id = $('input[name=transfer_id]').val();

    var open = localStorage.getItem('atkiosk-replenish-open'+transfer_id);
    open = open ? open.split('|') : [];
    var soh = localStorage.getItem('atkiosk-replenish-soh'+transfer_id);
    soh = soh ? soh.split('|') : [];

    $('tr.replenish').each(function() {
        var tr = $(this);
        var id = tr.data('position');
        var index = open.indexOf(id.toString());

        if (-1 < index)
        {
            tr.find('.quickfill-stock').trigger('click');
            tr.find('input.soh-item').val(soh[index]);
        }
    });

    var open = localStorage.getItem('atkiosk-planagram-open'+transfer_id);
    open = open ? open.split('|') : [];
    var closed = localStorage.getItem('atkiosk-planagram-closed'+transfer_id);
    closed = closed ? closed.split('|') : [];

    $('tr.planagram').each(function() {
        var tr = $(this);
        var id = tr.data('position') + '-' + tr.data('attribute-id');

        if (-1 < open.indexOf(id))
        {
            tr.find('.quickfill-commit-3').trigger('click');
        }
        else if (-1 < closed.indexOf(id))
        {
            tr.find('.quickfill-remove').trigger('click');
        }
    });
}

function atkiosk_local_draft() 
{
    var data = $("form#atkiosk-pick").serialize();
    var transfer_id = $('input[name=transfer_id]').val();

    var open = [];
    var soh = [];

    $('tr.replenish').each(function() {
        var tr = $(this);

        if (!tr.find('input.soh-item:visible').is('input')) return;

        var id = tr.data('position');
        var value = tr.find('input.soh-item').val();

        open.push(id);
        soh.push(value);
    });

    localStorage.setItem('atkiosk-replenish-open'+transfer_id, open.join('|'));
    localStorage.setItem('atkiosk-replenish-soh'+transfer_id, soh.join('|'));

    var open = [];
    var closed = [];

    $('tr.planagram').each(function() {
        var tr = $(this);
        var id = tr.data('position') + '-' + tr.data('attribute-id');

        if (tr.find('.label-success:visible').length)
        {
            open.push(id);
        }

        if (tr.find('.label-default:visible').length)
        {
            closed.push(id);
        }
    });

    localStorage.setItem('atkiosk-planagram-open'+transfer_id, open.join('|'));
    localStorage.setItem('atkiosk-planagram-closed'+transfer_id, closed.join('|'));

    if(Utils.isOnline()==false)
    {
        swal("Success!", "Changes have been saved", "success");
    }

    var button = $(this);
    POW.msg.rc('post', button.data('url'), $("form#atkiosk-pick").serialize());
}
;
// ============================================================
// apps/batchhistory.js
// ============================================================

POW.batchhistory = {};

$(function() {

    if (!$('#kiosk-attributes').is('body')) return;

    $('.kiosk-attributes-item').each(function( index ){
        POW.batchhistory[$( this ).attr('name')] = $( this ).val();
    });

    $('select[name="filter_historyItem[]"]').on("change",POW.batchhistory.fill_kiosk_value);
    $('button.attribute-filter').on("click",POW.batchhistory.get_history);
    $(document.body).on('click','#unqueue',POW.batchhistory.unqueue);
    $('#menu-toggle').on('click',POW.batchhistory.toggle_menu);
    $(document.body).on('click','#commit',POW.batchhistory.data_commit);

    POW.batchhistory.prepare_filters();
    POW.batchhistory.prepare_table();

});

POW.batchhistory.toggle_menu = function(e) {
     e.preventDefault();
    $("#wrapper").toggleClass("toggled");
}

POW.batchhistory.prepare_table = function(){
    POW.batchhistory.history_table_config = {
        responsive:true,
        "autoWidth": false ,
        fixedHeader: true,
        "pageLength" :100,
        columnDefs: [
            {"orderable": false, "targets":[0] }
        ],
        "fnDrawCallback": function () {
            $('#history_config_length .change_buttons').remove();
            $('#history_config_length').append($('#commit-buttons').html());
        }
    }
    if(POW.batchhistory.col_sort == 5){
        POW.batchhistory.history_table_config.order = [[ POW.batchhistory.col_sort, "desc" ]];
    }
    else{
        POW.batchhistory.history_table_config.order = [[ 1, "asc" ]];
    }
    $('#history_config').DataTable(POW.batchhistory.history_table_config);
}

POW.batchhistory.prepare_filters = function(){
    var status = POW.batchhistory.status;
    var json = POW.batchhistory.json;
    if(status!=""){
        if(status=="Queued" && json !=""){

            $('select[name="filter_historystatus[]"] option').prop('selected', false)
            $('select[name="filter_historystatus[]"] option[value="'+status+'"]').prop('selected', true).change();
            $('select[name="filter_kiosk[]"] option').prop('selected', false)
            $('select[name="filter_historystate[]"] option').prop('selected', false)

            $.each($.parseJSON(json), function(i,v) {
                $('select[name="filter_historyItem[]"] option[value="'+v[1]+'"]').prop('selected', 'selected');
                $('select[name="filter_kiosk[]"] option[data-id="'+v[2]+'"]').prop('selected', 'selected')
            });

            $('select[name="filter_historystatus[]"]').multiselect("refresh");
            $('select[name="filter_historyItem[]"]').multiselect("refresh");
            $('select[name="filter_kiosk[]"]').multiselect("refresh");
            POW.batchhistory.trigger_filter_click();
        }
    }
    else{
        $('select[name="filter_historystatus[]"]').multiselect("refresh");
        $('select[name="filter_historyItem[]"]').multiselect("refresh");
        $('select[name="filter_kiosk[]"]').multiselect("refresh");
    }
}

POW.batchhistory.trigger_filter_click = function(){
    $('button.attribute-filter:visible').click();
}

POW.batchhistory.fill_kiosk_value = function(){
    var el = $(this);
    var form = el.closest('form');
    var item_name = el.find('option:selected').val();
    var url  = appPath + '/configitem/getValues';
    var value = POW.batchhistory.value;
    var status = POW.batchhistory.status;
    var json = POW.batchhistory.json;
    var data = {itemName:item_name,value:value};

    POW.msg.rc('get', url, data, function(response){
        var historyitemvalue = form.find('select[name="filter_historyitemvalue[]"]');

        historyitemvalue.html("");
        historyitemvalue.html(response);

        if(status!="" && json ==""){
            form.find('select[name="filter_historystatus[]"] option').prop('selected', false);
            form.find('select[name="filter_historystatus[]"] option[value="'+status+'"]').prop('selected', true).change();
            POW.batchhistory.trigger_filter_click();
        }

        if(el.find('option:checked').length ==1){
            historyitemvalue.multiselect('enable');
            historyitemvalue.multiselect('rebuild');
        }
        else{
            historyitemvalue.html("");
            historyitemvalue.multiselect('rebuild');
            historyitemvalue.multiselect('disable');
        }
    });

};

POW.batchhistory.get_history = function(){
    var form = $(this).closest('form');
    var data = {'conditions' : form.serialize()};
    var url = appPath + '/kiosk/getConfigHistory';

    POW.msg.rc('post', url,  data, function(response){
        $('#kiosk_history').html(response);
        $('#history_config').DataTable(POW.batchhistory.history_table_config);
    });
}

POW.batchhistory.unqueue = function(e){
    e.preventDefault();

    var config = $('#config_name_hidden').val();
    var conditions = $('#filter_hidden').val();
    var selects = [];
    var i=0;

    $('#history_config input[name="chkbox"]:checkbox:checked').each(function() {

        var inputs = [];
        //for each checked checkbox, iterate through its parent's siblings and get all the input values
        var inputs = $(this).parent().siblings().find('#label_queued').map(function() {
            return $(this).val().trim();
        }).get();

        if(inputs.length > 0){
            i++;
        }
        inputs.push($(this).attr('data-id'));

        var id_value = $(this).attr('id')
        var res = id_value.split('-');
        inputs.push(res[1]);
        selects.push(inputs);
    });

    if(selects.length > 0){
        if(i > 0){
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to revert this action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, unqueue it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm) {
                        var url = appPath + '/kiosk/unQueueKiosks';
                        var data = {
                            'kiosks':JSON.stringify(selects),
                            'conds':conditions,
                            'config':config,
                            'rc':1
                        };
                        POW.msg.rc('get', url,  data, function(response){
                            POW.batchhistory.trigger_filter_click();
                            bootstrap_alert("info", "Changes have been Unqueued");
                        });
                    }
                });
        } else{
            bootstrap_alert('danger', 'Please select a kiosk with queued changes');
        }
    } else{
        bootstrap_alert('danger', "Please select atleast one kiosk with queued change to continue");
    }
}

POW.batchhistory.time_dialog = function(){
    var el = $('#dialog-commit');

    return el.dialog({
        autoOpen: false,
        resizable: true,
        width:300,
        position: { my: "center", at: "center", of: '#kiosk_selection_table' },
        modal: true,
        buttons:{
            "Submit":function(){
                var data = {
                    'selected' : POW.batchhistory.queued_hidden,
                    'date_unapplied' : $('#commit_time').val(),
                    'rc':1
                };
                var form = el.find('form');

                if(isFutureDate(POW.batchhistory.php_timestamp, data.date_unapplied)){
                    POW.msg.rc(form.attr('method'), form.attr('action'),  data, function(response){
                        POW.batchhistory.trigger_filter_click();
                        bootstrap_alert('success', "Request successfully completed.");
                    });
                    $(this).dialog("close");
                }
                else{
                    bootstrap_alert('danger', "Date should be today or past date.");
                }
            },
            "Cancel":function(){
                $(this).dialog("close");
            }
        }
    })
}

POW.batchhistory.data_commit = function(e){
    POW.batchhistory.queued_hidden = '';
    e.preventDefault();
    var elems = [];
    var tmp =[];
    var i=0;
    $('#history_config input[name="chkbox"]:checkbox:checked').each(function() {
        var array = [];
        //for each checked checkbox, iterate through its parent's siblings and get all the input values
        var array = $(this).parent().siblings().find('#label_queued').map(function() {
            return $(this).text();
        }).get();
        if (array.length > 0){
            i++;
        }

        array.push($(this).attr('data-id'));
        var id_value = $(this).attr('id')
        var res = id_value.split('-');
        array.push(res[1]);
        elems.push(array);
    });

    POW.batchhistory.queued_hidden = JSON.stringify(elems);

    if(elems.length > 0){
        if(i > 0){
            POW.batchhistory.time_dialog().dialog('open');
        } else{
            bootstrap_alert('danger', "Please select kiosks with queued data to commit");
        }
    } else{
        bootstrap_alert('danger', "Please select at least one kiosk with queued change to continue");
    }
};
// ============================================================
// apps/jobs.js
// ============================================================

POW.jobs = {};

$(function() {
    if (!$('#current-jobs').is('body')) return;

    $(document).on('html-update', POW.jobs.prepend_button);
    $(document).on('click', '.bulk-actions .dropdown-menu li:not(.transfer-job)', POW.jobs.submit_jobs);
    $(document).on('click', '.bulk-actions .dropdown-menu li.transfer-job', POW.jobs.open_transfer_job);
    $(document).on('keydown', '#transfer-job form input', function(e) {
        if (e.which == 13) e.preventDefault();
    });
    $(document).on('click', '#transfer-job form .btn-primary', POW.jobs.submit_transfer_job);
    $(document).on('change', '#jobs input[type=checkbox]', POW.jobs.toggle_batch_actions);
    $(document).on('change', '#create-job select[name=from_state],#create-job select[name=to_state]', POW.jobs.filter_names);

    POW.jobs.prepend_button();

    update_multiselect('select[multiple]');

    // start -- remove this in the future
    // current requirement: warehouse to kiosk, kiosk to warehouse only
    $(document).on('change', '#create-job select[name=location_from_type]', function() {
        POW.jobs.sync_from_to($(this), '#create-job select[name=location_to_type]')
    })
    $(document).on('change', '#create-job select[name=location_to_type]', function() {
        POW.jobs.sync_from_to($(this), '#create-job select[name=location_from_type]')
    })
    // end --
});

POW.jobs.prepend_button = function()
{
    $('#jobs_table_filter')
        .closest('.row')
        .find('div')
        .first()
        .prepend($('#current-jobs .action-buttons').html());
}

POW.jobs.toggle_batch_actions = function() {
    $('.bulk-actions .btn-primary').prop('disabled', $('#jobs form').serialize() ? false : true);
}

POW.jobs.submit_jobs = function(e)
{
    var el = $(this);
    var data = $('#jobs form').serialize();

    e.preventDefault();

    if (!data) {
        return bootstrap_alert('danger', 'Please select at least one job');
    }
    else {
        if (el.is('[destructive]')) {
            destructive_swal('Are you sure?', "This can't be undone", 'Delete', function () {
                show_backdrop();
                POW.msg.rc('post', el.data('url'), data);
            });
        }
        else {
            show_backdrop();
            POW.msg.rc('post', el.data('url'), data);
        }
    }
}

POW.jobs.sync_from_to = function(target, other)
{
    $(other).val(target.val() == 'inventory_location' ? 'kiosk' : 'inventory_location');

    POW.forms.process_hide_show(null, '#create-job');
}

POW.jobs.open_transfer_job = function() 
{
    var el = $(this);
    var transfer_ids = [];

    $('#jobs input[type=checkbox]:checked').each(function() {
        transfer_ids.push($(this).val());
    });

    if (!transfer_ids.length) {
        return bootstrap_alert('danger', 'No jobs selected');
    }

    POW.modals.show('transfer-job', el.data('url'));
}

POW.jobs.submit_transfer_job = function(e)
{
    var form = $(this).closest('form');
    var data = form.serialize()+'&'+$('#jobs form').serialize();

    POW.forms.submit.apply(this, [e, data]);
}

POW.jobs.filter_names = function(e)
{
    var select = $(this);

    POW.msg.rc('post', select.data('target')+'/'+select.attr('name'), select.closest('form').serialize());
}
;
// ============================================================
// apps/kiosk-manage.js
// ============================================================


$(function() {
    if (!$('#kiosk-manage').is('body')) return;

    $(document).on('blur', 'input[name=number]', POW.kiosk_manage_check_number)
})

POW.kiosk_manage_check_number = function(e)
{
    var regex = /^MPP\d{3}$/;
    var el = $(this);

    if (el.val()) {
        if (regex.test(el.val().trim())) {
            $('#suggestions').html('');

            POW.msg.rc('get', el.data('url')+'/'+el.val()+'/'+$('input[name=kiosk_id]').val(), null, function(data) {
                console.log(data);
                if (data != 'true') {
                    $('#suggestions').html("<small class='error'>"+ el.val()+ ' is already used. Suggested value is: <strong>'+ data +'</strong></small>');
                    el.closest('.form-group').addClass('has-error');
                    el.val(data);
                    el.focus();
                }
                else {
                    el.closest('.form-group').removeClass('has-error');
                }
            })

            return;
        } 
        else {
            $('#suggestions').html("<small class='error'> Invalid Kiosk Number. Kiosk number should be starting with 'MPP' followed by 3 digit number.<br/>ex: MPP001</small>");

            el.closest('.form-group').addClass('has-error');
            el.focus();

            return;
        }

        el.closest('.form-group').removeClass('has-error');
    }
};
;
// ============================================================
// apps/kiosklocation-manage.js
// ============================================================

function kiosk_location_init_map(latValue, lngValue) {
    var lat_input = $('input[name=lat]');
    var lng_input = $('input[name=lng]');
    var draggable = $('input[name=draggable]').val();
    var lat = lat_input.val() ? lat_input.val() : '-27.46715487370691';
    var lng = lng_input.val() ? lng_input.val() : '153.0158616362305';

    var myLatlng = new google.maps.LatLng(lat,lng);
    var mapOptions = {
        zoom: 14,
        center: myLatlng
    };
    var map = new google.maps.Map(document.getElementById("map"), mapOptions);
    var marker = new google.maps.Marker({
        position: myLatlng,
        draggable: draggable == 'true' ? true : false,
    });

    if (draggable == 'true') {
        google.maps.event.addListener(marker, 'dragend', function (event) {
            lat_input.val(this.getPosition().lat());
            lng_input.val(this.getPosition().lng());
        });
    }

    // To add the marker to the map, call setMap();
    marker.setMap(map);
}
;
// ============================================================
// apps/pickpack.js
// ============================================================

var DT = null;
$(document).ready(function() {
    $('#pick-pack-form #dodelete').on("click", function() {
        var transfer_id = $(this).data('id');
        destructive_swal('Are you sure?', "This action can not be undone", 'Yes, Delete', function() {
            App.PickDelete(transfer_id)
        })
    });

    $('#pick-pack-form .dosubmit').on("click", function() {
        var hidden = $('div.soh-boxes:hidden').length;
        if (hidden != 0) {
            alert("Please tick all the items.");
        } else {
            info_swal("This can not be undone. Are you sure you want to proceed?", '', 'Yes', function() {
                $('#pick-type').val("Fully Picked");
                App.PickNPack($("#pick-pack-form"), Utils.dateTime())
            });
        }
    });

    $('#pick-pack-form .dosave').on("click", function() {
        $('#pick-type').val("Partially picked");
        App.PickNPack($("#pick-pack-form"), Utils.dateTime());
    });

    $('#pick-pack-form .quickfill-commit').on("click", function() {
        //App.PickNPackCommit($(this).data('id'), $(this).data('currentid'),  Utils.dateTime());
        $(this).prev().show();
        $(this).next().next().hide();
        $(this).hide();
        $(this).parent().next().find(".collapsed-value").val($(this).parent().next().find(".collapsed-value").data('value'));
        $(this).parent().find(".quickfill-reset").show();
    });

    $('#pick-pack-form .multiselect').multiselect({
        maxHeight: 300,
        buttonWidth: '190',
    });

    $("#pick-pack-form .quickfill").on("click", function() {
        $(this).parent().hide();
        var value = $(this).data('value');
        $(this).parent().next().show();
        var input = $(this).parent().parent().find('input.controls');
        input.val(value);
    });

    //Picktable
    DT = $('#pick-pack-form #table-product-1').DataTable({
        responsive: false,
        "ordering": true,
        "searching": false,
        "autoWidth": false,
        "paging": false,
        "columnDefs": [{"orderable": false, "targets": 5 }]
    });

    $('#pick-pack-form #sorting').change(function() {
        var col = $(this).val();
        var item = col.split("|");
        col = item[0];
        var dir = item[1];
        console.log(dir)
        DT
            .column(col)
            .order(dir)
            .draw();
    });

});
;
// ============================================================
// apps/planagram-history.js
// ============================================================

$(function() {
    $(document).on('submit', "#planagram-history form", function(event) {
        event.preventDefault();

        var form = $(this);

        $("#result").empty().append( "Loading...please wait." );

        $.ajax({
            url: form.attr('action'),
            data: form.serialize(),
            type: 'post',
            success: function(data) {
                $('#result')
                    .empty()
                    .append(data)
                    .find('table')
                    .dataTable({
                        pageLength: 100,
                        fixedHeader: true,
                        order: [[0, 'asc'], [1, 'asc']]
                    });
            }
        })
    });

});
;
// ============================================================
// apps/planagram.js
// ============================================================

$(function() {
    var planagram = $('#planagram');

    if (!planagram.is('body')) return;

    // add batch actions dropdown to floating bar
    setInterval(function() {
        var floating = $('body > .fixedHeader-floating');

        if (floating.length) {
            floating.each(function() {
                if (!$(this).find('tr.float').length) {
                    $(this).find('thead').prepend('<tr class="float" role="row"><td colspan="14">' + $('#bulk-buttons .btn-group').prop('outerHTML') + '</td></tr>');
                }
            });
        }
        else if (!floating.length) {
            $('#config_items').find('tr.float').remove();
        }
        floating.css('table-layout','auto');
    }, 250);

    $(document).on('click', '#planagram form.filter button[type=submit]', function(e) {
        e.preventDefault();

        var el = $(this),
            form = el.closest('form'),
            kiosk_name = form.find('select[name=kiosk_name]');

        //currently multiple forms are using this event so we need to check the class attr that needs validation and continue to submit otherwise
        if (!kiosk_name.val() && kiosk_name.hasClass('kiosk_name_filter'))
        {
            //moving this inside the if statement cause it bypasses the validation if the length is no longer empty
            if(!$('.planagram-filter-message').length)
                $('#wrapper').prepend('<p class="planagram-filter-message text-center"><span class="label label-warning">At least 1 kiosk is required.</span></p>');
        }
        else
        {
            $('.planagram-filter-message').remove();
            form.submit();
        }
    });

    $("#searchFormDesktop, #searchForm").submit(function( event ) {

        App.initLoader( $( "#wrapper" ));
        // Stop form from submitting normally
        event.preventDefault();

        // Get some values from elements on the page:
        var $form = $( this ),
            url = $form.attr( "action" );
        var data = { 
            kiosk_name: $form.find("select[name=kiosk_name]").val(),
            kiosk_model: $form.find("select[name=kiosk_model]").val(),
            site_category: $form.find("select[name=site_category]").val(),
            product: $form.find("select[name=product]").val(),
            item_category: $form.find("select[name=item_category]").val(),
            state: $form.find("select[name=state]").val(),
            position: $form.find("select[name=position]").val(),
            par: $form.find("select[name=par]").val(),
            capacity: $form.find("select[name=capacity]").val(),
            status: $form.find("select[name=status]").val(),
            price_issue: $form.find("select[name=price_issue]").val(),
            min_price: $form.find("input[name=min_price]").val(),
            max_price: $form.find("input[name=max_price]").val(),
            item_type: $form.find("select[name=item_type]").val(),
            commit_type: $form.find("select[name=commit_type]").val(),
        };

        // Send the data using post
        var posting = $.post( url, data );

        // Put the results in a div
        posting.done(function( data ) {
            $('#filter_section').hide();
            $("#sidebar-wrapper").scrollTop(0);
            updateTable(data);
            App.destroy(".widget-box-layer");
            params = [];
            allocation = [];
            newPosition = false;
            kioskId = false;
        });
    });

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    var allocation = [];
    var params = [];
    var status = [];
    var newPosition = false;
    var kioskId = false;

    dialog = $( "#confirm" ).dialog({
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        title: 'Commit date and time',
        buttons: {
            //"Commit": saveAllocation('commit'),
            Commit: function() {
                // dialog.dialog( "close" );
                date = document.getElementById('commit_time').value;
                if (date) {
                    if (isFutureDate(date,date)) {
                        params['date'] = getTimeJS(date);
                        console.log(params['date'] );
                        saveAllocation('commit');

                        dialog.dialog( 'close' );
                    } else {
                        alert('You cannot commit for future dates');
                    }
                }
            }
        },
    });

    dialogposition = $( "#position-dialog" ).dialog({
        autoOpen: false,
        height: 350,
        width: 350,
        modal: true,
        title: 'Add Position',
        buttons: {
            Save: function() {
                if (($('#position_kiosk_name').val() == "") || ($('#position-confirm').val() == "") )  {
                    $( "#popup-msg" ).empty().append( "All fields are mandatory" );
                    $( "#popup-msg" ).addClass('alert alert-danger');
                } else {
                    kioskId = $('#position_kiosk_name').val();
                    newPosition  = $('#position-confirm').val();

                    $('#changes').show();
                    dialogposition.dialog('close');
                }
            }
        },
    });

    var selectAllocation = function (el)
    {
        if  (el.checked) {
            allocation.push(el.value);
        } else {
            index = allocation.indexOf(el.value);
            allocation.splice(index, 1);
        }

        if (allocation.length > 0) {
            $('#changes').show();
        } else {
            $('#changes').hide();
        }
    }

    var saveAllocation = function (action)
    {
        var attributes =[];
        var values = [];
        var status = [];
        price = document.getElementById('price').value
        if (price)  {
            attributes.push(3);
            values.push(price)
            status.push($("input[name='price_status']:checked").val());
        }

        capacity = document.getElementById('capacity').value;

        if (capacity) {
            attributes.push(5);
            values.push(capacity);
            status.push($("input[name='capacity_status']:checked").val());
        }

        par = document.getElementById('par').value;

        if (par) {
            attributes.push(6);
            values.push(par);
            status.push($("input[name='par_status']:checked").val());
        }

        product = document.getElementById('pdt_filter').value;

        if (product) {
            attributes.push(1);
            values.push(product);
            status.push($("input[name='product_status']:checked").val());
        }

        width = document.getElementById('spaces').value;

        if (width) {
            attributes.push(8);
            values.push(width);
            status.push($("input[name='spaces_status']:checked").val());
        }

        params['values'] = values;
        params['attributes'] = attributes;
        params['allocation'] = allocation;
        params['status'] =  status;
        params['action'] = action;
        params['position'] = newPosition;
        if (kioskId) {
            params['kioskId'] = kioskId;
        }

        if (newPosition != false && params['attributes'].length < 5) {
            alert('All properties are mandatory when you add new position');
            return false;
        }

        $.post(appPath +"/batchofferingchange/save", Object.assign({},params)).done( function (data) {
            if (data == '1') {
                $('#searchForm').submit();
                $('#changes').hide();
                if (action == "commit") {
                    msg = "Data queued has been commited successfully";
                } else if(action == "queued") {
                    msg = "Data  has been queued successfully";
                } else{
                    msg = "Data  has been unqueued successfully";
                }

                bootstrap_alert('success', msg);
            }
        });
    }

    function switchFilter()
    {
        if ($('#switchfilter').is(":visible")) {
            $('#switchfilter').hide();
        } else {
            $('#switchfilter').show();
        }
    }

    var validateValues = function()
    {
    }

    var planagram_filters = $('#filter-template').detach().html();
    $('#planagrams-mobile-filters').after(planagram_filters);
    $('#planagrams-desktop-filters').append(planagram_filters);

    $('[data-toggle="popover"]').popover({ trigger: "hover",placement: 'bottom' });
    $('.multiselect').each(function() {
        $(this).multiselect({
            allSelectedText: 'All',
            maxHeight: 300,
            numberDisplayed: 0,
            buttonWidth: '175',
            includeSelectAllOption: true,
            enableCaseInsensitiveFiltering: $(this).hasClass('enable-filtering'),
        });
    });

    $(document.body).on('click','#add-position',function(){
        $('#AddPositionModal').modal('show')
    });

    $('.unqueue-delete-tick').on('click',function(){
        var kiosk_id = $(this).data('id');
        var position = $(this).data('position');
        swal({
                title: "Are you sure?",
                text: "This will commit queued item",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, commit selected!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm){
                if (isConfirm) {
                    App.CommitQueuedOfferingItem(kiosk_id,position);
                } else {
                }
            });
    });

    $(document.body).on('click','#remove-selected',function(){
        var selected_ids = [];
        $('.offering-checkboxes').each(function(){
            if($(this).prop('checked')){
                selected_ids.push( [$(this).data('id'),$(this).data('position')]);
            }
        })

        if(selected_ids.length==0)
        {
            alert("please select kiosk")
        }
        else
        {
            swal({
                    title: "Remove Selection(s)",
                    text: "This will queue the selected position(s) to be removed",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, remove selected!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm) {
                        App.deleteOfferingQueue(selected_ids);
                        console.log(selected_ids)
                    }
                });
        }
    });


    $(document.body).on('click','#commit-position',function(){
        var selected_ids = [];
        $('.offering-checkboxes').each(function(){

            if($(this).prop('checked')){
                selected_ids.push( [$(this).data('id'),$(this).data('position')]);
            }
        })

        if(selected_ids.length==0)
        {
            alert("please select kiosk")
        }
        else
        {
            swal({
                    title: "Commit Selected",
                    text: "This will commit the selected position(s)",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, commit selected!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm) {
                        App.processingButton();
                        App.CommitSelectPosition(selected_ids);
                        console.log(selected_ids)
                    }
                });
        }
    });

    $(document.body).on('click','#unqueue-position',function(){
        var selected_ids = [];
        $('.offering-checkboxes').each(function(){
            if($(this).prop('checked')){
                selected_ids.push( [$(this).data('id'),$(this).data('position')]);
            }
        })

        if(selected_ids.length==0)
        {
            alert("Please select kiosk")
        }
        else
        {
            swal({
                    title: "Unqueue Selected",
                    text: "This will unqueue the selected position(s)",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, Unqueue selected!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm) {
                        App.BatchUnqueuPosition(selected_ids);
                        console.log(selected_ids)
                    }
                });
        }
    });

    $(document.body).on('click','#batch-modify',function(){
        var selected_ids = [];
        $('.offering-checkboxes').each(function(){

            if($(this).prop('checked')){
                // console.log(  $(this).data('id') );
                selected_ids.push( [$(this).data('id'),$(this).data('position')]);

            }

        })

        if(selected_ids.length==0)
        {
            alert("Please select kiosk")
        }
        else
        {
            $('#BatchModifyModal').modal('show')
        }
    });

    $('#position-number').keyup(function( event ) {
        App.getPositionKiosk($(this).val());
    });

    function toggle(source) {
        checkboxes = document.getElementsByClassName('allocation_check');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    var planagram_datatables = [];
    function updateTable(data) {

        if (planagram_datatables.length) {
            $.each(planagram_datatables, function(i) {
                console.log(i);
                planagram_datatables[i].fnDestroy();
                delete planagram_datatables[i];
            });

            planagram_datatables = [];
        }

        $("#result").empty().append(data);

        $('#config_items').each(function() {
            var t = $(this).dataTable({
                "paging":   true,
                "order": [[ 1, 'asc'], [3, 'asc']],
                responsive: false,
                "info":     false,
                "pageLength": 100,
                "autoWidth": false,
                fixedHeader: true,
                "columns": [
                    { "orderable": false },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true } ,
                    { "orderable": true },
                    { "orderable": true },
                    { "orderable": true } ,
                    { "orderable": false }
                ],
                "lengthMenu": [[10, 25, 50,100, 200, -1], [10, 25, 50,100,200, "All"]],
                fnDrawCallback: function() {
                }
            });
            planagram_datatables.push(t);
        });
        //need to move this out from the fnDrawCallback as it creates multiple buttons when the table is redrawn
        $('#config_items_length').prepend($('#bulk-buttons').html());
    }

    var filter = function ()
    {
        var values = new Array;
        var url = appPath +"/configitem/all/?";
        $("select[class='filter_crit'] option:selected").each(function() { // this function is to select all the filter criteria
            if($(this).val() != ''){
                values.push({
                    key:$(this).attr('data-id'),
                    value :$(this).val()
                });
            }
        });

        for(var i=0; i < values.length ; i++){
            url = url + values[i]['key']+'='+values[i]['value'] +'&&'; //url passing key value pair to the controller
        }

        document.location.href= url;
    }

    if ($('#planagram form.filter select[name=kiosk_name]:visible').val()) {
        $('#planagram form.filter:visible').submit();
    }
});
;
// ============================================================
// apps/stockadjustment.js
// ============================================================

$(document).ready(function(){
    var formState = 0;
    $('#indexNewpdttype').multiselect();
});
var indexNewChangeLocation = function () {

    formdata = $( "#stock-adjustment-form #form" ).serialize();

    value = $('#location').val();
    adjustment = $('#adjustment').val();

    if (value != "") {
        $('#productresults').html("<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;Loading...Please wait.");
        $.post(appPath +"/stockadjustmentnew/products/" + value,
            formdata,
            function (data) {
                $('#productresults').html(data);
            }
        ).done(function( data ) {
                $('#stock-adjustment-table').DataTable({
                    "columnDefs": [
                        { "orderable": false, "targets": [3] }
                    ],
                    "paging": false,
                    "info": false
                });
            });
        $('#typefilter').show();
    } else {
        $('#productresults').html('');
    }
};

var indexNewvalidateForm = function () {

    dateValue = $('#date').val();
    locationValue = $('#location').val();
    adjustment = $('#adjustment').val();
    var checkDate = $('#indexNewIsFutureDate').val();

    if (adjustment != "" && adjustment != 4 && adjustment != 10 && adjustment != 11) {
        pick = $('#field_' + adjustment).val();
        pickFlag = true;

    } else if (adjustment > 0) {
        pickFlag = false;
        pick = "";
    }

    if (dateValue == "" || locationValue == "" || adjustment == "" || (pickFlag && pick == "")) {
        bootstrap_alert('danger', 'Missing mandatory fields.');
        return false;
    } else if (!isFutureDate(checkDate, dateValue)) {
        bootstrap_alert('danger', 'Date should be today or past date.');
        return false;
    }

    if (indexNewconfirmation()) {

        var formValues = $('#form').serialize();
        console.log(formValues);
        if (typeof(Storage) !== "undefined") {
            localStorage.formValues = formValues;
        }

        if (!navigator.onLine) {
            alert("You're currently offline, preventing the data submission");
            event.preventDefault();
            return false;
        }
        return true;
    }
};

var indexNewupdateStock = function (id) {
    formState = 1;
    adjustment = $('#adjustment').val();

    value = $('#product_' + id).val();
    amount = $('#amount_' + id).val();

    if (amount < 0) {
        alert("Amount cannot be negative");
        indexNewchangeValue(id, "");
        $('#amount_' + id).val(null);
        return false;
    } else if (amount.trim() === "") {
        indexNewchangeValue(id, "");
        $('#amount_' + id).val(null);
        return false;
    }

    if (adjustment == 4 || adjustment == 9 || adjustment == 10 || adjustment == 12 || adjustment == 13 || adjustment == 14) {
        newvalue = parseInt(value) - parseInt(amount);
    } else {
        newvalue = parseInt(value) + parseInt(amount);
    }

    if (newvalue >= 0) {
        indexNewchangeValue(id, newvalue);
    } else if (newvalue < 0) {
        alert("New stock on hand cannot be negative");
        indexNewchangeValue(id, "");
        $('#amount_' + id).val(null);
    }
};

var indexNewchangeValue = function (id, newvalue) {
    $('#stock_count_' + id).val(newvalue);
    $('#stock_display_' + id).text(newvalue);
};

var indexNewchangeAdjustment = function () {

    var label = new Array();
    label[1] = 'Date & Time Order Received ';
    label[2] = 'Date & Time at Kiosk';
    label[3] = 'Date & Time at Kiosk';
    label[4] = 'Date & Time of Pick';
    label[6] = 'Date & Time of Pick';
    label[8] = 'Date & Time of Kiosk';
    label[7] = 'Date & Time';
    label[9] = 'Date & Time';

    label[10] = 'Date & Time Sent';
    label[11] = 'Date & Time Received';
    label[12] = 'Date Picked';
    label[13] = 'Date Picked';
    label[14] = 'Date Picked';


    $('.adjustment_fields').hide();
    value = $('#adjustment').val();
    if (value) {
        $('.field_' + value).show();
        $('#date-label').html(label[value]);
    }
};

var indexNewconfirmation = function () {
    var location = $("#location option:selected").text();
    var dateval = $("#date").val();

    dateStr = (dateval.split('T'));
    date = dateStr[0].split('-');

    if (confirm('This adjustment date is set to ' + date[2] + '/' + date[1] + '/' + date[0] + ' at ' + location + '. Do you want to continue?')) {
        return 1;
    } else {
        return 0;
    }
};;
// ============================================================
// apps/stocktakenew.js
// ============================================================

var stockTakeNewChangeWarehouse = function () {
    var filterValue = "";
    var pdtType = $('#stockTakeNewpdttype').val();
    if (pdtType) {
        filterValue = pdtType.join();
    }

    formdata = "";
    if(localStorage.formValues) {
        formdata = localStorage.formValues;
    }
    value = document.getElementById('warehouse').value;
    document.getElementById('productresults').innerHTML = "<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;Loading...Please wait.";
    if (value!="") {
        document.getElementById('productresults').innerHTML = "<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;Loading...Please wait.";
        $.post(appPath +"/stocktakenew/products/"+ value +"/"+ filterValue , formdata, function(data) {
            document.getElementById('productresults').innerHTML = data;
        }).done(function( data ) {

            $('#product-table').DataTable({
                "columnDefs": [
                    { "orderable": false, "targets": [3,4,5] },
                    { "type": "natural", "targets": 0}
                ],
                "paging": false,
                "info": false
            });
        });
        $('#typefilter').removeClass('hidden').addClass('shown');
    } else {
        document.getElementById('productresults').innerHTML = "";
    }
};

var stockTakeNewValidateForm = function ()
{
    dateValue = document.getElementById('date').value;
    locationValue = document.getElementById('warehouse').value;
    var checkDate = $('#stockTakeNewIsFutureDate').val();

    if (dateValue ==   ""  ) {
        bootstrap_alert('danger', 'Missing mandatory fields.');
        return false;
    } else if (!isFutureDate(checkDate, dateValue)) {
        bootstrap_alert('danger', 'Date should be today or past date.');
        return false;
    }

    if (stockTakeNewConfirmation()) {
        // Gather values
        var formValues = $('#stockTakeNewForm').serialize();
        //  var url = $(this).attr('action');
        // postForm(url, formValues);
        if(typeof(Storage) !== "undefined") {
            //   console.log("Storage supported");
            localStorage.formValues = formValues;
            // console.log(localStorage.formValues);
        }

        if (!navigator.onLine) {
            alert ("You're currently offline, preventing the data submission");
            event.preventDefault();
            return false;
        }
        return true;
    }

    return false;
};

var stockTakeNewUpdateStock = function(id) {
    value = $('#product_'+id).val();
    if (value.trim() == "")
        value = 0;

    amount = $('#amount_'+id).val();
    if (amount != "" && amount >= 0) {
        $('#stock_count_'+id).val(parseInt(amount)-parseInt(value));
        $('#stock_display_'+id).text(parseInt(amount)-parseInt(value));

    } else if(amount < 0) {
        alert("Value cannot be negative");
        $('#amount_'+id).val(0);

    } else if (amount.trim() === "") {
        $('#stock_count_'+id).val(null);
        $('#stock_display_'+id).text('');

        $('#amount_'+id).val(null);
        return false;

    }
};

var stockTakeNewConfirmation = function ()
{
    var location = $("#warehouse option:selected").text();
    var dateval = $("#date").val();

    dateStr = (dateval.split('T'));
    date = dateStr[0].split('-');

    if (confirm ('This adjustment date is set to '+ date[2] +'/'+ date[1] + '/'+ date[0] + ' at '+ location + '. Do you want to continue?')) {
        return 1;
    } else {
        return 0;
    }
};

$(document).ready(function(){
    $('#stockTakeNewpdttype').multiselect();

    $("#stockTakeNewForm").submit(function(event){
        if(!stockTakeNewValidateForm ()) {
            event.preventDefault();
        }
    });
});;
// ============================================================
// apps/validate.js
// ============================================================

var isFutureDate = function(today, dateValue)
{
	var currentDate = new Date();

	// Find the current time zone's offset in milliseconds.
	var timezoneOffset = currentDate.getTimezoneOffset() * 60 * 1000;
	todayValue = today;
	today = new Date().valueOf();
	idate = dateValue.split("-");
	selectedValue = new Date(dateValue);
	newSelectedValue = selectedValue.getTime() + timezoneOffset;
	selectedValueAU = new Date(newSelectedValue);
    idateValue = selectedValueAU.valueOf();

    return (idateValue - today ) <= 0 ? true : false;
};

$(document).ready(function(){

    $("#form").submit(function(event){
        var prefix = $(this).data('method-prefix');
        var validateFunc = 'validateForm';

        if (prefix  !== undefined)
            validateFunc = prefix+'validateForm';

        if(!window[validateFunc]()) {
            event.preventDefault();
        }

    });
});

var getTimeJS = function(dateValue)
{
	var currentDate = new Date();

	// Find the current time zone's offset in milliseconds.
	var timezoneOffset = currentDate.getTimezoneOffset() * 60 * 1000;

	today = new Date().valueOf();
	idate = dateValue.split("-");
	selectedValue = new Date(dateValue);
	newSelectedValue = selectedValue.getTime() + timezoneOffset;
	date = new Date(newSelectedValue);
	month = date.getMonth()+1;

	return date.getFullYear() +"-" + (prependValue(month)) +"-" + prependValue(date.getDate()) + " " + prependValue(date.getHours()) + ":"+ prependValue(date.getMinutes()) + ":"+ prependValue(date.getSeconds());
}

function prependValue(value)
{
	if (value < 10) {
		return "0" + value;
	}
	return value;
}

;
// ============================================================
// crap.js
// ============================================================


// creates a jquery ui pop u dialog to add new licensor
$(document).ready(function() {

    var dialog = $('#dialog-form').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        dialogClass: "ui-modal-wrapper",
        buttons: {
            "Submit": function() {
                var form_data = {
                    id: $('input[name="l_id"]').val(),
                    name: $('input[name="l_name"]').val(),
                    status: $('input[name="l_status"]').val(),
                    ajax: 1
                };
                if ($("#licensor_form").valid()) {
                    $.ajax({
                        url: appPath + "/party/addNewParty",
                        type: 'POST',
                        data: form_data,
                        success: function(msg) {
                            var html = '<option value="' + msg.licensor_id + '" data-party_id="' + msg.party_id + '" data-name="' + form_data['name'] + '" selected>' + form_data['name'] + "</option>";
                            $('.form select[name="licensor_id"]').append(html);
                            $('#agreement_form input[name="la_licensor_name"]').append(html);
                            $('.form input[name="s_licensor_id"]').append(html);
                            $('#licensor_form')[0].reset();
                            $('#dialog-form').dialog("close");
                        }
                    });
                }
            },
            "Cancel": function() {
                $('#licensor_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    // Licensor dialog form ends here

    var dialog1 = $('#dialog-help1').dialog({
        autoOpen: false,
        resizable: true,
        width: 400,
        show: "fade",
        hide: "fade",
        position: {
            my: "left top",
            at: "right middle",
            of: '#help-id'
        }
    });

    var dialog2 = $('#dialog-helpdiv').dialog({
        autoOpen: false,
        resizable: true,
        width: 400,
        show: "fade",
        hide: "fade",
        position: {
            my: "left top",
            at: "right + 20",
            of: '.help_id'
        }

    });

    var dialog3 = $('#dialog-help2').dialog({
        autoOpen: false,
        show: "fade",
        hide: "fade",
        resizable: true,
        width: 400,
        position: {
            my: "left top",
            at: "right middle",
            of: '.help-icon2'
        }
    });

    var dialog4 = $('#dialog-helpdiv2').dialog({
        autoOpen: false,
        resizable: true,
        show: "fade",
        hide: "fade",
        width: 400,
        position: {
            my: "left top",
            at: "right + 10",
            of: '.help-icon3'
        }
    });

    var dialogConfig = $('#hint-dialog').dialog({
        autoOpen: false,
        resizable: true,
        width: 400,
        modal: true,
        buttons: {
            "OK": function() {
                $(this).dialog('close');
            }
        }
    });



    var dialog_site = $('#dialog-form3').dialog({
        autoOpen: false,
        resizable: true,
        width: 500,
        modal: true,
        dialogClass: "ui-modal-wrapper",
        buttons: {
            "Submit": function() {
                var form_data3 = {
                    "id": $('input[name="s_id"]').val(),
                    "name": $('input[name="s_name"]').val(),
                    "address": $('input[name="s_address"]').val(),
                    "city": $('input[name="s_city"]').val(),
                    "state": $('input[name="s_state"]').val(),
                    "postcode": $('input[name="s_postcode"]').val(),
                    "licensor_id": $('input[name="s_licensor_id"]').attr('data-id'),
                    "days_per_week": $('input[name="s_day_per_week"]').val(),
                    "category": $('select[name="s_category"]').val(),
                    "status": $('input[name="s_status"]').val(),
                    "security_phone_number": $('input[name="s_security_phone_number"]').val(),
                    "concierge_phone": $('input[name="s_concierge_phone"]').val(),
                    "ajax": 1
                };
                if ($('#site_form').valid()) {
                    $.ajax({
                        url: appPath + '/site/addNewSite',
                        type: 'POST',
                        data: form_data3,
                        success: function(msg) {

                            var html = "<option value='" + form_data3['name'] + "' data-id=" + form_data3['id'] + " selected>" + form_data3['name'] + "</option>";
                            $('.form select[name="site_id"]').append(html);
                            $('#site_form')[0].reset();
                            $('#dialog-form3').dialog("close");

                        }
                    });

                }
            },
            "Cancel": function() {
                $('#site_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });

    var dialog_location = $('#dialog-form4').dialog({
        autoOpen: false,
        resizable: true,
        width: 500,
        modal: true,
        dialogClass: "ui-modal-wrapper",
        buttons: {
            "Submit": function() {
                var form_data4 = {
                    'location_id': $('input[name="location_id"]').val(),
                    "name": $('input[name="k_name"]').val(),
                    "site_id": $('input[name="k_site"]').val(),
                    "sales_multiplier": $('input[name="k_sales_multiplier"]').val(),
                    "location_within_site": $('textarea[name="k_location_within_site"]').val(),
                    "status": $('input[name="k_status"]').val(),
                    "warehouse_id": $('select[name="k_warehouse_id"]').val(),
                    "latitude": $('input[name="k_latitude"]').val(),
                    "longitude": $('input[name="k_longitude"]').val(),
                    "loading_dock": $('textarea[name="k_loading_dock"]').val(),
                    "photo": $('input[name="k_photo"]').val(),
                    "ajax": 1
                };
                if ($('#location_form').valid()) {
                    $.ajax({
                        url: appPath + '/kiosklocation/addNewKioskLocation',
                        type: 'POST',
                        data: form_data4,
                        success: function(msg) {

                            var html = "<option value=" + form_data4['location_id'] + " selected>" + msg.name + "</option>";
                            $('.form select[name="location_id"]').append(html);
                            $('#location_form')[0].reset();
                            $('#dialog-form4').dialog("close");
                        }
                    });

                }
            },
            "Cancel": function() {
                $('#location_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });

    //Add Unit of Measure Pop Up Dialog form
    var dialogUom = $('#dialog-uom').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        buttons: {
            "Submit": function() {
                var name = $('input[name="uom_name"]').val();
                if ($("#uom_form").valid()) {
                    var html = "<option value='" + name + "' selected>" + name + "</option>";
                    $('#config_form select[name="uom"]').append(html);
                    $('#uom_form')[0].reset();
                    $('#dialog-uom').dialog("close");
                }
            },
            "Cancel": function() {
                $('#uom_form')[0].reset();
                $(this).dialog("close");
            }

        }

    });


    /* Buttons for Agreement page */
    $('.licenseAdd').click(function() {
        dialog.dialog('open');
    });

    // Mouse hover effect on clicking help-id button
    $('#help-id').on("mouseenter", function() { //first help icon in License Agreement Page
        dialog1.dialog('open').parent().find('.ui-dialog-titlebar-close').hide(); //this is to close the titlebar on the dialog box
    }).on("mouseleave", function() {
        dialog1.dialog('close');

    });

    $('#help-iconid').hover( //second help icon in License Agreement Page
        function() {
            dialog3.dialog('open').parent().find('.ui-dialog-titlebar-close').hide();
            //console.log("Mouse enter")
        },
        function() {
            dialog3.dialog('close');
            //console.log("Mouse Leave")
        });
    $('#help-iconlast').hover( //third help icon in License Agreement Page
        function() {
            dialog3.dialog('open').parent().find('.ui-dialog-titlebar-close').hide();
        },
        function() {
            dialog3.dialog('close');
        });

    $('.help_id').hover( //First help icon in Deployments page
        function() {
            dialog2.dialog('open').parent().find('.ui-dialog-titlebar-close').hide();
        },
        function() {
            dialog2.dialog('close');
        });

    $('.help-icon3').hover(function() { //Second help icon in Deployments Page
        dialog4.dialog('open').parent().find('.ui-dialog-titlebar-close').hide();
    }, function() {
        dialog4.dialog('close');
    });

    $('#hint-id').mouseover(function() {
        dialogConfig.dialog('open');
    });
    $('#unit_add').click(function() {
        dialogUom.dialog('open');
    });

    /*Buttons for Agreement page ends here */
    /* Buttons for Deployment Page */

    $('#add_site').click(function() { //on clicking the Add agreement button, the dialog box opens as well as es populated with the Licensor
        var la = $('#d_lid option:selected').attr('data-name');
        $('#s_licensor_id').val(la);
        var l_id = $('#d_lid option:selected').val();
        $('#s_licensor_id').attr('data-id', l_id);
        dialog_site.dialog('open');
    });

    $('#add_location').click(function() {
        var k_id = $('#site_id option:selected').val();
        //alert(k_id);
        $('#k_site_id').val(k_id);
        var kname = $('#site_id option:selected').attr('data-id');
        //alert(kname);
        $('#k_site').val(kname);

        dialog_location.dialog('open');
    });

    function checkRules() {
        var msg = "";
        console.log($('#la_fixed').val());
        if ((($('#la_fixed').val() == "") || ($('#la_fixed').val() == "0.00")) &&
            (($('#la_commission1').val() == "") || ($('#la_commission1').val() == "0.0000"))
        ) 
        {
            msg = "Fixed Rate or Commission should be provided.";
        }

        if ($('#la_commission1').val() != "" && $('#la_commission1').val() != '0.0000') {
            // threshold should be present.
            if ($('#la_commission1_threshold').val() == "" || $('#la_commission1_threshold').val() == '0.00') {
                msg = "Commission 1 Threshold should be provided.";
            }
            if ($('#la_commission2').val() != "" && $('#la_commission2').val() != '0.0000') {
                // threshold should be present.
                if ($('#la_commission2_threshold').val() == "" || $('#la_commission2_threshold').val() == '0.00') {
                    msg = "Commission 2 Threshold should be provided.";
                }
            }


        } else {
            // commission 2 and threshold should be blank.
            if (($('#la_commission2').val() != "" && $('#la_commission2').val() != '0.0000') ||
                $('#la_commission2_threshold').val() != "" && $('#la_commission2_threshold').val() != '0.00') {
                msg = "Commission 2 or threshold should be provided after commission #1.";
            }
        }

        if (msg != "") {
            $('.alert').html(msg);
            $('.alert').show();
            msg = "";
            return false;
        }
        return true;

    }
    $("#agreement_form input[type='text']").blur(function() {
        $('.alert').hide();
    })
});

// jquery dialogues and actions for add/edit product page starts here
$(document).ready(function() {
    // add new product category dialog starts here
    var dialog_product_category_form = $('#dialog_product_category_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        buttons: {
            "Submit": function() {
                var form_data = {
                    name: $('input[name="category_name"]').val(),
                    name_abbreviation: $('input[name="name_abbreviation"]').val(),
                    parent_product_category_id: $('select[name="parent_product_category_id"]').val(),
                    status: $('#category_status').val(),
                };
                if ($("#product_category_form").valid()) {
                    $.ajax({
                        url: appPath + "/productcategory/add_new_product_category",
                        type: 'POST',
                        data: form_data,
                        dataType: 'json',
                        success: function(data) {
                            $('#product_category_form')[0].reset();
                            $('#parent_product_category_id').append('<option value="' + data.inserted_id + '">' + form_data.name + '</option>');
                            $('#category_select').append('<option value="' + data.inserted_id + '" selected>' + form_data.name + '</option>');
                            $('#dialog_product_category_form').dialog("close");
                        }
                    });
                }
            },
            "Cancel": function() {
                $('#product_category_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    $('#add_category').click(function() {
        dialog_product_category_form.dialog('open');
    });
    // add new product category dialog ends here

    // upload product images dialog starts here
    var dialog_upload_product_images_form = $('#dialog_upload_product_images_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 650,
        modal: true,
        buttons: {
            "Cancel": function() {
                $(this).dialog("close");
            }
        }
    });
    $('#upload_product_images_button').click(function() {
        dialog_upload_product_images_form.dialog('open');
    });
    // upload product images dialog ends here

    // add new product attribute dialog starts here
    var new_product_attribute_form = $('#dialog_new_product_attribute_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 600,
        modal: true,
        buttons: {
            "Submit": function() {
                var paramObj = $('#new_product_attribute_form').serializeObject();


                if ($("#new_product_attribute_form").valid()) {
                    $.ajax({
                        url: appPath + "/productattribute/save_product_attribute",
                        type: 'POST',
                        data: paramObj,
                        dataType: 'json',
                        success: function(data) {
                            console.log(data)
                            // adding the new option to the new attribute option dropdown for product
                            $('#new_attribute_values').append('<option data-unit="' + data.unit + '" value="' + data.id + '">' + data.name + '</option>');
                            // getting that option selected
                            $('#new_attribute_values').val(data.id);
                            // enabling the add product attribute button
                            $('#add_new_product_attribute_button').prop('disabled', false);
                            $('#dialog_new_product_attribute_form').dialog("close");
                            $('#new_product_attribute_form')[0].reset();

                            new AddProductAttribute(data);
                        }
                    });
                }
            },
            "Cancel": function() {
                $('#new_product_attribute_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    $('#new_product_attribute_button').click(function() {
        new_product_attribute_form.dialog('open');
    });
    // add new product attribute dialog ends here

    // add new unit dialog starts here
    var new_unit_form = $('#new_unit_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        dialogClass: "ui-modal-wrapper",
        buttons: {
            "Add Unit": function() {
                var unit_name = $('input[name="unit_name"]').val();
                if ($("#new_unit_form").valid()) {
                    $('#unit_of_measure_id_dropdown').append('<option value="' + unit_name + '" selected>' + unit_name + '</option>');
                    $('#new_unit_form')[0].reset();
                    $(this).dialog("close");
                }
            },
            "Cancel": function() {
                $('#new_unit_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    $('#add_new_unit_button').click(function() {
        new_unit_form.dialog('open');
    });
    // add new unit dialog ends here

    // add new product attribute dialog starts here
    var dialog_new_attribute_option_form = $('#new_attribute_option_form').dialog({
        autoOpen: false,
        resizable: true,
        width: 300,
        modal: true,
        dialogClass: "ui-modal-wrapper",
        buttons: {
            "Submit": function() {
                var form_data = {
                    option_name: $('input[name="option_name"]').val(),
                    sku_suffix: $('input[name="sku_suffix"]').val().toUpperCase(),
                };



                if ($("#new_attribute_option_form").valid()) {
                    $('#products_attribute_options_table tbody').append('<tr class="product_attribute_row"><td><input type="hidden" name="attribute_option_name[]" value="' + form_data.option_name + '" />' + form_data.option_name + '</td><td><input type="hidden" name="attribute_option_suffix[]" value="' + form_data.sku_suffix + '" />' + form_data.sku_suffix + '</td><td><a class="remove-attr-popup btn" onclick="remove_row($(this));"><i class="fa fa-trash-o" aria-hidden="true"></i> </a></td></tr>');
                    $('#new_attribute_option_form')[0].reset();
                    $(this).dialog("close");
                }
            },
            "Cancel": function() {
                $('#new_attribute_option_form')[0].reset();
                $(this).dialog("close");
            }
        }
    });
    $('#add_attribute_option_button').click(function() {
        $('#grey_box_attribute').val($('#new_product_attribute_form input[name="attribute_name"]').val());
        dialog_new_attribute_option_form.dialog('open');
    });
    // add new product attribute dialog ends here
})
// jquery dialogs and actions for add/edit product page ends here

function remove_row(obj) {
    obj.parent().parent().remove();
}

// form data to json
$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
