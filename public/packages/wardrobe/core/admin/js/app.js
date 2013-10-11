this["JST"] = this["JST"] || {};

this["JST"]["account/edit/templates/form.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<form class="form-horizontal center">\n  <div id="js-errors" class="hide">\n    <div class="alert alert-error">\n      <button type="button" class="close" data-dismiss="alert">×</button>\n      <span></span>\n    </div>\n  </div>\n  <div class="alert alert-success hide">\n    ' +
((__t = ( Lang.account_saved )) == null ? '' : __t) +
'\n  </div>\n  <div class="control-group">\n    <label class="control-label" for="first_name">' +
((__t = ( Lang.account_first_name )) == null ? '' : __t) +
'</label>\n    <div class="controls">\n      <input type="text" id="first_name" name="first_name" placeholder="' +
((__t = ( Lang.account_first_name )) == null ? '' : __t) +
'">\n    </div>\n  </div>\n  <div class="control-group">\n    <label class="control-label" for="last_name">' +
((__t = ( Lang.account_last_name )) == null ? '' : __t) +
'</label>\n    <div class="controls">\n      <input type="text" id="last_name" name="last_name" placeholder="' +
((__t = ( Lang.account_last_name )) == null ? '' : __t) +
'">\n    </div>\n  </div>\n  <div class="control-group">\n    <label class="control-label" for="email">' +
((__t = ( Lang.account_email )) == null ? '' : __t) +
'</label>\n    <div class="controls">\n      <input type="text" id="email" name="email" placeholder="' +
((__t = ( Lang.account_email )) == null ? '' : __t) +
'">\n    </div>\n  </div>\n  <div class="control-group">\n    <label class="control-label" for="password">' +
((__t = ( Lang.account_password )) == null ? '' : __t) +
'</label>\n    <div class="controls">\n      <input id="password" type="password" name="password" value="">\n      <span class="help-block">' +
((__t = ( Lang.account_password_keep )) == null ? '' : __t) +
'</span>\n    </div>\n  </div>\n  <div class="control-group">\n    <div class="controls">\n      <button type="submit" class="btn save">' +
((__t = ( Lang.account_save )) == null ? '' : __t) +
'</button>\n    </div>\n  </div>\n</form>\n';

}
return __p
};

this["JST"]["account/list/templates/grid.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="holder"></div>\n<button class="btn add-new"><i class="icon-plus-sign"></i> ' +
((__t = ( Lang.account_add_new )) == null ? '' : __t) +
'</button>';

}
return __p
};

this["JST"]["account/list/templates/item.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<a href="#" class="details">\n  <img src="" class="avatar img-polaroid" width="100" height="100">\n  ' +
((__t = ( first_name )) == null ? '' : __t) +
' ' +
((__t = ( last_name )) == null ? '' : __t) +
'\n</a>\n';
 if (canDelete()) { ;
__p += '\n  <a href="#" class="delete" title="' +
((__t = ( Lang.account_delete )) == null ? '' : __t) +
'"><i class="icon-trash"></i></a>\n';
 } ;
__p += '\n';

}
return __p
};

this["JST"]["account/new/templates/form.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<form class="form-horizontal center">\n  <div id="js-errors" class="hide">\n    <div class="alert alert-error">\n      <button type="button" class="close" data-dismiss="alert">×</button>\n      <span></span>\n    </div>\n  </div>\n  <div class="alert alert-success hide">\n    ' +
((__t = ( Lang.account_saved )) == null ? '' : __t) +
'\n  </div>\n  <div class="control-group">\n    <label class="control-label" for="first_name">' +
((__t = ( Lang.account_first_name )) == null ? '' : __t) +
'</label>\n    <div class="controls">\n      <input type="text" id="first_name" name="first_name" placeholder="' +
((__t = ( Lang.account_first_name )) == null ? '' : __t) +
'">\n    </div>\n  </div>\n  <div class="control-group">\n    <label class="control-label" for="last_name">' +
((__t = ( Lang.account_last_name )) == null ? '' : __t) +
'</label>\n    <div class="controls">\n      <input type="text" id="last_name" name="last_name" placeholder="' +
((__t = ( Lang.account_last_name )) == null ? '' : __t) +
'">\n    </div>\n  </div>\n  <div class="control-group">\n    <label class="control-label" for="email">' +
((__t = ( Lang.account_email )) == null ? '' : __t) +
'</label>\n    <div class="controls">\n      <input type="text" id="email" name="email" placeholder="' +
((__t = ( Lang.account_email )) == null ? '' : __t) +
'">\n    </div>\n  </div>\n  <div class="control-group">\n    <label class="control-label" for="password">' +
((__t = ( Lang.account_password )) == null ? '' : __t) +
'</label>\n    <div class="controls">\n      <input id="password" type="password" name="password" value="">\n    </div>\n  </div>\n  <div class="control-group">\n    <label class="control-label" for="password_confirm">' +
((__t = ( Lang.account_password_confirm )) == null ? '' : __t) +
'</label>\n    <div class="controls">\n      <input id="password_confirm" type="password" name="password_confirm" value="">\n    </div>\n  </div>\n  <div class="control-group">\n    <div class="controls">\n      <button type="submit" class="btn save">' +
((__t = ( Lang.account_add )) == null ? '' : __t) +
'</button>\n    </div>\n  </div>\n</form>\n';

}
return __p
};

this["JST"]["header/list/templates/header.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<nav>\n  <ul>\n    <li><a class="write" href="#"><i class="icon-plus"></i> ' +
((__t = ( Lang.write )) == null ? '' : __t) +
'</a></li>\n    <li><a class="posts" href="#post"><i class="icon-list"></i> ' +
((__t = ( Lang.posts )) == null ? '' : __t) +
'</a></li>\n    <li><a class="accounts" href="#accounts"><i class="icon-user"></i> ' +
((__t = ( Lang.accounts )) == null ? '' : __t) +
'</a></li>\n    <li><a href="' +
((__t = ( logoutUrl() )) == null ? '' : __t) +
'"><i class="icon-off"></i> ' +
((__t = ( Lang.logout )) == null ? '' : __t) +
'</a></li>\n  </ul>\n</nav>\n';

}
return __p
};

