<?php
/**
 * Header Related Functions
 **/

/**
 * Gets the header image for pages and taxonomy terms that have page header
 * images enabled.
 *
 * @author Jo Dickson
 * @since 0.0.0
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return array A set of Attachment IDs, one sized for use on -sm+ screens, and another for -xs
 **/
function ucfwp_get_header_images( $obj ) {
	$retval = array(
		'header_image'    => '',
		'header_image_xs' => ''
	);

	$retval = (array) apply_filters( 'ucfwp_get_header_images_before', $retval, $obj );
	// Exit early if this filter added a 'header_image' value
	if ( isset( $retval['header_image'] ) && $retval['header_image'] ) {
		return $retval;
	}

	if ( $obj_header_image = get_field( 'page_header_image', $obj ) ) {
		$retval['header_image'] = $obj_header_image;
	}
	if ( $obj_header_image_xs = get_field( 'page_header_image_xs', $obj ) ) {
		$retval['header_image_xs'] = $obj_header_image_xs;
	}

	$retval = (array) apply_filters( 'ucfwp_get_header_images_after', $retval, $obj );

	if ( isset( $retval['header_image'] ) && $retval['header_image'] ) {
		return $retval;
	}
	return false;
}


/**
 * Gets the header video sources for pages and taxonomy terms that have page
 * header videos enabled.
 *
 * @author Jo Dickson
 * @since 0.0.0
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return array A set of Attachment urls corresponding to available video filetypes
 **/
function ucfwp_get_header_videos( $obj ) {
	$retval = array(
		'webm' => '',
		'mp4'  => ''
	);

	$retval = (array) apply_filters( 'ucfwp_get_header_videos_before', $retval, $obj );
	$retval = array_filter( $retval );
	// Exit early if a 'mp4' value was provided early
	if ( isset( $retval['mp4'] ) && $retval['mp4'] ) {
		return $retval;
	}

	if ( $obj_header_video_mp4 = get_field( 'page_header_mp4', $obj ) ) {
		$retval['mp4'] = $obj_header_video_mp4;
	}
	if ( $obj_header_video_webm = get_field( 'page_header_webm', $obj ) ) {
		$retval['webm'] = $obj_header_video_webm;
	}

	$retval = (array) apply_filters( 'ucfwp_get_header_videos_after', $retval, $obj );
	$retval = array_filter( $retval );

	// MP4 must be available to display video successfully cross-browser
	if ( isset( $retval['mp4'] ) && $retval['mp4'] ) {
		return $retval;
	}
	return false;
}


/**
 * Returns texturized title text for use in the page header.
 *
 * @author Jo Dickson
 * @since 0.0.0
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string Header title text
 **/
 function ucfwp_get_header_title( $obj ) {
	$title = '';

	// Exit early if the title has been overridden early
	$title = (string) apply_filters( 'ucfwp_get_header_title_before', $title, $obj );
	if ( !empty( $title ) ) {
		return wptexturize( $title );
	}

	if ( ! $obj ) {
		// We intentionally don't add a fallback title for 404s;
		// this allows us to add a custom h1 to the default 404 template.
		if ( ! is_404() ) {
			$title = get_bloginfo( 'name', 'display' );
		}
	}
	else {
		// Checks listed below are copied directly from WP core
		// (see wp_get_document_title()).
		// NOTE: We still include support for templates that are disabled in
		// ucfwp_kill_unused_templates() in case a child theme re-enables
		// one of those templates.

		if ( is_search() ) {
			$title = sprintf( __( 'Search Results for &#8220;%s&#8221;' ), get_search_query() );
		} elseif ( is_front_page() ) {
			$title = get_bloginfo( 'name', 'display' );
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		} elseif ( is_home() || is_singular() ) {
			$title = single_post_title( '', false );
		} elseif ( is_category() || is_tag() ) {
			$title = single_term_title( '', false );
		} elseif ( is_author() && $author = get_queried_object() ) {
			$title = $author->display_name;
		} elseif ( is_year() ) {
			$title = get_the_date( _x( 'Y', 'yearly archives date format' ) );
		} elseif ( is_month() ) {
			$title = get_the_date( _x( 'F Y', 'monthly archives date format' ) );
		} elseif ( is_day() ) {
			$title = get_the_date();
		}
	}

	// Apply custom header title override, if available
	if ( $custom_header_title = get_field( 'page_header_title', $obj ) ) {
		$title = do_shortcode( $custom_header_title );
	}

	$title = (string) apply_filters( 'ucfwp_get_header_title_after', $title, $obj );

	return wptexturize( $title );
}


/**
 * Returns texturized subtitle text for use in the page header.
 *
 * @author Jo Dickson
 * @since 0.0.0
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string Header subtitle text
 **/
