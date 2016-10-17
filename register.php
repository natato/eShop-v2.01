<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Register extends CI_Controller {

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
		$this->load->view('register_view');
	}
	public function admin_panel(){
		$this->load->model("users_model");
		$users=$this->users_model->get_users();
		$users->result();
		$data=array(
			"users"=>$users
		);
		$this->load->view('admin_panel_view',$data);
	}
	public function register_user()
	{
		$this->load->library("form_validation");
		$this->form_validation->set_rules('fname', 'Fname', 'required');
		$this->form_validation->set_rules('lname', 'Lname', 'required');
		$this->form_validation->set_rules('mobile_N', 'Mobile_N', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('region', 'Region', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required|matches[repassword]');
		$this->form_validation->set_rules('repassword', 'Repassword', 'required');
		if($this->form_validation->run()==TRUE){
			$fname=$this->input->post('fname');
			$lname=$this->input->post('lname');
			$mobile_N=$this->input->post('mobile_N');
			$email=$this->input->post('email');
			$region=$this->input->post('region');
			$type=$this->input->post('type');
			$pass=md5($this->input->post('password'));
			$data=array(
				"username"=>$email,
				"firstname"=>$fname,
				"lastname"=>$lname,
				"password"=>$pass,
				"activated"=>1
			);
			$this->db->insert('user',$data);
			$this->db->select("id,lastname");
			$this->db->from('user');
			$this->db->where("username",$email);
			$x=$this->db->get();
			$result=$x->row();
			$id=$result->id;
			$data_user_details=array(
				"user_id"=>$id,
				"mobile_number"=>$mobile_N,
				"region"=>$region
			);
			$this->db->insert('user_details',$data_user_details);
			$client_data=array(
				"user_id"=>$id,
				"type"=>$type,
			);
			$this->db->insert('clients',$client_data);
			//echo "correct "
			$subject="eshop registration verification";
			$lkn=site_url('login/activate')."?token=".md5($email);
			$message="This email has been sent to you as part of your eshop registration. Please click on the link below to verify
			your account<br><a href='$lkn'>link</a>";
			$this->load->library("emailer");
			$this->load->library("emailer/emailer.php");
			$emailer=new emailer();
			$emailer->sendEmail($email,$message,$subject);
			
			//display  login view
			$this->load->view('login_view');
			
		}
		else{
			$this->load->view('register_view');
		}
	}
	
}

?>