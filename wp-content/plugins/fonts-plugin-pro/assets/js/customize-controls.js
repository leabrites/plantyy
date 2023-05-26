(function($){
  wp.customize( 'fpp_host_locally', function( fpp_host_locally ) {
      fpp_host_locally.bind( function( value ) {
          if( true == value ){
              $('#customize-control-fpp_use_woff2').show();
          } else {
              $('#customize-control-fpp_use_woff2').hide();
          }
      } );
  } );
  wp.customize( 'fpp_removal', function( fpp_removal ) {
      fpp_removal.bind( function( value ) {
          if( true == value ){
              $('#customize-control-fpp_rewrite').hide();
          } else {
              $('#customize-control-fpp_rewrite').show();
          }
      } );
  } );
})(jQuery);
