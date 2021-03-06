<?php

/**
 * Frontend Functions
 */

/**
 * Add Scripts and CSS in Frontend
 */
function wppsn_wp_enqueue_scripts()
{

    /* CSS */

    // Custom CSS
    wp_enqueue_style('wppsn_frontend_css', WPPSN_PLUGIN_CSS_URL . 'wppsn-frontend.css', array() , '2.0.3', 'all');

    // FlexSlider CSS
    wp_enqueue_style('wppsn_flexslider_css', WPPSN_PLUGIN_FLEXSLIDER_URL . 'flexslider.css', array() , '2.0.1', 'all');

    // Swipebox CSS
    wp_enqueue_style('wppsn_swipebox_css', WPPSN_PLUGIN_SWIPEBOX_URL . 'swipebox.css', array() , '2.0.1', 'all');

    /* JS */

    /**
     * Load FlowPlayer in head
     * It appears that loading it in footer causes problems
     */
    wp_enqueue_script('jquery');

        wp_enqueue_script('wppsn_frontend_video_playlist', WPPSN_PLUGIN_JS_URL . 'wppsn-frontend-video-playlist.js', array(
        'jquery'
    ) , '1.0.8', false);
    
    // Flexslider JS
    // Register only : it will be enqueue if 'wppsn-img-gallery' shortcode is found with carrousel mode
    wp_register_script('wppsn_flexslider_js', WPPSN_PLUGIN_FLEXSLIDER_URL . 'jquery.flexslider-min.js', array(
        'jquery'
    ) , '1.0.7', true);

    // Swipebox JS
    // Register only : it will be enqueue if 'wppsn-image' or 'wppsn-img-gallery' (with list or grid display mode) shortcodes are found
    wp_register_script('wppsn_swipebox_js', WPPSN_PLUGIN_SWIPEBOX_URL . 'jquery.swipebox.min.js', array(
        'jquery'
    ) , '1.0.7', true);
    wp_register_script('wppsn_swipebox_ios_orientation_fix_js', WPPSN_PLUGIN_SWIPEBOX_URL . 'ios-orientationchange-fix.js', array(
        'jquery'
    ) , '1.0.7', true);

    // Custom JS for Flexslider
    // Register only : it will be enqueue if 'wppsn-img-gallery' shortcode is found with carrousel mode
    wp_register_script('wppsn_frontend_carrousel_js', WPPSN_PLUGIN_JS_URL . 'wppsn-frontend-carrousel.js', array(
        'wppsn_flexslider_js'
    ) , '1.0.7', true);


    // Custom JS for Swipebox
    // Register only : it will be enqueue if 'wppsn-image' or 'wppsn-img-gallery' (with list or grid display mode) shortcodes are found
    wp_register_script('wppsn_frontend_swipebox_js', WPPSN_PLUGIN_JS_URL . 'wppsn-frontend-swipebox.js', array(
        'wppsn_swipebox_js',
        'wppsn_swipebox_ios_orientation_fix_js'
    ) , '1.0.7', true);

}

add_action('wp_enqueue_scripts', 'wppsn_wp_enqueue_scripts');

add_action('wp_enqueue_scripts', 'image_model');

function image_model()
{
    wp_enqueue_script('image_model', WPPSN_PLUGIN_JS_URL . 'wppsn-frontend-modal.js',array() , '2.0.2', 'all');
}

add_action('wp_enqueue_scripts', 'image_model_css');

function image_model_css()
{
    wp_enqueue_style('image_model_css', WPPSN_PLUGIN_CSS_URL . 'wppsn-frontend-modal.css');
}


function phraseanet_url(){

//Get Plugin Options
$wppsn_options 	= get_option( 'wppsn_options' );

//Get base url for embed videos
$phraseanet_url = $wppsn_options['client_base_url'];

$c = strlen($phraseanet_url);

if($phraseanet_url[$c-1]=='/'){

    $phraseanet_url = substr($phraseanet_url,0,$c-1);
}

return $phraseanet_url;

}