this["JST"]["post/_base/templates/form.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<form>\n  <input type="hidden" name="publish_date" id="publish_date" value="">\n  <div id="js-errors" class="hide">\n    <div class="alert alert-error">\n      <button type="button" class="close" data-dismiss="alert">×</button>\n      <span></span>\n    </div>\n  </div>\n  <div id="write">\n    <div class="info">\n      <div class="field">\n\n        <a href="' +
((__t = ( previewUrl() )) == null ? '' : __t) +
'" target="_blank" class="btn btn-mini preview pull-right">' +
((__t = ( Lang.post_preview )) == null ? '' : __t) +
'</a>\n        <button class="btn btn-mini btn-success publish pull-right">' +
((__t = ( submitBtnText() )) == null ? '' : __t) +
'</button>\n\n        <i data-dir="up" class="icon-chevron-sign-right js-toggle" title="' +
((__t = ( Lang.post_expand )) == null ? '' : __t) +
'"></i>\n        <input type="text" style="width: 50%" name="title" id="title" value="" placeholder="' +
((__t = ( Lang.post_title )) == null ? '' : __t) +
'">\n      </div>\n      <div class="details hide">\n        <div class="field">\n          <i class="icon-terminal" title="' +
((__t = ( Lang.post_slug )) == null ? '' : __t) +
'"></i>\n          <input type="text" style="width: 50%" name="slug" id="slug" value="" placeholder="' +
((__t = ( Lang.post_slug )) == null ? '' : __t) +
'">\n        </div>\n        <div class="field author">\n          <i class="icon-user" title="' +
((__t = ( Lang.post_author )) == null ? '' : __t) +
'"></i>\n          <select id="js-user" name="user_id"></select>\n        </div>\n        <div class="field status">\n          <i class="icon-off" title="Status"></i>\n          <label class="radio"><input type="radio" name="active" class="js-active" value="1" checked> ' +
((__t = ( Lang.post_published )) == null ? '' : __t) +
'</label>\n          <label class="radio"><input type="radio" name="active" class="js-active" value="0"> ' +
((__t = ( Lang.post_draft )) == null ? '' : __t) +
'</label>\n        </div>\n      </div>\n    </div>\n    <div class="content-area">\n      <textarea name="content" id="content" placeholder="' +
((__t = ( Lang.post_content )) == null ? '' : __t) +
'"></textarea>\n      <div class="tags-bar hide">\n        <input type="text" id="js-tags" name="tags" class="tags" style="width: 90%" value="" placeholder="' +
((__t = ( Lang.post_tags )) == null ? '' : __t) +
'">\n      </div>\n    </div>\n  </div>\n</form>\n\n<div id="date-form" style="display: none">\n  <form class="form-inline">\n    <label for="date">' +
((__t = ( Lang.post_publish_date )) == null ? '' : __t) +
'</label><br>\n    <input type="text" name="date" class="js-date" id="date" value="" placeholder="Next Thursday 10am">\n    <button class="btn js-setdate">' +
((__t = ( Lang.post_publish_date_set )) == null ? '' : __t) +
'</button>\n  </form>\n</div>\n\n<div id="film-form" style="display: none">\n  <form class="form-inline">\n    <label for="date">Video URL</label><br>\n    <input type="text" name="date" class="js-film" id="film" value="" placeholder="http://youtube.com/">\n    <button class="btn js-submitfilm">' +
((__t = ( Lang.post_publish_date_set )) == null ? '' : __t) +
'</button>\n  </form>\n</div>\n';

}
return __p
};

this["JST"]["post/list/templates/empty.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<td colspan="4">' +
((__t = ( Lang.posts_none )) == null ? '' : __t) +
'</td>';

}
return __p
};

this["JST"]["post/list/templates/grid.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="filter">\n  <input type="text" class="filter" id="js-filter" name="filter" placeholder="Filter">\n  <select name="active" id="js-sort">\n    <option value="">Any Status</option>\n    <option value="1">Active</option>\n    <option value="0">Draft</option>\n  </select>\n</div>\n<table class="table center-col">\n\t<thead>\n\t\t<tr>\n\t\t\t<th>' +
((__t = ( Lang.post_title )) == null ? '' : __t) +
'</th>\n\t\t\t<th>' +
((__t = ( Lang.post_status )) == null ? '' : __t) +
'</th>\n\t\t\t<th>' +
((__t = ( Lang.post_published )) == null ? '' : __t) +
'</th>\n\t\t\t<th></th>\n\t\t</tr>\n\t</thead>\n\t<tbody></tbody>\n</table>\n';

}
return __p
};

this["JST"]["post/list/templates/item.html"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<td class="title">\n  <img src="" class="avatar img-polaroid" width="18" height="18">\n  <a href="#" class="details">' +
((__t = ( title )) == null ? '' : __t) +
'</a>\n</td>\n<td class="status">' +
((__t = ( status() )) == null ? '' : __t) +
'</td>\n<td class="date js-format-date" data-date="' +
((__t = ( publish_date )) == null ? '' : __t) +
'">' +
((__t = ( publish_date )) == null ? '' : __t) +
'</td>\n<td class="actions">\n  <a href="#" target="_blank" title="Preview" class="preview"><i class="icon-zoom-in"></i></a>\n  <a href="#" class="delete" title="' +
((__t = ( Lang.post_delete )) == null ? '' : __t) +
'"><i class="icon-trash"></i></a>\n</td>\n';

}
return __p
};

(function(Backbone) {
  var methods, _sync;
  _sync = Backbone.sync;
  Backbone.sync = function(method, entity, options) {
    var sync;
    if (options == null) {
      options = {};
    }
    _.defaults(options, {
      beforeSend: _.bind(methods.beforeSend, entity),
      complete: _.bind(methods.complete, entity)
    });
    sync = _sync(method, entity, options);
    if (!entity._fetch && method === "read") {
      return entity._fetch = sync;
    }
  };
  return methods = {
    beforeSend: function() {
      return this.trigger("sync:start", this);
    },
    complete: function() {
      return this.trigger("sync:stop", this);
    }
  };
})(Backbone);


$.fn.avatar = function(email, size) {
  if (size == null) {
    size = 28;
  }
  return $(this).attr("src", '//www.gravatar.com/avatar/' + md5(email.toLowerCase()) + '?s=' + (size * 2));
};


$.fn.formatDates = function() {
  var $el;
  $el = $(this);
  return $el.each(function(index, param) {
    var format, item, originalDate, time;
    item = $(this);
    format = item.data("format");
    originalDate = item.data("date");
    if (typeof format === "undefined") {
      format = "MMM Do YYYY, hh:mma";
    }
    time = isNaN(originalDate) ? moment(originalDate, "YYYY-MM-DD HH:mm:ss") : moment.unix(originalDate);
    return item.text(time.local().format(format));
  });
};


$.fn.fillJSON = function(json) {
  var $el, key, val, _results;
  $el = $(this);
  _results = [];
  for (key in json) {
    val = json[key];
    if (key !== "active") {
      _results.push($el.find("[name='" + key + "']").val(val));
    } else {
      _results.push(void 0);
    }
  }
  return _results;
};

$.fn.showAlert = function(title, msg, type) {
  var $el, html;
  $el = $(this);
  html = "<div class='alert alert-block " + type + "'>    <button type='button' class='close' data-dismiss='alert'>×</button>    <h4 class='alert-heading'>" + title + "</h4>    <p>" + msg + "</p>  </div>";
  $el.html(html).fadeIn();
  return $(".alert").delay(3000).fadeOut(400);
};


(function(Backbone) {
  return _.extend(Backbone.Marionette.Application.prototype, {
    navigate: function(route, options) {
      if (options == null) {
        options = {};
      }
      return Backbone.history.navigate(route, options);
    },
    getCurrentRoute: function() {
      var frag;
      frag = Backbone.history.fragment;
      if (_.isEmpty(frag)) {
        return null;
      } else {
        return frag;
      }
    },
    startHistory: function() {
      if (Backbone.history) {
        return Backbone.history.start();
      }
    },
    register: function(instance, id) {
      var _ref;
      if ((_ref = this._registry) == null) {
        this._registry = {};
      }
      return this._registry[id] = instance;
    },
    unregister: function(instance, id) {
      return delete this._registry[id];
    },
    resetRegistry: function() {
      var controller, key, msg, oldCount, _ref;
      oldCount = this.getRegistrySize();
      _ref = this._registry;
      for (key in _ref) {
        controller = _ref[key];
        controller.region.close();
      }
      msg = "There were " + oldCount + " controllers in the registry, there are now " + (this.getRegistrySize());
      if (this.getRegistrySize() > 0) {
        return console.warn(msg, this._registry);
      } else {
        return console.log(msg);
      }
    },
    getRegistrySize: function() {
      return _.size(this._registry);
    }
  });
})(Backbone);

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

(function(Backbone, Marionette) {
  return Marionette.Region.Dialog = (function(_super) {

    __extends(Dialog, _super);

    function Dialog() {
      _.extend(this, Backbone.Events);
    }

    Dialog.prototype.onShow = function(view) {
      var options,
        _this = this;
      this.setupBindings(view);
      options = this.getDefaultOptions(_.result(view, "dialog"));
      this.$el.on("hidden", function() {
        return _this.closeDialog();
      });
      this.$el.on("shown", function() {
        return Snappy.execute("dialog:shown", view);
      });
      return this.$el.modal(options);
    };

    Dialog.prototype.getDefaultOptions = function(options) {
      if (options == null) {
        options = {};
      }
      return _.defaults(options, {
        backdrop: true,
        keyboard: true,
        show: true,
        remote: false,
        shown: null
      });
    };

    Dialog.prototype.setupBindings = function(view) {
      return this.listenTo(view, "dialog:close", this.closeDialog);
    };

    Dialog.prototype.closeDialog = function() {
      this.stopListening();
      this.close();
      return this.$el.modal("hide");
    };

    return Dialog;

  })(Marionette.Region);
})(Backbone, Marionette);


