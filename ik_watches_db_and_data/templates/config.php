<?php
/*
Template: IK Watches DB Data - Config Template
Author: Gabriel Caroprese / Inforket.com
Update Date: 27/10/2021
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$watches = new Ik_Watches();

wp_enqueue_media();
    // Enqueue custom script that will interact with wp.media
    wp_enqueue_script( 'Logo_Upload_Watches', plugins_url( '../js/uploader-image.js' , __FILE__ ), array('jquery'), '0.1' );


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['logourl'])){
        $url_logo = esc_url_raw($_POST['logourl']);
        update_option('ik_watches_db_img', $url_logo);
    }
    if (isset($_POST['postaladdress'])){
        $postal_address = sanitize_textarea_field($_POST['postaladdress']);
        $postal_address = str_replace('\\', '', $postal_address);
        update_option('ik_watches_db_postaladdress', $postal_address);
    }
    if (isset($_POST['phone'])){
        $phone_inserted = sanitize_text_field($_POST['phone']);
        $phone_inserted = str_replace('\\', '', $phone_inserted);
        update_option('ik_watches_db_phone', $phone_inserted);
    }
    if (isset($_POST['mobile'])){
        $mobile_phone = sanitize_text_field($_POST['mobile']);
        $mobile_phone = str_replace('\\', '', $mobile_phone);
        update_option('ik_watches_db_mobile', $mobile_phone);
    }
    if (isset($_POST['email'])){
        $email_added = sanitize_email($_POST['email']);
        update_option('ik_watches_db_email', $email_added);
    }
    if (isset($_POST['bank_account'])){
        $bank_account = wp_kses( $_POST['bank_account'], array( 
            'a' => array(
                'href' => array(),
            ),            
            'div' => array(
                'style' => array(),
            ),
            'br' => array(),
            'strong' => array(),
        ) );
        update_option('ik_watches_db_bank_account', $bank_account);
    }
}


$URLlogo = get_option('ik_watches_db_img');
$postaladdress = get_option('ik_watches_db_postaladdress');
$phone = get_option('ik_watches_db_phone');
$mobile = get_option('ik_watches_db_mobile');
$email = get_option('ik_watches_db_email');
$bank_account = get_option('ik_watches_db_bank_account');


?>
<style>
.error{ display: none; }
#ik_wchsdbd_panel_form label, #ik_wchsdbd_panel_form input, #ik_wchsdbd_panel_form textarea {
    display: block;
    width: 200px;
    text-align: center;
}
#ik_wchsdbd_panel_form label{ margin-bottom: 20px; }
#ik_wchsdbd_panel_form label span {
    padding-bottom: 2px;
    display: block;
}
#ik_wchsdbd_panel_form textarea{
    height: 70px;
}
.ik_wchsdbd_panel label img {
    width: 200px;
    max-height: 200px;
}
.ik_wchsdbd_button_red{
    background: red! important;
    border-color: red! important;
}
</style>
<div id="ik_wchsdbd_panel_form">
    <div class="ik_wchsdbd_panel">
    <h1>Watches DB Config</h1>
    <form action="" method="post" id="ik_wchsdbd_db_form" enctype="multipart/form-data" autocomplete="no">
        <label for="upload_image">
            <span>Receipt Logo</span>
        <?php
        if ($URLlogo != false & $URLlogo != NULL){
            $image = '<img id="ik_wchsdbd_preview_image" src="'.$URLlogo.'" />';
            $buttonDelete = '<input type="button" class="button-primary ik_wchsdbd_button_red" value="Delete" id="ik_wchsdbd_delete_img">';
        } else {
            $image = '<img id="ik_wchsdbd_preview_image" src="" style="display: none;" />';
            $buttonDelete = '';
        }
        echo $image;
        ?>
            <input type="hidden" name="ik_wchsdbd_image_id" id="ik_wchsdbd_image_id" value="0" class="regular-text" />
            <input type='button' class="button" value="<?php esc_attr_e( 'Upload Image' ); ?>" id="ik_wchsdbd_media_manager"/>
            <input type="hidden" id="ik_wchsdbd_logo_url" name="logourl" value="<?php echo $URLlogo; ?>" /><?php  echo $buttonDelete; ?>
        </label>
        <label>
            <span>Postal Address</span>
            <textarea required name="postaladdress"><?php echo $postaladdress; ?></textarea>
        </label>
        <label>
            <span>Bank Account Details</span>
            <textarea required name="bank_account"><?php echo $bank_account; ?></textarea>
        </label>
        <label>
            <span>Phone</span>
            <input required type="text" name="phone" value="<?php echo $phone; ?>" autocomplete="off" />
        </label>
        <label>
            <span>Mobile</span>
            <input required type="text" name="mobile" value="<?php echo $mobile; ?>" autocomplete="off" />
        </label>
        <label>
            <span>Contact Email</span>
            <input required type="text" name="email" value="<?php echo $email; ?>" autocomplete="off" />
        </label>
    	<input type="submit" class="button-primary" value="Save">
    </form>
</div>