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
        $this->load->model('Lecture_model');


        $this->load->helper(array('form', 'url', 'image'));
        $this->load->library('form_validation');
    }

    //All references of a particular lecture + lecture name - for pagination purposes
    public function references_pagination()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $lecture_id = $this->input->get('lecture_id');
            $references = $this->Reference_model->get_references_limit($lecture_id, $limit, $start);

            if(!$references)
            {
                echo json_encode(array('status' => "error", "error_message" => "No reference found"));
                return;
            }

            //Getting lecture name of references
            $lecture = $this->Lecture_model->get_lecture($lecture_id);

            //Total count of references
            $referenceTotal = $this->Reference_model->get_referenceTotal($lecture_id);

            echo json_encode(array('status' => "success", 'lecture' => $lecture ,"references" => $references, 'referenceTotal' => $referenceTotal));
            return;
        }
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

    public function editReference()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $reference_id = $data->reference_id;
            $reference_data = array(
                'time' => $data->time,
                'type' => $data->type,
                'value' => $data->value
            );
            if(array_key_exists('image', $data))
            {
                $image = $data->image;

                //Deleting previous images from API server
               // $course_PrevImage = $data->course_PrevImage;
                //$course_PrevThumbImage = $data->course_PrevThumbImage;
                //unlink("uploads/".$course_PrevImage);
                //unlink("uploads/".$course_PrevThumbImage);

                $imageName = substr($image,strrpos(($image),"/")+1);
                $url = $image;
                $contents = file_get_contents($url);
                $save_path="./uploads/". $imageName;

                file_put_contents($save_path,$contents);
                $reference_data['image'] =  $imageName;
                $reference_data['thumbImage'] =  createThumbnail($imageName);

            }

            if ($this->Reference_model->updateReference($reference_id,$reference_data))
            {
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

    public function deleteReference()
    {
        if($this->input->server('REQUEST_METHOD') == "GET") {
            $reference_id = $_REQUEST["reference_id"];
            $reference = $this->Reference_model->get_reference($reference_id);

            //Delete images from API server
            if($reference->type != "lecture")
            {
                unlink("uploads/".$reference->image);
                unlink("uploads/".$reference->thumbImage);
            }


            if($this->Reference_model->deleteReference($reference_id))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error"));
            return;

        }
    }

}