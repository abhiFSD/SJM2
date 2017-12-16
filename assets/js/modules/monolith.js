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


    });;