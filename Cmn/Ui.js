var $jscomp=$jscomp||{};$jscomp.scope={};$jscomp.findInternal=function(a,c,e){a instanceof String&&(a=String(a));for(var k=a.length,f=0;f<k;f++){var l=a[f];if(c.call(e,l,f,a))return{i:f,v:l}}return{i:-1,v:void 0}};$jscomp.ASSUME_ES5=!1;$jscomp.ASSUME_NO_NATIVE_MAP=!1;$jscomp.ASSUME_NO_NATIVE_SET=!1;$jscomp.defineProperty=$jscomp.ASSUME_ES5||"function"==typeof Object.defineProperties?Object.defineProperty:function(a,c,e){a!=Array.prototype&&a!=Object.prototype&&(a[c]=e.value)};
$jscomp.getGlobal=function(a){return"undefined"!=typeof window&&window===a?a:"undefined"!=typeof global&&null!=global?global:a};$jscomp.global=$jscomp.getGlobal(this);$jscomp.polyfill=function(a,c,e,k){if(c){e=$jscomp.global;a=a.split(".");for(k=0;k<a.length-1;k++){var f=a[k];f in e||(e[f]={});e=e[f]}a=a[a.length-1];k=e[a];c=c(k);c!=k&&null!=c&&$jscomp.defineProperty(e,a,{configurable:!0,writable:!0,value:c})}};
$jscomp.polyfill("Array.prototype.find",function(a){return a?a:function(a,e){return $jscomp.findInternal(this,a,e).v}},"es6","es3");$jscomp.arrayIteratorImpl=function(a){var c=0;return function(){return c<a.length?{done:!1,value:a[c++]}:{done:!0}}};$jscomp.arrayIterator=function(a){return{next:$jscomp.arrayIteratorImpl(a)}};$jscomp.makeIterator=function(a){var c="undefined"!=typeof Symbol&&Symbol.iterator&&a[Symbol.iterator];return c?c.call(a):$jscomp.arrayIterator(a)};
$jscomp.FORCE_POLYFILL_PROMISE=!1;
$jscomp.polyfill("Promise",function(a){function c(){this.batch_=null}function e(a){return a instanceof f?a:new f(function(r,c){r(a)})}if(a&&!$jscomp.FORCE_POLYFILL_PROMISE)return a;c.prototype.asyncExecute=function(a){null==this.batch_&&(this.batch_=[],this.asyncExecuteBatch_());this.batch_.push(a);return this};c.prototype.asyncExecuteBatch_=function(){var a=this;this.asyncExecuteFunction(function(){a.executeBatch_()})};var k=$jscomp.global.setTimeout;c.prototype.asyncExecuteFunction=function(a){k(a,
0)};c.prototype.executeBatch_=function(){for(;this.batch_&&this.batch_.length;){var a=this.batch_;this.batch_=[];for(var c=0;c<a.length;++c){var e=a[c];a[c]=null;try{e()}catch(n){this.asyncThrow_(n)}}}this.batch_=null};c.prototype.asyncThrow_=function(a){this.asyncExecuteFunction(function(){throw a;})};var f=function(a){this.state_=0;this.result_=void 0;this.onSettledCallbacks_=[];var c=this.createResolveAndReject_();try{a(c.resolve,c.reject)}catch(v){c.reject(v)}};f.prototype.createResolveAndReject_=
function(){function a(a){return function(r){e||(e=!0,a.call(c,r))}}var c=this,e=!1;return{resolve:a(this.resolveTo_),reject:a(this.reject_)}};f.prototype.resolveTo_=function(a){if(a===this)this.reject_(new TypeError("A Promise cannot resolve to itself"));else if(a instanceof f)this.settleSameAsPromise_(a);else{a:switch(typeof a){case "object":var c=null!=a;break a;case "function":c=!0;break a;default:c=!1}c?this.resolveToNonPromiseObj_(a):this.fulfill_(a)}};f.prototype.resolveToNonPromiseObj_=function(a){var c=
void 0;try{c=a.then}catch(v){this.reject_(v);return}"function"==typeof c?this.settleSameAsThenable_(c,a):this.fulfill_(a)};f.prototype.reject_=function(a){this.settle_(2,a)};f.prototype.fulfill_=function(a){this.settle_(1,a)};f.prototype.settle_=function(a,c){if(0!=this.state_)throw Error("Cannot settle("+a+", "+c+"): Promise already settled in state"+this.state_);this.state_=a;this.result_=c;this.executeOnSettledCallbacks_()};f.prototype.executeOnSettledCallbacks_=function(){if(null!=this.onSettledCallbacks_){for(var a=
0;a<this.onSettledCallbacks_.length;++a)l.asyncExecute(this.onSettledCallbacks_[a]);this.onSettledCallbacks_=null}};var l=new c;f.prototype.settleSameAsPromise_=function(a){var c=this.createResolveAndReject_();a.callWhenSettled_(c.resolve,c.reject)};f.prototype.settleSameAsThenable_=function(a,c){var e=this.createResolveAndReject_();try{a.call(c,e.resolve,e.reject)}catch(n){e.reject(n)}};f.prototype.then=function(a,c){function e(a,c){return"function"==typeof a?function(c){try{n(a(c))}catch(w){k(w)}}:
c}var n,k,p=new f(function(a,c){n=a;k=c});this.callWhenSettled_(e(a,n),e(c,k));return p};f.prototype.catch=function(a){return this.then(void 0,a)};f.prototype.callWhenSettled_=function(a,c){function e(){switch(f.state_){case 1:a(f.result_);break;case 2:c(f.result_);break;default:throw Error("Unexpected state: "+f.state_);}}var f=this;null==this.onSettledCallbacks_?l.asyncExecute(e):this.onSettledCallbacks_.push(e)};f.resolve=e;f.reject=function(a){return new f(function(c,e){e(a)})};f.race=function(a){return new f(function(c,
f){for(var k=$jscomp.makeIterator(a),l=k.next();!l.done;l=k.next())e(l.value).callWhenSettled_(c,f)})};f.all=function(a){var c=$jscomp.makeIterator(a),k=c.next();return k.done?e([]):new f(function(a,f){function p(c){return function(e){l[c]=e;n--;0==n&&a(l)}}var l=[],n=0;do l.push(void 0),n++,e(k.value).callWhenSettled_(p(l.length-1),f),k=c.next();while(!k.done)})};return f},"es6","es3");
$jscomp.polyfill("Promise.prototype.finally",function(a){return a?a:function(a){return this.then(function(c){return Promise.resolve(a()).then(function(){return c})},function(c){return Promise.resolve(a()).then(function(){throw c;})})}},"es9","es3");$jscomp.polyfill("Object.is",function(a){return a?a:function(a,e){return a===e?0!==a||1/a===1/e:a!==a&&e!==e}},"es6","es3");
$jscomp.polyfill("Array.prototype.includes",function(a){return a?a:function(a,e){var c=this;c instanceof String&&(c=String(c));var f=c.length;e=e||0;for(0>e&&(e=Math.max(e+f,0));e<f;e++){var l=c[e];if(l===a||Object.is(l,a))return!0}return!1}},"es7","es3");
$jscomp.checkStringArgs=function(a,c,e){if(null==a)throw new TypeError("The 'this' value for String.prototype."+e+" must not be null or undefined");if(c instanceof RegExp)throw new TypeError("First argument to String.prototype."+e+" must not be a regular expression");return a+""};$jscomp.polyfill("String.prototype.includes",function(a){return a?a:function(a,e){return-1!==$jscomp.checkStringArgs(this,a,"includes").indexOf(a,e||0)}},"es6","es3");
function _typeof(a){_typeof="function"===typeof Symbol&&"symbol"===typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&"function"===typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?"symbol":typeof a};return _typeof(a)}window.seraph_dlstat||(window.seraph_dlstat={});
(function(){function a(b){var d=1<arguments.length&&void 0!==arguments[1]?arguments[1]:null,a=2<arguments.length&&void 0!==arguments[2]?arguments[2]:null,g=3<arguments.length&&void 0!==arguments[3]?arguments[3]:!1,h=4<arguments.length&&void 0!==arguments[4]?arguments[4]:null;null===d&&(d="");null===h&&(h={});if(h.noTagsIfNoContent&&!d)return d;a=c(b,a,g);if(g)return a;p(d)?d[0]=a+d[0]:d=a+d;a=e(b);p(d)?d[d.length-1]+=a:d+=a;return d}function c(b){var d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:
!1;if(!b)return"";var a=1<arguments.length&&void 0!==arguments[1]?arguments[1]:null,g="";if(p(a))for(var h in a){var c=a[h];if(void 0!==c){if("style"==h)(c=p(c)?A(c):n(c,!1,'"'))||(c=void 0);else if(p(c)){for(var e="",f=!0,t=0;t<c.length;t++)f||(e+=" "),e+=c[t],f=!1;c=e}else c=n(c,!1,'"');void 0!==c&&(g+=" "+h+'="'+c+'"')}}else"string"===typeof a&&(g+=" "+a);return"<"+b+g+(d?" /":"")+">"}function e(b){return b?"</"+b+">":""}function k(b,d){var a=2<arguments.length&&void 0!==arguments[2]?arguments[2]:
!1,g=3<arguments.length&&void 0!==arguments[3]?arguments[3]:!1,c=4<arguments.length&&void 0!==arguments[4]?arguments[4]:null,m=5<arguments.length&&void 0!==arguments[5]?arguments[5]:null,e=6<arguments.length&&void 0!==arguments[6]?arguments[6]:null;p(e)||(e={});d&&(e.id=d);return f("checkbox",b,e,null,a,g,c,m)}function f(b,d,q){var g=3<arguments.length&&void 0!==arguments[3]?arguments[3]:null,h=4<arguments.length&&void 0!==arguments[4]?arguments[4]:!1,m=5<arguments.length&&void 0!==arguments[5]?arguments[5]:
!1,f=6<arguments.length&&void 0!==arguments[6]?arguments[6]:null,k=7<arguments.length&&void 0!==arguments[7]?arguments[7]:null,t="";p(q)||(q={});k&&(p(f)||(f={}),q.title=f.title=k);k="";if(p(d)){var l=d;d="";for(var n in l)if(p(n)){l="";var y=null;isset(n[2])&&(y=n[2]);var r=n[0];d+='<a href="#" id="'+r+'_view" onclick="var ctl = document.getElementById( \''+r+"' ); var iNew = ctl.selectedIndex + 1; if( iNew >= ctl.options.length ) iNew = 0; if( ctl.selectedIndex != iNew ) { ctl.selectedIndex = iNew; ctl.onchange(); } return false;\">";
for(var u in n[1])isset(u[2])&&u[2]&&(y=u[0]),l+='<option value="'+u[0]+'"',y==u[0]&&(d+=u[1],l+=" selected"),l+=">"+u[1]+"</option>";d+="</a>";k+='<select id="'+r+'"'+(m?' name="'+r+'"':"")+' style="display: none;"';k+=" onchange=\"var text = this.selectedIndex >= 0 ? this.options[ this.selectedIndex ].text : ''; document.getElementById( '"+r+"_view' ).text = text;\">";k+=l;k+="</select>"}else d+=n}t+=c("label",f);f=q.id;q.type=b;m&&f&&(q.name=f);g&&(q.value=g);h&&(q.checked="checked");t+=a("input",
null,q,!0);return t+=d+k+e("label")}function l(b){var d=1<arguments.length&&void 0!==arguments[1]?arguments[1]:"oninit";jQuery(b).find("[data-"+d+"]").each(function(){seraph_dlstat.Gen.EvalInCtx(jQuery(this).data(d),this)})}function r(b){var d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:!1;if(!b||!b.length)return"";var a=""+c("ul",1<arguments.length&&void 0!==arguments[1]?arguments[1]:null);for(var g=0;g<b.length;g++){var h=b[g];a+=c("li");a+=k(h.name,h.id,!1,d,null,null,{onchange:"seraph_dlstat.Ui._cb.CheckBoxTree_OnChange(this)"});
h.items&&(a+=r(h.items,null,d));a+=e("li")}return a+=e("ul")}function z(b,d,a){return seraph_dlstat.Gen.EvalInCtx("var isExpanding="+(a?"true":"false")+";"+d,b)}function v(b){b=jQuery("#seraph_dlstat_popup_"+b);1==b.attr("attr-modal")&&jQuery(".seraph_dlstat.popup_modal_overlay").hide();b.hide()}function n(b){var d=1<arguments.length&&void 0!==arguments[1]?arguments[1]:!1,a=2<arguments.length&&void 0!==arguments[2]?arguments[2]:null;if("string"!==typeof b)return b;b=seraph_dlstat.Gen.StrReplaceAll(b,
"&","&amp;");'"'===a&&(b=seraph_dlstat.Gen.StrReplaceAll(b,'"',"&quot;"));"'"===a&&(b=seraph_dlstat.Gen.StrReplaceAll(b,"'","&#039;"));b=seraph_dlstat.Gen.StrReplaceAll(b,"<","&lt;");b=seraph_dlstat.Gen.StrReplaceAll(b,">","&gt;");d&&(b=seraph_dlstat.Gen.StrReplaceAll(b," ","&nbsp;"));return b}function A(b){var d="",a;for(a in b){var g=b[a];void 0!==g&&null!==g&&(d&&(d+=";"),d+=a+":"+g)}return d}function p(b){return b&&"object"===_typeof(b)}function B(b,d,a){var g=!1;void 0===d?(d=b.height(),b.css("opacity",
0),b.css("height","0px")):g=!0;setTimeout(function(){g||b.addClass("smoothop");b.css("height",""+d+"px");setTimeout(function(){b.css("height","");b.css("opacity","");setTimeout(function(){b.removeClass("smoothop");a&&a(b)},200)},300)},g?50:0)}function C(b,d){var a=b.height();b.addClass("smoothop");b.css("opacity",0);setTimeout(function(){b.css("height",""+a+"px");setTimeout(function(){b.css("height","0px");setTimeout(function(){d&&d(b,a)},300)},50)},200)}function D(b,d){var a=!d.children().length;
b.find(".items-list-empty-content").first().toggle(a);d.toggle(!a)}function w(b){for(var d="",a=0;a<b.length;a++)d&&(d+=","),d+=b.charCodeAt(a).toString(10);return d}function x(b){b=b.split(",");for(var d=0;d<b.length;d++)b[d]=String.fromCharCode(parseInt(b[d],10));return b.join("")}function E(b,d,a){a||(a=d);jQuery(b).append(seraph_dlstat.Ui.Tag("span",seraph_dlstat.Ui.Tag("a","",{href:"","class":"dashicons dashicons-dismiss",onclick:"seraph_dlstat.Ui._cb.TokensList_DelItem(this);return false;"})+
seraph_dlstat.Ui.Tag("span",n(a)),{"data-id":d}))}function F(b,a,c){var d=jQuery(b),q=d.find('input[type="hidden"]');d=d.attr("masked");if(a)for(a=q.val(),d&&(a=x(a)),a=seraph_dlstat.Gen.JsonParse(decodeURIComponent(a))||[],q=0;q<a.length;q++)d=a[q],E(b,d,c?c(d):void 0);else b.innerHTML=q[0].outerHTML}function G(b){var a=parseInt(b,10);return a==b?a:b}function H(b){var a=jQuery(b).prop("checked"),c=jQuery(b).attr("data-id");jQuery(b.parentNode.parentNode.parentNode).find('[data-idParent="'+c+'"]').each(function(){jQuery(this).prop("checked",
a);H(this)})}function I(b){var a=jQuery(b).find('input[type="checkbox"]');if(a.length){var c=[];a.each(function(){var b=jQuery(this);b.is(":checked")&&c.push(G(b.attr("data-id")))});jQuery(b).find('input[type="hidden"]').attr("value",encodeURIComponent(JSON.stringify(c)))}}seraph_dlstat.Ui={EscHtml:n,HtmlToPlainText:function(b){var a=document.createElement("div");a.innerHTML=b;return a.textContent||a.innerText||""},Text:function(b){var a=1<arguments.length&&void 0!==arguments[1]?arguments[1]:0,c=
2<arguments.length&&void 0!==arguments[2]?arguments[2]:!1,g=3<arguments.length&&void 0!==arguments[3]?arguments[3]:!1;if("string"!==typeof b)return"";b=seraph_dlstat.Gen.StrReplaceAll(b,"&","&amp;");c&&(b=seraph_dlstat.Gen.StrReplaceAll(b,'"',"&quot;"),b=seraph_dlstat.Gen.StrReplaceAll(b,"'","&#39;"));b=seraph_dlstat.Gen.StrReplaceAll(b,"<","&lt;");b=seraph_dlstat.Gen.StrReplaceAll(b,">","&gt;");a&&(b=seraph_dlstat.Gen.StrReplaceAll(b," ","string"===typeof a?a:1==a?"&nbsp;":"&#32;"));if(g)for(a=0,
c=b.length;a<c;a++)g=b.charCodeAt(a),255>=g||(b=b.substr(0,a)+"&#"+g+";"+b.substr(a+1),a+=b.length-c,c=b.length);return b},Comment:function(b){"string"!==typeof b&&(b="");return"\x3c!-- "+b+" --\x3e"},Tag:a,TagOpen:c,TagClose:e,GetTagStyleAttrs:A,ParseTagStyleAttrs:function(b){"string"!==typeof b&&(b="");var a={};b=b.split(";");for(var c in b){var g=b[c],h=g.indexOf(":");if(-1!==h){var m=g.substr(0,h).trim();g=g.substr(h+1).trim();a[m]=g}}return a},Link:function(b,a){var d=2<arguments.length&&void 0!==
arguments[2]?arguments[2]:!1,g=3<arguments.length&&void 0!==arguments[3]?arguments[3]:null,h=4<arguments.length&&void 0!==arguments[4]?arguments[4]:null;null===h&&(h={});null===g&&(g={});if(a){var m="",f=g.linkPreContent;f&&(m+=f);h.href=a;d&&0!==a.indexOf("mailto:")&&(h.target="_blank");m+=c("a",h);p(b)?b[0]=m+b[0]:b=m+b}else if(g.noTextIfNoHref){if(!p(b))return"";b[0]="";b[b.length-1]="";3==b.length&&(b[1]="");return b}a&&(m=e("a"),(d=g.linkAfterContent)&&(m+=d),p(b)?b[b.length-1]+=m:b+=m);return b},
Button:function(b){var d=1<arguments.length&&void 0!==arguments[1]?arguments[1]:!1,c=2<arguments.length&&void 0!==arguments[2]?arguments[2]:null,g=3<arguments.length&&void 0!==arguments[3]?arguments[3]:null,h=4<arguments.length&&void 0!==arguments[4]?arguments[4]:"submit",m=5<arguments.length&&void 0!==arguments[5]?arguments[5]:null,e="button"==h&&-1!==b.indexOf("<");m||(m={});c&&(m.name=c);e||(m.type=h,m.value=b,b=null);Array.isArray(m.class)||(m.class=[]);m.class.push("button");d&&m.class.push("button-primary");
g&&(Array.isArray(g)?m.class=m.class.concat(g):m.class.push(g));return a(e?"button":"input",b,m)},CheckBox:k,RadioBox:function(b,a,c){var d=3<arguments.length&&void 0!==arguments[3]?arguments[3]:!1,h=4<arguments.length&&void 0!==arguments[4]?arguments[4]:null,q=5<arguments.length&&void 0!==arguments[5]?arguments[5]:null,e=6<arguments.length&&void 0!==arguments[6]?arguments[6]:null;p(e)||(e={});a&&(e.name=a);return f("radio",b,e,c,d,h,q)},CheckBoxTree:function(b){var a=1<arguments.length&&void 0!==
arguments[1]?arguments[1]:null,c=2<arguments.length&&void 0!==arguments[2]?arguments[2]:!1;p(a)||(a={});a.class=["seraph_dlstat_CheckBoxTree"];return r(b,a,c)},ComboShowDependedItems:function(b,a){function d(b){b=jQuery(b);var a=b.data(c);a||(a=b.val());return a}var c=2<arguments.length&&void 0!==arguments[2]?arguments[2]:"ns",h=void 0!==b.selectedIndex,e="";h?0<=b.selectedIndex&&(e=d(b.options[b.selectedIndex])):e=d(jQuery(b).find('input[type="radio"]:checked').get(0));var f=[];if(h)for(h=0;h<b.options.length;h++)f[d(b.options[h])]=
!0;else jQuery(b).find('input[type="radio"]').each(function(){f[d(this)]=!0});h=jQuery(a?a:b.parentNode);for(var k in f)k!=e&&h.find("."+c+"-"+k).each(function(){var b=jQuery(this),a=b.attr("name");a&&(b.attr("name-i",a),b.removeAttr("name"));b.hide()});h.find("."+c+"-"+e).each(function(){var b=jQuery(this),a=b.attr("name-i");a&&(b.attr("name",a),b.removeAttr("name-i"));b.removeClass("ctlInitHidden");b.show()})},Spinner:function(b,d){d||(d={});seraph_dlstat.Gen.SetObjField(d,"class.+","seraph_dlstat_spinner"+
(b?" big":""));return a("span",null,d)},LogItem:function(b,d){var c=2<arguments.length&&void 0!==arguments[2]?arguments[2]:!0;if("string"===typeof d){var g="dashicons-info",e="clrWpNone";switch(b){case "info":g="dashicons-info";e="clrWpInfo";break;case "normal":g="dashicons-info";e="clrWpNormal";break;case "success":g="dashicons-info";e="clrWpSucc";break;case "warning":g="dashicons-warning";e="clrWpWarn";break;case "error":g="dashicons-warning",e="clrWpErr"}c&&(c=d.slice(-1),"."!=c&&"?"!=c&&(d+="."));
return a("div",a("div",null,{"class":"icon dashicons "+g+" "+e})+a("div",d,{"class":"text"}),{"class":"logItem"})}},Init:l,Apply:function(b){var a=!0;jQuery(b).find("[data-onapply]").each(function(){!1===seraph_dlstat.Gen.EvalInCtx(jQuery(this).data("onapply"),this)&&(a=!1)});return a},PopupShow:function(b){var a=jQuery("#seraph_dlstat_popup_"+b),c=1==a.attr("attr-modal"),g=a.attr("attr-body");g&&(a.removeAttr("attr-body"),a.html(decodeURIComponent(g)));c&&(jQuery(".seraph_dlstat.popup_modal_overlay").show(),
jQuery(document).bind("keyup",function(a){if("Escape"===a.key)return v(b),!1}));a.show()},PopupClose:v,BannerMsgClose:function(b){b.find(".notice-dismiss").click()},ScriptLoad:function(b,a){var d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:null,c=3<arguments.length&&void 0!==arguments[3]?arguments[3]:!0;if(b)return new Promise(function(g,e){var h=d?b.getElementById(d):null;h?!0===c?g(!1):(b.head.removeChild(h),g(!0)):!0!==c?g(!1):(h=b.createElement("script"),h.type="text/javascript",h.src=
a,d&&(h.id=d),h.onload=function(){g(!0)},h.onerror=function(b){(b=seraph_dlstat.Gen.GetObjField(b,"target.src"))||(b=a);e("Can't load script: "+b)},b.head.appendChild(h))})},TokensList:{GetVal:function(b){var a=jQuery(b);b=a.find('input[type="hidden"]');a=a.attr("masked");b=b.val();a&&(b=x(b));(b=seraph_dlstat.Gen.JsonParse(decodeURIComponent(b)))||(b=[]);return b},AddItem:function(a,d,c){var b=jQuery(a),e=b.find('input[type="hidden"]');b=b.attr("masked");var f=e.val();b&&(f=x(f));(f=seraph_dlstat.Gen.JsonParse(decodeURIComponent(f)))||
(f=[]);f.includes(d)||(f.push(d),f=encodeURIComponent(JSON.stringify(f)),e.val(b?w(f):f),E(a,d,c))},DelAllItems:function(a){var b=jQuery(a),c=b.attr("masked");a.innerHTML=b.find('input[type="hidden"]')[0].outerHTML;a=encodeURIComponent(JSON.stringify([]));b.find('input[type="hidden"]').val(c?w(a):a)},InitItems:F,AreItemsInited:function(a){return a.children&&1<a.children.length},ShowProgress:function(a,d){d?jQuery(a).append(seraph_dlstat.Ui.Spinner(!0)):F(a,!1)}},TokensMetaTree:{Expand:function(a,
d,c){c?(c=JSON.parse(decodeURIComponent(jQuery(a).find('input[type="hidden"]').val())),a.innerHTML+=function J(a,b){var d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:0,c=3<arguments.length&&void 0!==arguments[3]?arguments[3]:0,e="";for(f in a){var f=G(f);var h=a[f];(void 0===h.parent?0:h.parent)==d&&(e+=seraph_dlstat.Ui.Tag("div",seraph_dlstat.Ui.CheckBox(h.name,void 0,b.includes(f),!1,{style:{"margin-bottom":"0!important",display:h.displayHidden?"none":null}},null,{"data-id":f,"data-idParent":d,
onchange:"seraph_dlstat.Ui.TokensMetaTree._OnChangeItem(this)"}),{style:{"padding-left":""+1.5*c+"em"}}),e+=J(a,b,f,c+1))}return e}(d,c)):(I(a),a.innerHTML=jQuery(a).find('input[type="hidden"]')[0].outerHTML)},Apply:I,_OnChangeItem:H},ItemsList:{_Init:function(a,d,c,e,f,m,k){a=jQuery(a.parentNode).closest(d).first().find("ul.items-list").first();a.length&&(a[0]._ItemsListCtx={level:k,onItemDel:c,nextItemId:e,contentNewItem:seraph_dlstat.Gen.StrReplaceAll(f,"scrapt>","script>")},m&&(a.sortable({placeholder:"ui-sortable-placeholder",
forcePlaceholderSize:!0}),a.disableSelection(),a.sortable("option","cancel",a.sortable("option","cancel")+",.rs")))},AddItem:function(a,d){a=jQuery(d.parentNode).closest(a).first();d=a.find("ul.items-list").first();if(d.length){var b=d[0]._ItemsListCtx;d.append(seraph_dlstat.Gen.StrReplaceAll(b.contentNewItem,"{{"+b.level+"itemId}}",b.nextItemId));b.nextItemId++;a.find(".items-list-empty-content").first().hide();d.show();a=d.children().last();l(a.get(0));l(a.get(0),"oninitnew");B(a)}},MoveItem:function(a,
d,c){if(jQuery(d.parentNode).closest(a).first().find("ul.items-list").first().length){a=jQuery(d).closest("li.item").first();var b=0<c?a.next():0>c?a.prev():void 0;b&&b.length&&C(a,function(a,d){0<c?a.insertAfter(b):a.insertBefore(b);B(a,d)})}},DelItem:function(a,d){var b=jQuery(d.parentNode).closest(a).first(),c=b.find("ul.items-list").first();c.length&&C(jQuery(d).closest("li.item").first(),function(a,d){(d=c[0]._ItemsListCtx.onItemDel)&&d(b,c,a);a.remove();D(b,c)})},DelAllItems:function(a,c){var b=
jQuery(c.parentNode).closest(a).first(),d=b.find("ul.items-list").first();if(d.length){var e=d.children();e.addClass("smoothop");e.css("opacity",0);setTimeout(function(){e.each(function(){var a=jQuery(this),c=d[0]._ItemsListCtx.onItemDel;c&&c(b,d,a);a.remove()});D(b,d)},200)}}},_cb:{CheckBoxTree_OnChange:function(a){function b(a){var d=a.parent().parent(),e=!0;a.siblings().each(function(){return e=jQuery(this).children("label").find('input[type="checkbox"]').prop("checked")===c});e&&c?(d.children("label").find('input[type="checkbox"]').prop({indeterminate:!1,
checked:c}),b(d)):e&&!c?(d.children("label").find('input[type="checkbox"]').prop("checked",c),d.children("label").find('input[type="checkbox"]').prop("indeterminate",0<d.find('input[type="checkbox"]:checked').length),b(d)):a.parents("li").children("label").find('input[type="checkbox"]').prop({indeterminate:!0,checked:!1})}var c=jQuery(a).prop("checked");a=jQuery(a).parent().parent();a.find('input[type="checkbox"]').prop({indeterminate:!1,checked:c});b(a)},ToggleButton_OnClick:function(a,c){function b(){f.find("[data-onexpand]").each(function(){z(this,
jQuery(this).data("onexpand"),k)});f.toggle();d.find(".dashicons").removeClass(k?"dashicons-arrow-down":"dashicons-arrow-up").addClass(k?"dashicons-arrow-up":"dashicons-arrow-down")}var d=jQuery(c),e=jQuery(c.parentNode),f=(-1!=a.indexOf("#")?jQuery(c.ownerDocument):jQuery(c.parentNode.parentNode)).find(a).first(),k="none"==f.css("display");a=f.data("onexpand");var l;a&&(l=z(f.get(0),a,k));if(l){var n=e.find(".seraph_dlstat_spinner");d.attr("disabled","");n.show();l.finally(function(){b();n.hide();
d.removeAttr("disabled")})}else b()},TokensList_DelItem:function(a){var b=jQuery(a.parentNode),c=jQuery(b[0].parentNode);a=c.find('input[type="hidden"]');var e=b.attr("data-id");c=c.attr("masked");b.remove();b=a.val();c&&(b=x(b));(b=seraph_dlstat.Gen.JsonParse(decodeURIComponent(b)))&&b.length&&(e=b.indexOf("string"==typeof b[0]?""+e:e),-1!=e&&(b.splice(e,1),b=encodeURIComponent(JSON.stringify(b)),a.val(c?w(b):b)))}}}})();