function ucfwp_get_header_subtitle( $obj ) {
	$subtitle = '';

	$subtitle = (string) apply_filters( 'ucfwp_get_header_subtitle_before', $subtitle, $obj );
	// Exit early if subtitle has been modified early
	if ( !empty( $subtitle ) ) {
		return wptexturize( $subtitle );
	}

	$subtitle = do_shortcode( get_field( 'page_header_subtitle', $obj ) );

	$subtitle = (string) apply_filters( 'ucfwp_get_header_subtitle_after', $subtitle, $obj );

	return wptexturize( $subtitle );
}


/**
 * Returns whether the page title or subtitle was designated as the page's h1.
 * Defaults to 'title' if the option isn't set.
 * Will force return a different value if the user screwed up (e.g. specified
 * "subtitle" but didn't provide a subtitle value).
 *
 * @author Jo Dickson
 * @since 0.0.0
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string Option value for the designated page header h1
 **/
if ( !function_exists( 'ucfwp_get_header_h1_option' ) ) {
	function ucfwp_get_header_h1_option( $obj ) {
		$subtitle = get_field( 'page_header_subtitle', $obj ) ?: '';
		$h1       = get_field( 'page_header_h1', $obj ) ?: 'title';

		if ( $h1 === 'subtitle' && trim( $subtitle ) === '' ) {
			$h1 = 'title';
		}

		return $h1;
	}
}


/**
 * Returns the type of header to use for the given object.
 * The value returned will represent an equivalent template part's name.
 *
 * @author Jo Dickson
 * @since TODO
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string The header type name
 */
if ( ! function_exists( 'ucfwp_get_header_type' ) ) {
	function ucfwp_get_header_type( $obj ) {
		$header_type = '';

		$videos = ucfwp_get_header_videos( $obj );
		$images = ucfwp_get_header_images( $obj );
		if ( $videos || $images ) {
			$header_type = 'media';
		}

		return apply_filters( 'ucfwp_get_header_type', $header_type, $obj );
	}
}


/**
 * Returns the header content type for the given page's header.
 * The value returned will represent an equivalent template part's name.
 *
 * @author Jo Dickson
 * @since TODO
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string The content type name
 */
function ucfwp_get_header_content_type( $obj ) {
	$content_type = get_field( 'page_header_content_type', $obj ) ?: '';
	$header_type  = ucfwp_get_header_type( $obj );

	// Required for compatibility with existing content type names:
	// set $header_content_type to an empty string to force the 'default'
	// header_content partial to be returned
	if ( $header_type === '' && $content_type === 'title_subtitle' ) {
		$content_type = '';
	}

	return apply_filters( 'ucfwp_get_header_content_type', $content_type, $obj );
}


/**
 * Returns inner navbar markup for ucf.edu's primary site navigation.
 *
 * TODO move markup to template part
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @return string HTML markup
 */
if ( !function_exists( 'ucfwp_get_mainsite_menu' ) ) {
	function ucfwp_get_mainsite_menu( $image=true ) {
		global $wp_customize;
		$customizing    = isset( $wp_customize );
		$feed_url       = get_theme_mod( 'mainsite_nav_url' ) ?: UCFWP_MAINSITE_NAV_URL;
		$transient_name = 'ucfwp_mainsite_nav_json';
		$result         = get_transient( $transient_name );

		if ( empty( $result ) || $customizing ) {
			// Try fetching the theme mod value or default
			$result = ucfwp_fetch_json( $feed_url );

			// If the theme mod value failed and it's not what we set as our
			// default, try again using the default
			if ( !$result && $feed_url !== UCFWP_MAINSITE_NAV_URL ) {
				$result = ucfwp_fetch_json( UCFWP_MAINSITE_NAV_URL );
			}

			if ( ! $customizing ) {
				set_transient( $transient_name, $result, (60 * 60 * 24) );
			}
		}

		if ( !$result ) { return ''; }
		$menu = $result;

		ob_start();
	?>
	<nav class="navbar navbar-toggleable-md navbar-mainsite py-2<?php echo $image ? ' py-sm-4 navbar-inverse header-gradient' : ' navbar-inverse bg-inverse-t-3 py-lg-4'; ?>" role="navigation" aria-label="Site navigation">
		<div class="container">
			<button class="navbar-toggler ml-auto collapsed" type="button" data-toggle="collapse" data-target="#header-menu" aria-controls="header-menu" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-text">Navigation</span>
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="header-menu">
				<ul id="menu-header-menu" class="nav navbar-nav nav-fill">
					<?php foreach ( $menu->items as $item ): ?>
					<li class="menu-item nav-item">
						<a href="<?php echo $item->url; ?>" target="<?php echo $item->target; ?>" class="nav-link">
							<?php echo $item->title; ?>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</nav>
	<?php
		return ob_get_clean();
	}
}


