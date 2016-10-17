<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends CI_Controller {

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
		$this->load->library("session");
	}
	public function search_products()
	{
		$products=null;
		$product=$this->input->post("product");
		$group=$this->input->post("group");
		$this->db->distinct();
		if(strlen($group)>0){
			$this->db->select("product.name AS product_name,product.id AS product_id,product_stock.id AS product_stock_id, product_stock.stock_date AS stock_date,product_stock.quantity AS quantity, product_stock.unit_price AS unit_price");
			$this->db->from("product");
			$this->db->join("product_stock","product.id=product_stock.product_id");
			$this->db->join("group_product_link","group_product_link.product_id=product.id");
			$this->db->join("group","group.id=group_product_link.group_id");
			$this->db->where("group.name LIKE '%$group%'");
			$this->db->where("product_stock.quantity > 0");
			$this->db->order_by("product.name");
			$this->db->order_by("product_stock.stock_date");
			$products=$this->db->get();
		}
		else if(strlen($product)>0){
			$this->db->select("product.name AS product_name,product.id AS product_id,product_stock.id AS product_stock_id, product_stock.stock_date AS stock_date,product_stock.quantity AS quantity, product_stock.unit_price AS unit_price");
			$this->db->from("product");
			$this->db->join("product_stock","product.id=product_stock.product_id");
			$this->db->where("product.name LIKE '%$product%'");
			$this->db->where("product_stock.quantity > 0");
			$this->db->order_by("product.name");
			$this->db->order_by("product_stock.stock_date");
			$products=$this->db->get();
		}
		if($products!=null&&$products->num_rows()>0){
			$info=array();
			$info["info_type"]="show_products";
			$x=array();
			foreach($products->result()as $row){
				array_push($x,$row);
			}
			$info["data"]=$x;
		}
		else{
			$info=array(
				"info_type"=>"product_search_error"
			);
		}
		$this->load->view('dashboard',$info);
	}
	public function graphs(){
		$this->load->view('product_graphs');
	}
	public function finance(){
		$this->load->view('products_finance');
	}
	public function stock(){
		$this->load->model('products_model');
		$client_id=$this->session->userdata("client_id");
		$prducts_all=$this->products_model->get_products_all($client_id);
		$groups=$this->products_model->get_groups_all($client_id);
		$x=array();
		$y=array();
		if($prducts_all!=null){
			foreach($prducts_all->result()as $row){
				array_push($x,$row);
			}
		}
		if($groups!=null){
			foreach($groups->result()as $row){
				array_push($y,$row);
			}
		}
		$tuple=array(
			"prducts_all"=>$x,
			"groups"=>$y
		);
		$this->load->view('products_stock',$tuple);
	}
	public function sales(){
		$this->load->view('products_sales');
	}
	public function records(){
		$this->load->view('products_records');
	}
	public function details(){
		$this->load->view('products_details');
	}
	public function summary(){
		$this->load->view('products_summary');
	}
	public function group_id_check($id){
		if($id==0){
			return false;
			$this->validation->set_message("group_id_error","Must select group");
		}
		else{
			return true;
		}
	}
	public function product_id_check($id){
		if($id==0){
			return false;
			$this->validation->set_message("product_id_error","Must select product");
		}
		else{
			return true;
		}
	}
	public function stock_product(){
		$this->load->library('form_validation');
		$fields["product"]="trim|required|callback_product_id_check";
		$fields["year"]="trim|required";
		$fields["month"]="trim|required";
		$fields["day"]="trim|required";
		$fields["quantity"]="trim|required";
		$fields["unit_price"]="trim|required";
		$fields["group"]="trim|required|callback_group_id_check";
		$this->form_validation->set_rules($fields);
		if ($this->form_validation->run() == FALSE){
			$product_id=$this->input->get('product');
			$year=$this->input->get('year');
			$month=$this->input->get('month');
			$day=$this->input->get('day');
			$quantity=$this->input->get('quantity');
			$unit_price=$this->input->get('unit_price');
			$group_id=$this->input->get('group');
			if($month=="Jan"){
				$month="01";
			}
			else if($month=="Feb"){
				$month="02";
			}
			else if($month=="Mar"){
				$month="03";
			}
			else if($month=="Apr"){
				$month="04";
			}
			else if($month=="May"){
				$month="05";
			}
			else if($month=="Jun"){
				$month="06";
			}
			else if($month=="Jul"){
				$month="07";
			}
			else if($month=="Aug"){
				$month="08";
			}
			else if($month=="Sept"){
				$month="09";
			}
			else if($month=="Oct"){
				$month="10";
			}
			else if($month=="Nov"){
				$month="11";
			}
			else if($month=="Dec"){
				$month="12";
			}
			$date=$year."-".$month."-".$day;
			$data=array(
				"quantity"=>$quantity,
				"unit_price"=>$unit_price,
				"stock_date"=>$date,
				"product_id"=>$product_id
			);
			$this->db->insert("product_stock",$data);
			$data2=array(
				"group_id"=>$group_id,
				"product_id"=>$product_id
			);
			$this->db->insert("group_product_link",$data2);
				
			$client_id=$this->session->userdata("client_id");
			$this->load->model("products_model");
			$prducts_all=$this->products_model->get_products_all($client_id);
			$groups=$this->products_model->get_groups_all($client_id);
			$x=array();
			$y=array();
			if($prducts_all!=null){
				foreach($prducts_all->result()as $row){
					array_push($x,$row);
				}
			}
			if($groups!=null){
				foreach($groups->result()as $row){
					array_push($y,$row);
				}
			}

			$tuple=array(
				"prducts_all"=>$x,
				"groups"=>$y
			);
			$this->load->view('products_stock',$tuple);
		}
		else{
			//error message
		}
	}
	public function add_group(){
		//$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('group_name', 'Group_name', 'required');
		$this->form_validation->set_rules('group_description', 'Group_description', 'required');
		if ($this->form_validation->run() == FALSE){
			$group_name=$this->input->get('group_name');
			$client_id=$this->session->userdata("client_id");
			$this->db->select("name");
			$this->db->from("group");
			$this->db->where("name",$group_name);
			$this->db->where("client_id",$client_id);
			$x=$this->db->get();
			if($x->num_rows()==0){
				$group_description=$this->input->get('group_description');
				
				$tuple=array(
					"name"=>$group_name,
					"description"=>$group_description,
					"client_id"=>$client_id
				);
				$this->db->insert("group",$tuple);
			}
			$this->load->model("products_model");
			$prducts_all=$this->products_model->get_products_all($client_id);
			$groups=$this->products_model->get_groups_all($client_id);
			$x=array();
			$y=array();
			if($prducts_all!=null){
				foreach($prducts_all->result()as $row){
					array_push($x,$row);
				}
			}
			if($groups!=null){
				foreach($groups->result()as $row){
					array_push($y,$row);
				}
			}
			$tuple=array(
				"prducts_all"=>$x,
				"groups"=>$y
			);
			$this->load->view('products_stock',$tuple);
			
		}
		else{
			//error message
		}
	}
	public function add_client_link($product_id,$client_id){
		$tuple=array(
			"client_id"=>$client_id,
			"product_id"=>$product_id,
			"date"=>date("Y-m-d")
		);
		$this->db->from("clients_products_link");
		$this->db->where("client_id",$client_id);
		$this->db->where("product_id",$product_id);
		$x=$this->db->get();
		if($x->num_rows()==0){
			$this->db->insert("clients_products_link",$tuple);
		}
	}
	public function get_product($product_name){
		$this->db->select("name,id");
		$this->db->from("product");
		$this->db->where("name",$product_name);
		$x=$this->db->get();
		return $x;
	}
	public function add_product(){
		//$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('product_name', 'Product_name', 'required');
		$this->form_validation->set_rules('product_description', 'Product_description', 'required');
		if ($this->form_validation->run() == FALSE){
			$product_name=$this->input->get('product_name');
			$x=$this->get_product($product_name);
			$client_id=$this->session->userdata("client_id");
			if($x->num_rows()==0){
				$product_description=$this->input->get('product_description');
				$tuple=array(
					"name"=>$product_name,
					"description"=>$product_description,
				);
				$this->db->insert("product",$tuple);
				$x=$this->get_product($product_name);
				$row=$x->row();
				$product_id=$row->id;
				$this->add_client_link($product_id,$client_id);
			}
			else{
				$row=$x->row();
				$product_id=$row->id;
				$this->add_client_link($product_id,$client_id);
			}
			$this->load->model("products_model");
			$prducts_all=$this->products_model->get_products_all($client_id);
			$groups=$this->products_model->get_groups_all($client_id);
			$x=array();
			$y=array();
			if($prducts_all!=null){
				foreach($prducts_all->result()as $row){
					array_push($x,$row);
				}
			}
			if($groups!=null){
				foreach($groups->result()as $row){
					array_push($y,$row);
				}
			}
			$tuple=array(
				"prducts_all"=>$x,
				"groups"=>$y
			);
			$this->load->view('products_stock',$tuple);
		}
		else{
			//error message
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */