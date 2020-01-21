<?php

/**
 * Plugin Name:       Test Task
 * Description:       Test Plugin.
 * Version:           1.0.0
 * Author:            Kate
 */

add_action( 'woocommerce_init', function(){
  if ( !is_admin() ) {
    if ( !WC()->session->has_session() ) {
      WC()->session->set_customer_session_cookie( true );
    }
  }
} );

function test_task_get_product_view() {
  $count = get_post_meta( get_the_ID(), 'post_views_count', true );
  
  return $count ? $count : 0;
}

function test_task_set_product_view() {
  global $woocommerce;
  $post_id = get_the_ID();
  $viewed_products = WC()->session->get( 'viewed_products' );

  if ( !empty( $viewed_products ) ) {
    if ( in_array( $post_id, $viewed_products ) ) {
      return;
    }
  } else {
    $viewed_products = [];
  }

  $key = 'post_views_count';
  $count = (int) get_post_meta( $post_id, $key, true );
  $count++;
  $viewed_products[] = $post_id;
  WC()->session->set( 'viewed_products', $viewed_products );
  update_post_meta( $post_id, $key, $count );
}

function test_task_get_last_purchase_date() {
  $date = get_post_meta( get_the_ID(), 'last_purchase_date', true );
  
  return !empty( $date ) ? "Дата последней покупки: $date" : '';
}

add_action( 'woocommerce_thankyou', 'test_task_set_purchase_date' );
function test_task_set_purchase_date( $order_id ) {
  $order = wc_get_order( $order_id );
  if ( !empty( $order ) ) {
    foreach ( $order->get_items() as $item ) {
      $product = $item->get_product();
      $product_id = $product->get_id();
      if ( $product_id ) {
        update_post_meta( $product_id, 'last_purchase_date', $order->get_date_created()->format( 'd.m.Y' ) );
      }
    }
  }
}