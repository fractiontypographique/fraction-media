/* Fraction Média — menu mobile. */
( function () {
	var bouton = document.querySelector( '.nav-toggle' );
	var nav = document.querySelector( '.nav' );
	if ( ! bouton || ! nav ) {
		return;
	}
	bouton.addEventListener( 'click', function () {
		var ouvert = nav.classList.toggle( 'open' );
		bouton.setAttribute( 'aria-expanded', ouvert ? 'true' : 'false' );
	} );
}() );
