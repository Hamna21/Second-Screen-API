<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lecture_Reference extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');

        $userName = "Developer";
        $password = "1234";
        $authentication = $this->input->server('PHP_AUTH_USER');
        if(empty( $authentication))
        {
            echo json_encode(array('status' => "error", "error_message" => "Authentication not found."));
            die();
        }
        if($userName!= $this->input->server('PHP_AUTH_USER') || $password != $this->input->server('PHP_AUTH_PW'))
        {
            echo json_encode(array('status' => "error", "error_message" => "Invalid API keys."));
            die();
        }

        $this->load->model('Reference_model');

        $this->load->helper(array('form', 'url', 'image'));
        $this->load->library('form_validation');
    }

    public function addReference()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $reference_data = array(
                'lecture_id' => $data->lecture_id,
                'time' => $data->time,
                'type' => $data->type,
                'value' => $data->value
            );

            if(!($reference_data['type']== "lecture"))
            {
                $reference_data['image'] = $data->image;
                $imageName = substr($reference_data['image'],strrpos($reference_data['image'],"/")+1);
                $url = $reference_data['image'];
                $contents = file_get_contents($url);
                $save_path="./uploads/". $imageName;

                file_put_contents($save_path,$contents);
                $reference_data['image'] =  $imageName;
                $reference_data['thumbImage'] =  createThumbnail($imageName);
            }



            if ($this->Reference_model->insertReference($reference_data)) {
                echo json_encode(array('status' => "success"));
                return;
            }
            else
            {
                echo json_encode(array('status' => "error in db"));
                return;
            }
        }
    }


}