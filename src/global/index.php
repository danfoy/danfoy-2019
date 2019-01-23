<?php
/**
 * Index template for danfoy-2019 WordPress theme
 *
 * This is the default template for the theme, so it will appear whenever
 * it isn't overridden by an alternative. It is therefore as bare-bones
 * as possible, as it should mostly only be used for quirky situations, such
 * as visitors arriving at pages for which there is no link from the site, but
 * which WordPress happily generates anyway based on the URL visited.
 *
 * @package df19
 * @since  1.0.0
 * @author Dan Foy
 */

get_header();

?>

<main class="main" aria-label="Content">

	<?php

	get_template_part( 'loop' );
	get_template_part( 'pagination' );

	?>

</main>

<?php

get_footer();
