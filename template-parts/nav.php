<?php
$image      = (bool) get_query_var( 'ucfwp_image_behind_nav', false );
$title_elem = ( is_home() || is_front_page() ) ? 'h1' : 'span';
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