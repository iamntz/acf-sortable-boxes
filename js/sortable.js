

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
    var $this = $(this);
    var parent = $this.closest('.js-sortableLayout__item');
    var target = $this.next();
    var media = $this.data('ntzwpmedia') || new NtzWPMedia({
      trigger : $this,
      target : target,
      onSelect : function(e){
        target.val( e.id );

        $('.js-image-size-1 img', parent).attr( 'src', e.sizes['column-layout-1'].url );
        $('.js-image-size-2 img', parent).attr( 'src', e.sizes['column-layout-2'].url );
        $('.js-image-size-3 img', parent).attr( 'src', e.sizes['column-layout-3'].url );
        $('.js-image-size-4 img', parent).attr( 'src', e.sizes['column-layout-4'].url );
      }
    });

    $this.data( 'ntzwpmedia', media );
    media.open();
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
    var itemTpl = $(this).parent().next('.ntz-sortableLayoutItem').html();

    $(itemTpl).appendTo( sortableWrapper );
    return false;
  });
});