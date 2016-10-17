<?php
	class Products_model extends CI_Model{
		public function __construct(){
		}
		public function get_products($client_id,$stock=false){
			$this->db->distinct();
			if($stock==true){
				$this->db->select("product.name AS product_name,product.id AS product_id");
			}
			else{
				$this->db->select("product.name AS product_name,product.id AS product_id,product_stock.id AS product_stock_id, product_stock.stock_date AS stock_date,product_stock.quantity AS quantity, product_stock.unit_price AS unit_price");
			}
			$this->db->from("product");
			$this->db->join("product_stock","product.id=product_stock.product_id");
			$this->db->join("clients_products_link","clients_products_link.product_id=product_stock.product_id");
			$this->db->join("clients","clients_products_link.client_id=clients.id");
			$this->db->where("clients_products_link.client_id",$client_id);
			$this->db->order_by("product.name");
			$this->db->order_by("product_stock.stock_date");
			$products=$this->db->get();
			if($products->num_rows()>0){
				return $products;
			}
			else{
				return "none";
			}
		}
		public function get_quantity_sold($product_id){
			$this->db->select("SUM(sale.quantity_sold) AS quantity_sold");
			$this->db->from("sale");
			$this->db->where("sale.product_id",$product_id);
			return $this->db->get();
		}
		public function get_products_set($client_id,$from=0){
			$this->db->distinct();
			$this->db->select("product.name AS product_name,product.id AS product_id");
			$this->db->from("product");
			$this->db->join("clients_products_link","clients_products_link.product_id=product.id");
			$this->db->join("clients","clients_products_link.client_id=clients.id");
			$this->db->where("clients_products_link.client_id",$client_id);
			$this->db->limit(2,$from);
			$products=$this->db->get();
			if($products->num_rows()>0){
				return $products;
			}
			else{
				return null;
			}
		}
		
		public function get_products_summary($product_id){
			$this->db->distinct();
			$this->db->select("product_stock.stock_date AS stock_date,product_stock.quantity AS stock_quantity, product_stock.product_id AS product_id");
			$this->db->from("product_stock");
			$this->db->join("product","product.id=product_stock.product_id");
			$this->db->where("product_stock.product_id",$product_id);
			$summary=$this->db->get();
			return $summary;
		}
		public function get_products_all($client_id,$from=null){
			$this->db->distinct();
			$this->db->select("product.name AS product_name,product.id AS product_id");
			$this->db->from("product");
			$this->db->join("clients_products_link","clients_products_link.product_id=product.id");
			$this->db->where("clients_products_link.client_id",$client_id);
			if($from!=null){
				$this->db->limit($from,2);
			}
			$products=$this->db->get();
			if($products->num_rows()>0){
				return $products;
			}
			else{
				return null;
			}
		}
		public function get_groups_all($client_id){
			$this->db->distinct();
			$this->db->select("group.name AS group_name,group.id AS group_id");
			$this->db->from("group");
			$this->db->where("client_id",$client_id);
			$groups=$this->db->get();
			if($groups->num_rows()>0){
				return $groups;
			}
			else{
				return null;
			}
		}
		public function stock_product($product_id,$stockdate,$quantity,$unit_price){
			$tuple=array(
				"product_id"=>$product_id,
				"stock_date"=>$stockdate,
				"quantity"=>$quantity,
				"unit_price"=>$unit_price
			);
			$this->db->insert("stock_product",$tuple);

			$products=$this->db->get();
			if($products->num_rows()>0){
				return $products;
			}
			else{
				return null;
			}

		}
		public function add_product(){
			
		}
		
		
	}
?>