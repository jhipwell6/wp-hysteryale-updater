<ul class="nav nav-tabs" id="equipment-tabs">
	<?php
		$i = 0;
		while ( have_rows('tabs') ) : the_row();
			$active = $i == 0 ? ' active' : '';
			$slug = sanitize_title( get_sub_field('tab_title') );
	?>
	<li class="nav-item">
		<a class="nav-link<?php echo $active; ?>" data-toggle="tab" id="<?php echo $slug; ?>-tab" href="#<?php echo $slug; ?>"><?php the_sub_field('tab_title'); ?></a>
	</li>
	<?php $i++; endwhile; ?>
</ul>
<div class="tab-content" id="equipment-details">
	<?php
		$i = 0;
		while ( have_rows('tabs') ) : the_row();
			$active = $i == 0 ? ' show active' : '';
			$slug = sanitize_title( get_sub_field('tab_title') );
	?>
	<div class="tab-pane fade<?php echo $active; ?>" id="<?php echo $slug; ?>" role="tabpanel" aria-labelledby="<?php echo $slug; ?>-tab">
		<?php the_sub_field('tab_content'); ?>
	</div>
	<?php $i++; endwhile; ?>
</div>