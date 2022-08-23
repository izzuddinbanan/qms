<?php

use Dingo\Api\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

$api = app('Dingo\Api\Routing\Router');

##testing
$api->version('v1', ['middlware' => 'api', 'namespace' => 'App\Http\Controllers'], function (Router $api) {
    $api->post('testing', 'FormAttributeController@saveAll')->middleware('jwt.auth');
});

$api->version('v1', ['middlware' => 'api', 'namespace' => 'App\Http\Controllers\Api\v1'], function (Router $api) {

    /* AUTH */
    $api->version('v1', ['prefix' => 'auth'], function (Router $api) {

        $api->post('login', 'AuthController@login');
        $api->post('logout', 'AuthController@logout')->middleware('jwt.auth');
        $api->post('pt', 'AuthController@pushToken')->middleware('jwt.auth');

        $api->post('user', 'AuthController@me')->middleware('jwt.auth');
        $api->post('editUser', 'AuthController@editUser')->middleware(['jwt.auth', 'languageApi']);
        $api->post('changePassword', 'AuthController@changePassword')->middleware('jwt.auth');

        $api->post('refresh', 'AuthController@refresh')->middleware('jwt.auth');

        $api->post('forgot-password', 'AuthController@forgotPassword');

    });

    $api->version('v1', ['prefix' => 'form'], function (Router $api) {
        $api->post('list', 'FormController@listForm')->middleware('jwt.auth');
        $api->post('all', 'FormController@allForm')->middleware('jwt.auth');
        $api->get('detail/{id}', 'FormController@showFormDetail')->middleware('jwt.auth');
        $api->post('submit', 'FormController@submit')->middleware('jwt.auth');
        $api->post('submission/list', 'FormController@getSubmissionListing')->middleware('jwt.auth');

        $api->post('list-option-form', 'FormController@listOptionForm')->middleware('jwt.auth');
        // $api->post('form-submit', 'FormController@formSubmit')->middleware('jwt.auth');
        $api->post('form-submission-list', 'FormController@formSubmissionList')->middleware('jwt.auth');
        $api->post('form-history', 'FormController@formHistory')->middleware('jwt.auth');
        $api->post('form-update', 'FormController@formUpdate')->middleware('jwt.auth');

    });

    //* PROJECT *//
    $api->version('v1', ['namespace' => 'Manage'], function (Router $api) {

        $api->version('v1', ['prefix' => 'form'], function (Router $api) {

            $api->post('form-create', 'DigitalFormController@create')->middleware('jwt.auth');
            $api->post('form-submit', 'DigitalFormController@submit')->middleware('jwt.auth');
            $api->post('link-issue', 'DigitalFormController@linkIssue')->middleware('jwt.auth');
            $api->post('list-link-issue', 'DigitalFormController@listLinkIssue')->middleware('jwt.auth');
            $api->post('owner-form-submit', 'DigitalFormController@ownerFormSubmit')->middleware('jwt.auth');
        });

    });

    //* PROJECT *//
    $api->version('v1', ['namespace' => 'Manage'], function (Router $api) {
        /* PROJECT */
        $api->version('v1', ['prefix' => 'project'], function (Router $api) {

            $api->post('listProject', 'ProjectController@listProject')->middleware('jwt.auth');

        });

        // ISSUE
        $api->version('v1', ['prefix' => 'issue'], function (Router $api) {

            $api->post('addIssue', 'IssueController@addIssue')->middleware('jwt.auth');
            $api->post('addInfo', 'IssueController@addInfo')->middleware('jwt.auth');
            $api->post('updateIssue', 'IssueController@updateIssue')->middleware('jwt.auth');
            $api->post('getGeneralSetting', 'IssueController@getGeneralSetting')->middleware('jwt.auth');
            // $api->get('getPrioritySetting', 'IssueController@getPrioritySetting')->middleware('jwt.auth');
            $api->post('acceptIssue', 'IssueController@acceptIssue')->middleware('jwt.auth');
            $api->post('startWork', 'IssueController@startWork')->middleware('jwt.auth');
            $api->post('closeIssue', 'IssueController@closeIssue')->middleware('jwt.auth');
            $api->post('voidIssue', 'IssueController@voidIssue')->middleware('jwt.auth');
            $api->post('mergeIssue', 'IssueController@mergeIssue')->middleware('jwt.auth');
            $api->post('splitIssue', 'IssueController@splitIssue')->middleware('jwt.auth');
            $api->post('assignIssue', 'IssueController@assignIssue')->middleware('jwt.auth');
            $api->post('declineIssue', 'IssueController@declineIssue')->middleware('jwt.auth');
            $api->post('redoIssue', 'IssueController@redoIssue')->middleware('jwt.auth');
            $api->post('closeAndHandover', 'IssueController@closeAndHandover')->middleware('jwt.auth');
            $api->post('oaSignOff', 'IssueController@oaSignOff')->middleware('jwt.auth');
            $api->post('closeInt_POA', 'IssueController@closeInt_POA')->middleware('jwt.auth');
        });

        // KEY
        $api->version('v1', ['prefix' => 'key'], function (Router $api) {

            $api->post('/', 'KeyController@keyManagement')->middleware('jwt.auth');
            $api->post('getAllKey', 'KeyController@getAllKey')->middleware('jwt.auth');

        });

        //HANDOVER FORM
        $api->version('v1', ['prefix' => 'handOverForm'], function (Router $api) {
            $api->post('/', 'HandoverFormController@getHandOverDetails')->middleware('jwt.auth');
            $api->post('submit', 'HandoverFormController@formSubmit')->middleware('jwt.auth');
            $api->post('/handOverChecklist', 'HandoverFormController@handOverChecklist')->middleware('jwt.auth');
        });

        /* LOCATION */
        $api->version('v1', ['prefix' => 'location'], function (Router $api) {

            $api->post('updateStatus', 'LocationController@updateStatus')->middleware('jwt.auth');

        });

        //* Plan *//
        $api->version('v1', ['prefix' => 'plan'], function (Router $api) {

            $api->post('/', 'PlanController@getAllPlan')->middleware('jwt.auth');
            // $api->get('view', 'PlanController@view')->middleware('jwt.auth');
            $api->post('/search', 'PlanController@filter')->middleware('jwt.auth');

        });

        //* notification *//
        $api->version('v1', ['prefix' => 'notification'], function (Router $api) {

            $api->post('/', 'NotificationController@notification')->middleware('jwt.auth');
            $api->post('view', 'NotificationController@viewNotification')->middleware('jwt.auth');
            $api->post('clear', 'NotificationController@clearNotification')->middleware('jwt.auth');

        });

        //* Subcontractor *//
        $api->version('v1', ['prefix' => 'subcontractor'], function (Router $api) {

            $api->post('add', 'SubConController@addSubContractor')->middleware('jwt.auth');
            $api->post('assign', 'SubConController@assignSubContractor')->middleware('jwt.auth');
        });

        //* Offline *//
        $api->version('v1', ['prefix' => 'offline'], function (Router $api) {

            $api->post('/sync', 'OfflineController@syncData')->middleware('jwt.auth');
            $api->post('/syncImage', 'OfflineController@syncImage')->middleware('jwt.auth');
        });

        //*GENERAL OPTION API *//
        $api->version('v1', ['prefix' => 'option'], function (Router $api) {

            $api->post('general', 'OptionController@general')->middleware('jwt.auth');

        });

        //*GENERAL DOCUMENT API *//
        $api->version('v1', ['prefix' => 'document'], function (Router $api) {

            $api->post('/', 'DocumentController@listDocument')->middleware('jwt.auth');

        });
    });

    $api->version('v1', ['prefix' => 'third-party'], function (Router $api) {

        /* AUTH */
        $api->version('v1', ['prefix' => 'auth'], function (Router $api) {

            $api->post('login', 'ThirdPartyAuthController@login');
            $api->post('logout', 'AuthController@logout')->middleware('jwt.auth');

            $api->post('refresh', 'AuthController@refresh')->middleware('jwt.auth');

        });

        $api->version('v1', ['prefix' => 'buyers'], function (Router $api) {

            $api->post('', 'ThirdParty\Users\ManageUserController@show')->middleware('jwt.auth');
            $api->post('batch-store', 'ThirdParty\Users\ManageUserController@batchStore')->middleware('jwt.auth');
            $api->post('store', 'ThirdParty\Users\ManageUserController@store')->middleware('jwt.auth');
            $api->post('update', 'ThirdParty\Users\ManageUserController@update')->middleware('jwt.auth');
            $api->post('delete', 'ThirdParty\Users\ManageUserController@destroy')->middleware('jwt.auth');

        });

        $api->version('v1', ['prefix' => 'defects'], function (Router $api) {

            $api->post('store', 'ThirdParty\Defects\ManageDefectController@store')->middleware('jwt.auth');

        });

        $api->version('v1', ['prefix' => 'configs'], function (Router $api) {

            $api->post('defect', 'ThirdParty\Configs\ManageConfigController@defect')->middleware('jwt.auth');

        });

    });

});
