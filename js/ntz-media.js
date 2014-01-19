var NtzWPMedia;
(function( $, document ){
  NtzWPMedia = function( opts ) {
    this.opts = opts || {
      trigger : null,
      target  : null,
      onSelect: $.noop
    };
  };


  NtzWPMedia.prototype.open = function() {
    var $this = this;
    if (this._frame) {
      this._frame.open();
      return;
    }

    this._frame = wp.media({
      multiple: false,
      library: {
        type: 'image'
      }
    });

    var frame = this._frame;

    frame.on('ready', function() {
      $('.media-modal').addClass('no-sidebar');
    });


    frame.on('close',function(){
      $('.media-modal').removeClass('no-sidebar');
    });


    frame.on('open',function() {
      var id = $this.opts.target.val();
      var selection = frame.state().get('selection');
      var attachment  = wp.media.attachment( id );

      attachment.fetch();
      selection.add( attachment );
    });


    frame.state('library').on('select', function() {
      var attachment = this.get('selection').first();
      $this.handleMediaAttachment(attachment);
    });

    frame.open();
  };


  NtzWPMedia.prototype.handleMediaAttachment = function( attachment ) {
    this.opts.onSelect.call( this, attachment.toJSON() );
  };

})( jQuery, document );
