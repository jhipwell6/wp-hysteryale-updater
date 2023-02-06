<div id="equipment-images" class="carousel slide" data-ride="carousel">
	<div class="carousel-inner">
		<?php 
			$i = 0;
			foreach ( $Equipment->get_images() as $image ) :
				$active = $i == 0 ? ' active' : '';
			
				$url = $Equipment->is_hyg_equipment() ? $image['path'] : $image['url'];
				$alt = $Equipment->is_hyg_equipment() ? $image['alt_text'] : $image['alt'];
		?>
		<div class="carousel-item<?php echo $active; ?>">
			<img src="<?php echo $url; ?>" class="d-block w-100" alt="<?php echo $alt; ?>">
		</div>
		<?php $i++; endforeach; ?>
	</div>
	<ol class="carousel-indicators">
		<?php
			$i = 0;
			foreach ( $Equipment->get_images() as $image ) :
				$active = $i == 0 ? ' class="active"' : '';
				$thumbnail = $Equipment->is_hyg_equipment() ? $image['path'] : $image['sizes']['thumbnail'];
				$alt = $Equipment->is_hyg_equipment() ? $image['alt_text'] : $image['alt'];
		?>
		<li data-target="#equipment-images" data-slide-to="<?php echo $i; ?>"<?php echo $active; ?>>
			<img src="<?php echo $thumbnail; ?>" class="d-block w-100" alt="<?php echo $alt; ?>">
		</li>
		<?php $i++; endforeach; ?>
	</ol>
</div>