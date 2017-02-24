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
if(AJAX_Compatible&&(typeof vb_disable_ajax=="undefined"||vb_disable_ajax<2)){vBulletin.events.systemInit.subscribe(function(){var A=new vB_ActivityStream()})}function vB_ActivityStream(){this.activetab=null;this.ajaxreq=null;this.init_tabs();this.options=activity_stream_options;this.hidemore={};this.updatetimer=null;this.idletimer=null;this.idle=false;if(this.options.refresh*60000>300000){this.idlerefresh=this.options.refresh*60000}else{this.idlerefresh=300000}if(this.options.sortby!="popular"){YAHOO.util.Event.addListener(document,"mousemove",this.reset_idle_timer,this);this.start_time()}this.newitemlist=[];this.prevnewmark=null}vB_ActivityStream.prototype.reset_idle_timer=function(A,B){if(B.idle==true){B.start_time();B.idle=false;B.new_activity();console.log("Gone Active")}else{B.idle=false}clearTimeout(B.idletimer);B.idletimer=setTimeout(function(){B.go_idle()},B.idlerefresh)};vB_ActivityStream.prototype.go_idle=function(){console.log("Gone Idle");this.idle=true};vB_ActivityStream.prototype.show_new_activity=function(D,F){F.start_time();YAHOO.util.Dom.addClass("newactivity_container","hidden");if(F.newitemlist.length==0){return }var C=YAHOO.util.Dom.get("activitylist");F.newitemlist.reverse();var E=true;var A=YAHOO.util.Dom.get("olderactivity");YAHOO.util.Dom.removeClass(A,"hidden");for(x in F.newitemlist){if(E){if(!C.hasChildNodes()){C.appendChild(A)}else{var B=C.insertBefore(A,C.firstChild)}E=false}if(!C.hasChildNodes()){var B=C.appendChild(F.newitemlist[x])}else{var B=C.insertBefore(F.newitemlist[x],C.firstChild)}}F.newitemlist=[]};vB_ActivityStream.prototype.start_time=function(A){if(this.options.sortby=="popular"){return }clearTimeout(this.updatetimer);thisC=this;this.updatetimer=setTimeout(function(){thisC.new_activity()},this.options.refresh*60000);console.log("Update Timer Started")};vB_ActivityStream.prototype.init_tabs=function(){var A=YAHOO.util.Dom.get("activity_tab_container");if(A){var C=A.getElementsByTagName("dd");for(var B=0;B<C.length;B++){if(!this.activetab&&YAHOO.util.Dom.hasClass(C[B],"selected")){this.activetab=C[B]}YAHOO.util.Event.addListener(C[B],"click",this.tab_click,this)}}YAHOO.util.Event.addListener("moreactivitylink","click",this.more_activity,this);YAHOO.util.Event.addListener("newactivitylink","click",this.show_new_activity,this)};vB_ActivityStream.prototype.more_activity=function(B,C){YAHOO.util.Event.stopPropagation(B);YAHOO.util.Event.stopEvent(B);if(YAHOO.util.Connect.isCallInProgress(C.ajaxreq)){return }YAHOO.util.Dom.addClass("moreactivitylink","hidden");YAHOO.util.Dom.removeClass("moreactivityprogress","hidden");var D={failure:vBulletin_AJAX_Error_Handler,timeout:vB_Default_Timeout,success:C.update_tab,scope:C,argument:{updatetype:"fetchpast"}};var A=SESSIONURL+"securitytoken="+SECURITYTOKEN+"&pp="+C.options.perpage+"&mindateline="+C.options.mindateline+"&minscore="+C.options.minscore+"&minid="+C.options.minid;if(C.options.type=="member"){A+="&do=loadactivitytab&u="+THISUSERID+"&tab="+C.activetab.id}else{A+="&sortby="+C.options.sortby+"&time="+C.options.time+"&show="+C.options.show}C.ajaxreq=YAHOO.util.Connect.asyncRequest("POST",fetch_ajax_url("activity.php"),D,A)};vB_ActivityStream.prototype.new_activity=function(){if(this.idle||YAHOO.util.Connect.isCallInProgress(this.ajaxreq)){this.start_time();return }var B={failure:vBulletin_AJAX_Error_Handler,timeout:vB_Default_Timeout,success:this.update_tab,scope:this,argument:{updatetype:"fetchfuture"}};var A=SESSIONURL+"securitytoken="+SECURITYTOKEN+"&pp="+this.options.perpage+"&maxdateline="+this.options.maxdateline+"&maxid="+this.options.maxid;if(this.options.type=="member"){A+="&do=loadactivitytab&u="+THISUSERID+"&tab="+this.activetab.id}else{A+="&sortby="+this.options.sortby+"&time="+this.options.time+"&show="+this.options.show}this.ajaxreq=YAHOO.util.Connect.asyncRequest("POST",fetch_ajax_url("activity.php"),B,A)};vB_ActivityStream.prototype.tab_click=function(C,D){YAHOO.util.Event.stopPropagation(C);YAHOO.util.Event.stopEvent(C);var A=D.activetab;D.activetab=this;if(this==A||YAHOO.util.Connect.isCallInProgress(D.ajaxreq)){D.activetab=A;return }var E={failure:vBulletin_AJAX_Error_Handler,timeout:vB_Default_Timeout,success:D.update_tab,scope:D,argument:{updatetype:"replace",newtab:this,oldtab:A}};var B=SESSIONURL+"do=loadactivitytab&securitytoken="+SECURITYTOKEN+"&u="+THISUSERID+"&pp="+D.options.perpage+"&tab="+this.id;D.ajaxreq=YAHOO.util.Connect.asyncRequest("POST",fetch_ajax_url("activity.php?do=loadactivitytab&u="+THISUSERID+"&pp="+D.options.perpage+"&tab="+this.id),E,B)};vB_ActivityStream.prototype.update_tab=function(I){if(I.responseXML){if(fetch_tag_count(I.responseXML,"error")){alert(I.responseXML.getElementsByTagName("error")[0].firstChild.nodeValue);return }YAHOO.util.Dom.addClass("moreactivityprogress","hidden");if(I.argument.updatetype=="replace"){YAHOO.util.Dom.addClass("olderactivity","hidden");YAHOO.util.Dom.addClass("newactivity_container","hidden");this.newitemlist=[]}var G=YAHOO.util.Dom.get("activitylist");if(I.argument.updatetype=="replace"){var H=YAHOO.util.Dom.getElementsByClassName("activitybit","li",G);if(H.length>0){for(var D=0;D<H.length;D++){G.removeChild(H[D])}}YAHOO.util.Dom.removeClass(I.argument.oldtab,"selected");YAHOO.util.Dom.addClass(I.argument.newtab,"selected")}if(fetch_tag_count(I.responseXML,"nada")){this.start_time();YAHOO.util.Dom.addClass("moreactivitylink","hidden");YAHOO.util.Dom.removeClass("noresults","hidden");return }var F=0;if(I.argument.updatetype=="replace"||I.argument.updatetype=="fetchpast"){var J=I.responseXML.getElementsByTagName("bit");if(J.length){for(var D=0;D<J.length;D++){if(J[D].firstChild){var C=string_to_node(J[D].firstChild.nodeValue);var E=G.appendChild(C);F++}}}}else{if(I.argument.updatetype=="fetchfuture"){var B=[];var J=I.responseXML.getElementsByTagName("bit");if(J.length){for(var D=0;D<J.length;D++){if(J[D].firstChild){var C=string_to_node(J[D].firstChild.nodeValue);B.push(C);F++}}}if(B.length>0){this.newitemlist=B.concat(this.newitemlist)}}}var A=I.responseXML.getElementsByTagName("totalcount")[0].firstChild.nodeValue;if(A>0){if(I.argument.updatetype=="replace"||I.argument.updatetype=="fetchpast"){this.options.minid=I.responseXML.getElementsByTagName("minid")[0].firstChild.nodeValue;this.options.mindateline=I.responseXML.getElementsByTagName("mindateline")[0].firstChild.nodeValue;this.options.minscore=I.responseXML.getElementsByTagName("minscore")[0].firstChild.nodeValue}if(I.argument.updatetype=="replace"||I.argument.updatetype=="fetchfuture"){this.options.maxid=I.responseXML.getElementsByTagName("maxid")[0].firstChild.nodeValue;this.options.maxdateline=I.responseXML.getElementsByTagName("maxdateline")[0].firstChild.nodeValue}if(I.argument.updatetype=="fetchfuture"&&F>0){YAHOO.util.Dom.get("newactivitycount").innerHTML=this.newitemlist.length;YAHOO.util.Dom.removeClass("newactivity_container","hidden")}}else{if(I.argument.updatetype=="replace"||I.argument.updatetype=="fetchpast"){this.options.mindateline=0;this.options.minscore=0;this.options.minid=""}if(I.argument.updatetype=="replace"){this.options.maxdateline=0;this.options.maxid=""}}if(I.argument.updatetype=="replace"||I.argument.updatetype=="fetchpast"){if(A==0||I.responseXML.getElementsByTagName("moreresults")[0].firstChild.nodeValue==0){YAHOO.util.Dom.addClass("moreactivitylink","hidden");YAHOO.util.Dom.removeClass("noresults","hidden")}else{YAHOO.util.Dom.removeClass("moreactivitylink","hidden");YAHOO.util.Dom.addClass("noresults","hidden")}}this.start_time()}};