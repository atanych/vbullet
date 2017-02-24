/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 4.2.0 Patch Level 2
|| # ---------------------------------------------------------------- # ||
|| # Copyright �2000-2012 vBulletin Solutions Inc. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
vBulletin.events.systemInit.subscribe(function(){var C,A=null,B=null;if(vBulletin.elements.vB_List_Filter){for(C=0;C<vBulletin.elements.vB_List_Filter.length;C++){B=vBulletin.elements.vB_List_Filter[C];new vB_List_Filter(B[0],B[1],B[2],B[3],B[4])}vBulletin.elements.vB_List_Filter=null}});function vB_List_Filter(A,C,B,D,E){this.filterbox=YAHOO.util.Dom.get(A);this.hide_vars=YAHOO.util.Dom.get("hide_vars");this.show_customized_vars=YAHOO.util.Dom.get("show_customized_vars");this.show_var_names=YAHOO.util.Dom.get("show_var_names");this.containers=C;this.haystack=B;this.descriptor=D;this.containershtml=new Array(i);this.containerlisteners=new Array(i);this.eventfn=E;for(i=0;i<this.containers.length;i++){this.containershtml[i]=YAHOO.util.Dom.get(this.containers[i]).parentNode.innerHTML}YAHOO.util.Event.on(this.filterbox,"keyup",this.perform_filter,this,true);YAHOO.util.Event.on(this.filterbox,"focus",this.handle_focus,this,true);YAHOO.util.Event.on(this.filterbox,"blur",this.handle_blur,this,true);YAHOO.util.Event.on(this.hide_vars,"click",this.perform_filter,this,true);YAHOO.util.Event.on(this.show_customized_vars,"click",this.perform_filter,this,true);YAHOO.util.Event.on(this.show_var_names,"click",this.perform_filter,this,true);this.labeltext=new String(this.filterbox.value);YAHOO.util.Dom.setStyle(this.filterbox,"display","inline")}vB_List_Filter.prototype.perform_filter=function(E){var D,H,B,I,F,C;if(this.filterbox.value==this.labeltext){D=""}else{D=this.filterbox.value}for(C=0;C<this.containers.length;C++){this.containerlisteners[C]=YAHOO.util.Event.getListeners(this.containers[C]);YAHOO.util.Dom.get(this.containers[C]).parentNode.innerHTML=this.containershtml[C];for(j=0;j<this.containerlisteners[C].length;j++){var A=this.containerlisteners[C][j];YAHOO.util.Event.addListener(this.containers[C],A.type,A.fn,A.obj,A.adjust)}}if(this.eventfn!=null){this.eventfn()}H=PHP.trim(D.toLowerCase());console.log("vB_List_Filter :: Filtering results to entries containing '%s'.",H);for(B in this.haystack){F=(B.toLowerCase().indexOf(H)!=-1?"block":"none");for(C=0;C<this.containers.length;C++){var G=YAHOO.util.Dom.get(this.containers[C]);I=YAHOO.util.Dom.get(this.containers[C]+this.descriptor+this.haystack[B]);if(I!=null){if(F=="none"){I.parentNode.removeChild(I)}}}}toggle_hide_vars(E);toggle_customized_vars(E)};vB_List_Filter.prototype.handle_focus=function(A){YAHOO.util.Dom.removeClass(this.filterbox,"filterbox_inactive");if(this.filterbox.value==this.labeltext){this.filterbox.value=""}};vB_List_Filter.prototype.handle_blur=function(A){if(PHP.trim(this.filterbox.value)==""){YAHOO.util.Dom.addClass(this.filterbox,"filterbox_inactive");this.filterbox.value=this.labeltext}};