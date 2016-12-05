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
$route['api/isValidHash'] = 'User/isValidHash';   //Checking if hash is valid

$route['api/signup'] = 'User/signup';  //Signup

//-----------------------User-----------------------//

$route['api/user/profile']  = 'User/user_profile'; //Getting user profile
$route['api/user/update']  = 'User/update_user';  //Updating user profile
$route['api/user/update_password']  = 'User/update_password';  //Updating user password
$route['api/user/update_token']  = 'User/updateToken';  //Updating user password


//-----------------------Course-----------------------//

$route['api/courses'] = 'Course/courses'; //list of all courses
$route['api/course'] = 'Course/course'; //A single course
$route['api/search/course'] = 'Course/search_course'; //search a course by name
$route['api/course_user/add'] = 'Course/add_user_course'; //User adding a course
$route['api/course_user/delete'] = 'Course/delete_user_course'; //User deleting a course
$route['api/user/courses'] = 'Course/user_courses'; //User viewing registered courses
$route['api/course/lectures'] = 'Course/course_lectures'; //All lectures in a course

//-----------------------Category-----------------------//

$route['api/categories'] = 'Category/categories';  //list of all categories
$route['api/category/courses'] = 'Category/courses_category'; //list of courses which match a category


//------Comments-----------//

$route['api/course/comments'] = 'Comment/comments_course'; //All comments of a course
$route['api/course/create_comment'] = 'Comment/create_comment'; //Creating comment for a course

$route['api/lecture/comments'] = 'Comment/comments_lecture'; //All comments of a course
$route['api/lecture/create_comment'] = 'Comment/create_comment_lecture'; //Creating comment for a course

//------Lectures-----------//
$route['api/lecture'] = 'Lecture/lecture'; //Getting a single lecture by it's ID
$route['api/lectures'] = 'Lecture/lectures'; //Getting all lectures of a course
$route['api/lectures_reference'] = 'Lecture/lectures_reference'; //Getting all lectures of a course
$route['api/current_lecture'] = 'Lecture/currentLecture'; //Getting current lecture on TV

//--------------Quiz---------//
$route['api/quiz/response'] = 'Quiz/quiz_response'; //quiz response


//------Questions-----------//
$route['api/quiz/questions/notification'] = 'Quiz/quiz_questions'; //questions of a quiz


//-----REFERENCE----------------//
$route['api/reference/add'] = 'Lecture_Reference/addReference'; //Getting a single lecture by it's ID


//-----NOTIFICATION------------//
$route['api/notifications'] = 'Notification/notifications'; //notifications of a user




//----------------------------------------------DASHBOARD----------------------------------------------------------//


$route['api/totalCount'] = 'User/totalCount';

//------------------Admin-----------------//
$route['api/loginAdmin'] = 'User/loginAdmin'; //Login link

//------------------Course-----------------//
$route['api/courses/dashboard'] = 'Course/courses_dashboard'; //list of all course within limit
$route['api/course-total'] = 'Course/course_total'; //Total count of courses
$route['api/course/add'] = 'Course/addCourse'; //admin add course
$route['api/course/edit'] = 'Course/editCourse'; //admin edit course
$route['api/course/delete'] = 'Course/deleteCourse'; //admin delete course
$route['api/course/join'] = 'Course/course_join'; //
$route['api/categories_teachers'] = 'Course/categories_teachers_Course'; //Categories and Teachers information


//------------------Category-----------------//
$route['api/category'] = 'Category/category';  //Single category
$route['api/categories/dashboard'] = 'Category/categories_dashboard'; //list of all categories within limit
$route['api/category/add'] = 'Category/addCategory'; //admin add category
$route['api/category/edit'] = 'Category/editCategory'; //admin edit category
$route['api/category/delete'] = 'Category/deleteCategory'; //admin delete category


//------------------Lecture-----------------//
$route['api/lectures/dashboard'] = 'Lecture/lectures_dashboard'; //list of all lectures within limit
$route['api/lecture/add'] = 'Lecture/addLecture'; //admin add lecture
$route['api/lecture/edit'] = 'Lecture/editLecture'; //admin edit lecture
$route['api/lecture/delete'] = 'Lecture/deleteLecture'; //admin delete lecture


//------------------Teacher-----------------//
$route['api/teacher'] = 'Teacher/teacher'; //Get a single teacher by ID
$route['api/teachers/dashboard'] = 'Teacher/teachers_dashboard'; //list of all teachers within limit
$route['api/teacher/add'] = 'Teacher/addTeacher'; //admin add Teacher
$route['api/teacher/edit'] = 'Teacher/editTeacher'; //admin edit Teacher
$route['api/teacher/delete'] = 'Teacher/deleteTeacher'; //admin delete Teacher



//------------------Quiz-----------------//
$route['api/quiz/add'] = 'Quiz/addQuiz'; //admin add quiz
$route['api/quiz/edit'] = 'Quiz/editQuiz'; //admin edit quiz
$route['api/quiz/delete'] = 'Quiz/deleteQuiz'; //admin delete quiz

$route['api/lecture/quiz'] = 'Quiz/quizzes'; //quizzes of a lecture
$route['api/lecture/quiz_pagination'] = 'Quiz/quizzes_pagination'; //quizzes of a lecture


//------------------Question-----------------//
$route['api/question/add'] = 'Question/addQuestion'; //admin add question
$route['api/question/edit'] = 'Question/editQuestion'; //admin edit question
$route['api/question/delete'] = 'Question/deleteQuestion'; //admin delete question

$route['api/quiz/questions'] = 'Question/questions'; //questions of a quiz
$route['api/quiz/questions_pagination'] = 'Question/questions_pagination'; //questions of a quiz for pagination


