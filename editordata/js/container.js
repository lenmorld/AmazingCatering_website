var fb_container={olderbrother:"",itemSelected:null,sortableProps:{start:function(a,b){$(".column").addClass("current");$("#layout-center").css("cursor","-webkit-grabbing");fb_container.olderbrother="";if($(b.item[0]).prev()[0]){fb_container.olderbrother=$(b.item[0]).prev()[0].id}if($(b.item).hasClass("fb-item")){$(".ui-placeholder-bis").width($(b.item[0]).width())}},opacity:0.5,cursor:"copy",accept:".drag_tool",connectWith:".column",placeholder:"ui-placeholder",forcePlaceholderSize:"true",stop:function(c,d){fb_editor.backgroundActive();$(".column").removeClass("current");$("#layout-center").css("cursor","auto");var e="";if($(d.item[0]).prev().get(0)){e=$(d.item[0]).prev().get(0).id}if((e!=fb_container.olderbrother)&&!fb_undo.isInsertAction){var a={};a.type_operation="move";a.markup=fb_utils.getouterHtml(d.item[0]);a.id_father=this.id;a.id_element=d.item[0].id;a.id_previous_brother=fb_container.olderbrother;a.id_newbrother=e;var b=fb_editor.JSONParser.stringify(a);appVar.pushUndo_(b)}$(d.item[0]).css("filter","");$(d.item[0]).css("zoom","");$(d.item[0]).css("z-index","")}},init:function(){if($("#section1","#docContainer").length>0){return}$("#docContainer").append(fb_utils.createDiv("section1","section",null,fb_utils.createDiv("column1","column",null,null)));$("#docContainer").append(fb_utils.createDiv("fb-captcha_control","fb-captcha","text-align:center; display:none; cursor: default;",fb_utils.createImg(null,null,null,"editordata/images/recaptcha.png")));$("#docContainer").append(fb_utils.createDiv("fb-submit-button-div","fb-item-alignment-left",null,'<input type="submit" value="Submit" class="fb-button-special" id="fb-submit-button">'));$(".column").addClass("img_background");$("#docContainer").prepend(fb_utils.createDiv("fb-form-header1","fb-form-header","",'<a id="fb-link-logo1" class="fb-link-logo" href="" target="_blank"><img alt="Alternative text" title="Alternative text" id="fb-logo1" class="fb-logo" src="common/images/image_default.png" style="display:none"/></a>'));$("#fb-form-header1").css("min-height","20px");var a=document.getElementById("docContainer");fb_toolbox.assignDefaultValues(a,"form");var b=document.getElementById("fb-submit-button");fb_toolbox.assignDefaultValues(b,"submit");pValidationConfig.set_config_subtype("fb-submit-button","hover","background-image","");$("#column1").sortable(fb_container.sortableProps);fb_editor.notBackgroundActive();$("#docContainer input, #docContainer select").live("mousedown",function(c){c.preventDefault();return false})},initLoaded:function(){$(".fb-item").removeClass("selected-object");$("#fb-submit-button-div").removeClass("selected-object");fb_container.legacyOldProjects();var g=$("#docContainer").children();for(var h=0;h<g.length;h++){var c=$(g[h]).children();for(var e=0;e<c.length;e++){if($(c[e]).hasClass("column")){$(c[e]).sortable(fb_container.sortableProps);var b=$(c[e]).children();for(var d=0;d<b.length;d++){$(b[d]).mousedown(fb_editor.itemToSelectable)}}}}$("#docContainer a").live("click",function(i){i.preventDefault();return false});$("#fb-submit-button-div").mousedown(fb_editor.itemToSelectable);$("#fb-form-header1").mousedown(fb_editor.itemToSelectable);$("#docContainer").click(function(){if(fb_container.itemSelected){fb_container.itemSelected.trigger("mousedown")}});fb_container.itemSelected=null;fb_editor.notBackgroundActive();fb_panel.clear();$("#control_properties_set").append('<p id="properties_placeholder">Select an element and its editable properties will appear here.</p>');fb_editor.initPlaceholderFallback();var n=$("#fb-captcha_control img").attr("src");var f=n.search("editordata/images");var l=n.substr(0,f);n=n.replace(l,"");$("#fb-captcha_control img").attr("src",n);$("#fb-captcha_control").dblclick(function(){$("#formproperties").trigger("click");return false});for(var a in pValidationConfig.validation_config){if(pValidationConfig.validation_config.hasOwnProperty(a)){for(var m in pValidationConfig.validation_config[a]){if(m=="hover"){fb_panel.applyBackgroundHover(a);break}}}}},legacyOldProjects:function(){if(!$("#fb-form-header1").length){$("#docContainer").prepend(fb_utils.createDiv("fb-form-header1","fb-form-header","",'<a id="fb-link-logo1" class="fb-link-logo" href="" target="_blank"><img alt="Alternative text" title="Alternative text" id="fb-logo1" class="fb-logo" src="common/images/image_default.png" style="display:none"/></a>'))}if(!$("#fb-form-header1 img").attr("src").length){$("#fb-form-header1 img").attr("src","common/images/image_default.png")}if($("#fb-submit-button").val()==""){$("#fb-submit-button").val(" ")}$("#docContainer").css("-webkit-transform","");$("#docContainer").removeClass("fb-two-column");$.each($(".fb-item .fb-checkbox, .fb-item .fb-radio","#docContainer"),function(e,d){var c=$(d).parent();if(c.hasClass("fb-one-column")||c.hasClass("fb-two-column")||c.hasClass("fb-three-column")){return}else{c.addClass("fb-side-by-side")}});$.each($("select option","#docContainer"),function(d,c){if(!pValidationConfig.get_config_value($(c).parent().get(0).id,"contactList")&&$(c).val().length&&$(c).val()!=$(c).text()){$(c).val($(c).text())}});$.each($(".fb-hint","#docContainer"),function(f,e){var c=$("*",$(e).parent());for(var d=0;d<c.length;d++){if($(c[d]).attr("data-hint")!=undefined){if($(c[d]).attr("data-hint").length==0){$(e).remove()}else{if(($(c[d]).attr("data-hint").search("\n")==-1)&&($(e).html().search("<br>")!=-1||$(e).html().search("<BR>")!=-1)){var g=$(e).html();g=fb_utils.replaceString(g,"<br>","\n");g=fb_utils.replaceString(g,"<BR>","\n");$(c[d]).attr("data-hint",g)}}break}}});var b=pValidationConfig.get_config();$.each(b,function(d,c){if("digits" in c){delete c.digits;c.decimals=0}});pValidationConfig.set_config(b);var a=pPaymentsConfig.get_config();$.each(a,function(e,d){if("type" in d&&d.type=="donation"){d.type="amount"}if("donation" in d){var c=fb_utils.cloneObject(d.donation);delete d.donation;d.amount=c}});pPaymentsConfig.set_config(a)}};