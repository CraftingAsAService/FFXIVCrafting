var career={init:function(){$("#supporter-producer-class, #receiver-recipient-class, #gatherer-class").length>0&&$("#supporter-producer-class, #receiver-recipient-class, #gatherer-class").multiselect({buttonClass:"btn btn-sm btn-primary",buttonWidth:"auto",buttonContainer:'<div class="btn-group" />',maxHeight:!1,buttonText:function(t){if(0==t.length)return'None selected <b class="caret"></b>';var e="";return t.each((function(){e+=$(this).text()+", "})),e.substr(0,e.length-2)+' <b class="caret"></b>'}}),$("#supporter-supported-classes, #receiver-producer-classes, #gathering-supported-classes, #battling-supported-classes").length>0&&$("#supporter-supported-classes, #receiver-producer-classes, #gathering-supported-classes, #battling-supported-classes").multiselect({buttonClass:"btn btn-sm btn-primary",buttonWidth:"auto",buttonContainer:'<div class="btn-group" />',maxHeight:!1,buttonText:function(t){if(0==t.length)return'None selected <b class="caret"></b>';if(t.length>1)return"these "+t.length+' Classes  <b class="caret"></b>';var e="";return t.each((function(){e+=$(this).text()+", "})),e.substr(0,e.length-2)+' <b class="caret"></b>'}})}};$(career.init);