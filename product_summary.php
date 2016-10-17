<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_summary extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('products_summary');
	}
	public function count_products()
	{
		$this->load->library("session");
		$client_id=$this->session->userdata("client_id");
		$this->load->model("products_model");
		$products=$this->products_model->get_products($client_id,true);
		echo $products->num_rows();
	}
	public function products_array()
	{
		$this->load->library("session");
		$from=$this->input->post("from");
		$client_id=$this->session->userdata("client_id");
		$this->load->model("products_model");
		$prod_set=$this->products_model->get_products_set($client_id,$from);
		if($prod_set!=null){
			$product_summary_array=array();
			foreach($prod_set->result() as $r):
				$summary=$this->products_model->get_products_summary($r->product_id);
				if($summary->num_rows()==1){
					$row=$summary->row();
					$summary_item=array();
					$summary_item["product_id"]=$row->product_id;
					$summary_item["product_name"]=$r->product_name;
					$summary_item["stock_date"]=$row->stock_date;
					$summary_item["stock_quantity"]=$row->stock_quantity;
					$quant_sold=$this->products_model->get_quantity_sold($row->product_id);
					$quant_sold=$quant_sold->row();
					$quantity_sold=$quant_sold->quantity_sold;
					$summary_item["quantity_in_stock"]=$summary_item["stock_quantity"]-$quantity_sold;
					array_push($product_summary_array,$summary_item);
				}
				else{
					$row_max_date=$summary->row();
					foreach($summary->result() as $row){
						if($row!=$row_max_date){
							$row_max_date->stock_quantity=$row_max_date->stock_quantity+$row->stock_quantity;
							($row_max_date->stock_date>$row->stock_date)?:$row_max_date->stock_date=$row->stock_date;
						}
						
					}
					$summary_item=array();
					$summary_item["product_id"]=$row_max_date->product_id;
					$summary_item["product_name"]=$r->product_name;
					$summary_item["stock_date"]=$row_max_date->stock_date;
					$summary_item["stock_quantity"]=$row_max_date->stock_quantity;
					$quant_sold=$this->products_model->get_quantity_sold($row_max_date->product_id);
					$quant_sold=$quant_sold->row();
					$quantity_sold=$quant_sold->quantity_sold;
					$summary_item["quantity_in_stock"]=$summary_item["stock_quantity"]-$quantity_sold;
					array_push($product_summary_array,$summary_item);

				}		
			endforeach;
			$product_summary_array=json_encode($product_summary_array);
			echo $product_summary_array;
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */