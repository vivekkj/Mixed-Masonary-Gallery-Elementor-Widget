( function( $ ) {
	'use strict';

	var currentIndex = 0;
	var currentItems = [];
	var touchStartX = 0;
	var touchEndX = 0;

	function getYouTubeId( url ) {
		var match = String( url ).match( /(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([^&?/]+)/ );
		return match ? match[1] : null;
	}

	function getVimeoId( url ) {
		var match = String( url ).match( /vimeo\.com\/(?:video\/)?([0-9]+)/ );
		return match ? match[1] : null;
	}

	function isVideoFile( url ) {
		return /\.(mp4|webm|ogg)(\?.*)?$/i.test( String( url ) );
	}

	function escapeAttr( value ) {
		return String( value || '' )
			.replace( /&/g, '&amp;' )
			.replace( /"/g, '&quot;' )
			.replace( /'/g, '&#039;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' );
	}

	function buildVideoHtml( url ) {
		var youtubeId = getYouTubeId( url );
		var vimeoId = getVimeoId( url );

		if ( youtubeId ) {
			return '<div class="emg-lightbox-video"><iframe src="https://www.youtube.com/embed/' + youtubeId + '?autoplay=1&rel=0" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe></div>';
		}

		if ( vimeoId ) {
			return '<div class="emg-lightbox-video"><iframe src="https://player.vimeo.com/video/' + vimeoId + '?autoplay=1" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div>';
		}

		if ( isVideoFile( url ) ) {
			return '<div class="emg-lightbox-video"><video src="' + escapeAttr( url ) + '" controls autoplay playsinline></video></div>';
		}

		return '<div class="emg-lightbox-video"><iframe src="' + escapeAttr( url ) + '" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe></div>';
	}

	function ensureLightbox() {
		if ( $( '.emg-lightbox' ).length ) {
			return;
		}

		$( 'body' ).append(
			'<div class="emg-lightbox" role="dialog" aria-modal="true">' +
				'<button type="button" class="emg-lightbox-close" aria-label="Close">&times;</button>' +
				'<button type="button" class="emg-lightbox-prev" aria-label="Previous">&#8249;</button>' +
				'<button type="button" class="emg-lightbox-next" aria-label="Next">&#8250;</button>' +
				'<div class="emg-lightbox-counter"></div>' +
				'<div class="emg-lightbox-inner">' +
					'<div class="emg-lightbox-content"></div>' +
					'<div class="emg-lightbox-caption"></div>' +
				'</div>' +
			'</div>'
		);
	}

	function collectGalleryItems( galleryId ) {
		var items = [];

		$( '.js-emg-lightbox[data-emg-gallery="' + galleryId + '"]' ).each( function() {
			var $item = $( this );

			items.push( {
				type: $item.attr( 'data-emg-type' ) || 'image',
				src: $item.attr( 'data-emg-src' ) || $item.attr( 'href' ),
				title: $item.attr( 'data-emg-title' ) || ''
			} );
		} );

		return items;
	}

	function renderCurrentItem() {
		if ( ! currentItems.length || ! currentItems[ currentIndex ] ) {
			return;
		}

		var item = currentItems[ currentIndex ];
		var html = item.type === 'video'
			? buildVideoHtml( item.src )
			: '<img src="' + escapeAttr( item.src ) + '" alt="' + escapeAttr( item.title ) + '">';

		$( '.emg-lightbox-content' ).html( html );
		$( '.emg-lightbox-caption' ).text( item.title || '' );
		$( '.emg-lightbox-counter' ).text( ( currentIndex + 1 ) + ' / ' + currentItems.length );

		if ( currentItems.length <= 1 ) {
			$( '.emg-lightbox-prev, .emg-lightbox-next' ).addClass( 'is-disabled' );
		} else {
			$( '.emg-lightbox-prev, .emg-lightbox-next' ).removeClass( 'is-disabled' );
		}
	}

	function openLightbox( galleryId, index ) {
		ensureLightbox();

		currentItems = collectGalleryItems( galleryId );
		currentIndex = parseInt( index, 10 ) || 0;

		if ( currentIndex < 0 ) {
			currentIndex = 0;
		}

		if ( currentIndex >= currentItems.length ) {
			currentIndex = currentItems.length - 1;
		}

		renderCurrentItem();

		$( '.emg-lightbox' ).addClass( 'is-active' );
		$( 'body' ).addClass( 'emg-lightbox-open' );
	}

	function closeLightbox() {
		$( '.emg-lightbox' ).removeClass( 'is-active' );
		$( '.emg-lightbox-content' ).empty();
		$( '.emg-lightbox-caption' ).empty();
		$( '.emg-lightbox-counter' ).empty();
		$( 'body' ).removeClass( 'emg-lightbox-open' );

		currentIndex = 0;
		currentItems = [];
	}

	function showPrevious() {
		if ( currentItems.length <= 1 ) {
			return;
		}

		currentIndex = ( currentIndex - 1 + currentItems.length ) % currentItems.length;
		renderCurrentItem();
	}

	function showNext() {
		if ( currentItems.length <= 1 ) {
			return;
		}

		currentIndex = ( currentIndex + 1 ) % currentItems.length;
		renderCurrentItem();
	}

	$( document ).on( 'click', '.js-emg-lightbox', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var $link = $( this );

		openLightbox(
			$link.attr( 'data-emg-gallery' ),
			$link.attr( 'data-emg-index' )
		);

		return false;
	} );

	$( document ).on( 'click', '.emg-lightbox-close', closeLightbox );

	$( document ).on( 'click', '.emg-lightbox-prev', function( e ) {
		e.preventDefault();
		e.stopPropagation();
		showPrevious();
	} );

	$( document ).on( 'click', '.emg-lightbox-next', function( e ) {
		e.preventDefault();
		e.stopPropagation();
		showNext();
	} );

	$( document ).on( 'click', '.emg-lightbox', function( e ) {
		if ( e.target === this ) {
			closeLightbox();
		}
	} );

	$( document ).on( 'keydown', function( e ) {
		if ( ! $( '.emg-lightbox' ).hasClass( 'is-active' ) ) {
			return;
		}

		if ( e.key === 'Escape' ) {
			closeLightbox();
		} else if ( e.key === 'ArrowLeft' ) {
			showPrevious();
		} else if ( e.key === 'ArrowRight' ) {
			showNext();
		}
	} );

	$( document ).on( 'touchstart', '.emg-lightbox', function( e ) {
		touchStartX = e.originalEvent.changedTouches[0].screenX;
	} );

	$( document ).on( 'touchend', '.emg-lightbox', function( e ) {
		touchEndX = e.originalEvent.changedTouches[0].screenX;

		if ( Math.abs( touchEndX - touchStartX ) < 50 ) {
			return;
		}

		if ( touchEndX < touchStartX ) {
			showNext();
		} else {
			showPrevious();
		}
	} );

} )( jQuery );
