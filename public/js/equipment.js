var equipment={options:{job:job,level:level,craftable_only:craftable_only,level_range:3,boring_stats:false},init:function(){$.fn.inlineStyle=function(a){return this.prop("style")[$.camelCase(a)]};this.table_events();this.page_events();equipment_tour.init()},cell_events:function(){equipment.mark_upgrades();equipment.same_cell_heights();if(typeof(initXIVDBTooltips)!="undefined"){initXIVDBTooltips()}$("#gear tbody td:visible .td-navigation .item-next").on("click",function(e){e.preventDefault();var h=$(this).closest("td");h.trigger("mouseleave");var a=h.find(".items");var g=a.find(".item.active");var b=g.next();if(b.length!=1){b=a.children(":first-child")}g.removeClass("active").addClass("hidden");b.removeClass("hidden").addClass("active");h.trigger("mouseenter");var d=parseInt(h.find(".td-navigation .total").html());var c=h.find(".td-navigation .current");var f=parseInt(c.html());f++;if(f>d){f=1}c.html(f);equipment.mark_cannot_equips();equipment.stat_summary(h);$("#gear td:visible .slot-wrap").css("height","inherit");equipment.same_cell_heights()});$("#gear tbody td:visible").on("mouseenter",this.compare);$("#gear tbody td:visible").on("mouseleave",this.uncompare);$("[rel=tooltip]").tooltip();$("#gear tbody tr:first-child td:visible").each(function(){equipment.stat_summary($(this))})},table_events:function(){equipment.cell_events();$(".previous-gear").click(function(){var b=$(this);if(b.hasClass("disabled")){return}$(".previous-gear, .next-gear").addClass("disabled");var a=equipment.options.level-1;if(a<1){$(".previous-gear, .next-gear").removeClass("disabled");return}equipment.options.level=a;if($("td[data-level="+a+"]").length>0){equipment.column_display();equipment.fix_rows();equipment.mark_upgrades();equipment.same_cell_heights();if(a-1>0&&$("td[data-level="+(a-1)+"]").length==0){equipment.load_column(a-1,"Pre")}else{$(".previous-gear, .next-gear").removeClass("disabled")}}else{equipment.load_column(a)}});$(".next-gear").click(function(){var b=$(this);if(b.hasClass("disabled")){return}$(".previous-gear, .next-gear").addClass("disabled");var a=equipment.options.level+1;if(a>48){$(".previous-gear, .next-gear").removeClass("disabled");return}equipment.options.level=a;if($("td[data-level="+(a+equipment.options.level_range-1)+"]").length>0){equipment.column_display();equipment.fix_rows();equipment.mark_upgrades();equipment.same_cell_heights();if(a+equipment.options.level_range<=50&&$("td[data-level="+(a+equipment.options.level_range-1)+"]").length==0){equipment.load_column(a+equipment.options.level_range-1,"Pre")}else{$(".previous-gear, .next-gear").removeClass("disabled")}}else{equipment.load_column(a+equipment.options.level_range-1)}})},slim:function(a){$("#gear")[(a?"add":"remove")+"Class"]("slim");$("td:visible .slot-wrap").css("height","inherit");equipment.options.level_range=a?4:3;equipment.column_display();equipment.same_cell_heights()},same_cell_heights:function(){$("#gear tbody tr").each(function(){var b=$(this),a=0;$("td:visible .slot-wrap",b).each(function(){var c=$(this);var e=0;if($(this).inlineStyle("height")!=""){e=16}var d=c.innerHeight()-e;if(d>a){a=d}});$("td:visible .slot-wrap",b).height(a)})},mark_upgrades:function(){$("#gear tbody td:visible").each(function(){var e=$(this);var d=e.data("level");var a=e.closest("tr").find("td[data-level="+(d-1)+"]");if(a.length==0&&d!=1){return}var b=e.find(".item:first-child").data("itemIlvl");var c=a.find(".item:first-child").data("itemIlvl");e[(b==c?"remove":"add")+"Class"]("upgrade")});equipment.mark_cannot_equips()},mark_cannot_equips:function(){$("#gear tbody td.cannot-equip").removeClass("cannot-equip");$(".why-cannot-equip").remove();$('#gear tbody td:visible .item.active:not([data-cannot-equip=""])').each(function(){var c=$(this);var e=c.data("cannotEquip").split(",");var a=$(".name-box a",c).html();var g=c.closest("td");var f=g.data("level");for(var b=0;b<e.length;b++){var d=$(".slot-"+e[b].capitalize()+"[data-level="+f+"]");d.append('<div class="why-cannot-equip">'+a+"<br>Cannot equip gear to "+e[b].capitalize()+"</div>");d.addClass("cannot-equip")}equipment.stat_summary(g)})},load_column:function(b,a){if(typeof(a)=="undefined"){a=""}if(parseInt(b)>50){$(".previous-gear, .next-gear").removeClass("disabled");return}$.ajax({url:"/equipment/load",type:"post",dataType:"json",data:{job:equipment.options.job,level:b,craftable_only:equipment.options.craftable_only},beforeSend:function(){noty({text:a+"Loading Level "+b,type:"warning",layout:"bottomRight",timeout:2500})},success:function(c){$.each(c.slots,function(d,e){$("#gear .slot-row[data-slot="+d+"]").append(e)});$("#gear thead tr").append(c.head);$("#gear tfoot tr").append(c.foot);equipment.column_display();equipment.fix_rows();equipment.cell_events();$(".previous-gear, .next-gear").removeClass("disabled")}})},fix_rows:function(){$("#gear tr").each(function(){var c=$(this);var b=c.find("th:visible, td:visible").get();b.sort(function(e,d){var f=parseInt($(e).data("level")),g=parseInt($(d).data("level"));return g>f?-1:g<f?1:0});for(var a=0;a<b.length;a++){b[a].parentNode.appendChild(b[a])}})},column_display:function(c){var b=equipment.options.level;if(b>50-equipment.options.level_range+1){b=50-equipment.options.level_range+1}$("#gear td[data-level]").addClass("hidden");$("#gear th[data-level]").addClass("hidden");for(var a=b;a<equipment.options.level+equipment.options.level_range;a++){$("#gear td[data-level="+a+"]").removeClass("hidden");$("#gear th[data-level="+a+"]").removeClass("hidden")}},compare:function(){var c=$(this);var a=c.find(".item.active .stats-box");var b=c.prev("td").find(".item.active .stats-box");a.find(".stat").each(function(){var d=$(this);var e=b.find('.stat[data-stat="'+d.data("stat")+'"]');if(e.length==0){$("span",d).html(d.data("amount")).addClass("text-success")}else{var f=parseInt(d.data("amount"))-parseInt(e.data("amount"));$("span",d).html(f).addClass("text-"+(f>0?"success":(f<0?"danger":"warning")))}})},uncompare:function(){$(this).find(".stats-box .stat").each(function(){var a=$(this);$("span",a).html(a.data("amount")).removeClass("text-success").removeClass("text-danger").removeClass("text-warning")})},stat_summary:function(c){var a=$(c).data("level");var h=[],i=[];$("#gear tbody tr td[data-level="+a+']:not("cannot-equip")').each(function(){$(this).find(".item.active .stat").each(function(){var l=$(this);var k=l.data();if(l.hasClass("hidden")){i[i.length]=k.stat}if(typeof(h[k.stat])==="undefined"){h[k.stat]=0}h[k.stat]+=k.amount})});var f=$("#gear tfoot tr:first-child th[data-level="+a+"] .stats-box");f.html("");for(var e in h){var g=h[e];var b=$("<div>");if(i.indexOf(e)>-1){b.addClass("hidden")}b.addClass("col-sm-6").addClass("text-center").addClass("stat").attr("data-stat",e).attr("data-amount",g);var j=$("<span>");j.html("+"+g+" &nbsp; ");var d=$("<img>");d.attr("src","/img/stats/"+e+".png").addClass("stat-icon").attr("rel","tooltip").attr("title",e);b.append(j);b.append(d);f.append(b)}},all_stats:function(){$("#gear td:visible .stats-box .boring").toggleClass("hidden");$("#gear td:visible .slot-wrap").css("height","inherit");equipment.same_cell_heights()},page_events:function(){$("#toggle-slim").on("switch-change",function(b,a){equipment.slim(a.value);$("html, body").animate({scrollTop:$("#table-options").offset().top},"slow")});if($("#toggle-slim").bootstrapSwitch("status")){equipment.options.level_range=4}$("#toggle-all-stats").on("switch-change",function(b,a){equipment.options.boring_stats=a.value;equipment.all_stats();$("html, body").animate({scrollTop:$("#table-options").offset().top},"slow")});$("#craftable_only_switch").change(function(){$(this).closest("form").submit()})}};var equipment_tour={tour:null,first_run:true,init:function(){var a=$("#start_tour");equipment_tour.tour=new Tour({orphan:true,onStart:function(){return a.addClass("disabled",true)},onEnd:function(){return a.removeClass("disabled",true)}});a.click(function(b){b.preventDefault();if($("#toggle-slim").bootstrapSwitch("status")){$("#toggle-slim").bootstrapSwitch("setState",false)}if(equipment_tour.first_run==true){equipment_tour.build()}if($(this).hasClass("disabled")){return}equipment_tour.tour.restart()})},build:function(){var c=$("#gear td:visible.upgrade")[0];var b=$(".item.active .stats-box .stat",c)[0];var a=$("#gear td:visible .item.active .crafted_by")[0];equipment_tour.tour.addSteps([{element:c,title:"Upgrade",content:"Boxes with a border and circle icon indicate an upgrade."},{element:b,title:"Stats",content:"These are the stats for the item.  Hovering on them will tell you how much of an upgrade it is from the previous item.",placement:"bottom"},{element:a,title:"Crafted By & Buyable",content:"<p>This item can be crafted by the class indicated. <strong>Clicking on it will add that item to your Crafting Cart!</strong></p><p>The coins indicate that you can buy them from a vendor.</p>"},{element:".previous-gear",title:"Level Traversing",content:"Click this bar (and the one on the right) to decrease (or increase) the gear level."},{element:"#toggle-slim",title:"Slim Mode",content:"If you don't want to see stats, turn this mode on.",placement:"top"},{element:"#toggle-all-stats",title:"Boring Stats",content:"Boring stats are hidden by default.  Use this option to turn them on.  An example of a boring stat would be Magic Damage to a Disciple of War class.",placement:"top"}]);equipment_tour.first_run=false}};$(equipment.init());function unique(a){return $.grep(a,function(c,b){return b==$.inArray(c,a)})};