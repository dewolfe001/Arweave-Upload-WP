<?php

include ARWEAVE_UPLOAD_PLUGIN_PATH . '/vendor/autoload.php';

class ArweaveUploadPublish{
  public function register(){
    add_action('publish_post',  array($this, 'arweave_upload'), 10, 2);
  }

  /**
  * uploads the data to the arweave
  */
  function arweave_upload($id, $post_obj){

    // if(wp_is_post_revision( $id )) { // if post is just being revised, then don't publish
    //   return;
    // }

    $arweave = new \Arweave\SDK\Arweave('http', '209.97.142.169', 1984);

    $wallet = $this-> get_wallet();

    //if wallet is not valid, do not proceed
    if(!$wallet){
      return;
    }

    $transaction = $arweave->createTransaction($wallet, [
      'data' => $this->stringify_post($post_obj),
      'tags' => [
          'Content-Type' => 'WP Post'
      ]
    ]);

    $transaction_id = $transaction->getAttribute('id');

    $meta_key = 'arweave_txn_id';

    add_post_meta($post_obj->ID, $meta_key, $transaction_id);

    //$arweave->commit($transaction);
  }

  /**
  * Uses the Wordpress custom keyfile setting to generate a new Arweave wallet
  */
  function get_wallet(){

    $keyfile = get_option("keyfile");

    if ($keyfile == "") {
        return NULL;
    }
    if (!json_decode($keyfile, true)) {
        return NULL;
    }

    return new Arweave\SDK\Support\Wallet(json_decode($keyfile, true));
  }

  /**
  * Takes a WP Post object and converts into a JSON string to store in Arweave
  */
  function stringify_post($wp_post){
    $parameters = array('ID', 'post_author', 'post_name', 'post_type',
    'post_title', 'post_date', 'post_date_gmt', 'post_content',
    'post_excerpt', 'post_status', 'comment_status', 'ping_status',
    'post_password', 'post_parent', 'post_modified',
    'post_modified_gmt', 'comment_count');

    //TODO allow user to set which fields to save perhaps?

    $result = array();
    foreach ($parameters as $p) {
      $result[$p] = $wp_post -> $p;
    }

    return json_encode($result);
  }

}