/**
 * Define the shortcode 'wppsn-image'
 * @param  array $atts Array of attributes
 * @return html        HTML replacing the shortcode
 */
function wppsn_shortcode_single_image($atts)
{
    global $post;

    $output = '';

    // Add Swipebox JS to the footer
    wp_enqueue_script('wppsn_frontend_swipebox_js');

    extract(shortcode_atts(array(
        'title' => '',
        'alt' => '',
        'legend' => '',
        'download' => 0,
        'url' => '',
        'download_link'=>'',
    ) , $atts));

    $title = trim($title);
    $alt = trim($alt);
    $legend = trim($legend);
    $url = trim($url);
	$downloadLink = '';
    $id = md5($url);
    $download_url = trim($download_link);

    //var_dump($url);
    // Download link ?
    if ($download == 1)
    {
        $downloadLink = '<a href="' . $download_url . ((false === strpos('?', $download_url)) ? '&download=1' : '&download') . '">' . __('Download', 'wp-phraseanet') . '</a>';
    }

    $output .= '<div class="wppsn-image wp-caption">

		<img class="single_image wp-image-" src="' . $url . '" alt="' . $alt . '" name="' . $title . '" id="'.$id.'"  onclick="openModel(this.id)"  style="cursor: pointer;width: 100%;" >
	'
;

    if ($legend != '' || $downloadLink != '')
    {
        $output .= '	<p style="margin-right:0%" class="wp-caption-text">' . $legend . (($legend != '') ? ' - ' : '') . $downloadLink . '</p>';
    }

    $output .= "<div id='myModal_$id' class='modal'><span class='close'>&times;</span><img class='modal-content' id='img_$id'><div id='caption_$id' class='caption' ></div></div></div>";

    return $output;

}

add_shortcode('wppsn-image', 'wppsn_shortcode_single_image');


 function wppsn_shortcode_single_video($atts)
{
    

    $phraseanet_url  = phraseanet_url();

    $output = '';
    wp_enqueue_script('wppsn_frontend_video_js');

    // Get Attributes
    extract(shortcode_atts(array(
        'title' => '',
        'mp4' => '',
        'webm' => '',
        'ogg' => '',
        'splash' => ''
    ) , $atts));

    $title = trim($title);
    $mp4 = trim($mp4);
    $webm = trim($webm);
    $ogg = trim($ogg);
    $splash = trim($splash);

    $url = '';
    if($mp4!=''){
        $url = $mp4;
    }elseif($webm!=''){
        $url = $webm;
    }elseif($ogg!=''){
        $url = $ogg;
    }else{
        $url = $splash;
    }    

    $output .= '<div class="content">';
  
    $output .= '<div class="rwd-media">';

    $output .= '<iframe class="responsive-iframe" src="'.$phraseanet_url.'/embed/?url='.$url.'" frameborder="0" allowfullscreen="" webkitallowfullscreen="" mozallowfullscree=""></iframe></div></div>';

    return $output;

}

add_shortcode('wppsn-video', 'wppsn_shortcode_single_video');





function wppsn_shortcode_single_audio($atts)
{

    $phraseanet_url = phraseanet_url();



    $output = '';
    wp_enqueue_script('wppsn_frontend_video_js');

    // Get Attributes
    extract(shortcode_atts(array(
        'title' => '',
        'mp4' => '',
        'webm' => '',
        'ogg' => '',
        'mpeg' => '',
        'splash' => ''
    ) , $atts));

    $title = trim($title);
    $mp4 = trim($mp4);
    $webm = trim($webm);
    $ogg = trim($ogg);
    $mpeg = trim($mpeg);
    
    $splash = trim($splash);

    $url = '';
    if($mp4!=''){
        $url = $mp4;
    }elseif($webm!=''){
        $url = $webm;
    }elseif($ogg!=''){
        $url = $ogg;
    }elseif($mpeg!=''){
        $url = $mpeg;
    }

    $output .= '<div class="content">';
  
    $output .= '<div class="rwd-media">';

    $output .= '<iframe class="responsive-iframe" src="'.$phraseanet_url.'/embed/?url='.$url.'" frameborder="0" allowfullscreen="" webkitallowfullscreen="" mozallowfullscree=""></iframe></div></div>';

    return $output;

}

add_shortcode('wppsn-audio', 'wppsn_shortcode_single_audio');


/**
 * Define the shortcode 'wppsn-img-gallery'
 * @param  array $atts Array of attributes
 * @return html        HTML replacing the shortcode
 */
function wppsn_shortcode_image_gallery($atts)
{
    global $post;

    $output = '';

    extract(shortcode_atts(array(
        'display' => 'list',
        'titles' => '',
        'alts' => '',
        'legends' => '',
        'previews' => '',
        'urls' => '',
        'download' => '',
        'full_url'=> ''
    ) , $atts));

    // Explode Strings to Arrays
    $allTitles = explode('||', $titles);
    $allAlts = explode('||', $alts);
    $allLegends = explode('||', $legends);
    $allThumbs = explode('||', $previews);
    $allUrls = explode('||', $urls);
    $allDownlods = explode('||', $download);
       
    switch ($display)
    {

        case 'grid':

            // Add Swipebox JS to the footer
            wp_enqueue_script('wppsn_frontend_swipebox_js');

            $cpt = 1;
            $galleryID = 'gallery-' . md5(microtime(true));
            usleep(1);

            $output .= '<style type="text/css">
							#' . $galleryID . ' {
								margin: auto;
							}
							#' . $galleryID . ' .gallery-item {
								float: left;
								margin-top: 10px;
								text-align: center;
								
								position: relative;
								width: 40%;
							}
							#' . $galleryID . ' img {
								border: 2px solid #cfcfcf;
							}
							#' . $galleryID . ' .gallery-caption {
								margin-left: 0;
							}

							  .image {
								opacity: 1;
								display: block;
								width: 100%;
								height: auto;
								transition: .5s ease;
								backface-visibility: hidden;
							  }
							  
							  .middle {
								transition: .5s ease;
								opacity: 0;
								position: absolute;
								top: 50%;
								left: 50%;
								transform: translate(-50%, -50%);
								-ms-transform: translate(-50%, -50%);
								text-align: center;
                              }
                              
                              .middle2{
                                width: 100%;
                                padding: 1px 1px 1px 1px;
                                text-align: center;
                              }
							  
							  .gallery-item:hover .image {
								opacity: 0.3;
							  }
							  
							  .gallery-item:hover .middle {
								opacity: 1;
							  }
							
							  .text {
								background-color: #06060638;
								color: white;
								font-size: 16px;
								padding: 16px 32px;
								
							  };
							  a:link {
								color: black;
							  }

							  a:hover {
								color: white;
								text-decoration: none;
							  }
							  
						

						</style>';

            $output .= '<div  id="' . $galleryID . '" class="wppsn-gallery gallery">';

            $random  = random_int(1000,9999999);
            $slideIndex = $random;

            foreach ($allTitles as $i => $t)
            {

                $title = trim($t);
                $alt = trim($allAlts[$i]);
                $legend = trim($allLegends[$i]);
                $preview = trim($allThumbs[$i]);
                $url = trim($allUrls[$i]);
                $download = trim($allDownlods[$i]);
   
                $download_url = '';
                if ($download == 'on')
                {

                    $download_url = "<a href='$url&download=1'>Download</a>";
                }

                //Navigation
                $nav = "<a class='prev' onclick='plusSlides($slideIndex,0)'>&#10094;</a><a class='next' onclick='plusSlides($slideIndex,1)'>&#10095;</a>";

                $output .= "<div class='gallery-item'>
  <img src='$preview' alt='Avatar' class='image' >
  <div id='middle_text' class='middle'>
  
  <div class='text'>
  
  
  <a href='javascript:void(0)'  id='$slideIndex'  rel='$preview' name='$title' onclick='openModel($slideIndex)' >View</a> 
  <br>

  $download_url

  </div></div></div><div id='myModal_$slideIndex' class='modal'><span class='close'>&times;</span><img class='modal-content' id='img_$slideIndex'>
  <div id='nav_$slideIndex' > 
			$nav
    </div>                                  
  <div id='caption_$slideIndex' class='caption' >
  
  </div>
  
 
  </div>
  
 ";

                $output .= '</dl>';
                $slideIndex = $slideIndex + 1; //inc the counter
                // 3 columns break
                if ($cpt % 3 == 0)
                {
                    $output .= '<br style="clear:both;">';
                }

                $cpt++;

            }

            // To be sure :)
            $output .= '<br style="clear:both;">';

            $output .= '</div>';

        break;

        case 'carrousel':

            $uniqueString = md5(microtime(true));

            $sliderID = 'wppsn-slider-' . $uniqueString;
            $carrouselID = 'wppsn-carrousel-' . $uniqueString;
            usleep(1);

            // Add Flexslider JS to the Footer
            wp_enqueue_script('wppsn_frontend_carrousel_js');

            // Create the slider First
            $output .= '<div id="' . $sliderID . '" class="wppsn-slider flexslider">
		  					<ul class="slides">';

            foreach ($allTitles as $i => $t)
            {

                $title = trim($t);
                $alt = trim($allAlts[$i]);
                $legend = trim($allLegends[$i]);
                $preview = trim($allThumbs[$i]);
                $url = trim($allUrls[$i]);
                $download = trim($allDownlods[$i]);
                

               


                $output .= '<li style="    background-color: #rgb(10 10 10 / 12%);" >
								<img src="' . $preview . '" title="' . $title . '" alt="' . $alt . '">';

                                if ($download == 'on')
                                {
                
                                    $output .= "<p style='text-align: center;' class='flex-caption'> <a href='$url&download=1'>Download</a> </p>";
                                }


                // Legend ?
                if ($title != '')
                {
                    $output .= '<p style="text-align: center;" class="flex-caption">' . $title . '</p>';
                }

               

                $output .= '</li>';

            }

            $output .= '	</ul>
		  				</div>';

            // Create the carrousel Secondly
            $output .= '<div id="' . $carrouselID . '" class="wppsn-carrousel flexslider">
		  					<ul class="slides">';

            foreach ($allTitles as $i => $t)
            {

                $title = trim($t);
                $alt = trim($allAlts[$i]);
                $legend = trim($allLegends[$i]);
                $preview = trim($allThumbs[$i]);
                $url = trim($allUrls[$i]);

                $output .= '<li>
								<img src="' . $preview . '" title="' . $title . '" alt="' . $alt . '">
							</li>';

            }

            $output .= '	</ul>
		  				</div>';

        break;

        default: // List
            // Add Swipebox JS to the footer
            wp_enqueue_script('wppsn_frontend_swipebox_js');

            foreach ($allTitles as $i => $t)
            {

                $title = trim($t);
                $alt = trim($allAlts[$i]);
                $legend = trim($allLegends[$i]);
                $url = trim($allUrls[$i]);
                $id = md5($url);
                $download = trim($allDownlods[$i]);

                $downloadLink = "<a href='$url&download=1'>Download</a>";

                $output .= '<div class="wppsn-image wp-caption">
								<a href="javascript:void(0)" title="' . $title . '" class="wppsn-image-post-' . $post->ID . '">
									<img class="wp-image-" src="' . $url . '" alt="' . $alt . '" name="' . $title . '" id="'.$id.'"  onclick="openModel(this.id)"  style="cursor: pointer;" >
								</a>';


                                if ($legend != '' || $download == 'on')
                                {
                                    $output .= '	<p class="wp-caption-text">' . $legend . (($legend != '') ? ' - ' : '') . $downloadLink . '</p>';
                                }
                           

                $output .= "</div> <div id='myModal_$id' class='modal'><span class='close'>&times;</span><img class='modal-content' id='img_$id'><div id='caption_$id' class='caption' ></div></div>";

        
        

            }

        break;

    }

    return $output;

}

