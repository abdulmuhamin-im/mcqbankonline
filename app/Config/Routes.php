<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
 $routes->get('admin/backup', 'DatabaseBackup::download', ['filter' => 'auth']);
 
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard/admin', 'Dashboard::admin');
    $routes->get('dashboard/member', 'Dashboard::member');
});
$routes->get('/', 'Home::index');
$routes->group('master', ['filter' => 'auth'], static function ($routes) {
    $routes->get('departments', 'MasterData::departments');
    $routes->post('departments', 'MasterData::departments');
    $routes->get('program-departments', 'ProgramDepartmentMapping::index');
    $routes->post('program-departments', 'ProgramDepartmentMapping::index');
    $routes->get('subjects', 'MasterData::subjects');
    $routes->post('subjects', 'MasterData::subjects');
    $routes->get('systems', 'MasterData::systems');
    $routes->post('systems', 'MasterData::systems');
    $routes->get('topics', 'MasterData::topics');
    $routes->post('topics', 'MasterData::topics');
   /*  $routes->get('subtopics', 'MasterData::subtopics');
    $routes->post('subtopics', 'MasterData::subtopics'); */
    $routes->get('systems-by-subject/(:num)', 'MasterData::systemsBySubject/$1');
    $routes->get('subdisciplines', 'MasterData::subdisciplines');
    $routes->post('subdisciplines', 'MasterData::subdisciplines');
    $routes->get('subdisciplines-by-subject/(:num)','MasterData::subdisciplinesBySubject/$1');
    $routes->get('mcqs', 'Mcqs::index');
    $routes->post('mcqs/create', 'Mcqs::create');
    $routes->get('mcqs/edit/(:num)', 'Mcqs::edit/$1');
    $routes->post('mcqs/update/(:num)', 'Mcqs::update/$1');
   $routes->get('mcqs/new', 'Mcqs::new');
    $routes->post('mcqs/save', 'Mcqs::save');
    $routes->get('mcqs/my-questions', 'Mcqs::myQuestions');
    $routes->get('mcqs/submissions', 'Mcqs::submissions');
    $routes->get('mcqs/submissions/download', 'Mcqs::downloadSubmissions');
    $routes->post('mcqs/submit/(:num)', 'Mcqs::submit/$1');
    $routes->get('mcqs/systems-by-subject/(:num)', 'Mcqs::systemsBySubject/$1');
    $routes->get('mcqs/topics-by-system/(:num)', 'Mcqs::topicsBySystem/$1');
    $routes->get('faculty/mcqs', 'FacultyMcqs::index');
    $routes->post('faculty/mcqs/save', 'FacultyMcqs::save');
    $routes->get('faculty/mcqs/edit/(:num)', 'FacultyMcqs::edit/$1');
    $routes->get('faculty/mcqs/systems-by-subject', 'FacultyMcqs::systemsBySubject');
    $routes->get('faculty/mcqs/topics-by-system/(:num)', 'FacultyMcqs::topicsBySystem/$1');
    $routes->get('change-password', 'ChangePassword::index', ['filter' => 'auth']);
    $routes->post('change-password', 'ChangePassword::update', ['filter' => 'auth']);
    $routes->get('programs', 'Programs::index');
    $routes->post('programs', 'Programs::index');
    $routes->get('teachers', 'Teachers::index');
    $routes->post('teachers', 'Teachers::index');
    $routes->get('program-subjects', 'ProgramSubjectMapping::index');
    $routes->post('program-subjects/save', 'ProgramSubjectMapping::save');
    $routes->get('admin/users', 'Users::index');
    $routes->get('admin/users', 'Users::index');
    $routes->get('admin/users/new', 'Users::new');
    $routes->get('admin/users/edit/(:num)', 'Users::edit/$1');
    $routes->post('admin/users/save', 'Users::save');
    $routes->post('admin/users/toggle-status/(:num)', 'Users::toggleStatus/$1');
   
});
