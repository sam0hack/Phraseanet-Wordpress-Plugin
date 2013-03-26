<?php
/**
 * Frontend Functions
 */


/**
 * Add Scripts and CSS in Frontend
 */
function wppsn_wp_enqueue_scripts() {

	// Custom CSS
	wp_enqueue_style( 'wppsn_frontend_css', WPPSN_PLUGIN_CSS_URL . 'wppsn-frontend.css', array(), '1.0.4', 'all' );

	// Flowplayer CSS
	wp_enqueue_style( 'wppsn_flowplayer_css', WPPSN_PLUGIN_FLOWPLAYER_URL . 'skin/minimalist.css', array(), '1.0.4', 'all' );

	// FlexSlider CSS
	wp_enqueue_style( 'wppsn_flexslider_css', WPPSN_PLUGIN_FLEXSLIDER_URL . 'flexslider.css', array(), '1.0.4', 'all' );

	// Swipebox CSS
	wp_enqueue_style( 'wppsn_swipebox_css', WPPSN_PLUGIN_SWIPEBOX_URL . 'swipebox.css', array(), '1.0.4', 'all' );

	// Flowplayer JS
	// Register only : it will be enqueue if 'wppsn-video' shortcode is found
	wp_register_script( 'wppsn_flowplayer_js', WPPSN_PLUGIN_FLOWPLAYER_URL . 'flowplayer.js', array( 'jquery' ), '1.0.4', true );

	// Flexslider JS
	// Register only : it will be enqueue if 'wppsn-img-gallery' shortcode is found with carrousel mode
	wp_register_script( 'wppsn_flexslider_js', WPPSN_PLUGIN_FLEXSLIDER_URL . 'jquery.flexslider-min.js', array( 'jquery' ), '1.0.4', true );

	// Swipebox JS
	// Register only : it will be enqueue if 'wppsn-image' or 'wppsn-img-gallery' (with list or grid display mode) shortcodes are found
	wp_register_script( 'wppsn_swipebox_js', WPPSN_PLUGIN_SWIPEBOX_URL . 'jquery.swipebox.min.js', array( 'jquery' ), '1.0.4', true );

	// Custom JS for Flexslider
	// Register only : it will be enqueue if 'wppsn-img-gallery' shortcode is found with carrousel mode
	wp_register_script( 'wppsn_frontend_carrousel_js', WPPSN_PLUGIN_JS_URL . 'wppsn-frontend-carrousel.js', array( 'wppsn_flexslider_js' ), '1.0.4', true );

	// Custom JS for Flowplayer
	// Register only : it will be enqueue if 'wppsn-video-playlist' shortcode is found
	wp_register_script( 'wppsn_frontend_video_playlist_js', WPPSN_PLUGIN_JS_URL . 'wppsn-frontend-video-playlist.js', array( 'wppsn_flowplayer_js' ), '1.0.4', true );

	// Custom JS for Swipebox
	// Register only : it will be enqueue if 'wppsn-image' or 'wppsn-img-gallery' (with list or grid display mode) shortcodes are found
	wp_register_script( 'wppsn_frontend_swipebox_js', WPPSN_PLUGIN_JS_URL . 'wppsn-frontend-swipebox.js', array( 'wppsn_swipebox_js' ), '1.0.4', true );

}

add_action( 'wp_enqueue_scripts', 'wppsn_wp_enqueue_scripts' );


/**
 * Define the shortcode 'wppsn-image'
 * @param  array $atts Array of attributes
 * @return html        HTML replacing the shortcode
 */
function wppsn_shortcode_single_image( $atts ) {
	global $post;

	$output = '';

	// Add Swipebox JS to the footer
	wp_enqueue_script( 'wppsn_frontend_swipebox_js' );

	extract( shortcode_atts( array(
	    'title'				=> '',
	    'alt'				=> '',
	    'legend'			=> '',
	    'download'			=> 0,
	    'url'				=> ''
	  ), $atts ) );

	$title = trim( $title );
	$alt = trim( $alt );
	$legend = trim( $legend );
	$url = trim( $url );
	$downloadLink = '';

	// Download link ?
	if ( $download == 1 ) {
		$downloadLink = '<a href="' . $url . ( ( false === strpos( '?', $url ) ) ? '?download' : '&download' ) . '">' . __( 'Download', 'wp-phraseanet' ) . '</a>';
	}

	// Encapsulate the image in a div for responsive design
	$output .= '<div class="wppsn-image wp-caption">
					<a href="' . $url . '" title="' . $title . '" class="wppsn-image-post-' . $post->ID . '">
						<img class="wp-image-" src="' . $url . '" alt="' . $alt . '">
					</a>';

	if ( $legend != '' || $downloadLink != '' ) {
		$output .= '	<p class="wp-caption-text">' . $legend . ( ( $legend != '' ) ? ' - ' : '' ) . $downloadLink . '</p>';
	}

	$output .= '</div>';

	return $output;

}

