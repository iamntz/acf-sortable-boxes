

jQuery(document).ready(function($){

  function sortableInit( sortable ) {
    if( sortable.is( ':ui-sortable') ){ return; }
    sortable.sortable({
      forceHelperSize     : true,
      forcePlaceholderSize: true,
      placeholder         : "sortableLayout__item-placeholder",
      helper              : "clone",
      revert : 200
    });
  }//sortableInit

  sortableInit( $('.js-ntzSortableLayout') );

  $(document).on('click', '.js-sortableLayout__flipper', function(){
    $(this).closest('.js-sortableLayout__item').toggleClass('flipped');
    return false;
  });


  $(document).on('click', '.js-ntzSortableLayout .js-uploader', function(){
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


  $(document).on('change', '.js-sortableLayout__changeSize', function(){
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


  $(document).on('click', '.js-sortableLayout__addItem', function(){
    var wrapper = $(this).closest('.js-sortableLayoutWrapper');
    var itemTpl = $('.ntz-sortableLayoutItem', wrapper).html();

    var sortable = $('.js-ntzSortableLayout', wrapper);
    $(itemTpl).appendTo( sortable );
    sortableInit( sortable );
    return false;
  });
});