add_shortcode('wppsn-img-gallery', 'wppsn_shortcode_image_gallery');

/**
 * Define the shortcode 'wppsn-video-playlist'
 * @param  array $atts Array of attributes
 * @return html        HTML replacing the shortcode
 */
function wppsn_shortcode_video_playlist($atts)
{
    $phraseanet_url  = phraseanet_url();

    $output = '';
    wp_enqueue_script('wppsn_frontend_video_playlist_js');

    // Get Attributes
    extract(shortcode_atts(array(
        'titles' => '',
        'mp4s' => '',
        'thumbs' => '',
        'gifs'=> '',
        'splash' => '',
        'durations'=>'',
    ) , $atts));

    // Explode Strings to Arrays
    $allTitles = explode('||', $titles);
    $allMp4s = explode('||', $mp4s);
    $splash = trim($splash);
    $allThubms = explode("||",$thumbs);
    $allGifs = explode('||',$gifs);    
    $allDuration = explode('||',$durations);
    $url  = '';

    $random  = random_int(1000,9999999);
    foreach($allTitles as $i=>$t){

        
        if(!empty(trim($allMp4s[$i]))){
            $url = trim($allMp4s[$i]);
            break;
        }

        $random = $random * $i;
    }

    

    //$styleSplash = ($splash != '') ? ' style="background-image:url(' . $splash . ')"' : '';

    $output .= '<div class="content">';
  
    $output .= '<div class="rwd-media">';


    $output .= '<div id="'.$random.'_playlist_container"> <iframe id="'.$random.'_videos_playlist" class="responsive-iframe" src="'.$phraseanet_url.'/embed/?url='.$url.'" frameborder="0" allowfullscreen="" webkitallowfullscreen="" mozallowfullscree=""></iframe></div></div>  </div>';

    $output .= '<div style="margin-top:10px">';

    foreach ($allTitles as $i => $t)
    {

        $j = $i * $random;
        $color = trim($allMp4s[$i])!='' ? 'color: black;
        cursor: pointer;' : 'color: red';
       
        
        if($i==0){
            $color .= 'background-color:rgb(22 22 23 / 7%)';
        }else{
            $color .= 'background-color: white';
        }


        //Duration and fortmat 
        $secs = floor($allDuration[$i]);
        $milli = (int) (($allDuration[$i] - $secs) * 1000);
        $hours = ($secs / 3600);
        $hours = (int) $hours <=0 ? 00: $hours;
        $hours = $hours < 10 ? '0'.$hours : $hours;
        $minutes = (($secs / 60) % 60);
        $minutes = (int) $minutes <=0 ? 00: $minutes;
        $minutes = $minutes < 10 ? '0'.$minutes : $minutes;
        $seconds = $secs % 60;
        $seconds = (int) $seconds <=0 ? 00: $seconds;
        $seconds = $seconds < 10 ? '0'.$seconds : $seconds;


       $output .= '
       
<div class="'.$random.'_plist" onmouseout="gif(this.id,0)" name="'.$random.'" onmouseover="gif(this.id,1)" id="'.$j.'" style="margin-bottom: 18px;border-radius: 5px; box-shadow: 5px 5px 5px grey;'.$color.' ">


<div onclick="play(this.id,'.$j.','.$random.')" id="'.trim($allMp4s[$i]).'"  >


<input type="hidden" id="gif_'.$j.'" value="'.$allGifs[$i].'">
<input type="hidden" id="web_img_'.$j.'" value="'.$allThubms[$i].'">

<div style="float:left;"><img id="img_'.$j.'" style="border-radius:5px;border-right: 20px solid #00000000;" src="'.$allThubms[$i].'" /></div>



<div style="">

<div style="font-size: 22px; font-weight:400 ;line-height: 33px;">'.trim($t).'</div>

<div  style="" ><small>'.$hours.':'.$minutes.':'.$seconds.'</small></div>

<div style="clear: left;"/></div>
</div> </div>

</div>

';
          
    
    }

    $output .= '</div>
					';

    return $output;

}

