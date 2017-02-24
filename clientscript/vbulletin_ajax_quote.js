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
function vB_MultiQuote_Loader(A,B){this.editorid=A;this.threadid=B;YAHOO.util.Event.on("multiquote_more","click",this.fetch,this,true);YAHOO.util.Event.on("multiquote_deselect","click",this.deselect,this,true)}vB_MultiQuote_Loader.prototype.fetch=function(A){YAHOO.util.Event.stopEvent(A);this.handle_unquoted_posts("fetch")};vB_MultiQuote_Loader.prototype.deselect=function(A){YAHOO.util.Event.stopEvent(A);this.handle_unquoted_posts("deselect")};vB_MultiQuote_Loader.prototype.handle_unquoted_posts=function(A){YAHOO.util.Connect.asyncRequest("POST",fetch_ajax_url("newreply.php?do=unquotedposts&threadid="+this.threadid),{success:this.handle_ajax_unquoted_response,failure:this.handle_ajax_error,timeout:vB_Default_Timeout,scope:this},SESSIONURL+"securitytoken="+SECURITYTOKEN+"&do=unquotedposts&threadid="+this.threadid+"&wysiwyg="+vB_Editor[this.editorid].is_wysiwyg_mode()+"&type="+PHP.urlencode(A));return false};vB_MultiQuote_Loader.prototype.handle_ajax_error=function(A){vBulletin_AJAX_Error_Handler(A)};vB_MultiQuote_Loader.prototype.handle_ajax_unquoted_response=function(D){if(D.responseXML){var A=D.responseXML.getElementsByTagName("quotes");var F=D.responseXML.getElementsByTagName("mqpostids");if(A.length){if(vB_Editor[this.editorid].is_wysiwyg_mode()){var G=CKEDITOR.dom.element.createFromHtml("<span>"+A[0].firstChild.nodeValue+"</span>");vB_Editor[this.editorid].editor.insertElement(G)}else{vB_Editor[this.editorid].editor.insertText(A[0].firstChild.nodeValue)}var E=fetch_object("multiquote_empty_input");if(E){E.value="all"}}else{if(F.length){var B="";if(F[0].firstChild){B=F[0].firstChild.nodeValue}set_cookie("vbulletin_multiquote",B)}}var C=fetch_object("unquoted_posts");if(C){C.style.display="none"}}};function init_unquoted_posts(A,B){new vB_MultiQuote_Loader(A,B)};