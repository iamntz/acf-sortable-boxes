jQuery(document).ready(function($){

});



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

  sortableWrapper.on('click', '.js-uploader', function(){
    var id = $(this).closest('.thumbnail').attr('data-id');

    acf.fields.gallery.set({ $el : $(this).closest('.js-ntzSortableLayout') }).popup();


    $(this).blur();
    return false;
  });


  sortableWrapper.on('change', '.js-sortableLayout__changeSize', function(){
    var parent = $(this).closest('.js-sortableLayout__item');
    if( this.value == 'x' ){
      if( confirm( "Are you sure you want to delete this tile?" ) ){
        parent.fadeOut(function(){
          $(this).remove();
        });
      }
    }else {
      parent.attr('data-colspan', this.value);
    }
  });

  $('.js-sortableLayout__addItem').on('click', function(){
    var itemTpl = $('#ntz-sortableLayoutItem').html();

    $(itemTpl).appendTo( sortableWrapper );
    return false;
  });
});