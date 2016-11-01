<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller
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
        $this->load->model('Category_model');
        $this->load->helper(array('form', 'url', 'image'));
        $this->load->library('form_validation');
    }

    //List of all categories
    public function categories()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $categories = $this->Category_model->get_categories();
            if(!$categories)
            {
                echo json_encode(array('status' => "error", "error_message" => "No category found!"));
                return;
            }
            echo json_encode(array('status' => "success", "categories" => $categories));
            return;
        }
    }

    //Return all courses in a category
    public function courses_category()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $category_id = $this->input->get('category_id');
            $courses = $this->Category_model->get_courses_category($category_id);

            if(!$courses)
            {
                echo json_encode(array('status' => "error", "error_message" => "No courses found in this category!"));
                return;
            }

            echo json_encode(array('status' => "success", "courses" => $courses));
            return;
        }
    }

    //-------------------DASHBOARD-----------------------//

    //List of categories specified within limit and total count of categories
    public function categories_dashboard()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $categories = $this->Category_model->get_categories_limit($limit, $start);
            if(!$categories)
            {
                echo json_encode(array('status' => "error", "error_message" => "No category found!"));
                return;
            }
            $categoryTotal = $this->Category_model->getCategoryTotal();
            if(!$categoryTotal)
            {
                echo json_encode(array('status' => "error"));
                return;
            }
            echo json_encode(array("status" => "success","categories" => $categories, "categoryTotal" => $categoryTotal));
            return;
        }
    }

    //Returning a single category by it's ID
    public function category()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $categoryID = $_REQUEST["categoryID"];
            $category = $this->Category_model->get_category($categoryID);
            if(!$category)
            {
                echo json_encode(array('status' => "error", "error_message" => "No category found!"));
                return;
            }
            echo json_encode(array('status' => "success", "category" => $category));
            return;
        }
    }

    //Add a new Category in table
    public function addCategory()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));

            $category_data = array(
                'category_Name' => $data->category_Name,
                'category_Image' => $data->category_Image
            );


            $this->form_validation->set_data($category_data); //Setting Data
            $this->form_validation->set_rules($this->Category_model->getCategoryRegistrationRules()); //Setting Rules

            //Reload add Category page if Validation fails - api validation
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'categoryName_Error' => form_error('category_Name'),
                    'categoryImageError' => form_error('image_Path')
                );

                echo json_encode(array('status' => "error in validation", 'error_messages' => $error_data));
                return;
            }

            $imageName = substr($category_data['category_Image'],strrpos($category_data['category_Image'],"/")+1);
            $url = $category_data['category_Image'];
            $contents = file_get_contents($url);
            $save_path="./uploads/". $imageName;

            file_put_contents($save_path,$contents);
            $category_data['category_Image'] =  $imageName;
            $category_data['category_ThumbImage'] =  createThumbnail($imageName);

            if ($this->Category_model->insertCategory($category_data)) {
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

    public function editCategory()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));

            $categoryID = $data->category_ID;
            $category_data = array(
                'category_Name' => $data->category_Name
            );

            /* $this->form_validation->set_data($category_data); //Setting Data
            $this->form_validation->set_rules($this->Category_model->getCategoryEditRules()); //Setting Rules

            //Reloading edit category page if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'categoryName_Error' => form_error('category_Name')
                );

                echo json_encode(array('status' => "Error in Validation", 'error_messages' => $error_data));
                return;
            }
            }*/

            //If new image was uploaded
            if(array_key_exists('category_Image', $data))
            {
                $categoryImage = $data->category_Image;

                //Deleting previous images from API server
                $category_PrevImage = $data->category_PrevImage;
                $category_PrevThumbImage = $data->category_PrevThumbImage;
                unlink("uploads/".$category_PrevImage);
                unlink("uploads/".$category_PrevThumbImage);


                $imageName = substr($categoryImage,strrpos($categoryImage,"/")+1);
                $url = $categoryImage;
                $contents = file_get_contents($url);
                $save_path="./uploads/". $imageName;

                file_put_contents($save_path,$contents);
                $category_data['category_Image'] =  $imageName;
                $category_data['category_ThumbImage'] =  createThumbnail($imageName);

            }
            if ($this->Category_model->updateCategory($categoryID,$category_data)) {
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

    public function deleteCategory()
    {
        if($this->input->server('REQUEST_METHOD') == "GET") {
            $categoryID = $_REQUEST["categoryID"];
            $category = $this->Category_model->get_category($categoryID);

            //Delete images from server
            unlink("uploads/".$category['category_ThumbImage']);
            unlink("uploads/".$category['category_Image']);
            if($this->Category_model->deleteCategory($categoryID))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error"));
            return;

        }
    }




}