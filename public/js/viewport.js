var viewport={current:null,sizes:{mobile:{width:0},tablet:{width:751},desktop:{width:970}},init:function(){viewport.determine_size(!0),viewport.detect_resize()},determine_size:function(a){var b="mobile",c=$(window).innerWidth();$.each(viewport.sizes,function(a){c>=this.width&&(b=a)}),viewport.current!=b&&(viewport.current=b,a!==!0&&$.event.trigger({type:"viewportchanged"}))},detect_resize:function(){$(window).bind("resize",function(){viewport.determine_size()})}};$(viewport.init);