add_shortcode( 'wppsn-image', 'wppsn_shortcode_single_image' );


/**
 * Define the shortcode 'wppsn-image'
 * @param  array $atts Array of attributes
 * @return html        HTML replacing the shortcode
 */
function wppsn_shortcode_single_video( $atts ) {

	$output = '';
	$uniqueString = md5( microtime( true ) );
	$playerID = 'wppsn-video-player-' . $uniqueString;
	usleep(1);

	// Add FLowplayer JS to the footer
	wp_enqueue_script( 'wppsn_flowplayer_js' );

	// Get Attributes
	extract( shortcode_atts( array(
	    'title'				=> '',
	    'mp4'				=> '',
	    'webm'				=> '',
	    'ogg'				=> ''
	  ), $atts ) );

	$title = trim( $title );
	$mp4 = trim( $mp4 );
	$webm = trim( $webm );
	$ogg = trim( $ogg );

	$output .= '<div class="wppsn-video-player-wrapper">
					<div data-swf="' . WPPSN_PLUGIN_FLOWPLAYER_URL . 'flowplayer.swf' . '" class="wppsn-video-player flowplayer color-light">
						<video>
							<source type="video/mp4" src="' . $mp4 . '">';

	if ( $webm != '' ) {
		$output .= '<source type="video/webm" src="' . $webm . '">';
	}

	if ( $ogg != '' ) {
		$output .= '<source type="video/ogg" src="' . $ogg . '">';
	}

	$output .= '		</video>
					</div>
					<p class="wppsn-video-player-title">' . $title . '</p>
				</div>';

	return $output;

}

add_shortcode( 'wppsn-video', 'wppsn_shortcode_single_video' );


/**
 * Define the shortcode 'wppsn-img-gallery'
 * @param  array $atts Array of attributes
 * @return html        HTML replacing the shortcode
 */