Backbone.Marionette.Renderer.render = function(template, data) {
  var path;
  path = JST[template + ".html"];
  if (!path) {
    throw "Template " + template + " not found!";
  }
  return path(data);
};


_.mixin({
  stripTrailingSlash: function(url) {
    if (url.slice(-1) === '/') {
      return url.substr(0, url.length - 1);
    } else {
      return url;
    }
  }
});

var Storage;

Storage = (function() {

  function Storage(opts) {
    if (opts == null) {
      opts = {};
    }
    this.key = opts.id || "new";
  }

  Storage.prototype.getKey = function() {
    return "post-" + this.key;
  };

  Storage.prototype.put = function(data, ttl) {
    if (ttl == null) {
      ttl = 30000;
    }
    $.jStorage.set(this.getKey(), data, ttl);
    return $.jStorage.publish(this.getKey(), data);
  };

  Storage.prototype.get = function(default_val) {
    if (default_val == null) {
      default_val = {};
    }
    return $.jStorage.get(this.getKey(), default_val);
  };

  Storage.prototype.destroy = function() {
    return $.jStorage.deleteKey(this.getKey());
  };

  return Storage;

})();


this.Wardrobe = (function(Backbone, Marionette) {
  var App;
  App = new Backbone.Marionette.Application();
  App.on("initialize:before", function(options) {
    App.environment = $('meta[name=env]').attr("content");
    App.csrfToken = $("meta[name='token']").attr('content');
    this.currentUser = App.request("set:current:user", options.user);
    this.allUsers = App.request("set:all:users", options.users);
    this.apiUrl = _.stripTrailingSlash(options.api_url);
    this.adminUrl = _.stripTrailingSlash(options.admin_url);
    return this.blogUrl = _.stripTrailingSlash(options.blog_url);
  });
  App.reqres.setHandler("get:current:user", function() {
    return App.currentUser;
  });
  App.reqres.setHandler("get:all:users", function() {
    return App.allUsers;
  });
  App.reqres.setHandler("get:url:api", function() {
    return App.apiUrl;
  });
  App.reqres.setHandler("get:url:admin", function() {
    return App.adminUrl;
  });
  App.reqres.setHandler("get:url:blog", function() {
    return App.blogUrl;
  });
  App.addRegions({
    headerRegion: "#header-region",
    topnavRegion: "#top-region",
    mainRegion: "#main-region",
    footerRegion: "footer-region"
  });
  App.addInitializer(function() {
    return App.module("HeaderApp").start();
  });
  App.reqres.setHandler("default:region", function() {
    return App.mainRegion;
  });
  App.commands.setHandler("register:instance", function(instance, id) {
    if (App.environment === "dev") {
      return App.register(instance, id);
    }
  });
  App.commands.setHandler("unregister:instance", function(instance, id) {
    if (App.environment === "dev") {
      return App.unregister(instance, id);
    }
  });
  App.on("initialize:after", function() {
    return this.startHistory();
  });
  return App;
})(Backbone, Marionette);


this.Wardrobe.module("Entities", function(Entities, App, Backbone, Marionette, $, _) {
  return App.commands.setHandler("when:fetched", function(entities, callback) {
    var xhrs;
    xhrs = _.chain([entities]).flatten().pluck("_fetch").value();
    return $.when.apply($, xhrs).done(function() {
      return callback();
    });
  });
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Entities", function(Entities, App, Backbone, Marionette, $, _) {
  return Entities.Collection = (function(_super) {

    __extends(Collection, _super);

    function Collection() {
      return Collection.__super__.constructor.apply(this, arguments);
    }

    Collection.prototype.initialize = function(attributes, options) {
      options || (options = {});
      this.bind("error", this.defaultErrorHandler);
      return this.init && this.init(attributes, options);
    };

    Collection.prototype.defaultErrorHandler = function(model, error) {
      switch (error.status) {
        case 401:
          return document.location.href = "" + (App.request("get:url:admin")) + "/logout";
      }
    };

    Collection.prototype.sync = function(method, model, options) {
      options.headers = _.extend({
        "X-CSRF-Token": App.csrfToken
      }, options.headers);
      return Backbone.sync(method, model, options);
    };

    return Collection;

  })(Backbone.Collection);
});

