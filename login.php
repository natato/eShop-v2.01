<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {

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
		$this->load->view('login_view');
	}
	public function transfer_dashboard()
	{
		$this->load->library("session");
		$uname=$this->input->post("username");
		$pass=$this->input->post("password");
		$this->db->where("username",$uname);
		$this->db->where("password",md5($pass));
		$this->db->from("user");
		$user=$this->db->get();
		if($user->num_rows()==1){
			$user=$user->row();
			$id=$user->id;
			$activated=$user->activated;
			$usertype=$user->type;
			if($activated==1){
				$this->db->from("clients");
				$this->db->where("user_id",$id);
				$client=$this->db->get();
				$row=$client->row();
				$client_id=$row->id;
				$client_type=$row->type;
				$this->session->set_userdata("client_id",$client_id);
				$this->session->set_userdata("client_type",$client_type);
				$this->session->set_userdata("usertype",$usertype);
				$name=$user->firstname." ".$user->lastname;
				$this->session->set_userdata("name",$name);
				$this->session->set_userdata("id",$user->id);
				$this->session->set_userdata("username",$user->username);
				$this->load->model("products_model");
				$client_id=$this->session->userdata("client_id");
				$products=$this->products_model->get_products($client_id);
				$info=array();
				if($products!=null){
					$info["info_type"]="show_products";
					$x=array();
					foreach($products->result()as $row){
						array_push($x,$row);
					}
					$info["data"]=$x;
					$info["type"]=$usertype;
				}
				$this->load->view('dashboard',$info);
			}
		}
		else{
			$this->session->set_userdata("error","Wrong password or username");
			$this->load->view('login_view');
		}
		
	}
	public function dashboard(){
		$this->load->library("session");
		$this->load->model("products_model");
		$client_id=$this->session->userdata("client_id");
		$usertype=$this->session->userdata("usertype");
		$products=$this->products_model->get_products($client_id);
		$info=array();
		if($products!=null){
			$info["info_type"]="show_products";
			$x=array();
			foreach($products->result()as $row){
				array_push($x,$row);
			}
			$info["data"]=$x;
			$info["type"]=$usertype;
		}
		$this->load->view('dashboard',$info);
	}
	public function logout()
	{
		$this->load->view('login_view');
		$this->session->unset_userdata("client_id");
	}
	public function load_register(){
		$this->load->view("register_view");
	}
	public function register(){
	}
	public function activate(){
		$token=$this->input->get('token');
		$sql="UPDATE user SET activated=1 WHERE md5(username)='$token'";
		$this->db->query($sql);
		$this->load->view('login_view');
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */