<?php 
	$content = $Equipment->get_content();
	$tabs = $content && isset( $content['tabs'] ) ? $content['tabs'] : array();
	if ( ! empty( $tabs ) ) :
?>
<div class="accordion" id="equipment-details-list">
	<?php
		
		$i = 0;
		foreach ( $tabs as $tab ) :
			$tab_slug = sanitize_title( $tab['title'] );
	?>
	<div class="card">
		<div class="card-header p-0" id="heading-<?php echo $tab_slug; ?>">
			<h4 class="mb-0">
				<button class="btn btn-link btn-block text-left px-0 collapsed" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $tab_slug; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $tab_slug; ?>">
					<?php echo $tab['title']; ?>
				</button>
			</h4>
		</div>
		<div id="collapse-<?php echo $tab_slug; ?>" class="collapse" aria-labelledby="heading-<?php echo $tab_slug; ?>" data-parent="#equipment-details-list.accordion">
			<div class="card-body">
				<?php echo $tab['content']; ?>
			</div>
		</div>
	</div>
	<?php $i++; endforeach; ?>
</div>
<?php endif; ?>