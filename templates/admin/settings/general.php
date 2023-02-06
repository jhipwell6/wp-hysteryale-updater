<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <label for="<?php echo 'hysteryale_updater__new_type_limitation'; ?>">
                Available Types
            </label>
        </th>
        <td>
            <input type="hidden" name="hysteryale_updater__type_limitation" value="" />
			<?php foreach ( $available_types as $id => $type ): ?>
	            <label>
					<input type="checkbox"
						   name="hysteryale_updater__type_limitation[]"
						   id="hysteryale_updater__type_limitation"
						   value="<?php echo $id; ?>"
						   <?php echo in_array( $id, $types ) ? 'checked' : ''; ?>> <?php echo $type; ?>
	            </label><br>
			<?php endforeach; ?>
        </td>
    </tr>
</table>