function wppsn_shortcode_image_gallery( $atts ) {
	global $post;

	$output = '';

	extract( shortcode_atts( array(
		'display'			=> 'list',
	    'titles'			=> '',
	    'alts'				=> '',
	    'legends'			=> '',
	    'thumbs'			=> '',
	    'urls'				=> ''
	  ), $atts ) );

	// Explode Strings to Arrays
	$allTitles = explode( '||', $titles );
	$allAlts = explode( '||', $alts );
	$allLegends = explode( '||', $legends );
	$allThumbs = explode( '||', $thumbs );
	$allUrls = explode( '||', $urls );

	switch( $display ) {

		case 'grid':

			// Add Swipebox JS to the footer
			wp_enqueue_script( 'wppsn_frontend_swipebox_js' );

			$cpt = 1;
			$galleryID = 'gallery-' . md5( microtime( true ) );
			usleep(1);

			$output .= '<style type="text/css">
							#' . $galleryID . ' {
								margin: auto;
							}
							#' . $galleryID . ' .gallery-item {
								float: left;
								margin-top: 10px;
								text-align: center;
								width: 33%;
							}
							#' . $galleryID . ' img {
								border: 2px solid #cfcfcf;
							}
							#' . $galleryID . ' .gallery-caption {
								margin-left: 0;
							}
						</style>';

			$output .= '<div id="' . $galleryID . '" class="wppsn-gallery gallery">';

			foreach( $allTitles as $i => $t ) {

				$title = trim( $t );
				$alt = trim( $allAlts[$i] );
				$legend = trim( $allLegends[$i] );
				$thumb = trim( $allThumbs[$i] );
				$url = trim( $allUrls[$i] );

				$output .= '<dl class="gallery-item">
								<dt class="gallery-icon">
									<a href="' . $url . '" title="' . $title . '" class="wppsn-image-post-' . $post->ID . '">
										<img src="' . $thumb . '" class="attachment-thumbnail" alt="' . $alt . '">
									</a>
								</dt>';

				// Legend ?
				if ( $legend != '' ) {
					$output .= '<dd class="wp-caption-text gallery-caption">' . $legend . '</dd>';
				}
								
				$output .= '</dl>';

				// 3 columns break
				if ( $cpt % 3 == 0 ) {
					$output .= '<br style="clear:both;">';
				}

				$cpt++;

			}

			// To be sure :)
			$output .= '<br style="clear:both;">';

			$output .= '</div>';

			break;

		case 'carrousel':

			$uniqueString = md5( microtime( true ) );

			$sliderID = 'wppsn-slider-' . $uniqueString;
			$carrouselID = 'wppsn-carrousel-' . $uniqueString;
			usleep(1);

			// Add Flexslider JS to the Footer
			wp_enqueue_script( 'wppsn_frontend_carrousel_js' );

			// Create the slider First
			
			$output .= '<div id="' . $sliderID . '" class="wppsn-slider flexslider">
		  					<ul class="slides">';

		  	foreach( $allTitles as $i => $t ) {

		  		$title = trim( $t );
				$alt = trim( $allAlts[$i] );
				$legend = trim( $allLegends[$i] );
				$thumb = trim( $allThumbs[$i] );
				$url = trim( $allUrls[$i] );

				$output .= '<li>
								<img src="' . $url . '" title="' . $title . '" alt="' . $alt . '">';

				// Legend ?
				if ( $legend != '' ) {
					$output	.= '<p class="flex-caption">' . $legend . '</p>';
				}

				$output .= '</li>';

		  	}

		  	$output .= '	</ul>
		  				</div>';
			

			// Create the carrousel Secondly

			$output .= '<div id="' . $carrouselID . '" class="wppsn-carrousel flexslider">
		  					<ul class="slides">';

		  	foreach( $allTitles as $i => $t ) {

		  		$title = trim( $t );
				$alt = trim( $allAlts[$i] );
				$legend = trim( $allLegends[$i] );
				$thumb = trim( $allThumbs[$i] );
				$url = trim( $allUrls[$i] );

				$output .= '<li>
								<img src="' . $url . '" title="' . $title . '" alt="' . $alt . '">
							</li>';

		  	}

		  	$output .= '	</ul>
		  				</div>';

			break;

		default: // List

			// Add Swipebox JS to the footer
			wp_enqueue_script( 'wppsn_frontend_swipebox_js' );

			foreach( $allTitles as $i => $t ) {

				$title = trim( $t );
				$alt = trim( $allAlts[$i] );
				$legend = trim( $allLegends[$i] );
				$url = trim( $allUrls[$i] );

				$output .= '<div class="wppsn-image wp-caption">
								<a href="' . $url . '" title="' . $title . '" class="wppsn-image-post-' . $post->ID . '">
									<img class="wp-image-" src="' . $url . '" alt="' . $alt . '">
								</a>';

				if ( $legend != '' ) {
					$output .= '	<p class="wp-caption-text">' . $legend . '</p>';
				}

				$output .= '</div>';

			}

			break;

	}

	return $output;

}

add_shortcode( 'wppsn-img-gallery', 'wppsn_shortcode_image_gallery' );


/**
 * Define the shortcode 'wppsn-video-playlist'
 * @param  array $atts Array of attributes
 * @return html        HTML replacing the shortcode
 */
function wppsn_shortcode_video_playlist( $atts ) {

	$output = '';
	$uniqueString = md5( microtime( true ) );
	$playerID = 'wppsn-video-playlist-player-' . $uniqueString;
	usleep(1);

	// Add Flowplayer JS to the footer
	wp_enqueue_script( 'wppsn_frontend_video_playlist_js' );

	// Get Attributes
	extract( shortcode_atts( array(
	    'titles'			=> '',
	    'mp4s'				=> ''
	  ), $atts ) );

	// Explode Strings to Arrays
	$allTitles = explode( '||', $titles );
	$allMp4s = explode( '||', $mp4s );

	$output .= '<div class="wppsn-video-playlist-player-wrapper">
					<div data-swf="' . WPPSN_PLUGIN_FLOWPLAYER_URL . 'flowplayer.swf' . '" class="wppsn-video-playlist-player flowplayer color-light">
						<video>
							<source type="video/mp4" src="' . trim( $allMp4s[0] ) . '">
						</video>
						<div class="fp-playlist">';

							foreach( $allTitles as $i => $t ) {
								$output .= '<a href="' . trim( $allMp4s[$i] ) . '">' . trim( $t ) . '</a>';
							}

	$output .= '		</div>
					</div>
				</div>';

	return $output;

}

add_shortcode( 'wppsn-video-playlist', 'wppsn_shortcode_video_playlist' );