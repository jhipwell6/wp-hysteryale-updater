<div class="wrap">
	<?php if ( ! empty( $types ) ) : ?>

	    <p>For each product, select to create fresh or link with existing product. When all products are configured, click Import.</p>

	    <div class="importer-buttons">
	        <button type="button" class="button action js-importer-start">Import</button>
	    </div>

	    <div class="progress">
	    </div>

	    <br/>

	    <a href='?page=<?php echo $this->options_key ?>&tab=importer&force=1'>Refresh list of products</a>

		<h4>Jump to Product Type</h4>
		<ul>
			<?php foreach ( $types as $type ): if ( empty( $type ) ) continue; ?>
				<li><a href="#product-type-section-<?php echo strtolower( $type ) ?>"><?php echo $type ?></a></li>
			<?php endforeach ?>
		</ul>

	    <form id='hysteryale_updater__import_config'>
			<table class="form-table">
				<?php foreach ( $types as $type ) : if ( empty( $type ) ) continue; ?>

		            <tr style='border:2px solid #23282e' id='product-type-section-<?php echo strtolower( $type ) ?>'>
		                <th colspan='4' style='background-color:#23282e;padding: 10px 30px'>
		                    <h3 style='color:#fff'><?php echo $type ?></h3>
		                </th>
		            </tr>

					<?php foreach ( $tree[$type] as $product ) : ?>
						<?php
						if ( ! isset( $product['api_id'] ) )
							continue;
						$post_id = $product['__post_id'];
						$update = $product['__update'];
						?>
			            <tr style='border:2px solid #ddd'>
			                <th style='background-color:#ddd;padding: 10px 30px' colspan='2'><?php echo $product['title'] ?></th>
			                <th style='background-color:#ddd;padding: 10px 30px'>Action</th>
			                <th style='background-color:#ddd;padding: 10px 30px'>Existing Equipment</th>
			            </tr>
			            <tr style='border:2px solid #ddd' class='hysteryale_updater__config_row'>
			                <td valign='top'>
			                    <img style='height:70px' src='<?php echo $product['image']; ?>'/>
			                </td>
			                <td valign='top'>
			                    <p><a href='<?php echo $product['url'] ?>' target='_blank'>View on <?php echo $type; ?></a>
			                    <p><?php echo $product['type']; ?></p>
			                </td>
			                <td valign='top'>
			                    <input type="hidden" class="hysteryale_updater__config_type" value="<?php echo $type ?>" />
			                    <input type="hidden" class="hysteryale_updater__config_hysteryale" value="<?php echo $product['api_id'] ?>" />
			                    <select class="hysteryale_updater__config_import"
			                            id="hysteryale_updater__product_config_<?php echo $product['api_id'] ?>" >
			                        <option value='ignore' >Ignore</option>
			                        <option value='create' >Create New</option>
			                        <option value='update' <?php selected( $update ) ?>>Update Existing: </option>
			                    </select>
			                </td>
			                <td valign='top'>
			                    <select class="hysteryale_updater__config_existing"
			                            id="hysteryale_updater__product_existing_<?php echo $product['api_id'] ?>" >
			                        <option value='' >Select One...</option>
									<?php foreach ( $equipments as $equipment ) : ?>
										<?php
										$selected = ( $equipment->ID == $post_id );
										if ( ! $selected and ! $update ) {
											$simple_hysteryale_title = preg_replace( '/\W/', '', strtolower( strip_tags( $product['title'] ) ) );
											$simple_wp_title = preg_replace( '/\W/', '', strtolower( strip_tags( $equipment->post_title ) ) );
											$selected = ( $simple_hysteryale_title == $simple_wp_title );
										}
										?>
				                        <option value='<?php echo $equipment->ID ?>' <?php selected( $selected ) ?>><?php echo $equipment->post_title ?></option>
									<?php endforeach ?>
			                    </select>
			                </td>
			            </tr>
					<?php endforeach; ?>

				<?php endforeach; ?>
			</table>
	    </form>

	<?php else: ?>
	    <p>Please enable the product types that you wish to import under General tab.</p>
	    <p><a class="button button-primary" href="options-general.php?page=hysteryale-updater-settings&tab=general">Hyster-Yale Settings</a></p>

	<?php endif; ?>

</div>

