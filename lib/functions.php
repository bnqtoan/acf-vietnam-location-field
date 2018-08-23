<?php

function _vnl_province_list( $province_id, $country_field ) {
	$provinces = acf_field_VN_LOCATION_FIELD::_acf_get_provinces();
	?>
    <select name="<?php echo $country_field; ?>">
        <option value="">Tỉnh / thành phố</option>
		<?php foreach ( $provinces AS $ID => $province ): ?>
            <option value="<?= $ID; ?>"
			        <?php if ( $province_id == $ID ): ?>selected<?php endif; ?>><?php echo $province; ?></option>
		<?php endforeach; ?>
    </select>
	<?php
}

function _vnl_district_list( $province_id, $district_id, $city_field ) {
	$cities = acf_field_VN_LOCATION_FIELD::_acf_get_districts( $province_id );
	?>
    <select name="<?= $city_field; ?>">
        <option value="">Quận / huyện</option>
		<?php foreach ( $cities AS $ID => $city ): ?>
            <option value="<?= $ID; ?>"
			        <?php if ( $district_id == $ID ): ?>selected<?php endif; ?>><?php echo $city; ?></option>
		<?php endforeach; ?>
    </select>
	<?php
}

function _vnl_location_list(){
    ?>
    <ul>
        <li><?php _vnl_province_list( '01', 'province_id' ); ?></li>
        <li><?php _vnl_district_list( '01', '00', 'district_id' ); ?></li>
    </ul>
<?php
}