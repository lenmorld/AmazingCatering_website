function NativeAppAPI(){}var appVar=new NativeAppAPI();if(navigator.appName=="Microsoft Internet Explorer"){appVar=window.external}else{NativeAppAPI.prototype.isMac=true;appVar=window.nativeApp}if(!appVar||appVar.platform==undefined){NativeAppAPI.prototype.pushUndo_=function(a){};NativeAppAPI.prototype.displayAlert_=function(a){alert(a)};NativeAppAPI.prototype.projectName=function(){return"project_name"};NativeAppAPI.prototype.bringToFront=function(){return};NativeAppAPI.prototype.canStoreInDataBase=function(){return true};NativeAppAPI.prototype.canEnablePayments=function(){return true};NativeAppAPI.prototype.canAttachToMail=function(){return true};NativeAppAPI.prototype.resetFocus=function(){};NativeAppAPI.prototype.rescale=function(){};NativeAppAPI.prototype.getPredefinedList_=function(a){};NativeAppAPI.prototype.updateNameInSettings_=function(a){};NativeAppAPI.prototype.getBackGroundImage_=function(a){};NativeAppAPI.prototype.displayAlertEnablePayments=function(){return};if(navigator.appName=="Microsoft Internet Explorer"){NativeAppAPI.prototype.platform=function(){return"win"}}else{NativeAppAPI.prototype.platform=function(){return"mac"}}appVar=new NativeAppAPI()}function js_form_is_empty(){var a=true;var b=document.getElementById("column1");if((b.childNodes.length>0)||($("#docContainer").attr("captcha")==true)){a=false}return a.toString()}function js_focused(){if(fb_panel.inputFocused){return"YES"}else{return"NO"}}function js_input_text_selected(){return fb_editor.isTextSelected()}function js_isitemselected(){if(!fb_panel.inputFocused&&fb_container.itemSelected){return"YES"}else{return"NO"}}function js_isitem(a){var b=/"source":/;if(b.test(a)){return"YES"}else{return"NO"}}function js_copy(){return fb_clipboard.doCopy()}function js_cut(){return fb_clipboard.doCut()}function js_paste(a){return fb_clipboard.doPaste(a)}function js_delete(){return fb_clipboard.doDelete()}function js_undo_change(a){fb_undo.doUndo(a)}function js_redo_change(a){fb_undo.doRedo(a)}function js_get_Counters(){var a=fb_toolbox.itemsCounter;return a.toString()}function js_get_html(){fb_editor.triggerActiveElementChange();if(fb_container.itemSelected){$(fb_container.itemSelected).removeClass("selected-object")}var a=fb_utils.getouterHtml($("#docContainer").get(0),true,true);if(fb_container.itemSelected){$(fb_container.itemSelected).addClass("selected-object")}return a}function js_get_validation(){return pValidationConfig.get_json()}function js_get_validation_for_client(){return pValidationConfig.get_json_for_client()}function js_get_validation_for_server(){var a=pValidationConfig.get_json_for_server();return a}function js_get_payments_rules(){return pPaymentsConfig.get_json()}function js_get_payments_rules_for_server(){return pPaymentsConfig.get_json_for_server()}function js_put_Counters(a){fb_toolbox.itemsCounter=a}function js_put_html(a){$(".ui-layout-center").html(a);fb_container.initLoaded();fb_views.form();$("#elements").trigger("click")}function js_put_validation(a){pValidationConfig.set_json(a)}function js_put_payments_rules(a){pPaymentsConfig.set_json(a)}function js_get_preview_html(){fb_editor.triggerActiveElementChange();var b=appVar.projectName();var d="<title>"+decodeURIComponent(b)+"</title>";if(fb_container.itemSelected){$(fb_container.itemSelected).removeClass("selected-object")}var c=document.getElementById("theme").getAttribute("href");var a='<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';a+='<script type="text/javascript" src="common/js/form_init.js" id="form_init_script" data-name="" ><\/script>\n';a+='<link rel="stylesheet" type="text/css" href="'+c+'" id="theme" />\n';a+=d;a+="</head><body>";a+=get_form_html("preview",false);a+="</body></html>";if(fb_container.itemSelected){$(fb_container.itemSelected).addClass("selected-object")}return style_html(a)}function js_get_publish_body(c,a,d){fb_editor.triggerActiveElementChange();if(fb_container.itemSelected){$(fb_container.itemSelected).removeClass("selected-object")}var b="<!-- Start of the body content for CoffeeCup Web Form Builder -->\n";b+=get_form_html(d,c,a)+"\n";b+="<!-- End of the body content for CoffeeCup Web Form Builder -->\n";if(fb_container.itemSelected){$(fb_container.itemSelected).addClass("selected-object")}return b}function js_get_publish_header(c){var b=c?"":appVar.projectName()+"/";var d=document.getElementById("theme").getAttribute("href");var a="<!-- Start of the headers for CoffeeCup Web Form Builder -->\n";a+='<script type="text/javascript" src="'+b+'common/js/form_init.js" data-name="'+b+'" id="form_init_script"><\/script>\n';a+='<link rel="stylesheet" type="text/css" href="'+b+d+'" id="theme" />\n';a+="<!-- End of the headers for CoffeeCup Web Form Builder -->\n";return a}function js_get_publish_html(a,d){fb_editor.triggerActiveElementChange();var c=get_css_file("theme",true);var e="<title>"+decodeURIComponent(appVar.projectName())+"</title>";if(fb_container.itemSelected){$(fb_container.itemSelected).removeClass("selected-object")}var b='<!DOCTYPE HTML>\n<html>\n<head>\n<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">\n';b+=js_get_publish_header(true)+"\n";b+=e+"\n";b+="</head>\n<body>\n";b+=js_get_publish_body(true,a,d)+"\n";b+="</body>\n</html>";if(fb_container.itemSelected){$(fb_container.itemSelected).addClass("selected-object")}return style_html(b)}function js_get_embedded_code(a){if(fb_container.itemSelected){$(fb_container.itemSelected).removeClass("selected-object")}var d=(parseInt($("#docContainer").outerHeight(true))+30);if(appVar.platform()=="win"){d+=$(".column").children().length*5}var b="www.coffeecup.com/api/sdrive/forms/form.js";var c="<script type=\"text/javascript\">document.write(unescape(\"%3Cscript src='http\" +  (document.location.protocol == 'https:' ? 's' : '') + \"://";c+=b+"?name="+appVar.projectName();c+="%26slug="+a;c+="%26width="+(parseInt($("#docContainer").outerWidth(true)));c+="%26height="+d;c+="' type='text/javascript'%3E%3C/script%3E\"));<\/script>";if(fb_container.itemSelected){$(fb_container.itemSelected).addClass("selected-object")}return c}function js_get_manual_iframe(){if(fb_container.itemSelected){$(fb_container.itemSelected).removeClass("selected-object")}var c=(parseInt($("#docContainer").outerHeight(true))+30);var e=(parseInt($("#docContainer").outerWidth(true)));if(appVar.platform()=="win"){c+=$(".column").children().length*6}var d=appVar.projectName();var b='<script type="text/javascript">document.write(unescape("%3Ciframe src=\\"'+d+"/"+d+'.html\\"';b+=' width=\\"'+e+'\\"';b+=' height=\\"'+c+'\\"';b+='allowtransparency=\\"true\\" scrolling=\\"no\\" frameborder=\\"0\\"%3E';b+='&lt;a href=\\"'+d+'.php\\" title=\\"'+d+'\\"&gt;Check out my CoffeeCup Form&lt;/a&gt;%3C/iframe%3E"));<\/script>\n';var a='<noscript><iframe width="'+e;a+='" height="'+c;a+='" style="border:none; background:transparent; overflow:hidden;" src="'+d+"/"+d+'.html">';a+='&lt;a href="'+d+'.php" title="'+d+'"&gt;Check out my CoffeeCup Form&lt;/a&gt;</iframe></noscript>';if(fb_container.itemSelected){$(fb_container.itemSelected).addClass("selected-object")}return b+style_html(a)}function js_apply_theme(c,f,b){var a=[];var e=[];$("#css_ui").next().remove();$("#css_ui").after('<link id="theme" rel="stylesheet" type="text/css" href="'+c+"?version="+Math.floor(Math.random()*1001)+'"/>');if(f!=undefined&&f.length>0){a=JSON.parse(f)}if(b!=undefined&&b.length>0){e=JSON.parse(b)}pValidationConfig.set_plugins(a,e);$(".jsplugin").remove();$(".cssplugin").remove();for(var d=0;d<a.length;d++){var g=document.createElement("script");g.setAttribute("type","text/javascript");g.setAttribute("src",a[d]);g.setAttribute("class","jsplugin");document.getElementsByTagName("head")[0].appendChild(g)}for(var d=0;d<e.length;d++){$("head").append('<link rel="stylesheet" type="text/css" href="'+e[d]+'" class="cssplugin"/>')}if($("#formproperties").hasClass("current")){window.setTimeout(function(){fb_views.form()},1000)}if($("#objectproperties").hasClass("current")){window.setTimeout(function(){if(fb_container.itemSelected){fb_container.itemSelected.trigger("mousedown")}},1000)}if(($("#fb-submit-button").attr("style")!=undefined)&&($("#fb-submit-button").attr("style").search("theme/")>0)){$("#fb-submit-button").css("background-image","")}fb_panel.applyBackgroundHover("fb-submit-button")}function get_form_html(f,b,d){var l=$(".ui-layout-center").clone();var m=appVar.projectName();l.find(".placeholder").removeClass("placeholder");l.find(".fb-spacer-placeholder").removeClass("fb-spacer-placeholder");l.find("#docContainer").attr("data-form",f);if(f!="preview"){if(b&&d.length==0){l.find("#docContainer").attr("action","../"+m+".php")}else{l.find("#docContainer").attr("action",d)}l.find("#docContainer .column").prepend('<div style="display:none;" id="fb_error_report" ></div>');l.find("#docContainer .column").prepend('<div style="display:none; min-height:'+$(".column").height()+'px;" id="fb_confirm_inline" ></div>');$.each($(".fb-hint",l.find("#docContainer")),function(r,q){var o=$("*",$(q).parent());for(var p=0;p<o.length;p++){if($(o[p]).attr("data-hint")!=undefined){$(o[p]).attr("data-hint","");break}}});var h='<input type="hidden" name="fb_form_custom_html">';var k='<input type="hidden" name="fb_form_embedded">';var c='<input type="hidden" id="fb_js_enable" name="fb_js_enable">';l.find("#docContainer").append(h);l.find("#docContainer").append(k);l.find("#docContainer").append(c)}else{l.find("#docContainer").attr("action","")}if(f=="manual"||f=="automated"){var j=4;if(navigator.appName=="Microsoft Internet Explorer"){j=5}$.each($("#docContainer img",l),function(p,o){$(o,l).attr("src",m+"/"+$(o,l).attr("src"))});$.each($("#docContainer, #docContainer *",l),function(t,s){var r=$(s,l).attr("style");if(r&&r.search(/background-image/i)!=-1){var p=$(s,l).css("background-image");var u=p.search(/file/i);var o=p.search("common");if(o==-1){o=p.search("theme")}if(u>=0){var q=p.substr(u,o-u);p=p.replace(q,"")}p=p.substr(0,j)+m+"/"+p.substr(j,p.length);$(s,l).css("background-image",p)}})}var g=pValidationConfig.get_config_value("reCaptcha","captcha_status");var e=g=="manual"?pValidationConfig.get_config_value("reCaptcha","public_key"):"_FB_RECAPTCHA_";if(f!="preview"){if(g!="none"&&g!=""){l.find("#fb-captcha_control").get(0).innerHTML="__FB__CAPTCHA__"}else{l.find("#fb-captcha_control").remove()}}var n=style_html(fb_utils.getouterHtml(l.find("#docContainer").get(0),false,true));var a=pValidationConfig.get_config_value("reCaptcha","theme");a=a.length?a:"clean";var i='<script type="text/javascript">var RecaptchaOptions = { theme : \''+a+'\' };<\/script><script type="text/javascript"									   src="https://www.google.com/recaptcha/api/challenge?k='+e+'">									<\/script>									<noscript>										<iframe src="https://www.google.com/recaptcha/api/noscript?k='+e+'"											height="300" width="500" style="border:none;"></iframe><br>										<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>										<input type="hidden" name="recaptcha_response_field" value="manual_challenge">									</noscript>';n=n.replace("__FB__CAPTCHA__",i);return n}function get_css_file(f,d){var a=$("link[id="+f+"]").clone();var b=a.attr("href");if(d){a.attr("href",appVar.projectName()+"/"+b)}var e=$('<div style="display:none"></div>');a.appendTo(e);var c=e.html();e.remove();return c}function get_elements_names(a){return JSON.stringify(pValidationConfig.get_all_names(a))}function get_email_names(){return JSON.stringify(pValidationConfig.get_email_names())}function get_contactlist_names(){return JSON.stringify(pValidationConfig.get_contactlist_names())}function js_disable_store_in_database(){fb_panel.disableStoreInDatabase()}function js_disable_attach_to_mail(){fb_panel.disableAttachToMail()}function js_refresh_editor(){fb_panel.refreshEditor()}function js_reset_focus(a){$(a).focus();if(appVar.platform()=="win"){$("body").css("zoom","1");$("body").css("zoom","")}}function js_force_resize(){$(window).trigger("resize")}function js_reset_styles(){fb_editor.resetStyles()}function js_apply_list(a,b){fb_panel.applyPredefinedList(a,b)}function js_apply_background_image(a,c){var b=encodeURIComponent(c);if(a=="fb-submit-button"){fb_panel.applySubmitButtonBackGroundImage(a,b)}else{if(a=="fb-logo1"){fb_panel.applyLogoBackGroundImage(a,b)}else{if((a=="fb-form-header1")||(a=="fb-submit-button-div")||(a=="docContainer")){fb_panel.applyGenerealBackGroundImage(a,b)}else{fb_panel.applyImageElementSrc(a,b)}}}}function js_get_images_applied(){return decodeURIComponent(fb_utils.getAllImages().join("|"))}function js_get_mailchimp_field_type(g){var f=new Array();var e=new Array();switch(g){case"address":case"text":var b=$("input","#docContainer");for(var c=0;c<b.length;c++){if($(b[c]).get(0).id.search("text")!=-1){e.push(b[c])}}break;case"zip":var b=$(".fb-regex input","#docContainer");for(var c=0;c<b.length;c++){if(pValidationConfig.get_config_value($(b[c]).get(0).id,"regex_config").option=="ZIP Code (USA)"){e.push(b[c])}}break;case"number":var b=$("input","#docContainer");for(var c=0;c<b.length;c++){if($(b[c]).get(0).id.search("number")!=-1){e.push(b[c])}}break;case"email":var b=$("input","#docContainer");for(var c=0;c<b.length;c++){if($(b[c]).get(0).id.search("email")!=-1){e.push(b[c])}}break;case"radio":e=$("input[type=radio]","#docContainer");break;case"checkboxes":e=$("input[type=checkbox]","#docContainer");break;case"dropdown":var b=$("select","#docContainer");for(var c=0;c<b.length;c++){if(navigator.appName=="Microsoft Internet Explorer"){if(($(b[c]).get(0).id.search("select")!=-1)&&(!$(b[c]).get(0).getAttribute("multiple"))){e.push(b[c])}}else{if(($(b[c]).get(0).id.search("select")!=-1)&&($(b[c]).get(0).getAttribute("multiple")==null)){e.push(b[c])}}}break;case"date":e=$(".datepicker","#docContainer");break;case"birthday":var b=$("input, select","#docContainer");var d=$("select[multiple]","#docContainer").get();for(var c=0;c<b.length;c++){if($(b[c]).get(0).id.search("number")!=-1||$(b[c]).get(0).id.search("text")!=-1||$(b[c]).get(0).id.search("select")!=-1){e.push(b[c])}}for(var c=0;c<d.length;c++){var a=e.indexOf(d[c]);if(a!=-1){e.splice(a,1)}}break;case"phone":var b=$("input","#docContainer");for(var c=0;c<b.length;c++){if($(b[c]).get(0).id.search("tel")!=-1){e.push(b[c])}}break;case"imageurl":var b=$("input","#docContainer");for(var c=0;c<b.length;c++){if($(b[c]).get(0).id.search("url")!=-1){e.push(b[c]);continue}if($(b[c]).get(0).id.search("file")!=-1){e.push(b[c]);continue}}break;case"url":var b=$("input","#docContainer");for(var c=0;c<b.length;c++){if($(b[c]).get(0).id.search("url")!=-1){e.push(b[c]);continue}}break}$.each(e,function(j,i){var h=$(i).attr("name");if($(i).attr("type")=="checkbox"){h=h.substr(0,h.length-2)}if($.inArray(h,f)<0){f.push(h)}});return(f.join("|"))}function js_get_mailchimp_subscribe(){var b=new Array();var a=$("input[type=checkbox], input[type=radio], select","#docContainer").get();$.each(a,function(e,d){var c=$(d).attr("name");if($(d).attr("type")=="checkbox"){b.push(c.substr(0,c.length-2)+"="+$(d).attr("value"))}else{if(d.tagName.toLowerCase()=="select"){if($(d).attr("multiple")){c=c.substr(0,c.length-2)}$.each($("option",d),function(g,f){b.push(c+"="+$(f).attr("value"))})}else{b.push(c+"="+$(d).attr("value"))}}});return(b.join("|"))}function js_create_mailchimp_fields(a){var n=document.getElementById("docContainer");var l=fb_undo.getFormParams(n);var s=fb_editor.JSONParser.parse(a);var e=new Array();for(var q=0;q<s.length;q++){var r=fb_utils.createDiv("item"+ ++fb_toolbox.itemsCounter,$(this).data("class"),null,null);fb_editor.backgroundActive();$("#column1").append(r);var b="";var m=false;switch(s[q].field_type){case"address":case"text":b="tool_textfield";m=true;break;case"zip":b="tool_regex";m=true;break;case"birthday":case"number":b="tool_number";m=true;break;case"email":b="tool_email";m=true;break;case"radio":b="tool_radio";break;case"dropdown":b="tool_dropdown";break;case"date":b="tool_date";break;case"phone":b="tool_phone";m=true;break;case"imageurl":b="tool_upload";break;case"url":b="tool_url";m=true;break;case"checkboxes":b="tool_checkbox";break;default:b="tool_textfield";m=true;break}$("#item"+fb_toolbox.itemsCounter).html($("#"+b).data("markup"));$("#item"+fb_toolbox.itemsCounter).addClass($("#"+b).data("class"));$("#item"+fb_toolbox.itemsCounter).mousedown(fb_editor.itemToSelectable);var k=document.getElementById("item"+fb_toolbox.itemsCounter);var o="";if(k){o=fb_toolbox.generateIds(k);fb_toolbox.assignDefaultValidation(k);fb_toolbox.generateNames(k)}if(s[q].choices.length){switch(s[q].field_type){case"dropdown":$("#"+o).children().remove();if(s[q].required){r='<option value="">Choose one</option>';$("#"+o).append(r)}for(var p=0;p<s[q].choices.length;p++){r='<option value="'+s[q].choices[p]+'" >'+s[q].choices[p]+"</option>";$("#"+o).append(r)}fb_toolbox.generateIds($("#"+o).parent().parent()[0]);break;case"radio":case"checkboxes":var c=s[q].field_type=="radio"?"radio":"checkbox";var f=$("#"+o).attr("name");var g=$("#"+o).parent().parent();g.children().remove();for(var p=0;p<s[q].choices.length;p++){r='<label><input type="'+c+'" value="'+s[q].choices[p]+'" name="'+f+'"><span class="fb-fieldlabel">'+s[q].choices[p]+"</span></label>";g.append(r)}fb_toolbox.generateIds(g.parent()[0]);break}}if(s[q].required){$("#"+o).attr("required","required");pValidationConfig.set_config_type(o,"required",true)}if(m&&s[q].field_size){$("#"+o).attr("maxlength",s[q].field_size);pValidationConfig.set_config_type(o,"maxlength",s[q].field_size)}if(s[q].field_type=="zip"){pValidationConfig.set_config_type(o,"regex_config",{option:"ZIP Code (USA)",value:pValidationConfig.reg_exp_defaults["ZIP Code (USA)"]})}if(s[q].name!=undefined){var t;if($("#"+o).attr("type").toLowerCase()=="checkbox"||$("#"+o).attr("type").toLowerCase()=="radio"){t=$("#"+o).parent().parent().parent().children().children()[0]}else{t=$("#"+o).parent().parent().children().children()[0]}$(t).html(s[q].name);if(s[q].name==""){$(t).css("display","none")}else{$(t).css("display","inline")}pValidationConfig.set_config_type(o,"label",s[q].name)}var d=$("#"+o).attr("name");if($("#"+o).attr("type")=="checkbox"){d=d.substr(0,d.length-2)}e.push({tag:s[q].tag,field_match:d})}$("#layout-center").scrollTop($("#layout-center").attr("scrollHeight"));l.markupredo=fb_utils.getouterHtml(n);var h=fb_editor.JSONParser.stringify(l);appVar.pushUndo_(h);return JSON.stringify(e)};