var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Entities", function(Entities, App, Backbone, Marionette, $, _) {
  return Entities.Model = (function(_super) {

    __extends(Model, _super);

    function Model() {
      this.saveError = __bind(this.saveError, this);

      this.saveSuccess = __bind(this.saveSuccess, this);
      return Model.__super__.constructor.apply(this, arguments);
    }

    Model.prototype.initialize = function(attributes, options) {
      options || (options = {});
      this.bind("error", this.defaultErrorHandler);
      return this.init && this.init(attributes, options);
    };

    Model.prototype.defaultErrorHandler = function(model, error, test) {
      switch (error.status) {
        case 500:
          return $("#js-alert").showAlert(Lang.error, Lang.error_fivehundred, "alert-error");
        case 401:
          return document.location.href = "" + (App.request("get:url:admin")) + "/logout";
      }
    };

    Model.prototype.destroy = function(options) {
      if (options == null) {
        options = {};
      }
      this.set({
        _destroy: true
      });
      return Model.__super__.destroy.call(this, options);
    };

    Model.prototype.isDestroyed = function() {
      return this.get("_destroy");
    };

    Model.prototype.save = function(data, options) {
      var isNew;
      if (options == null) {
        options = {};
      }
      isNew = this.isNew();
      _.defaults(options, {
        wait: true,
        success: _.bind(this.saveSuccess, this, isNew, options.collection),
        error: _.bind(this.saveError, this)
      });
      this.unset("_errors");
      return Model.__super__.save.call(this, data, options);
    };

    Model.prototype.saveSuccess = function(isNew, collection) {
      if (isNew) {
        if (collection) {
          collection.add(this);
        }
        if (collection) {
          collection.trigger("model:created", this);
        }
        return this.trigger("created", this);
      } else {
        if (collection == null) {
          collection = this.collection;
        }
        if (collection) {
          collection.trigger("model:updated", this);
        }
        return this.trigger("updated", this);
      }
    };

    Model.prototype.saveError = function(model, xhr, options) {
      if (xhr.status === 400) {
        return this.set({
          _errors: $.parseJSON(xhr.responseText)
        });
      }
    };

    Model.prototype.sync = function(method, model, options) {
      options.headers = _.extend({
        "X-CSRF-Token": App.csrfToken
      }, options.headers);
      return Backbone.sync(method, model, options);
    };

    return Model;

  })(Backbone.Model);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Entities", function(Entities, App, Backbone, Marionette, $, _) {
  var API;
  Entities.Post = (function(_super) {

    __extends(Post, _super);

    function Post() {
      return Post.__super__.constructor.apply(this, arguments);
    }

    Post.prototype.urlRoot = function() {
      return App.request("get:url:api") + "/post";
    };

    return Post;

  })(App.Entities.Model);
  Entities.PostCollection = (function(_super) {

    __extends(PostCollection, _super);

    function PostCollection() {
      return PostCollection.__super__.constructor.apply(this, arguments);
    }

    PostCollection.prototype.model = Entities.Post;

    PostCollection.prototype.url = function() {
      return App.request("get:url:api") + "/post";
    };

    return PostCollection;

  })(App.Entities.Collection);
  API = {
    getAll: function() {
      var post;
      post = new Entities.PostCollection;
      post.fetch({
        reset: true
      });
      return post;
    },
    getPost: function(id) {
      var post;
      post = new Entities.Post({
        id: id
      });
      post.fetch();
      return post;
    },
    newPost: function() {
      return new Entities.Post;
    }
  };
  App.reqres.setHandler("post:entities", function() {
    return API.getAll();
  });
  App.reqres.setHandler("post:entity", function(id) {
    return API.getPost(id);
  });
  return App.reqres.setHandler("new:post:entity", function() {
    return API.newPost();
  });
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Entities", function(Entities, App, Backbone, Marionette, $, _) {
  var API;
  Entities.Tag = (function(_super) {

    __extends(Tag, _super);

    function Tag() {
      return Tag.__super__.constructor.apply(this, arguments);
    }

    Tag.prototype.urlRoot = function() {
      return App.request("get:url:api") + "/tag";
    };

    return Tag;

  })(App.Entities.Model);
  Entities.TagCollection = (function(_super) {

    __extends(TagCollection, _super);

    function TagCollection() {
      return TagCollection.__super__.constructor.apply(this, arguments);
    }

    TagCollection.prototype.model = Entities.Tag;

    TagCollection.prototype.url = function() {
      return App.request("get:url:api") + "/tag";
    };

    return TagCollection;

  })(App.Entities.Collection);
  API = {
    getAll: function(cb) {
      var tags;
      tags = new Entities.TagCollection;
      tags.fetch({
        reset: true,
        success: function(collection, response, options) {
          if (cb) {
            return cb(tags);
          }
        },
        error: function() {
          throw new Error("Tags not fetched");
        }
      });
      return tags;
    }
  };
  return App.reqres.setHandler("tag:entities", function(cb) {
    return API.getAll(cb);
  });
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Entities", function(Entities, App, Backbone, Marionette, $, _) {
  var API;
  Entities.User = (function(_super) {

    __extends(User, _super);

    function User() {
      return User.__super__.constructor.apply(this, arguments);
    }

    User.prototype.urlRoot = function() {
      return App.request("get:url:api") + "/user";
    };

    return User;

  })(App.Entities.Model);
  Entities.UsersCollection = (function(_super) {

    __extends(UsersCollection, _super);

    function UsersCollection() {
      return UsersCollection.__super__.constructor.apply(this, arguments);
    }

    UsersCollection.prototype.model = Entities.User;

    UsersCollection.prototype.url = function() {
      return App.request("get:url:api") + "/user";
    };

    return UsersCollection;

  })(App.Entities.Collection);
  API = {
    setCurrentUser: function(currentUser) {
      return new Entities.User(currentUser);
    },
    setAllUsers: function(users) {
      return new Entities.UsersCollection(users);
    },
    getUser: function(id) {
      var user;
      user = new Entities.User({
        id: id
      });
      user.fetch();
      return user;
    },
    getUserEntities: function(cb) {
      var users;
      users = new Entities.UsersCollection;
      return users.fetch({
        success: function() {
          return cb(users);
        }
      });
    },
    newUser: function() {
      return new Entities.User;
    }
  };
  App.reqres.setHandler("set:current:user", function(currentUser) {
    return API.setCurrentUser(currentUser);
  });
  App.reqres.setHandler("set:all:users", function(users) {
    return API.setAllUsers(users);
  });
  App.reqres.setHandler("user:entities", function(cb) {
    return API.getUserEntities(cb);
  });
  App.reqres.setHandler("user:entity", function(id) {
    return API.getUser(id);
  });
  return App.reqres.setHandler("new:user:entity", function() {
    return API.newUser();
  });
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
  __slice = [].slice;

