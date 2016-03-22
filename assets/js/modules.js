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

  var Tabs = function(el) {
    this.$tabs = $(el);

    this.toggle = this.toggle.bind(this);

    this.$tabs.on('click', 'a', function(e){
      e.preventDefault();
      this.toggle(e.target);
    }.bind(this));

    this.toggle(this.$tabs.find('a').first());
  }

  Tabs.prototype.toggle = function(target) {
    var $target = $(target);

    if(this.$active) this.$active.attr('data-active', null);

    this.$tabs.find('a').each(function(){
      targetForLink(this).hide();
    })

    targetForLink($target).show();
    this.$active = $target.attr('data-active', 'data-active');
  };

  $.fn.tabs = function(){
    return this.each(function(){
      new Tabs(this);
    });
  };
})(jQuery);

(function($) {

  var Modules = function(el) {
    var element  = $(el);
    var api      = element.data('api');
    var sortable = element.data('sortable');

    element.find('.tabs').tabs();

    if(sortable === false) return false;


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