add_shortcode('wppsn-videoplaylist', 'wppsn_shortcode_video_playlist');


//Audio
function wppsn_shortcode_audio_playlist($atts)
{

    $phraseanet_url  = phraseanet_url();

    $output = '';
    wp_enqueue_script('wppsn_frontend_video_playlist_js');

    // Get Attributes
    extract(shortcode_atts(array(
        'titles' => '',
        'mpegs' => '',
        'splash' => '',
        'durations'=>'',
        'thumbs'=>''
        
    ) , $atts));

    // Explode Strings to Arrays
    $allTitles = explode('||', $titles);
    $allmpegs = explode('||', $mpegs);
    $splash = trim($splash);
    $allDuration = explode('||',$durations);
    $allThubms = explode('||',$thumbs);
    $url  = '';

    $random  = random_int(1000,9999999);

    foreach($allTitles as $i=>$t){
        
        if(!empty(trim($allmpegs[$i]))){
            $url = trim($allmpegs[$i]);
            break;
        }

        $random = $random * $i;

    }

    
    $styleSplash = ($splash != '') ? ' style="background-image:url(' . $splash . ')"' : '';

    $output .= '<div class="content">';
  
    $output .= '<div class="rwd-media">';


    $output .= '<div id="'.$random.'_playlist_container"> <iframe id="'.$random.'_videos_playlist" class="responsive-iframe" src="'.$phraseanet_url.'/embed/?url='.$url.'" frameborder="0" allowfullscreen="" webkitallowfullscreen="" mozallowfullscree=""></iframe></div></div>  </div>';

    $output .= '<div style="margin-top:10px" >';

    foreach ($allTitles as $i => $t)
    {

        $color = trim($allmpegs[$i])!='' ? 'color: black;
        cursor: pointer;' : 'color: red';
       
        
        if($i==0){
            $color .= 'background-color:rgb(22 22 23 / 7%)';
        }else{
            $color .= 'background-color: white';
        }


        //Duration and fortmat 
        $secs = floor($allDuration[$i]);
        $milli = (int) (($allDuration[$i] - $secs) * 1000);
        $hours = ($secs / 3600);
        $hours = (int) $hours <=0 ? 00: $hours;
        $hours = $hours < 10 ? '0'.$hours : $hours;
        $minutes = (($secs / 60) % 60);
        $minutes = (int) $minutes <=0 ? 00: $minutes;
        $minutes = $minutes < 10 ? '0'.$minutes : $minutes;
        $seconds = $secs % 60;
        $seconds = (int) $seconds <=0 ? 00: $seconds;
        $seconds = $seconds < 10 ? '0'.$seconds : $seconds;

      
       $output .= '
       
       <div class="'.$random.'_plist"  id="'.$i.'" style="margin-bottom: 18px;border-radius: 12px; box-shadow: 5px 5px 5px grey;'.$color.' ">
       
       
       <div onclick="play(this.id,'.$i.','.$random.')" id="'.trim($allmpegs[$i]).'" >
       
        
       <div style="float:left;"><img id="img_'.$i.'" style="border-radius:5px;border-right: 20px solid #00000000;" src="'.$allThubms[$i].'" /></div>
       
       
       
       <div style="">
       
       <div style="font-size: 22px; font-weight:400 ;line-height: 33px;">'.trim($t).'</div>
       
       <div  style="" ><small>'.$hours.':'.$minutes.':'.$seconds.'</small></div>
       
       <div style="clear: left;"/></div>
       </div> </div>
       
       </div>
       
       ';
    
    }

    $output .= '</div>
					</div>
				</div>';

    return $output;

}

add_shortcode('wppsn-audioplaylist', 'wppsn_shortcode_audio_playlist');