this.Wardrobe.module("Controllers", function(Controllers, App, Backbone, Marionette, $, _) {
  return Controllers.Base = (function(_super) {

    __extends(Base, _super);

    function Base(options) {
      if (options == null) {
        options = {};
      }
      this.region = options.region || App.request("default:region");
      Base.__super__.constructor.call(this, options);
      this._instance_id = _.uniqueId("controller");
      App.execute("register:instance", this, this._instance_id);
    }

    Base.prototype.close = function() {
      var args;
      args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
      delete this.region;
      delete this.options;
      Base.__super__.close.call(this, args);
      return App.execute("unregister:instance", this, this._instance_id);
    };

    Base.prototype.show = function(view) {
      this.listenTo(view, "close", this.close);
      return this.region.show(view);
    };

    return Base;

  })(Marionette.Controller);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Views", function(Views, App, Backbone, Marionette, $, _) {
  return Views.CollectionView = (function(_super) {

    __extends(CollectionView, _super);

    function CollectionView() {
      return CollectionView.__super__.constructor.apply(this, arguments);
    }

    CollectionView.prototype.itemViewEventPrefix = "childview";

    return CollectionView;

  })(Marionette.CollectionView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Views", function(Views, App, Backbone, Marionette, $, _) {
  return Views.CompositeView = (function(_super) {

    __extends(CompositeView, _super);

    function CompositeView() {
      return CompositeView.__super__.constructor.apply(this, arguments);
    }

    CompositeView.prototype.itemViewEventPrefix = "childview";

    return CompositeView;

  })(Marionette.CompositeView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Views", function(Views, App, Backbone, Marionette, $, _) {
  return Views.ItemView = (function(_super) {

    __extends(ItemView, _super);

    function ItemView() {
      return ItemView.__super__.constructor.apply(this, arguments);
    }

    ItemView.prototype.fillJSON = function(data) {
      var _ref, _ref1;
      if (data == null) {
        data = {};
      }
      if ((_ref = this.model) != null ? _ref.isNew() : void 0) {
        return this.$('form').fillJSON(data);
      } else {
        return this.$('form').fillJSON(((_ref1 = this.model) != null ? _ref1.toJSON() : void 0) || data);
      }
    };

    ItemView.prototype.changeErrors = function(model, errors, options) {
      if (_.isEmpty(errors)) {
        return this.removeErrors();
      } else {
        return this.addErrors(errors);
      }
    };

    ItemView.prototype.addErrors = function(errors) {
      var error, name, _results;
      if (errors == null) {
        errors = {};
      }
      this.$("#js-errors").show().find("span").html(Lang.post_errors);
      _results = [];
      for (name in errors) {
        error = errors[name];
        _results.push(this.addError(error));
      }
      return _results;
    };

    ItemView.prototype.addError = function(error) {
      var sm;
      sm = $("<li>").text(error);
      return this.$("#js-errors span").append(sm);
    };

    ItemView.prototype.removeErrors = function() {
      return this.$("#js-errors").hide();
    };

    return ItemView;

  })(Marionette.ItemView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Views", function(Views, App, Backbone, Marionette, $, _) {
  return Views.Layout = (function(_super) {

    __extends(Layout, _super);

    function Layout() {
      return Layout.__super__.constructor.apply(this, arguments);
    }

    return Layout;

  })(Marionette.Layout);
});

var __slice = [].slice;

this.Wardrobe.module("Views", function(Views, App, Backbone, Marionette, $, _) {
  var _remove;
  _remove = Marionette.View.prototype.remove;
  return _.extend(Marionette.View.prototype, {
    remove: function() {
      var args, _ref,
        _this = this;
      args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
      if (App.environment === "dev") {
        console.log("removing", this);
      }
      if ((_ref = this.model) != null ? typeof _ref.isDestroyed === "function" ? _ref.isDestroyed() : void 0 : void 0) {
        return this.$el.fadeOut(400, function() {
          return _remove.apply(_this, args);
        });
      } else {
        return _remove.apply(this, args);
      }
    }
  });
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("AccountApp", function(AccountApp, App, Backbone, Marionette, $, _) {
  var API;
  AccountApp.Router = (function(_super) {

    __extends(Router, _super);

    function Router() {
      return Router.__super__.constructor.apply(this, arguments);
    }

    Router.prototype.appRoutes = {
      "accounts": "list",
      "account/new": "new",
      "account/edit/:id": "edit"
    };

    return Router;

  })(Marionette.AppRouter);
  API = {
    list: function() {
      return new AccountApp.List.Controller;
    },
    "new": function() {
      return new AccountApp.New.Controller({
        region: App.mainRegion
      });
    },
    edit: function(id, account) {
      return new AccountApp.Edit.Controller({
        region: App.mainRegion,
        id: id,
        account: account
      });
    }
  };
  App.vent.on("account:clicked", function() {
    App.navigate("/accounts");
    return API.list();
  });
  App.vent.on("account:new:clicked", function() {
    App.navigate("/account/new");
    return API["new"]();
  });
  App.vent.on("account:edit:clicked", function(account) {
    App.navigate("/account/edit/" + account.id);
    return API.edit(account.id, account);
  });
  App.vent.on("account:created account:updated", function() {
    $("#js-alert").showAlert("Success!", "Account was successfully saved.", "alert-success");
    App.navigate("accounts");
    return API.list();
  });
  return App.addInitializer(function() {
    return new AccountApp.Router({
      controller: API
    });
  });
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("AccountApp.Edit", function(Edit, App, Backbone, Marionette, $, _) {
  return Edit.Controller = (function(_super) {

    __extends(Controller, _super);

    function Controller() {
      return Controller.__super__.constructor.apply(this, arguments);
    }

    Controller.prototype.initialize = function(options) {
      var account, id,
        _this = this;
      account = options.account, id = options.id;
      account || (account = App.request("user:entity", id));
      this.listenTo(account, "updated", function() {
        return App.vent.trigger("account:updated", account);
      });
      return App.execute("when:fetched", account, function() {
        var view;
        view = _this.getEditView(account);
        return _this.show(view);
      });
    };

    Controller.prototype.getEditView = function(account) {
      return new Edit.User({
        model: account
      });
    };

    return Controller;

  })(App.Controllers.Base);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("AccountApp.Edit", function(Edit, App, Backbone, Marionette, $, _) {
  return Edit.User = (function(_super) {

    __extends(User, _super);

    function User() {
      return User.__super__.constructor.apply(this, arguments);
    }

    User.prototype.template = "account/edit/templates/form";

    User.prototype.className = "span12";

    User.prototype.events = {
      "click .save": "save"
    };

    User.prototype.modelEvents = {
      "change:_errors": "changeErrors"
    };

    User.prototype.onRender = function() {
      return this.fillJSON();
    };

    User.prototype.save = function(e) {
      var data;
      e.preventDefault();
      data = {
        first_name: this.$('#first_name').val(),
        last_name: this.$('#last_name').val(),
        email: this.$('#email').val(),
        password: this.$('#password').val(),
        active: 1
      };
      return this.model.save(data);
    };

    return User;

  })(App.Views.ItemView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("AccountApp.List", function(List, App, Backbone, Marionette, $, _) {
  return List.Controller = (function(_super) {

    __extends(Controller, _super);

    function Controller() {
      return Controller.__super__.constructor.apply(this, arguments);
    }

    Controller.prototype.initialize = function() {
      var users, view;
      users = App.request("get:all:users");
      view = this.getListView(users);
      this.show(view);
      return this.listenTo(view, "childview:account:delete:clicked", function(child, args) {
        var confirmMsg, model;
        model = args.model;
        confirmMsg = Lang.account_delete_confirm.replace("##first_name##", _.escape(model.get("first_name"))).replace("##last_name##", _.escape(model.get("last_name")));
        if (confirm(confirmMsg)) {
          return model.destroy();
        } else {
          return false;
        }
      });
    };

    Controller.prototype.getListView = function(users) {
      return new List.Accounts({
        collection: users
      });
    };

    return Controller;

  })(App.Controllers.Base);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("AccountApp.List", function(List, App, Backbone, Marionette, $, _) {
  List.AccountItem = (function(_super) {

    __extends(AccountItem, _super);

    function AccountItem() {
      return AccountItem.__super__.constructor.apply(this, arguments);
    }

    AccountItem.prototype.template = "account/list/templates/item";

    AccountItem.prototype.className = "account";

    AccountItem.prototype.triggers = {
      "click .delete": "account:delete:clicked"
    };

    AccountItem.prototype.events = {
      "click .details": "edit"
    };

    AccountItem.prototype.templateHelpers = function() {
      return {
        canDelete: function() {
          var me;
          me = App.request("get:current:user");
          if (me.id !== this.id) {
            return true;
          } else {
            return false;
          }
        }
      };
    };

    AccountItem.prototype.onShow = function() {
      var $avEl;
      $avEl = this.$(".avatar");
      return $avEl.avatar(this.model.get("email"), $avEl.attr("width"));
    };

    AccountItem.prototype.edit = function(e) {
      e.preventDefault();
      return App.vent.trigger("account:edit:clicked", this.model);
    };

    return AccountItem;

  })(App.Views.ItemView);
  return List.Accounts = (function(_super) {

    __extends(Accounts, _super);

    function Accounts() {
      return Accounts.__super__.constructor.apply(this, arguments);
    }

    Accounts.prototype.template = "account/list/templates/grid";

    Accounts.prototype.itemView = List.AccountItem;

    Accounts.prototype.itemViewContainer = ".holder";

    Accounts.prototype.className = "accounts";

    Accounts.prototype.events = {
      "click .add-new": "new"
    };

    Accounts.prototype["new"] = function(e) {
      e.preventDefault();
      return App.vent.trigger("account:new:clicked");
    };

    return Accounts;

  })(App.Views.CompositeView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("AccountApp.New", function(New, App, Backbone, Marionette, $, _) {
  return New.Controller = (function(_super) {

    __extends(Controller, _super);

    function Controller() {
      return Controller.__super__.constructor.apply(this, arguments);
    }

    Controller.prototype.initialize = function() {
      var user, view;
      user = App.request("new:user:entity");
      this.listenTo(user, "created", function() {
        return App.vent.trigger("account:created", user);
      });
      view = this.getNewView(user);
      return this.show(view);
    };

    Controller.prototype.getNewView = function(user) {
      return new New.User({
        model: user,
        collection: App.request("get:all:users")
      });
    };

    return Controller;

  })(App.Controllers.Base);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("AccountApp.New", function(New, App, Backbone, Marionette, $, _) {
  return New.User = (function(_super) {

    __extends(User, _super);

    function User() {
      return User.__super__.constructor.apply(this, arguments);
    }

    User.prototype.template = "account/new/templates/form";

    User.prototype.className = "span12";

    User.prototype.events = {
      "click .save": "save"
    };

    User.prototype.modelEvents = {
      "change:_errors": "changeErrors"
    };

    User.prototype.onRender = function() {
      return this.fillJSON();
    };

    User.prototype.save = function(e) {
      var data;
      e.preventDefault();
      data = {
        first_name: this.$('#first_name').val(),
        last_name: this.$('#last_name').val(),
        email: this.$('#email').val(),
        password: this.$('#password').val(),
        active: 1
      };
      return this.model.save(data, {
        collection: this.collection
      });
    };

    return User;

  })(App.Views.ItemView);
});


this.Wardrobe.module("DropzoneApp", function(DropzoneApp, App, Backbone, Marionette, $, _) {
  var API;
  API = {
    setupDropzone: function() {
      var myDropzone;
      myDropzone = new Dropzone(document.body, {
        url: App.request("get:url:api") + "/dropzone",
        method: "POST",
        clickable: false
      });
      myDropzone.on("drop", function(file) {
        return App.vent.trigger("post:new");
      });
      myDropzone.on("error", function(file, message, xhr) {
        var msg;
        msg = $.parseJSON(message);
        return $("#js-alert").showAlert("Error!", msg.error.message, "alert-error");
      });
      return myDropzone.on("success", function(file, contents) {
        return App.vent.trigger("post:new:seed", contents);
      });
    }
  };
  return DropzoneApp.on("start", function() {
    return API.setupDropzone();
  });
});


this.Wardrobe.module("HeaderApp", function(HeaderApp, App, Backbone, Marionette, $, _) {
  var API;
  this.startWithParent = false;
  API = {
    list: function() {
      return new HeaderApp.List.Controller({
        region: App.headerRegion
      });
    }
  };
  return HeaderApp.on("start", function() {
    return API.list();
  });
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("HeaderApp.List", function(List, App, Backbone, Marionette, $, _) {
  return List.Controller = (function(_super) {

    __extends(Controller, _super);

    function Controller() {
      return Controller.__super__.constructor.apply(this, arguments);
    }

    Controller.prototype.initialize = function() {
      var listView;
      listView = this.getListView();
      return this.show(listView);
    };

    Controller.prototype.getListView = function() {
      return new List.Header;
    };

    return Controller;

  })(App.Controllers.Base);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("HeaderApp.List", function(List, App, Backbone, Marionette, $, _) {
  return List.Header = (function(_super) {

    __extends(Header, _super);

    function Header() {
      return Header.__super__.constructor.apply(this, arguments);
    }

    Header.prototype.template = "header/list/templates/header";

    Header.prototype.tagName = "header";

    Header.prototype.events = {
      "click .write": "newPost",
      "click .accounts": "accounts"
    };

    Header.prototype.onRender = function() {
      return this.generateAvatar(App.request("get:current:user"));
    };

    Header.prototype.templateHelpers = {
      logoutUrl: function() {
        return "" + (App.request("get:url:admin")) + "/logout";
      }
    };

    Header.prototype.generateAvatar = function(user) {
      var $avEl;
      $avEl = this.$(".avatar");
      return $avEl.avatar(user.get("email"), $avEl.attr("width"));
    };

    Header.prototype.accounts = function(e) {
      e.preventDefault();
      return App.vent.trigger("account:clicked");
    };

    Header.prototype.newPost = function(e) {
      e.preventDefault();
      return App.vent.trigger("post:new:clicked");
    };

    return Header;

  })(App.Views.ItemView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("Views", function(Views, App, Backbone, Marionette, $, _) {
  return Views.PostView = (function(_super) {

    __extends(PostView, _super);

    function PostView() {
      return PostView.__super__.constructor.apply(this, arguments);
    }

    PostView.prototype.template = "post/_base/templates/form";

    PostView.prototype.className = "span12";

    PostView.prototype.initialize = function(opts) {
      var _this = this;
      App.vent.on("post:new:seed", function(contents) {
        return _this.fillForm(contents);
      });
      this.tagsShown = false;
      return this.storage = opts.storage;
    };

    PostView.prototype.events = {
      "click .publish": "save",
      "click .js-toggle": "toggleDetails",
      "click .icon-tags": "toggleTags",
      "click .icon-user": "showUsers",
      "click .icon-ellipsis-horizontal": "insertReadMore",
      "click input[type=radio]": "changeBtn",
      "keyup #title": "localStorage",
      "change #js-user": "localStorage"
    };

    PostView.prototype.insertReadMore = function() {
      if (this.editor.codemirror.getValue().match(/!-- more/g)) {
        return this.$("#js-errors").show().find("span").html(Lang.post_more_added);
      } else {
        this.$(".icon-ellipsis-horizontal").addClass("disabled");
        return this.insert('<!-- more -->');
      }
    };

    PostView.prototype.modelEvents = {
      "change:_errors": "changeErrors"
    };

    PostView.prototype.templateHelpers = {
      submitBtnText: function() {
        if ((this.active != null) || this.active === "1") {
          return Lang.post_publish;
        } else {
          return Lang.post_save;
        }
      },
      previewUrl: function() {
        var id;
        id = this.id ? this.id : "new";
        return "" + (App.request("get:url:blog")) + "/post/preview/" + id;
      }
    };

    PostView.prototype.onShow = function() {
      var _this = this;
      this.setUpEditor();
      this.setupUsers();
      this.setupCalendar();
      this.setupFilm();
      this.localStorage();
      this._triggerActive();
      if (this.model.isNew()) {
        this.$('.js-toggle').trigger("click");
        $('#title').slugIt({
          output: "#slug"
        });
      }
      return App.request("tag:entities", function(tags) {
        return _this.setUpTags(tags);
      });
    };

    PostView.prototype._triggerActive = function() {
      if (this.model.isNew()) {
        return this;
      }
      if (this.model.get("active")) {
        return this.$(".js-active[value=1]").trigger("click");
      } else {
        return $(".js-active[value=0]").trigger("click");
      }
    };

    PostView.prototype.setUpEditor = function() {
      var toolbar,
        _this = this;
      toolbar = ['bold', 'italic', '|', 'quote', 'unordered-list', 'ordered-list', 'ellipsis-horizontal', '|', 'link', 'image', 'code', 'film', '|', 'undo', 'redo', '|', 'tags', 'calendar'];
      this.editor = new Editor({
        element: document.getElementById("content"),
        toolbar: toolbar
      });
      this.imageUpload(this.editor);
      return this.editor.codemirror.on("change", function(cm, change) {
        return _this.localStorage();
      });
    };

    PostView.prototype.localStorage = function() {
      return this.storage.put({
        title: this.$('#title').val(),
        slug: this.$('#slug').val(),
        active: this.$('input[type=radio]:checked').val(),
        content: this.editor.codemirror.getValue(),
        tags: this.$("#js-tags").val(),
        user_id: this.$("#js-user").val(),
        publish_date: this.$("#publish_date").val()
      });
    };

    PostView.prototype.setupUsers = function() {
      var $userSelect, stored, user, users;
      $userSelect = this.$("#js-user");
      users = App.request("get:all:users");
      users.each(function(item) {
        return $userSelect.append($("<option></option>").val(item.id).html(item.get("first_name") + " " + item.get("last_name")));
      });
      if (this.model.isNew()) {
        user = App.request("get:current:user");
        stored = this.storage.get();
        if (stored != null ? stored.user_id : void 0) {
          return $userSelect.val(stored.user_id);
        } else {
          return $userSelect.val(user.id);
        }
      } else {
        return $userSelect.val(this.model.get("user_id"));
      }
    };

    PostView.prototype.setUpTags = function(tags) {
      return this.$("#js-tags").selectize({
        persist: true,
        delimiter: ',',
        maxItems: null,
        options: this.generateTagOptions(tags),
        render: {
          item: function(item) {
            return "<div><i class='icon-tag'></i> " + item.text + "</div>";
          },
          option: function(item) {
            return "<div><i class='icon-tag'></i> " + item.text + "</div>";
          }
        },
        create: function(input) {
          return {
            value: input,
            text: input
          };
        }
      });
    };

    PostView.prototype.generateTagOptions = function(tags) {
      var opts, tag;
      opts = (function() {
        var _i, _len, _ref, _results;
        _ref = tags.pluck("tag");
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          tag = _ref[_i];
          if (tag !== "") {
            _results.push({
              value: tag,
              text: tag
            });
          }
        }
        return _results;
      })();
      return this.customTags(opts);
    };

    PostView.prototype.customTags = function(opts) {
      var tag, val, _i, _len, _ref;
      val = $("#js-tags").val();
      if (val !== "") {
        _ref = val.split(",");
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          tag = _ref[_i];
          if (tag !== "") {
            opts.push({
              value: tag,
              text: tag
            });
          }
        }
      }
      return opts;
    };

    PostView.prototype.toggleTags = function(e) {
      if (this.tagsShown) {
        this.$('.editor-toolbar a, .editor-toolbar i').show();
        this.$(".tags-bar").hide();
      } else {
        this.$('.editor-toolbar a, .editor-toolbar i').hide();
        this.$('.icon-tags').show();
        this.$(".tags-bar").show();
        this.$("js-tags").focus();
      }
      return this.tagsShown = !this.tagsShown;
    };

    PostView.prototype.setupCalendar = function() {
      return this.$(".icon-calendar").qtip({
        show: {
          event: "click"
        },
        content: {
          text: $("#date-form").html()
        },
        position: {
          at: "right center",
          my: "left center",
          viewport: $(window),
          effect: false
        },
        events: {
          render: function(event, api) {
            $(".js-date").each(function() {
              return $(this).val($("#publish_date").val());
            });
            return $(".js-setdate").click(function(e) {
              var pubDate;
              e.preventDefault();
              pubDate = $(e.currentTarget).parent().find('input').val();
              $("#publish_date").val(pubDate);
              return $('.icon-calendar').qtip("hide");
            });
          }
        },
        hide: "unfocus"
      });
    };

    PostView.prototype.setupFilm = function() {
      var _this = this;
      return this.$(".icon-film").qtip({
        show: {
          event: "click"
        },
        content: {
          text: $("#film-form").html()
        },
        position: {
          at: "right center",
          my: "left center",
          viewport: $(window),
          effect: false
        },
        events: {
          render: function(event, api) {
            return $(".js-submitfilm").click(function(e) {
              var filmInput, filmUrl;
              e.preventDefault();
              filmInput = $(e.currentTarget).parent().find('input');
              filmUrl = filmInput.val();
              _this.attachFilm(filmUrl);
              filmInput.val('');
              return $('.icon-film').qtip("hide");
            });
          }
        },
        hide: "unfocus"
      });
    };

    PostView.prototype.attachFilm = function(filmUrl) {
      if (filmUrl.match(/youtube.com/g)) {
        return this.bulidYoutubeIframe(filmUrl);
      } else if (filmUrl.match(/vimeo.com/g)) {
        return this.buildVimeoIframe(filmUrl);
      } else {

      }
    };

    PostView.prototype.bulidYoutubeIframe = function(filmUrl) {
      var filmIframe;
      filmUrl = filmUrl.replace(/https?:\/\//, '//');
      filmUrl = filmUrl.replace(/watch\?v=/, 'embed/');
      filmIframe = '<iframe width="560" height="315" src="' + filmUrl + '" frameborder="0" allowfullscreen></iframe>';
      return this.insert(filmIframe);
    };

    PostView.prototype.buildVimeoIframe = function(originalFilmUrl) {
      var filmIframe, filmUrl;
      filmUrl = originalFilmUrl.replace(/https?:\/\/vimeo.com\//, '//player.vimeo.com/video/');
      filmIframe = '<iframe src="' + filmUrl + '?title=0&amp;byline=0&amp;portrait=0" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
      return this.insert(filmIframe);
    };

    PostView.prototype.insert = function(string) {
      return this.editor.codemirror.replaceSelection(string);
    };

    PostView.prototype.save = function(e) {
      e.preventDefault();
      return this.processFormSubmit({
        title: this.$('#title').val(),
        slug: this.$('#slug').val(),
        active: this.$('input[type=radio]:checked').val(),
        content: this.editor.codemirror.getValue(),
        tags: this.$("#js-tags").val(),
        user_id: this.$("#js-user").val(),
        publish_date: this.$("#publish_date").val()
      });
    };

    PostView.prototype.processFormSubmit = function(data) {
      return this.model.save(data, {
        collection: this.collection
      });
    };

    PostView.prototype.collapse = function($toggle) {
      this.$toggle = $toggle;
      this.$toggle.data("dir", "up").addClass("icon-chevron-sign-right").removeClass("icon-chevron-sign-down");
      return this.$(".details.hide").hide();
    };

    PostView.prototype.expand = function($toggle) {
      this.$toggle = $toggle;
      this.$toggle.data("dir", "down").addClass("icon-chevron-sign-down").removeClass("icon-chevron-sign-right");
      return this.$(".details.hide").show();
    };

    PostView.prototype.toggleDetails = function(e) {
      this.$toggle = $(e.currentTarget);
      if (this.$toggle.data("dir") === "up") {
        return this.expand(this.$toggle);
      } else {
        return this.collapse(this.$toggle);
      }
    };

    PostView.prototype.changeBtn = function(e) {
      this.localStorage();
      if (e.currentTarget.value === "1") {
        return this.$(".publish").text(Lang.post_publish);
      } else {
        return this.$(".publish").text(Lang.post_save);
      }
    };

    PostView.prototype.imageUpload = function(editor) {
      var options;
      options = {
        uploadUrl: App.request("get:url:api") + "/dropzone/image",
        allowedTypes: ["image/jpeg", "image/png", "image/jpg", "image/gif"],
        progressText: "![Uploading file...]()",
        urlText: "![file]({filename})",
        errorText: "Error uploading file"
      };
      return inlineAttach.attachToCodeMirror(editor.codemirror, options);
    };

    return PostView;

  })(App.Views.ItemView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("PostApp.Edit", function(Edit, App, Backbone, Marionette, $, _) {
  return Edit.Controller = (function(_super) {

    __extends(Controller, _super);

    function Controller() {
      return Controller.__super__.constructor.apply(this, arguments);
    }

    Controller.prototype.initialize = function(options) {
      var id, post,
        _this = this;
      post = options.post, id = options.id;
      post || (post = App.request("post:entity", id));
      this.storage = new Storage({
        id: post.id
      });
      this.listenTo(post, "updated", function() {
        _this.storage.destroy();
        return App.vent.trigger("post:updated", post);
      });
      return App.execute("when:fetched", post, function() {
        var view;
        view = _this.getEditView(post);
        return _this.show(view);
      });
    };

    Controller.prototype.getEditView = function(post) {
      return new Edit.Post({
        model: post,
        storage: this.storage
      });
    };

    return Controller;

  })(App.Controllers.Base);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("PostApp.Edit", function(Edit, App, Backbone, Marionette, $, _) {
  return Edit.Post = (function(_super) {

    __extends(Post, _super);

    function Post() {
      return Post.__super__.constructor.apply(this, arguments);
    }

    Post.prototype.onRender = function() {
      this.fillJSON();
      this._setDate();
      this._setActive();
      return this._setTags();
    };

    Post.prototype._setDate = function() {
      var date, publishDate;
      publishDate = this.model.get("publish_date");
      if (_.isObject(publishDate)) {
        publishDate = publishDate.date;
      }
      date = moment.utc(publishDate, "YYYY-MM-DDTHH:mm:ss");
      this.$(".js-date").val(date.format("MMM Do YYYY, hh:mma"));
      return this.$("#publish_date").val(date.format("MMM Do, YYYY h:mm A"));
    };

    Post.prototype._setActive = function() {
      if (parseInt(this.model.get("active")) === 1) {
        this.$(".publish").text(Lang.post_publish);
        return this.$('input:radio[name="active"]').filter('[value="1"]').attr('checked', true);
      } else {
        this.$(".publish").text(Lang.post_save);
        return this.$('input:radio[name="active"]').filter('[value="0"]').attr('checked', true);
      }
    };

    Post.prototype._setTags = function() {
      var tags;
      tags = _.pluck(this.model.get("tags"), "tag");
      return this.$("#js-tags").val(tags);
    };

    return Post;

  })(App.Views.PostView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("PostApp.List", function(List, App, Backbone, Marionette, $, _) {
  return List.Controller = (function(_super) {

    __extends(Controller, _super);

    function Controller() {
      return Controller.__super__.constructor.apply(this, arguments);
    }

    Controller.prototype.initialize = function() {
      var post,
        _this = this;
      post = App.request("post:entities");
      return App.execute("when:fetched", post, function() {
        var view;
        view = _this.getListView(post);
        _this.show(view);
        return _this.listenTo(view, "childview:post:delete:clicked", function(child, args) {
          var model;
          model = args.model;
          if (confirm(Lang.post_delete_confirm.replace("##post##", _.escape(model.get("title"))))) {
            return model.destroy();
          } else {
            return false;
          }
        });
      });
    };

    Controller.prototype.getListView = function(post) {
      return new List.Posts({
        collection: post
      });
    };

    return Controller;

  })(App.Controllers.Base);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("PostApp.List", function(List, App, Backbone, Marionette, $, _) {
  List.PostItem = (function(_super) {

    __extends(PostItem, _super);

    function PostItem() {
      return PostItem.__super__.constructor.apply(this, arguments);
    }

    PostItem.prototype.template = "post/list/templates/item";

    PostItem.prototype.tagName = "tr";

    PostItem.prototype.attributes = function() {
      if (this.model.get("active") === "1") {
        return {
          "class": "post-item post-" + this.model.id
        };
      } else {
        return {
          "class": "post-item draft post-" + this.model.id
        };
      }
    };

    PostItem.prototype.triggers = {
      "click .delete": "post:delete:clicked"
    };

    PostItem.prototype.events = {
      "click .details": "edit",
      "click .preview": "preview"
    };

    PostItem.prototype.onShow = function() {
      var $avEl, allUsers, user;
      allUsers = App.request("get:all:users");
      $avEl = this.$(".avatar");
      if (allUsers.length === 1) {
        return $avEl.hide();
      } else {
        user = this.model.get("user");
        $avEl.avatar(user.email, $avEl.attr("width"));
        return this.$('.js-format-date').formatDates();
      }
    };

    PostItem.prototype.templateHelpers = {
      status: function() {
        if (parseInt(this.active) === 1 && this.publish_date > moment().format('YYYY-MM-DD HH:mm:ss')) {
          return Lang.post_scheduled;
        } else if (parseInt(this.active) === 1) {
          return Lang.post_active;
        } else {
          return Lang.post_draft;
        }
      }
    };

    PostItem.prototype.edit = function(e) {
      e.preventDefault();
      return App.vent.trigger("post:item:clicked", this.model);
    };

    PostItem.prototype.preview = function(e) {
      var storage;
      e.preventDefault();
      storage = new Storage({
        id: this.model.id
      });
      storage.put(this.model.toJSON());
      return window.open("" + (App.request("get:url:blog")) + "/post/preview/" + this.model.id, '_blank');
    };

    return PostItem;

  })(App.Views.ItemView);
  List.Empty = (function(_super) {

    __extends(Empty, _super);

    function Empty() {
      return Empty.__super__.constructor.apply(this, arguments);
    }

    Empty.prototype.template = "post/list/templates/empty";

    Empty.prototype.tagName = "tr";

    return Empty;

  })(App.Views.ItemView);
  return List.Posts = (function(_super) {

    __extends(Posts, _super);

    function Posts() {
      return Posts.__super__.constructor.apply(this, arguments);
    }

    Posts.prototype.template = "post/list/templates/grid";

    Posts.prototype.itemView = List.PostItem;

    Posts.prototype.emptyView = List.Empty;

    Posts.prototype.itemViewContainer = "tbody";

    Posts.prototype.className = "span12";

    Posts.prototype.events = {
      "keyup #js-filter": "filter",
      "change #js-sort": "sort"
    };

    Posts.prototype.hideAll = function() {
      return this.$el.find(".post-item").hide();
    };

    Posts.prototype.filter = function(e) {
      return this.handleFilter();
    };

    Posts.prototype.sort = function(e) {
      return this.handleFilter();
    };

    Posts.prototype.handleFilter = function() {
      var filter, sorter,
        _this = this;
      this.hideAll();
      sorter = this.$("#js-sort").val();
      filter = this.$("#js-filter").val();
      if (sorter === "" && filter === "") {
        return this.$el.find(".post-item").show();
      }
      return this.collection.filter(function(post) {
        return _this.isMatch(post, sorter, filter);
      });
    };

    Posts.prototype.isMatch = function(post, sorter, filter) {
      var foundId, pattern;
      foundId = sorter === "" || post.get("active") === sorter ? post.id : null;
      if (foundId && filter !== "") {
        pattern = new RegExp(filter, "gi");
        foundId = pattern.test(post.get("title"));
      }
      if (foundId) {
        return this.$el.find(".post-" + post.id).show();
      }
    };

    return Posts;

  })(App.Views.CompositeView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("PostApp.New", function(New, App, Backbone, Marionette, $, _) {
  return New.Controller = (function(_super) {

    __extends(Controller, _super);

    function Controller() {
      return Controller.__super__.constructor.apply(this, arguments);
    }

    Controller.prototype.initialize = function(options) {
      var post, view,
        _this = this;
      post = App.request("new:post:entity");
      this.storage = new Storage;
      this.listenTo(post, "created", function() {
        _this.storage.destroy();
        return App.vent.trigger("post:created", post);
      });
      view = this.getNewView(post);
      return this.show(view);
    };

    Controller.prototype.getNewView = function(post) {
      return new New.Post({
        model: post,
        storage: this.storage
      });
    };

    return Controller;

  })(App.Controllers.Base);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("PostApp.New", function(New, App, Backbone, Marionette, $, _) {
  return New.Post = (function(_super) {

    __extends(Post, _super);

    function Post() {
      return Post.__super__.constructor.apply(this, arguments);
    }

    Post.prototype.onRender = function() {
      this.fillJSON($.jStorage.get("post-new"));
      this.$(".publish").text(Lang.post_publish);
      return this.$("#date").attr("placeholder", moment().format("MMM Do, YYYY [9am]"));
    };

    Post.prototype.fillForm = function(contents) {
      this.$("#slug").val(contents.fields.slug);
      this.$("#title").val(contents.fields.title);
      this.editor.codemirror.setValue(contents.content);
      this.$("#publish_date").val(contents.fields.date);
      if (contents.fields.tags.length > 0) {
        return $("#js-tags").val(contents.fields.tags.join());
      }
    };

    return Post;

  })(App.Views.PostView);
});

var __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

this.Wardrobe.module("PostApp", function(PostApp, App, Backbone, Marionette, $, _) {
  var API;
  PostApp.Router = (function(_super) {

    __extends(Router, _super);

    function Router() {
      return Router.__super__.constructor.apply(this, arguments);
    }

    Router.prototype.appRoutes = {
      "": "add",
      "post": "list",
      "post/add": "add",
      "post/edit/:id": "edit"
    };

    return Router;

  })(Marionette.AppRouter);
  API = {
    list: function() {
      this.setActive();
      return new PostApp.List.Controller;
    },
    add: function() {
      this.setActive(".write");
      return new PostApp.New.Controller;
    },
    edit: function(id, item) {
      this.setActive();
      return new PostApp.Edit.Controller({
        id: id,
        post: item
      });
    },
    setActive: function(type) {
      if (type == null) {
        type = ".posts";
      }
      return $('ul.nav li').removeClass("active").find(type).parent().addClass("active");
    }
  };
  App.vent.on("post:load", function() {
    App.navigate("post");
    return API.list();
  });
  App.vent.on("post:created post:updated", function(item) {
    $("#js-alert").showAlert(Lang.post_success, Lang.post_saved, "alert-success");
    App.navigate("post/edit/" + item.id);
    return API.edit(item.id, item);
  });
  App.vent.on("post:new:clicked post:new", function() {
    App.navigate("/", {
      trigger: false
    });
    return API.add();
  });
  App.vent.on("post:item:clicked", function(item) {
    App.navigate("post/edit/" + item.id);
    return API.edit(item.id, item);
  });
  return App.addInitializer(function() {
    return new PostApp.Router({
      controller: API
    });
  });
});
