<?php
/*
Template: IK Watches DB Data - DB Connection Template
Author: Gabriel Caroprese / Inforket.com
Update Date: 09/10/2021
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$watches = new Ik_Watches();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['valuehostname']) && isset($_POST['valueusername']) && isset($_POST['valuedbname']) && isset($_POST['valuepassword'])){

    // I get the values from the form
    
    $valuehostname = sanitize_text_field($_POST['valuehostname']);
    $valuedbname = sanitize_text_field($_POST['valuedbname']);
    $valueusername = sanitize_text_field($_POST['valueusername']);
    $valuepassword = sanitize_text_field($_POST['valuepassword']);

    
    /* 
       I check the DB access data
    */
    
    $config_wchsdbd  = array (
            'valuehostname' =>$valuehostname,
            'valuedbname' => $valuedbname,
            'valueusername' =>$valueusername,
            'valuepassword' =>$valuepassword,
    );       
    
    update_option('ik_wchsdbd_config', $config_wchsdbd);
    
}
?>
<style>
.error{ display: none; }
label, input {
    display: block;
    width: 200px;
    text-align: center;
}
label{ margin-bottom: 20px; }
label span {
    padding-bottom: 2px;
    display: block;
}
input[type=submit] {
    color: #fff;
    border: 0;
    cursor: pointer;
    padding: 7px 15px;
}
#ik_wchsdbd_connection_test{
    display: inline-block;
    padding: 15px;
    color: #fff;
    min-width: 167px;
    text-align: center;
    font-size: 15px;
    text-transform: uppercase;
    margin-top: 30px;
    border: 2px solid #ccc;
}
.ik_errorconnection{
    background: red;
}
.ik_okconnection{
    background: green;
}
</style>
<div id="panel-form-wchsdbd">
    <div class="ik_wchsdbd_panel">
    <h1>Watches DB Connector</h1>
    <form action="" method="post" id="db-wchsdbd-form" enctype="multipart/form-data" autocomplete="no">
        
        <?php 
            // I check existing values for connecting to DB
            $valueHostname = $watches->get_dataconfig('valuehostname');
            $valueDBname = $watches->get_dataconfig('valuedbname');
            $valueusername = $watches->get_dataconfig('valueusername');
            $valuepassword = $watches->get_dataconfig('valuepassword');
        ?>
        
        <label>
            <span>DB Server</span>
            <input required type="text" name="valuehostname" value="<?php echo $valueHostname; ?>" placeholder="Enter the hostname or IP" autocomplete="off" />
        </label>        
        <label>
            <span>DB Name</span>
            <input required type="text" name="valuedbname" value="<?php echo $valueDBname; ?>" placeholder="Enter the name of the DB" autocomplete="off" />
        </label>
        <label>
            <span>DB User</span>
            <input required type="text" name="valueusername" value="<?php echo $valueusername; ?>" placeholder="Enter the username" autocomplete="off" />
        </label>
        <label>
            <span>DB User Password</span>
            <input required type="password" name="valuepassword" onfocus="this.removeAttribute('readonly');" value="<?php echo $valuepassword; ?>" placeholder="Enter the password" autocomplete="new-password" />
        </label>

    	<input type="submit" class="button-primary" value="Save">
    </form>
    
    <?php 
    // I show the state of the connection
    echo $watches->db_connection_check();
    ?>
</div>