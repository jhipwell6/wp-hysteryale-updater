<div class="equipment-block">
    <?php if ( $Equipment->get_image() ) : ?>
    <div class="equipment-image">
    	<img src="<?php echo $Equipment->get_image(); ?>" alt="<?php echo $Equipment->get_title(); ?>" />
    </div>
    <?php endif; ?>
    <div class="equipment-text">
        <h3 class="equipment-title"><?php echo $Equipment->get_manufacturer(); ?> <?php echo $Equipment->get_title(); ?></h3>
        <?php if ( $Equipment->has_price() ) : ?><h5 class="equipment-price"><?php echo $Equipment->get_price(); ?></h5><?php endif; ?>
    </div>
    <div class="equipment-link">
		<a href="<?php echo $Equipment->get_url(); ?>">View Details</a>
    </div>
</div>
