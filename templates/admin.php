<script>

function airweave_upload_handleFiles(files){
  var fr = new FileReader()
  fr.onload = function (ev) {
    //wallet = JSON.parse(ev.target.result)
    document.getElementById("keyfile").value = ev.target.result;
  }
  fr.readAsText(files[0])
}

function airweave_upload_onremove(){
  document.getElementById("keyfile").value = "";
}

</script>

<?php settings_errors();?>
<form method="post" action="options.php">
  <?php settings_fields('arweave-upload-group'); ?>
  <?php do_settings_sections('arweave_upload'); ?>
  <?php submit_button();?>
</form>

<h1>Post Transaction ID Table</h1>
<table cellpadding="10" border="1">
  <tr>
    <th> Post Title </th>
    <th> Arweave Transaction ID(s) </th>
  </tr>

  <?php
  $args = array(
      'meta_key'  => 'arweave_txn_id',
  );
  $the_query = new WP_Query($args);

  // The Loop
  if ( $the_query->have_posts() ) {
    echo '<tr>';
      $posts = $the_query->posts;
      foreach ($posts as $post) {

        $custom_fields = get_post_custom($post->ID);
        $field = $custom_fields['arweave_txn_id'];

        $field_display = '';
        foreach($field as $key => $value){
          $field_display = $field_display . '<p>' . $value . '</p>';
        }

        echo '<th>' . $post->post_title . '</th>';
        echo '<th>' . $field_display . '</th>';
      }
    echo '</tr>';
  }

 ?>

</table>
