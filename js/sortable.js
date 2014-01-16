jQuery(document).ready(function($){
  var sortableWrapper = $('.js-ntzSortableLayout');
  sortableWrapper.sortable({
    forceHelperSize     : true,
    forcePlaceholderSize: true,
    placeholder         : "sortableLayout__item-placeholder",
    helper              : "clone",
    revert : 200
  });

  sortableWrapper.on('click', '.js-sortableLayout__flipper', function(){
    $(this).closest('.js-sortableLayout__item').toggleClass('flipped');
    return false;
  });

});