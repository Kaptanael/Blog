<?php

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/CreatorJwt.php';

class Account extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array("api/user_model"));
        $this->load->library(array("form_validation"));
        $this->load->helper('security');
        $this->creatorJwt = new CreatorJwt();
        header("Content-type: application/json");
    }

    public function users_get()
    {
        $users = $this->user_model->get_users();

        if (count($users) > 0) {

            $this->response(array(
                "data" => $users
            ), REST_Controller::HTTP_OK);
        } else {

            $this->response(array(
                "data" => $users
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function register_post()
    {
        $name =  $this->security->xss_clean($this->input->post("name"));
        $email = $this->security->xss_clean($this->input->post("email"));
        $mobile = $this->security->xss_clean($this->input->post("mobile"));
        $upazila_id = $this->security->xss_clean($this->input->post("upazila_id"));

        $this->form_validation->set_rules("name", "Name", "trim|required|max_length[128]");
        $this->form_validation->set_rules("email", "Email", "trim|required|valid_email|max_length[128]");
        $this->form_validation->set_rules("mobile", "Mobile", "trim|required|max_length[11]");
        $this->form_validation->set_rules("upazila_id", "Upazila", "trim|required|integer");

        if ($this->form_validation->run() === FALSE) {

            $this->response(array(
                "message" => $this->form_validation->error_array()
            ), REST_Controller::HTTP_BAD_REQUEST);
        } else {

            $user = array(
                "name" => $name,
                "email" => $email,
                "mobile" => $mobile,
                "upazila_id" => $upazila_id
            );

            try {
                if ($this->user_model->insert_user($user)) {

                    $this->response(array(
                        "message" => "User has been created"
                    ), REST_Controller::HTTP_CREATED);
                } else {

                    $this->response(array(
                        "message" => "Failed to create user"
                    ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                }
            } catch (Exception $e) {
                $this->response(array(
                    "message" => "Failed to create user"
                ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function login_post()
    {
        $email = $this->security->xss_clean($this->input->post("email"));
        $password =  $this->security->xss_clean($this->input->post("password"));

        $this->form_validation->set_rules("email", "Email", "trim|required|valid_email|max_length[128]");
        $this->form_validation->set_rules("password", "Password", "trim|required|max_length[256]");

        if ($this->form_validation->run() === FALSE) {

            $this->response(array(
                "message" => $this->form_validation->error_array()
            ), REST_Controller::HTTP_BAD_REQUEST);
        } else {

            if (!empty($email) && !empty($password)) {

                $user = $this->user_model->get_user($email, $password);

                if ($user) {

                    $tokenData["id"] = $user[0]["id"];
                    $tokenData["name"] = $user[0]["name"];
                    $tokenData["role"] = "admin";
                    $tokenData["timeStamp"] = Date("Y-m-d h:i:s");
                    $token = $this->creatorJwt->GenerateToken($tokenData);
                    $this->response(array("token" => $token), REST_Controller::HTTP_OK);
                } else {

                    $this->response(array(
                        "message" => "Email or password is incorrect"
                    ), REST_Controller::HTTP_BAD_REQUEST);
                }
            }
        }
    }

    public function create_password_post()
    {
        //$data = json_decode(file_get_contents("php://input"));
        //$id = isset($data->id) ? $data->id : "";

        $id =  $this->security->xss_clean($this->input->post("id"));
        $password =  $this->security->xss_clean($this->input->post("password"));

        $this->form_validation->set_rules("id", "Id", "trim|required");
        $this->form_validation->set_rules("password", "Password", "trim|required|max_length[256]");

        if ($this->form_validation->run() === FALSE) {

            $this->response(array(
                "message" => $this->form_validation->error_array()
            ), REST_Controller::HTTP_BAD_REQUEST);
        } else {

            if ($this->user_model->is_user_exists($id)) {

                if ($this->user_model->insert_password($id, $password)) {

                    $this->response(array(
                        "message" => "Password has been created"
                    ), REST_Controller::HTTP_OK);
                } else {

                    $this->response(array(
                        "message" => "Failed to create password"
                    ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {

                $this->response(array(
                    "message" => "No user found to create password"
                ), REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }
}
