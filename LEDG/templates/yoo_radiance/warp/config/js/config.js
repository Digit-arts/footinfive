/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

(function(a){a.fn.profiles=function(i){function f(b){b=="default"?d.addClass("default"):d.removeClass("default");a("[data-profile]").not(a('[data-profile="'+b+'"]').show()).hide()}function j(b){if(b&&!a('option[value="'+b+'"]',e[0]).length){var c=a(i).clone(true).attr("data-profile",b);c.find('[name^="profile_data"]').attr("name",function(a,c){return c.replace("profile_data[default]","profile_data["+b+"]")});c.children("li").each(function(){a(this).addClass("ignore").children(".label").before('<input class="ignore" type="checkbox" />')});
c.appendTo(a(i).parent());e.append('<option value="'+b+'">'+b+"</option>");a(e[0]).val(b).trigger("change")}}function h(b,c){c&&b!=c&&!a('option[value="'+c+'"]',e[0]).length&&(a('[data-profile="'+b+'"]').attr("data-profile",c).find('[name^="profile_data"]').attr("name",function(a,d){return d.replace("profile_data["+b+"]","profile_data["+c+"]")}),a('input[name^="profile_map"][value="'+b+'"]',d).attr("value",c),e.find('option[value="'+b+'"]').attr("value",c).html(c))}function k(b){g.find("option:selected").attr("selected",
false);g.find("option:disabled").attr("disabled",false);a('input[type="hidden"]',d).each(function(){var c=a(this).attr("name").replace(/^profile_map\[(.*)\]$/i,"$1"),d=a(this).val()==b?"selected":"disabled";g.find('[value="'+c+'"]').attr(d,true)});g.show().find("select").focus()}function m(b){a('input[name^="profile_map"][value="'+b+'"]',d).remove();g.find("option:selected").each(function(){d.append('<input type="hidden" name="profile_map['+a(this).val()+']" value="'+b+'" />')})}var d=this.first(),
e=a.merge(a("> select",d),a("select.profile")),g=a(".items",d);a('[data-profile][data-profile!="default"] > li').each(function(){a(this).children(".label").before(a('<input class="ignore" type="checkbox" />').attr("checked",!a(this).hasClass("ignore")))});f("default");a("#config").delegate("input.ignore","change",function(){a(this).is(":checked")?a(this).closest("li").removeClass("ignore"):a(this).closest("li").addClass("ignore")});a(e[0]).bind("change",function(){f(a(this).val())});a("> a.add",d).bind("click",
function(a){a.preventDefault();j(prompt("Please enter a profile name"))});a("> a.rename",d).bind("click",function(b){b.preventDefault();var b=a(e[0]).val(),c=prompt("Please enter a profile name",b);h(b,c)});a("> a.remove",d).bind("click",function(b){b.preventDefault();b=a(e[0]).val();a('[data-profile="'+b+'"]').remove();a('input[name^="profile_map"][value="'+b+'"]',d).remove();e.find('option[value="'+b+'"]').remove();a(e[0]).trigger("change")});a("> a.assign",d).bind("click",function(b){b.preventDefault();
k(a(e[0]).val())});a("select",g).bind("blur",function(){m(a(e[0]).val());g.hide()});return this};var j={get:function(a){return window.sessionStorage?sessionStorage.getItem(a):0},set:function(a,f){window.sessionStorage&&sessionStorage.setItem(a,f)}};a.fn.tabs=function(){return this.each(function(){var i=a(this).addClass("content").wrap('<div class="tabs-box" />').before('<ul class="nav" />'),f=a(this).prev("ul.nav");i.children("li").each(function(){f.append("<li><a>"+a(this).hide().attr("data-name")+
"</a></li>")});f.children("li").bind("click",function(h){h.preventDefault();var h=a("li",f).removeClass("active").index(a(this).addClass("active").get(0)),k=i.children("li").hide();a(k[h]).show();j.set("warp.settings.index",h)});var l=j.get("warp.settings.index")?j.get("warp.settings.index"):0;f.children().eq(l).trigger("click")})}})(jQuery);
