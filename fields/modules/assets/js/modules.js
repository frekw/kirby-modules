(function($) {
  idFromHref = function(href) {
    return '#' + href.replace(/(.*)#/, '');
  };

  var targetForHref = function(href){
    return $(idFromHref(href));
  };

  var targetForLink = function(el) {
    return targetForHref($(el).attr('href'));
  };

  var Tabs = function(el, options) {
    this.options = $.extend({}, options)

    this.$tabs = $(el);

    this.toggle = this.toggle.bind(this);

    this.$tabs.on('click', 'a', function(e){
      e.preventDefault();
      this.toggle(e.target);

      if(this.options.onChange) this.options.onChange(e);
    }.bind(this));

    if(this.$tabs.find('a[data-active="data-active"]').length > 0){
      this.$tabs.find('a').each(function(){
        targetForLink(this).hide();
      });

      this.activate(this.$tabs.find('a[data-active="data-active"]'));
    } else {
      this.toggle(this.$tabs.find('a').first());
    }
  }

  Tabs.prototype.activate = function($target){
    targetForLink($target).show();
    this.$active = $target.attr('data-active', 'data-active');
  };

  Tabs.prototype.toggle = function(target) {
    var $target = $(target);

    if(this.$active) this.$active.attr('data-active', null);

    this.$tabs.find('a').each(function(){
      targetForLink(this).hide();
    })

    this.activate($target);
  };

  $.fn.tabs = function(options){
    return this.each(function(){
      new Tabs(this, options);
    });
  };
})(jQuery);

(function($) {
  var defaults = {
    toggle: '.accordion-toggle',
    content: '.accordion-content'
  };

  var Accordion = function(el, options){
    this.$el = $(el);
    this.options = options;

    this.toggle = this.toggle.bind(this);

    this.$el.on('click', options.toggle, function(e){
      e.preventDefault();
      e.stopPropagation();

      this.toggle($(e.target));

      if(this.options.onChange) {
        this.options.onChange(e);
      }
    }.bind(this));

    this.$el.find(options.toggle).each(function(i, e){
      var $el = $(e);

      if($el.is('.accordion--closed')){
        this.close($el);
      } else {
        this.open($el);
      }
    }.bind(this));
  };

  Accordion.prototype.toggle = function($target){
    if($target.is('.accordion--closed')){
      this.open($target);
    } else {
      this.close($target);
    }
  };

  Accordion.prototype.open = function($target){
    $target.removeClass('accordion--closed').addClass('accordion--open');
    $target.next(this.options.content).show();
  };

  Accordion.prototype.close = function($target){
    $target.removeClass('accordion--open').addClass('accordion--closed');
    $target.next(this.options.content).hide();
  };

  $.fn.accordion = function(options){
    options = options || {};
    options = $.extend({}, defaults, options);

    return this.each(function(){
      new Accordion(this, options);
    });
  };
})(jQuery);

(function($) {

  var last = function(arr){
    if(arr.length < 1) return null;

    return arr[arr.length - 1];
  }

  var first = function(arr) {
    if(!arr.length) return null;

    return arr[0]
  }

  // var stateFromEl()

  var Modules = function(el) {
    var element  = $(el);
    var api      = element.data('api');
    var sortable = element.data('sortable');

    element.find('.tabs').tabs({
      onChange: function(e){
        var $el = $(e.target);
        var parts = $el.attr('href').split('-');
        var id = last(parts);
        var tab = first(parts.splice(-2, 1));

        $('input[name$="[' + id + '][_editor_state][active_tab]"]').val(tab);
        $el.closest('form').trigger('keep');
      }
    });

    element.accordion({
      toggle: '> .modules-entries > .modules-entry > .accordion-toggle',
      onChange: function(e){
        var $el = $(e.target);
        var parts = $el.closest('.modules-entry').attr('id').split('-');
        var id = last(parts);
        var collapsed = $el.is('.accordion--closed');

        $('input[name$="[' + id + '][_editor_state][collapsed]"]').val(collapsed);
        $el.closest('form').trigger('keep');
      }
    });

    if(sortable === false) return false;

    // This is really ugly. Oh well.
    var cb = function(){ $(this).closest('form').trigger('keep'); };
    element.find('> .modules-entries > .modules-actions > .modules-add-button').on('click', cb);
    element.find('> .modules-entries > .modules-empty > p > .modules-empty-add-button').on('click', cb);
    element.find('> .modules-entries > .modules-entry .modules-entry-delete').on('click', cb);


    element.sortable({
      items: '.modules-entry',
      axis: 'y',
      delay: 150,
      scroll: true,
      tolerance: 'pointer',

      update: function() {
        var ids = [];

        $.each($(this).sortable('toArray'), function(i, id) {
          ids.push(id.replace('modules-entry-', ''));
        });

        $.post(api, {ids: ids}, function() {
          // app.content.reload();
        });
      }
    });
  };

  $.fn.modules = function() {

    return this.each(function() {

      if($(this).data('modules')) {
        return $(this);
      } else {
        var modules = new Modules(this);
        $(this).data('modules', modules);
        return $(this);
      }

    });
  };
})(jQuery);
