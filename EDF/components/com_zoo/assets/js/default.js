/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

jQuery(function(a){a("select.auto-submit").bind("change",function(){a('form[name="adminForm"]').submit()});var f={};a.matchHeight=a.matchHeight||function(b,d,e){var h=a(window),c=b&&f[b];if(!c){var c=f[b]={id:b,elements:d,deepest:e,match:function(){var b=this.revert(),c=0;a(this.elements).each(function(){c=Math.max(c,a(this).outerHeight())}).each(function(g){var d="outerHeight";"border-box"==b[g].css("box-sizing")&&(d="height");var e=a(this),g=b[g],d=g.height()+(c-e[d]());g.css("min-height",d+"px")})},
revert:function(){var b=[],c=this.deepest;a(this.elements).each(function(){var d=c?a(this).find(c+":first"):a(this);b.push(d.css("min-height",""))});return b},remove:function(){h.unbind("resize orientationchange",i);this.revert();delete f[this.id]}},i=function(){c.match()};h.bind("resize orientationchange",i)}return c};var d=[];a.onMediaQuery("(min-width: 480px) and (max-width: 959px)",{valid:function(){d=[];a.each(".categories .row > .width25;.categories .row > .width20;.categories > .width25;.categories > .width20;.subcategories > .width25;.subcategories > .width20;.items .row > .width25;.items .row > .width20".split(";"),
function(b,f){for(var b=0,e=a(f),h=parseInt(e.length/2);b<h;b++){var c="zoo-pair-"+d.length;a.matchHeight(c,[e.get(b*2),e.get(b*2+1)]).match();d.push(c)}})},invalid:function(){a.each(d,function(){a.matchHeight(this).remove()})}})});
