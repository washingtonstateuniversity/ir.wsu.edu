<nav id="spine-sitenav" class="spine-sitenav">
	<ul>
	<?php
	$spine_site_args = array(
		'theme_location'  => 'site',
		'menu'            => 'site',
		'container'       => false,
		'container_class' => false,
		'container_id'    => false,
		'menu_class'      => null,
		'menu_id'         => null,
		'items_wrap'      => '%3$s',
		'depth'           => 5,
	);
	wp_nav_menu( $spine_site_args );

	if ( is_user_logged_in() ) {
		$spine_site_args = array(
			'theme_location'  => 'site-restricted',
			'menu'            => 'site-restricted',
			'container'       => false,
			'container_class' => false,
			'container_id'    => false,
			'menu_class'      => null,
			'menu_id'         => null,
			'items_wrap'      => '%3$s',
			'depth'           => 5,
		);
		wp_nav_menu( $spine_site_args );
	}

	$spine_site_args = array(
		'theme_location'  => 'site-bottom',
		'menu'            => 'site-bottom',
		'container'       => false,
		'container_class' => false,
		'container_id'    => false,
		'menu_class'      => null,
		'menu_id'         => null,
		'items_wrap'      => '%3$s',
		'depth'           => 5,
	);
	wp_nav_menu( $spine_site_args );
	?>
	</ul>
</nav>
