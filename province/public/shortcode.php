<?php

//Register shortcode
add_shortcode( 'state_list_dropdown','state_lists_fun' );

//define function to show output
function state_lists_fun( $atts, $content = '', $tag ){


    $html = '';
    $html .= '<div><select id="select_state">';
    	$html .= '<option>Select State</option>';
    $html .= '</select></div>';

    $html .= '<br>';

    $html .= '<div class="done dropload"><select id="select_province" disabled>';
    	$html .= '<option>Select Province</option>';
    $html .= '</select></div>';

    $html .= '<br>';

    $html .= '<div class="done dropload"><select id="select_municipal" disabled>';
    	$html .= '<option>Select Municipal</option>';
    $html .= '</select></div>';

    $html .= '<br>';

    $html .= '<div class="done dropload"><select id="select_zip" disabled>';
    	$html .= '<option>Select Zip Code</option>';
    $html .= '</select></div>';


    return $html;


}





add_action('wp_footer', 'load_state_data');
function load_state_data()
{
?>
	<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
	<script type="text/javascript">
		get_state_data();
		/* Get  State*/
		function get_state_data()
		{

	        jQuery.ajax({
			    type:"POST",
			    url:"<?php echo admin_url('admin-ajax.php'); ?>",
			    data: {action:'my_special_ajax_call_get_state_list'},
			    dataType: "json",
			    success:function(res)
			    {
			    	var state_option = '';
			    	var state_option = '<option>Select State</option>';
				    $.each(res.states, function (key, val) {
				        state_option += '<option value="'+val.id+'">'+val.state_name+'</option>';
				    });
				
					jQuery('#select_state').html('');
				    jQuery('#select_state').html(state_option);


			    }
			});
		}

		/* Get  Province */
		jQuery('#select_state').on('change',function(){

			var state_id = jQuery(this).val();

			jQuery('#select_province').parent().removeClass('done');
	        jQuery.ajax({
			    type:"POST",
			    url:"<?php echo admin_url('admin-ajax.php'); ?>",
			    data: {action:'my_special_ajax_call_get_province_list',state_id:state_id},
			    dataType: "json",
			    success:function(res)
			    {

			    	var province_option = '';
			    	var province_option = '<option>Select Province</option>';
				    $.each(res.provinces, function (key, val) {
				        province_option += '<option value="'+val.id+'">'+val.provinces_name+'</option>';
				    });

					jQuery('#select_province').html('');
				    jQuery('#select_province').html(province_option);
				    jQuery('#select_province').prop('disabled', false);
					jQuery('#select_province').parent().addClass('done');

			    }
			});

		});


		/* get municipal & zip */
		jQuery('#select_province').on('change',function(){

			var provinces_id = jQuery(this).val();
			jQuery('#select_municipal').parent().removeClass('done');


	        jQuery.ajax({
			    type:"POST",
			    url:"<?php echo admin_url('admin-ajax.php'); ?>",
			    data: {action:'my_special_ajax_call_get_municipal_list',provinces_id:provinces_id},
			    dataType: "json",
			    success:function(res)
			    {
			    	var municipal_option = '';
			    	var municipal_option = '<option>Select Municipal</option>';
				    $.each(res.municipal, function (key, val) {
				        municipal_option += '<option value="'+val.id+'">'+val.municipal_name+'</option>';
				    });

					jQuery('#select_municipal').html('');
				    jQuery('#select_municipal').html(municipal_option);
				    jQuery('#select_municipal').prop('disabled', false);
					jQuery('#select_municipal').parent().addClass('done');


			    }
			});

		});



		/* Get ZIPCode */ 
		jQuery('#select_municipal').on('change',function(){

			var municipal_id = jQuery(this).val();

			jQuery('#select_zip').parent().removeClass('done');
	        jQuery.ajax({
			    type:"POST",
			    url:"<?php echo admin_url('admin-ajax.php'); ?>",
			    data: {action:'my_special_ajax_call_get_municipal_zipcode',municipal_id:municipal_id},
			    dataType: "json",
			    success:function(res)
			    {

				    var zipcode_option = '';
			    	var zipcode_option = '<option>Select ZipCode</option>';
				    $.each(res.municipal_zip, function (key, val) 
				    {
				    	var zips = val.zip_code.split("-");

				    	if (zips.length == 2) 
				    	{
				    		var i = zips[0];
				    		var j = zips[1];
				    		for (var i = i; i <= j; i++) {
				        		zipcode_option += '<option value="'+i+'">'+i+'</option>';
				    		}

				    	} 
				    	else 
				    	{
				        	zipcode_option += '<option value="'+zips+'">'+zips+'</option>';

				    	}



				    });

					jQuery('#select_zip').html('');
				    jQuery('#select_zip').html(zipcode_option);
				    jQuery('#select_zip').prop('disabled', false);
					jQuery('#select_zip').parent().addClass('done');

			    }
			});

		});


	</script>

<?php
}




/* Get States */
add_action('wp_ajax_my_special_ajax_call_get_state_list', 'get_states');
add_action('wp_ajax_nopriv_my_special_ajax_call_get_state_list', 'get_states');
function get_states()
{

	global $wpdb;
	$table_name = 'states';
	$states = $wpdb->get_results( "SELECT * FROM $table_name");

    echo json_encode(array('success' => true , 'states' => $states));

    wp_die();

}



/* Get provinces */
add_action('wp_ajax_my_special_ajax_call_get_province_list', 'get_province');
add_action('wp_ajax_nopriv_my_special_ajax_call_get_province_list', 'get_province');
function get_province()
{
	$state_id = $_POST['state_id'];
	global $wpdb;
	$table_name = 'provinces';
	$provinces = $wpdb->get_results( "SELECT * FROM $table_name WHERE state_id = $state_id");

    echo json_encode(array('success' => true , 'provinces' => $provinces));

    wp_die();

}


/* Get municipal & zip */
add_action('wp_ajax_my_special_ajax_call_get_municipal_list', 'get_municipal');
add_action('wp_ajax_nopriv_my_special_ajax_call_get_municipal_list', 'get_municipal');
function get_municipal()
{

	$provinces_id = $_POST['provinces_id'];
	global $wpdb;
	$table_name = 'municipals';
	$municipal = $wpdb->get_results( "SELECT * FROM $table_name WHERE provinces_id = $provinces_id");

    echo json_encode(array('success' => true , 'municipal' => $municipal));

    wp_die();

}



/* Get municipal & zip */
add_action('wp_ajax_my_special_ajax_call_get_municipal_zipcode', 'get_municipal_zipcode');
add_action('wp_ajax_nopriv_my_special_ajax_call_get_municipal_zipcode', 'get_municipal_zipcode');
function get_municipal_zipcode()
{

	$municipal_id = $_POST['municipal_id'];
	global $wpdb;
	$table_name = 'municipals';
	$municipal = $wpdb->get_results( "SELECT * FROM $table_name WHERE id = $municipal_id");

    echo json_encode(array('success' => true , 'municipal_zip' => $municipal));

    wp_die();

}


