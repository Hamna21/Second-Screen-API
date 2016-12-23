<?php

class Category_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //--------SELECT----------
    //Return all categories
    public function get_categories()
    {
        $query = $this->db
            ->order_by('category_id', "DESC")
            ->get('category');
        return $query->result_array();
    }

    //Getting a single category by it's ID
    public function get_category($category_id)
    {
        $query = $this->db
            ->where('category_id', $category_id)
            ->get('category');

        return $query->row_array();
    }

    //Getting a category by name - To check unique name constraint
    public function get_category_name($category_id,$category_name)
    {
        $query = $this->db
            ->where('category_id !=', $category_id)
            ->where('category_name', $category_name)
            ->get('category');

        if($query->num_rows() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //Return all courses in a specific category
    public function get_courses_category($category_id)
    {
        $query = $this->db
            ->select('course_id, course_name, course_description, course_image')
            ->where('category_id', $category_id)
            ->get('course');

        return $query->result_array();
    }

    //Category Total Count
    public function getCategoryTotal()
    {
        $this->db->from('category');
        return $this->db->count_all_results();
    }

    //Getting all categories within limit - for pagination purposes
    public function get_categories_limit($limit, $start)
    {
        $query = $this->db
            ->limit($limit, $start)
            ->order_by('category_id', "DESC")
            ->get('category');

        return $query->result_array();
    }


    //------------INSERT------------//

    //Insert new Category
    public function insertCategory($categoryData)
    {
        if($this->db->insert('category', $categoryData))
        {
            return true;
        }
    }

    //--------UPDATE----------
    //Update a category by its ID
    public function updateCategory($categoryID, $categoryData)
    {
        $this->db->where("category_id", $categoryID);
        $this->db->update("category", $categoryData);
        return true;
    }

    //--------DELETE---------
    //Delete a course by its ID
    public function deleteCategory($categoryID)
    {
        $this->db->where('category_id', $categoryID);
        $this->db->delete('category');
        return true;
    }


    //------------AJAX HELPER FUNCTIONS-----------//

    //Finding a category by its Name - exist in DB or not
    public function getCategory_Name($categoryName)
    {
        $exist = "Category Name already in database - Try Again!";
        $query = $this->db
            ->where('category_name',$categoryName)
            ->get('category');

        if($query->num_rows() > 0)
        {
            return $exist;
        }
    }



    //------------Validation Functions-----------//

    //Category Registration Validation rules!
    public function getCategoryRegistrationRules()
    {
        $config = array(
            array(
                'field' => 'category_name',
                'label' => 'Category Name',
                'rules' => 'required|regex_match[/^[A-Za-z0-9_ -]+$/]|is_unique[category.category_Name]'
            ),
            array(
                'field' => 'category_image',
                'label' => 'Category Image',
                'rules' => 'required'
            )
        );

        return $config;
    }

    //Category Edit Validation rules!
    public function getCategoryEditRules()
    {
        $config = array(
            array(
                'field' => 'category_name',
                'label' => 'Category Name',
                'rules' => 'required|regex_match[/^[A-Za-z0-9_ -]+$/]'
            )
        );

        return $config;
    }

}