/**
 * Returns HTML markup for the primary site navigation.  Falls back to the
 * ucf.edu primary navigation if a header menu is not set.
 *
 * TODO move markup to template part
 *
 * @author Jo Dickson
 * @since 0.0.0
 * @param bool $image Whether or not a media background is present in the page header.
 * @return string Nav HTML
 **/
if ( !function_exists( 'ucfwp_get_nav_markup' ) ) {
	function ucfwp_get_nav_markup( $image=true ) {
		$title_elem = ( is_home() || is_front_page() ) ? 'h1' : 'span';

		ob_start();

		if ( has_nav_menu( 'header-menu' ) ) {
	?>
		<nav class="navbar navbar-toggleable-md navbar-custom<?php echo $image ? ' py-2 py-sm-4 navbar-inverse header-gradient' : ' navbar-inverse bg-inverse-t-3'; ?>" role="navigation" aria-label="Site navigation">
			<div class="container d-flex flex-row flex-nowrap justify-content-between">
				<<?php echo $title_elem; ?> class="mb-0">
					<a class="navbar-brand mr-lg-5" href="<?php echo get_home_url(); ?>"><?php echo bloginfo( 'name' ); ?></a>
				</<?php echo $title_elem; ?>>
				<button class="navbar-toggler ml-auto align-self-start collapsed" type="button" data-toggle="collapse" data-target="#header-menu" aria-controls="header-menu" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-text">Navigation</span>
					<span class="navbar-toggler-icon"></span>
				</button>
				<?php
				$container_class = 'collapse navbar-collapse';
				if ( !$image ) {
					$container_class = $container_class . ' align-self-lg-stretch';
				}
				wp_nav_menu( array(
					'container'       => 'div',
					'container_class' => $container_class,
					'container_id'    => 'header-menu',
					'depth'           => 2,
					'fallback_cb'     => 'bs4Navwalker::fallback',
					'menu_class'      => 'nav navbar-nav ml-md-auto',
					'theme_location'  => 'header-menu',
					'walker'          => new bs4Navwalker()
				) );
				?>
			</div>
		</nav>
	<?php
		}
		else {
			echo ucfwp_get_mainsite_menu( $image );
		}

		return ob_get_clean();
	}
}


/**
 * Returns an array of src's for a page header's media background
 * <picture> <source>s, by breakpoint.  Will return a unique set of src's
 * depending on the page's header height.
 *
 * @author Jo Dickson
 * @since 0.2.1
 * @param string $header_height Name of the header's height
 * @param array $images Assoc. array of image size names and attachment IDs (expects a return value from ucfwp_get_header_images())
 * @return array Assoc. array of breakpoint names and image urls (see ucfwp_get_media_background_picture_srcs())
 */
if ( ! function_exists( 'ucfwp_get_header_media_picture_srcs' ) ) {
	function ucfwp_get_header_media_picture_srcs( $header_height, $images ) {
		$bg_image_srcs = array();

		switch ( $header_height ) {
			case 'header-media-fullscreen':
				$bg_image_srcs = ucfwp_get_media_background_picture_srcs( null, $images['header_image'], 'bg-img' );
				$bg_image_src_xs = ucfwp_get_media_background_picture_srcs( $images['header_image_xs'], null, 'header-img' );

				if ( isset( $bg_image_src_xs['xs'] ) ) {
					$bg_image_srcs['xs'] = $bg_image_src_xs['xs'];
				}

				break;
			default:
				$bg_image_srcs = ucfwp_get_media_background_picture_srcs( $images['header_image_xs'], $images['header_image'], 'header-img' );
				break;
		}

		return $bg_image_srcs;
	}
}


/**
 * Returns header markup for the current object.
 *
 * @author Jo Dickson
 * @since 0.0.0
 * @return string HTML for the page header
 **/
if ( !function_exists( 'ucfwp_get_header_markup' ) ) {
	function ucfwp_get_header_markup() {
		$retval = '';
		$obj    = ucfwp_get_queried_object();

		$template_part_slug = ucfwp_get_template_part_slug( 'header' );
		$template_part_name = ucfwp_get_header_type( $obj );

		set_query_var( 'ucfwp_obj', $obj );

		ob_start();
		_ucfwp_get_template_part( $template_part_slug, $template_part_name );
		$retval = ob_get_clean();

		return apply_filters( 'ucfwp_get_header_markup', $retval, $obj );
	}
}


/**
 * Returns subnavigation markup for the current object.
 *
 * @author Jo Dickson
 * @since 0.0.0
 * @return string HTML for the page header
 **/
if ( !function_exists( 'ucfwp_get_subnav_markup' ) ) {
	function ucfwp_get_subnav_markup() {
		$obj = ucfwp_get_queried_object();
		$include_subnav = get_field( 'page_header_include_subnav', $obj );

		if ( class_exists( 'Section_Menus_Common' ) && $include_subnav ) {
			return do_shortcode( '[section-menu]' );
		}
	}
}
