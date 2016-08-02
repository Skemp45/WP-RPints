<?php
// //Insert data into table
function RPDB_insert_data($apiK, $apiS) {
 global $wpdb;
 $table_name = $wpdb->prefix . 'raspberrypints';
  $results = $wpdb->get_row( "SELECT id FROM ". $table_name." LIMIT 1" );
  $apiId = $results->id;
  if($apiId < 1){
    $wpdb->insert(
     $table_name,
     array(
       'api_key' => $apiK,
       'api_secret' => $apiS
     )
   );
  }
  else
  {
  $wpdb->update(
    $table_name,
    array(
      'api_key' => $apiK,
      'api_secret' => $apiS
    ),
    array(
      'id' => 1
    )
  );
  }

  register_activation_hook( __FILE__, 'RPDB_insert_data' );

  // do_action('showSuccess');
  // do_action('refreshChanges');
}

  if (isset($_POST["apiKey"])){
    $apiK = $_POST["apiKey"];
    $apiS = $_POST["apiSecret"];
    RPDB_insert_data($apiK, $apiS);
  }

?>
