<?php
/**
 * Created by PhpStorm.
 * User: Hardner07@gmail.com
 * Date: 6/21/2019
 * Time: 9:45 PM
 */

class Order_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_orders() {
        $query = $this->db->get('orders');
        return $query->result();
    }

    public function add_order()
    {
        $table = $this->input->post('table');
        $this->db->query("
                  INSERT INTO orders (order_table, order_status, order_printed)
                              VALUES ('$table', 'draft', 'false')
        ");
        $this->session->current_order = $this->db->insert_id();
    }

    public function add_items($item)
    {
        $order_id = $this->session->current_order;
        $item_id = $item->item_id;
        $item_count = $this->input->post('item_count');
        $comment = $this->input->post('item_comment');
        $this->db->query("
                  INSERT INTO order_items (order_id, item_id, item_count, comment, delivered)
                              VALUES ('$order_id', '$item_id', '$item_count', '$comment', 'false')
        ");
    }

    public function get_current_price() : float
    {
        $price = 0.00;
        $order_id = $this->session->current_order;
        $query = $this->db->query("SELECT * FROM order_items WHERE order_id = '$order_id'");
        foreach($query->result() as $item) {
            $query = $this->db->query("SELECT item_price FROM items WHERE item_id = $item->item_id");
            $price += $query->row()->item_price * $item->item_count;
        }
        return $price;
    }

    public function delete_order() {
        $order_id = $this->input->post('order_id');
        $this->db->query("DELETE FROM orders WHERE order_id = $order_id");
        $this->db->query("DELETE FROM order_items WHERE order_id = $order_id");
        $this->session->unset_userdata('current_order');
    }

    public function open_order() {
        $this->session->current_order = $this->input->post('order_id');
    }
}