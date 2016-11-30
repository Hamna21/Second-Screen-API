<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Crontab\Crontab;
use Crontab\Job;

//Setting crontab for lecture notification - 10 minutes earlier
if ( ! function_exists('lectureNotification'))
{
    //Helper function for setting cronjob at specified time
    function lectureNotification($minute, $hour, $day, $month, $course_id, $lecture_name, $lecture_time)
    {
        //Setting url - path to controller/function which is to be executed
        $command = '/usr/bin/wget ';
        $url = '"http://107.180.106.216/second_screen_api/Notification/sendLectureRequest?lecture_name='
            .$lecture_name
            .'&lecture_time='
            .$lecture_time
            .'&course_id='
            .$course_id
            .'"';
        $commandURL = $command.$url;

        $job = new Job();
        $job
            ->setMinute($minute)
            ->setHour($hour)
            ->setDayOfMonth($day)
            ->setMonth($month)
            ->setDayOfWeek('*')
            ->setCommand($commandURL);

        $crontab = new Crontab();
        $crontab->addJob($job);
        $crontab->write();
    }
}

//Setting crontab for quiz notification
if ( ! function_exists('quizNotification'))
{
    //Helper function for setting cronjob at specified time
    function quizNotification($minute, $hour, $day, $month, $quiz_id, $quiz_title, $lecture_id)
    {
        //Setting url - path to controller/function which is to be executed
        $command = '/usr/bin/wget ';
        $url = '"http://107.180.106.216/second_screen_api/Notification/sendQuizRequest?quiz_id='
            .$quiz_id
            .'&quiz_title='
            .$quiz_title
            .'&lecture_id='
            .$lecture_id
            .'"';
        $commandURL = $command.$url;

        $job = new Job();
        $job
            ->setMinute($minute)
            ->setHour($hour)
            ->setDayOfMonth($day)
            ->setMonth($month)
            ->setDayOfWeek('*')
            ->setCommand($commandURL);

        $crontab = new Crontab();
        $crontab->addJob($job);
        $crontab->write();
    }
}

//Setting crontab for lecture notification - exact lecture time
if ( ! function_exists('currentLectureNotification'))
{
    //Helper function for setting cronjob at specified time
    function currentLectureNotification($minute, $hour, $day, $month, $course_id, $lecture_name, $lecture_time, $lecture_id)
    {
        //Setting url - path to controller/function which is to be executed
        $command = '/usr/bin/wget ';
        $url = '"http://107.180.106.216/second_screen_api/Notification/send_currentLectureRequest?lecture_name='
            .$lecture_name
            .'&lecture_time='
            .$lecture_time
            .'&course_id='
            .$course_id
            .'&lecture_id='
            .$lecture_id
            .'"';
        $commandURL = $command.$url;

        $job = new Job();
        $job
            ->setMinute($minute)
            ->setHour($hour)
            ->setDayOfMonth($day)
            ->setMonth($month)
            ->setDayOfWeek('*')
            ->setCommand($commandURL);

        $crontab = new Crontab();
        $crontab->addJob($job);
        $crontab->write();
    }
}