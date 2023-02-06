<ul class="nav nav-tabs" id="equipment-tabs">
	<?php if ( ! empty( $Equipment->get_hygapi_features() ) ) : ?>
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" id="features-tab" href="#features">Features</a>
	</li>
	<?php endif; ?>
	<?php if ( ! empty( $Equipment->get_hygapi_properties() ) ) : ?>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" id="specs-tab" href="#specs">Specs</a>
	</li>
	<?php endif; ?>
	<?php if ( ! empty( $Equipment->get_hygapi_properties('industries') ) ) : ?>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" id="industries-tab" href="#industries">Industries</a>
	</li>
	<?php endif; ?>
	<?php if ( ! empty( $Equipment->get_hygapi_assets() ) ) : ?>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" id="downloads-documentation-tab" href="#downloads-documentation">Downloads &amp; Documentation</a>
	</li>
	<?php endif; ?>
</ul>
<div class="tab-content" id="equipment-details">
	<?php if ( ! empty( $Equipment->get_hygapi_features() ) ) : ?>
	<div class="tab-pane fade show active" id="features" role="tabpanel" aria-labelledby="features-tab">
		<ul>
			<?php foreach ( $Equipment->get_hygapi_features() as $feature ) : ?>
			<li><?php echo $feature; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	<?php if ( ! empty( $Equipment->get_hygapi_properties() ) ) : ?>
	<div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
		<div id="equipment-details-list">
			<ul class="list-group list-group-flush">
				<?php
					foreach ( $Equipment->get_hygapi_properties() as $prop => $value ) :
						if ( ( is_array( $value ) && empty( $value ) ) || $value == '' || $prop == 'industries' )
							continue;
				?>
				<li class="list-group-item"><strong><?php echo $Equipment->to_label( $prop ); ?>:</strong> <?php echo is_array( $value ) ? $Equipment->to_list( $value ) : $value; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php endif; ?>
	<?php if ( ! empty( $Equipment->get_hygapi_properties('industries') ) ) : ?>
	<div class="tab-pane fade" id="industries" role="tabpanel" aria-labelledby="industries-tab">
		<ul>
			<?php foreach ( $Equipment->get_hygapi_properties('industries') as $industry ) : ?>
			<li><?php echo $industry; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	<?php if ( ! empty( $Equipment->get_hygapi_assets() ) ) : ?>
	<div class="tab-pane fade" id="downloads-documentation" role="tabpanel" aria-labelledby="downloads-documentation-tab">
		<ul>
			<?php foreach ( $Equipment->get_hygapi_assets() as $asset ) : ?>
			<li><a href="<?php echo $asset['path']; ?>" target="_blank"><?php echo $asset['name']; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
</div>