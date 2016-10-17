<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Users extends CI_Controller {

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
		$this->load->model("users_model");
		$users=$this->users_model->get_users();
		$data=array(
			"users"=>$users
		);
		$this->load->view('user_view',$data);
	}
	public function edit($id)
	{
		$this->load->library("session");
		$this->session->set_userdata("editid",$id);
		$this->load->model("users_model");
		$user=$this->users_model->get_user($id);
		$user=$user->row();
		$this->load->view("edit_user_view",array("user"=>$user));
	}
	public function update_user(){
		$type=$this->input->post("type");
		$activate=$this->input->post("activate");
		$this->load->library("session");
		$id=$this->session->userdata("editid");
		$this->load->model("users_model");
		$this->users_model->edit_type($id,$type);
		$this->users_model->edit_activation($id,$activate);
		$users=$this->users_model->get_users();
		$data=array(
			"users"=>$users
		);
		$this->load->view('user_view',$data);
	}

	
	
}

?>