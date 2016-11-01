<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//--------------Login/Signup-----------------------//

$route['api/login'] = 'User/login'; //Login link
$route['api/logout'] = 'User/logout'; //Logout link
$route['api/forgotPassword'] = 'User/forgotPassword';  //Forgot password - enter email here
$route['api/resetPassword'] = 'User/resetPassword';   //Reset password
$route['api/isValidHash'] = 'User/isValidHash';   //Reset password

$route['api/signup'] = 'User/signup';  //Signup

//-----------------------User-----------------------//

$route['api/user/profile']  = 'User/user_profile'; //Getting user profile
$route['api/user/update']  = 'User/update_user';  //Updating user profile

//-----------------------Admin-----------------------//
$route['api/loginAdmin'] = 'User/loginAdmin'; //Login link

$route['api/totalCount'] = 'User/totalCount';


//-----------------------Course-----------------------//

$route['api/courses'] = 'Course/courses'; //list of all courses
$route['api/course'] = 'Course/course'; //A single course
$route['api/search/course'] = 'Course/search_course'; //search a course by name
$route['api/course/add'] = 'Course/add_user_course'; //User adding a course
$route['api/user/courses'] = 'Course/user_courses'; //User viewing registered courses
$route['api/course/lectures'] = 'Course/course_lectures'; //All lectures in a course

//-----------------------Category-----------------------//

$route['api/categories'] = 'Category/categories';  //list of all categories
$route['api/category/courses'] = 'Category/courses_category'; //list of courses which match a category


//------Comments-----------//

$route['api/course/comments'] = 'Comment/comments_course'; //All comments of a course
$route['course/create_comment'] = 'Comment/create_comment'; //Creating comment for a course

//------Lectures-----------//
$route['api/lecture'] = 'Lecture/lecture'; //Getting a single lecture by it's ID

//------------DASHBOARD--------
$route['api/courses/dashboard'] = 'Course/courses_dashboard'; //list of all course within limit
$route['api/course-total'] = 'Course/course_total'; //Total count of courses
$route['api/course/add'] = 'Course/addCourse'; //admin add course
$route['api/course/edit'] = 'Course/editCourse'; //admin edit course
$route['api/course/delete'] = 'Course/deleteCourse'; //admin delete course
$route['api/course/join'] = 'Course/course_join'; //
$route['api/categories_teachers'] = 'Course/categories_teachers_Course'; //Categories and Teachers information

$route['api/category'] = 'Category/category';  //Single category
$route['api/categories/dashboard'] = 'Category/categories_dashboard'; //list of all categories within limit
$route['api/category/add'] = 'Category/addCategory'; //admin add category
$route['api/category/edit'] = 'Category/editCategory'; //admin edit category
$route['api/category/delete'] = 'Category/deleteCategory'; //admin delete category


$route['api/lectures/dashboard'] = 'Lecture/lectures_dashboard'; //list of all lectures within limit
$route['api/lecture/add'] = 'Lecture/addLecture'; //admin add lecture
$route['api/lecture/edit'] = 'Lecture/editLecture'; //admin edit lecture
$route['api/lecture/delete'] = 'Lecture/deleteLecture'; //admin delete lecture


$route['api/teacher'] = 'Teacher/teacher'; //Get a single teacher by ID
$route['api/teachers/dashboard'] = 'Teacher/teachers_dashboard'; //list of all teachers within limit
$route['api/teacher/add'] = 'Teacher/addTeacher'; //admin add Teacher
$route['api/teacher/edit'] = 'Teacher/editTeacher'; //admin edit Teacher
$route['api/teacher/delete'] = 'Teacher/deleteTeacher'; //admin delete Teacher




