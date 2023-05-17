(function( $ ) {
  var timeout;

  $( 'body' ).on( 'click', '.type-strings  .controls .add', function() {
    var $input = $( '.type-strings .strings input' ).last().clone();

    $input.val( '' );
    $input.insertBefore( '.type-strings .strings label' );
    // $( '.type-strings .strings' ).append( $input );
  });

  /**
   * show the remove button, and assign its index to the label
   */
  $( 'body' ).on( 'mouseenter', '.type-strings .strings input, .type-strings .strings label', function() {
    if( ! $( this ).hasClass( 'button' ) ) {
      $( '.type-strings .strings label' )
        .css( { top: $( this ).position().top + 4 } )
        .data( 'index', $( this ).index() );
    }

    $( '.type-strings .strings label' ).addClass( 'visible' );

    clearTimeout( timeout );
  }).mouseleave(function() {
    timeout = setTimeout( function() {
      $( '.type-strings .strings label' ).removeClass( 'visible' );
    }, 600);
  });

  //Remove the row
  $( '.type-strings .strings label' ).click( function() {
    var index = $( this ).data( 'index' );
    var input = $( '.type-strings .strings input' ).get( index );

    $( input ).remove();
  } );

  //Update the preview
  $( '#nine3digital-preview input[name="update"]' ).click( function() {
    updatePreview();
  });

  $( '#nine3digital-typed input' ).change( function() {
    updatePreview();
  });

  function updatePreview() {
    $("#preview .text").remove();

    var $span = $( '<span>' ).addClass( 'text' );
    $( '#preview' ).empty().append( $span );

    //Get the strings
    var $inputs = $( '.type-strings .strings input' );
    var strings = [];

    $inputs.each( function() {
      const string = $(this).val().replace('&amp;', '&').replace('&', '&amp;');

      strings.push( string );
    });

    //Set the parameters only if is not empty
    var optionsKeys = [ 'typeSpeed', 'startDelay', 'backSpeed', 'backDelay', 'loopCount', 'shuffle' ];
    var options = {
      strings: strings
    };

    optionsKeys.forEach( function( key ) {
      var val = $( 'input[name="' + key + '"]' ).val();

      if( val != "" ) {
        options[ key ] = parseInt( val );
      }
    } );

    if( $( 'input[name="loop"]' ).get()[0].checked ) {
      options[ 'loop' ] = 1;
    }

    if( $( 'input[name="shuffle"]' ).get()[0].checked ) {
      options[ 'shuffle' ] = 1;
    }

    $("#preview .text").typed( options );

    //The shortcode
    var shortcode = "[typed";
    var i = 0;
    for( var key in options ) {
      var value = options[key];
      if( typeof( value ) == "object" ) {
        var values = options[key];

        values.forEach( function( v ) {
          // Replace & with &amp; otherwise the browser freezes.
          var v = v.replace('&amp;', '&'); // in case the user already used &amp;
          v = v.replace('&', '&amp;');
          v = v.replace(/"/g, '&quot;');
          shortcode += " string" + i + "=\"" + v + "\"";

          i++;
        } );
      } else {
        shortcode += " " + key + '="' + value + '"';
      }
    }

    //Encode the html tags
    $( 'div#typed' ).html( $('<div/>').text(shortcode).html() + ']' );
    $( 'div#php' ).html( '&lt;?php echo do_shortcode( \'' + $('<div/>').text(shortcode).html() + ']\' ) ?&gt;' );
  }

  $( document ).ready( function() {
    $( '#nine3digital-preview input[name="update"]' ).trigger( 'click' );    
  });
})(jQuery);
