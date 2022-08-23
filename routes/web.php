<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('command', function () {
    \Artisan::call('notify:issues');
    return redirect()->route('home');
});

Route::get('/test', function () {
	return view('components.template-limitless.main');
});





## TESTING ROUTE ##
Route::get('downloadZip', 'HomeController@downloadZip');
Route::get('resizeImage', 'HomeController@resizeImage');
Route::get('command', function () {
    \Artisan::call('notify:issues');
    return redirect()->route('home');
});
## TESTING ROUTE ##


Route::any('removeNotification', 'HomeController@removeNotification')->name('removeNotification');


Route::resource('profile', 'ProfileController')->middleware('auth');
// Route::resource('updatePass', 'PasswordController')->middleware('auth');

Auth::routes(['register' => false]);

// Route::any('/', 'HomeController@index')->name('home')->middleware('auth');
Route::any('dashboard', 'HomeController@mainDashboard')->name('mainDashboard')->middleware('auth');
Route::any('notification/{id}', 'HomeController@viewNotification')->name('viewNotification')->middleware('auth');
Route::any('switchClient/{client_id}', 'HomeController@switchClient')->name('switchClient');


## SYSTEM USER
Route::resource('user', 'UserController');
Route::any('user/edit', 'UserController@edit')->name('user.edit');
Route::any('user/updateUser', 'UserController@updateUser')->name('user.updateUser');
Route::any('user/delete/{id}', 'UserController@destroy')->name('user.destroy');
Route::any('user/listRole', 'UserController@listRole')->name('user.listRole');

## GROUP CONTRACTOR
Route::resource('group', 'groupController');
Route::any('group/delete/{id}', 'groupController@destroy')->name('group.destroy');

## FORM
Route::resource('form', 'FormController');  
Route::any('form/delete/{id}', 'FormController@destroy')->name('form.destroy');
Route::resource('form_attribute', 'FormAttributeController');  
Route::post('form_attribute/setSequenceRole',  'FormAttributeController@setSequenceAndRole')->name('form_attribute.setSequenceRole');

Route::post('form_attribute/saveAll', 'FormAttributeController@saveAll')->name('form_attribute.saveAll');
Route::post('form_attribute/print', 'FormAttributeController@printPDF')->name('form_attribute.printPDF');

Route::get('form/{id}/version', 'FormVersionController@index')->name('version.index');  
Route::post('version/{id}/duplicate', 'FormVersionController@duplicate')->name('version.duplicate');  
Route::any('version/{id}/publish', 'FormVersionController@publish')->name('version.publish');
Route::post('section', 'FormSectionController@store')->name('section.store');
Route::put('section/{id}', 'FormSectionController@update')->name('section.update');
Route::delete('section/{id}', 'FormSectionController@destroy')->name('section.destroy');
Route::resource('form/version', 'FormVersionController', ['except' => [ 'index' ]]); 

## CONTRACTOR -> add user(contactor)
Route::resource('contractor', 'contractorController');
Route::any('contractor/verifyUser', 'contractorController@verifyUser')->name('contractor.verifyUser');
Route::any('contractor/delete/{id}', 'contractorController@destroy')->name('contractor.destroy');

//ISSUE
// Route::resource('issue', 'Board\IssueController');
Route::get('issue', 'Board\IssueController@index')->name('issue.index');
Route::get('issue/{id}', 'Board\IssueController@show')->name('issue.show');
Route::post('issue/info', 'IssueController@addInfo')->name('addInfo');
Route::get('issues/count', 'Board\IssueController@getCount')->name('issue.getCount');
Route::get('issues/listing', 'Board\IssueController@getIssueListing')->name('issue.getListing');
Route::get('issues/export', 'Board\IssueController@export')->name('issue.export');
Route::get('issue/download/{id}', 'Board\IssueController@downloadReport')->name('issue.download');

//UNIT
Route::resource('unit', 'Board\UnitController');
Route::get('units/issues/listing/{id}', 'Board\UnitController@getUnitIssuesListing')->name('unit.issues.listing');
Route::get('units/count', 'Board\UnitController@getCount')->name('unit.getCount');
Route::get('units/listing', 'Board\UnitController@getListing')->name('unit.getListing');
Route::get('units/general/export', 'Board\UnitController@export')->name('unit.general.export');
Route::get('units/individual/export/{id}', 'Board\UnitController@exportUnit')->name('unit.individual.export');
Route::post('units/update-details/{id}', 'Board\UnitController@updateUnitDetails')->name('unit.update-details');

//HANDOVER REPORT
Route::resource('handoverreport', 'Board\HandoverReportController');
Route::get('datatable/handoverreport', 'Board\HandoverReportController@indexData')->name('handoverreport.indexdata');
Route::get('handover/handover/{id}', 'Board\HandoverReportController@handover')->name('handover.handover');

//COMMON AREA
Route::resource('commonarea', 'Board\CommonAreaController');
Route::get('commonareas/count', 'Board\CommonAreaController@getCount')->name('commonarea.getCount');
Route::get('commonareas/listing', 'Board\CommonAreaController@getListing')->name('commonarea.getListing');
Route::get('commonareas/general/export', 'Board\CommonAreaController@export')->name('commonarea.general.export');

Route::get('contractors', 'Board\ContractorController@index')->name('contractors.index');
Route::get('contractors/count', 'Board\ContractorController@getCount')->name('contractors.getCount');
Route::get('contractors/issues/listing', 'Board\ContractorController@getListing')->name('contractors.getListing');
Route::get('contractors/{id}/issues', 'Board\ContractorController@show')->name('contractors.show');
Route::get('contractors/general/export', 'Board\ContractorController@export')->name('contractors.general.export');
Route::get('contractors/issues/listing/{id}', 'Board\ContractorController@getContractorIssueListing')->name('contractors.issues.listing');
Route::get('contractors/issues/count/{id}', 'Board\ContractorController@getContractorIssueCount')->name('contractors.issues.getCount');
Route::get('contractors/individual/export/{id}', 'Board\ContractorController@exportContractorIssues')->name('contractors.individual.listing');

##Plan Viewer
Route::resource('plan', 'PlanController');
Route::post('plan/details', 'PlanController@planDetails')->name('planDetails');
Route::post('plan/locationDetails', 'PlanController@locationDetails')->name('locationDetails');
Route::post('plan/detailProject', 'PlanController@detailProject')->name('detailProject');
Route::post('plan/issue/store', 'PlanController@issueStore')->name('plan.issueStore');
Route::post('plan/issue/info/store', 'PlanController@issueInfoStore')->name('plan.issueInfoStore');
Route::post('plan/issue/view', 'PlanController@issueDetails')->name('plan.issueDetails');
Route::post('plan/issue/edit', 'PlanController@editIssue')->name('plan.editIssue');
Route::post('plan/issue/update', 'PlanController@updateIssue')->name('plan.updateIssue');
Route::post('plan/issue/documents', 'PlanController@getIssueDocuments')->name('plan.getIssueDocuments');
Route::get('plan/issue/document/{id}', 'PlanController@getIssueDocument')->name('plan.getIssueDocument');
Route::post('plan/resetPosition', 'PlanController@resetPosition')->name('plan.resetPosition');
Route::post('plan/moveIssue', 'PlanController@moveIssue')->name('plan.moveIssue');
Route::post('plan/voidIssue', 'PlanController@voidIssue')->name('plan.voidIssue');
Route::post('plan/joinIssue', 'PlanController@joinIssue')->name('plan.joinIssue');
Route::post('plan/updateLocationStatus', 'PlanController@updateLocationStatus')->name('plan.updateLocationStatus');
Route::post('plan/viewMerge', 'PlanController@viewMerge')->name('plan.viewMerge');
Route::post('plan/listJoinIssue', 'PlanController@listJoinIssue')->name('plan.listJoinIssue');
Route::post('plan/splitIssue', 'PlanController@splitIssue')->name('plan.splitIssue');
Route::post('plan/storeMergeIssue', 'PlanController@storeMergeIssue')->name('plan.storeMergeIssue');
Route::post('plan/mergeHistory', 'PlanController@mergeHistory')->name('plan.mergeHistory');


##setup mode
Route::post('plan/setupMode/editLink', 'PlanController@setupModeEditLink')->name('plan.setupMode.editLink');
Route::post('plan/setupMode/updateLink', 'PlanController@setupModeUpdateLink')->name('plan.setupMode.updateLink');

# PROJECT
// Route::resource('project', 'ProjectController');
// Route::any('project/chooseLangSetup', 'ProjectController@chooseLangSetup')->name('project.chooseLangSetup'); 
// Route::any('project/view', 'ProjectController@show')->name('project.show'); //route to switch project ID
// Route::any('project/delete/{id}', 'ProjectController@destroy')->name('project.destroy');

// PROJECT ->step1
// Route::resource('step1', 'ProjectSetup\Step1Controller');
// Route::post('step1/chooseLanguage', 'ProjectSetup\Step1Controller@chooseLangSetup')->name('step1.chooseLangSetup'); 

//PROJECT ->step2->drawing sets
// Route::resource('step2', 'ProjectSetup\Step2Controller');
// Route::any('step2/updateSort', 'ProjectSetup\Step2Controller@updateSort')->name('step2.updateSort');
// Route::any('step2/updateSortPlan', 'ProjectSetup\Step2Controller@updateSortPlan')->name('step2.updateSortPlan');
// Route::any('step2/delete/{id}', 'ProjectSetup\Step2Controller@destroy')->name('step2.destroy');
// //PROJECT ->step2->drawing plan
// Route::any('step2/store/{set_id}', 'ProjectSetup\Step2Controller@storePlan')->name('step2.storePlan');
// Route::any('step2/duplicatePlan/{id}', 'ProjectSetup\Step2Controller@duplicatePlan')->name('step2.duplicatePlan');
// Route::any('step2/update/{set_id}', 'ProjectSetup\Step2Controller@updatePlan')->name('step2.updatePlan');
// Route::any('step2/viewPlan', 'ProjectSetup\Step2Controller@viewPlan')->name('step2.viewPlan');
// Route::any('step2/delete/plan/{id}', 'ProjectSetup\Step2Controller@destroyPlan')->name('step2.destroyPlan');
// Route::any('step2/setDefault/{plan_id}/{set_id}', 'ProjectSetup\Step2Controller@setDefault')->name('step2.setDefault');
// Route::any('step2/batchUpload/', 'ProjectSetup\Step2Controller@batchUpload')->name('step2.batchUpload');


//PROJECT ->step3
Route::resource('step3', 'ProjectSetup\Step3Controller');
Route::any('step3/updatelink', 'ProjectSetup\Step3Controller@update')->name('step3.updates');
Route::any('step3/detailsMarker', 'ProjectSetup\Step3Controller@detailsMarker')->name('step3.getDetailsMarker');
Route::any('step3/getAllSet', 'ProjectSetup\Step3Controller@getAllSet')->name('step3.getAllSet');
Route::any('step3/updatePosition', 'ProjectSetup\Step3Controller@updatePosition')->name('step3.updatePosition');
Route::any('step3/getPos', 'ProjectSetup\Step3Controller@getPos')->name('step3.getPos');
Route::any('project/step3/listPlan', 'ProjectSetup\Step3Controller@listPlan')->name('step3.listPlan');
Route::any('project/step3/viewPlan', 'ProjectSetup\Step3Controller@viewPlan')->name('step3.viewPlan');
Route::any('project/step3/allPoint', 'ProjectSetup\Step3Controller@allPoint')->name('step3.allPoint');

//PROJECT ->step4
Route::resource('step4', 'ProjectSetup\Step4Controller');
Route::any('step4/updateLocation', 'ProjectSetup\Step4Controller@update')->name('step4.updateLocation');
Route::any('step4/getPos', 'ProjectSetup\Step4Controller@getPos')->name('step4.getPos');
Route::any('step4/getLocationDetails', 'ProjectSetup\Step4Controller@getLocationDetails')->name('step4.getLocationDetails');
Route::any('step4/viewPlan', 'ProjectSetup\Step4Controller@viewPlan')->name('step4.viewPlan');
Route::any('step4/updatePosition', 'ProjectSetup\Step4Controller@updatePosition')->name('step4.updatePosition');
// Route::any('step4/detailsMarker', 'ProjectSetup\Step4Controller@detailsMarker')->name('step4.getDetailsMarker');


// //PROJECT ->step5->user
// Route::resource('step5', 'ProjectSetup\Step5Controller');
// Route::any('project/step5/store', 'ProjectSetup\Step5Controller@store')->name('project.step5.store');
// Route::any('project/step5/listRole', 'ProjectSetup\Step5Controller@listRole')->name('project.step5.listRole');
// Route::any('project/step5/saveUserProject', 'ProjectSetup\Step5Controller@saveUserProject')->name('step5.saveUserProject');
// Route::any('project/step5/destroyUserProject', 'ProjectSetup\Step5Controller@destroyUserProject')->name('step5.destroyUserProject');
// Route::any('project/step5/setAsDefault', 'ProjectSetup\Step5Controller@setAsDefault')->name('step5.setAsDefault');

//PROJECT ->step6
// Route::resource('step6', 'ProjectSetup\Step6Controller');
// Route::get('step6/contractor/{id}', 'ProjectSetup\Step6Controller@contractor')->name('step6.contractor');
// Route::get('step6/updateContractor', 'ProjectSetup\Step6Controller@updateContractor')->name('step6.updateContractor');

//PROJECT ->step7
// Route::resource('step7', 'ProjectSetup\Step7Controller');
// Route::any('step7/Add', 'ProjectSetup\Step7Controller@show');
// Route::any('step7/storePriority', 'ProjectSetup\Step7Controller@storePriority')->name('step7.storePriority');
// Route::any('step7/removePriority', 'ProjectSetup\Step7Controller@removePriority')->name('step7.removePriority');
// Route::any('step7/setDefaultCon', 'ProjectSetup\Step7Controller@setDefaultCon')->name('step7.setDefaultCon');
// Route::any('step7/storeIssue', 'ProjectSetup\Step7Controller@storeIssue')->name('step7.storeIssue');

// //PROJECT ->step8
// Route::resource('step8', 'ProjectSetup\Step8Controller');
// Route::any('project/step8', 'ProjectSetup\Step8Controller@index')->name('project.step8');


//ISSUE SETUP -> Category
Route::resource('setting_category', 'IssueSetup\CategoryController');
Route::any('setting_category/update', 'IssueSetup\CategoryController@update')->name('setting_category.update');
Route::any('setting_category/edit', 'IssueSetup\CategoryController@edit')->name('setting_category.edit');
Route::any('setting_category/destroy/{id}', 'IssueSetup\CategoryController@destroy')->name('setting_category.destroy');

//ISSUE SETUP -> type
Route::resource('setting_type', 'IssueSetup\TypeController');
Route::any('setting_type/edit', 'IssueSetup\TypeController@edit')->name('setting_type.edit');
Route::any('setting_type/update', 'IssueSetup\TypeController@update')->name('setting_type.update');
Route::any('setting_type/delete/{id}', 'IssueSetup\TypeController@destroy')->name('setting_type.delete');
Route::any('issue/type/listCat', 'IssueSetup\TypeController@listCat')->name('setting_type.listCat');
Route::any('issue/type/catLang', 'IssueSetup\TypeController@catLang')->name('setting_type.catLang');

//ISSUE SETUP -> Issue
Route::resource('setting_issue', 'IssueSetup\IssueController');
Route::any('setting_issue/edit', 'IssueSetup\IssueController@edit')->name('setting_issue.edit');
Route::any('setting_issue/update', 'IssueSetup\IssueController@update')->name('setting_issue.update');
Route::any('setting_issue/delete/{id}', 'IssueSetup\IssueController@destroy')->name('setting_issue.delete');
Route::any('setting_issue/listCat', 'IssueSetup\IssueController@listCat')->name('setting_issue.listCat');
Route::any('setting_issue/listType', 'IssueSetup\IssueController@listType')->name('setting_issue.listType');

//ISSUE SETUP -> Priority
Route::resource('setting_priority', 'IssueSetup\PriorityController');
Route::any('setting_priority/edit', 'IssueSetup\PriorityController@edit')->name('setting_priority.edit');
Route::any('setting_priority/update', 'IssueSetup\PriorityController@update')->name('setting_priority.update');
Route::any('setting_priority/delete/{id}', 'IssueSetup\PriorityController@destroy')->name('setting_priority.delete');

//LOG VIEWER
Route::get('logviewer', 'LogViewerController@index')->name('log-viewer.index');

Route::get('reset-success', function () {
    return view('activated');
});



// PHASE 2

Route::resource('group-form', 'GroupFormController');  
Route::get('datatable/group-form', GroupFormController::class . '@indexData')->name('group-form.index-data');

Route::resource('document', 'DocumentController');  
Route::get('datatable/document', DocumentController::class . '@indexData')->name('document.index-data');
Route::get('datatable/document/show/{id}', DocumentController::class . '@showData')->name('document.show-data');

// PROJECT SETTINGS
// Route::resource('set-general', 'ProjectSetup\SetGeneralController');  

// Route::resource('set-drawing', 'ProjectSetup\SetDrawingController');  
// Route::get('datatable/set-drawing', ProjectSetup\SetDrawingController::class . '@indexData')->name('set-drawing.index-data');

// Route::resource('set-form', 'ProjectSetup\SetFormController');  

// Route::resource('set-location', 'ProjectSetup\SetLocationController');  



Route::resource('set-document', 'ProjectSetup\SetDocumentController');  



// DIGITAL FORM - 
Route::get('form/{id}/status', 'FormStatusController@index')->name('form-status.index');  
Route::get('form/{id}/status/datatable', 'FormStatusController@indexData')->name('form-status.index-data');  
Route::get('form/{id}/status/create', 'FormStatusController@create')->name('form-status.create');  
Route::get('form/{id}/status/edit/{status}', 'FormStatusController@edit')->name('form-status.edit');  
Route::post('form/status/store', 'FormStatusController@store')->name('form-status.store');  
Route::post('form/status/destroy/{id}', 'FormStatusController@store')->name('form-status.destroy');  
Route::post('form/status/update/{id}', 'FormStatusController@update')->name('form-status.update');  

Route::get('closeAndHandover/{id}', 'CloseAndHandoverController@index')->name('closeAndHandover.index');
Route::post('closeAndHandover/submit/{id}', 'CloseAndHandoverController@submit')->name('closeAndHandover.submit');

// CUSTOMER
Route::get('customer/export', 'CustomerController@export')->name('customer.export');
Route::get('customer/exportsample', 'CustomerController@export_sample')->name('customer.export_sample');
Route::post('customer/import', 'CustomerController@import')->name('customer.import');
Route::get('customer/listing', 'CustomerController@getListing')->name('customer.getListing');
Route::get('datatable/customer', CustomerController::class . '@indexData')->name('customer.index-data');
Route::post('customer/check-customer', CustomerController::class . '@checkCustomer')->name('customer.check-customer');
Route::resource('customer', 'CustomerController');

 // PASSWORD SETUP
 Route::get('password/setup/{token}', 'PasswordSetupController@index')->name('password.setup');
 Route::post('password/setup/update/{token}', 'PasswordSetupController@update')->name('password.update'); 
 Route::get('password/setup/complete', 'PasswordSetupController@show')->name('password.setupcomplete');


## NEW ENHANCE ROUTE ##

Route::middleware(['auth'])->group(function () {

	
	Route::any('/', 'HomeController@index')->name('home');
	Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

	## SWITCH USER ##
	Route::get('switch-user/{id}', 'HomeController@switchUser')->name('switchUser');
	## SWITCH LANGUAGE ##
	Route::get('switch-language/{id}', 'HomeController@switchLanguage')->name('switch-language');

	## POSTER | API DOCUMENTATION ##
	Route::resource('poster', 'SwaggerAPIController');

    Route::group(['namespace' => 'Manages'], function () { 

    	## CLIENTS ##
    	Route::group(['namespace' => 'Clients'], function () { 
			Route::resource('client', 'ClientController');
			Route::get('datatable/clients', ClientController::class . '@indexData')->name('client.index-data');
    	});

    	
		## APP VERSION ##
    	Route::group(['namespace' => 'AppVersions'], function () { 
			Route::resource('app-version', 'AppversionController');
			Route::get('datatable/app-version', AppversionController::class . '@indexData')->name('app-version.index-data');
    	});
        
    	## AUDIT TRAILS ##
    	Route::group(['namespace' => 'Audits'], function () { 
			Route::resource('audit', 'AuditController');
			Route::get('datatable/audit', AuditController::class . '@indexData')->name('audit.index-data');

    	});


    	## PROJECTS ##
    	Route::group(['namespace' => 'Projects'], function () { 
			Route::resource('project', 'ProjectController');
			Route::post('project/switch-project', ProjectController::class . '@switchProject')->name('project.switch-project');
    	});

    	## PROJECTS SETTINGS ##
    	Route::group(['namespace' => 'ProjectSettings'], function () { 
    		Route::prefix('project-setting')->group(function () {

				##SET GENERAL
				Route::resource('set-general', 'SetGeneralController');
				Route::post('project/set-general/set-lang', 'SetGeneralController@setLang')->name('set-general.setLang'); 

				##SET DRAWING SET
				Route::resource('set-drawing-set', 'SetDrawingSetController'); 
				Route::get('datatable/set-drawing-set', SetDrawingSetController::class . '@indexData')->name('set-drawing-set.index-data');

				##SET DRAWING PLAN
				Route::resource('set-drawing-plan', 'SetDrawingPlanController'); 
				Route::get('datatable/set-drawing-plan', SetDrawingPlanController::class . '@indexData')->name('set-drawing-plan.index-data');
				Route::get('set-default/set-drawing-plan/{id}', SetDrawingPlanController::class . '@setDefault')->name('set-drawing-plan.default');
				Route::post('clone/set-drawing-plan', SetDrawingPlanController::class . '@clonePlan')->name('set-drawing-plan.clone');

				##SET DRAWING PLAN
				Route::resource('set-link', 'SetLinkController'); 
				Route::post('list-plan/set-link', SetLinkController::class . '@listPlan')->name('set-link.list-plan');
				Route::post('view-plan/set-link', SetLinkController::class . '@viewPlan')->name('set-link.view-plan');
				Route::post('get-pos/set-link', SetLinkController::class . '@getPos')->name('set-link.get-pos');
				Route::post('update-pos/set-link', SetLinkController::class . '@updatePosition')->name('set-link.update-pos');
				Route::post('get-detail-marker/set-link', SetLinkController::class . '@detailsMarker')->name('set-link.get-detail-marker');
				Route::post('update/set-link', SetLinkController::class . '@update')->name('set-link.update-other');
				Route::post('get-all-set/set-link', SetLinkController::class . '@getAllSet')->name('set-link.get-all-set');
				Route::post('remove-drill/set-link', SetLinkController::class . '@destroy')->name('set-link.remove-drill');

				##SET INSPECTION
				Route::resource('set-inspection', 'SetInspectionController'); 

				##SET LOCATION
				Route::resource('set-location', 'SetLocationController'); 
				Route::post('update-location/set-location', SetLocationController::class . '@update')->name('update-location.set-location');
				Route::post('set-location/duplication', SetLocationController::class . '@duplicate')->name('set-location.duplicate');  
				Route::post('remove-location/set-location', SetLocationController::class . '@destroy')->name('set-location.remove-location');  
				Route::post('set-location/list-form-selected', 'SetLocationController@listFormSelect')->name('set-location.listFormSelect');  
				Route::post('set-location/duplication', 'SetLocationController@duplicate')->name('set-location.duplicate');  
				Route::post('set-location/list-drawing-plan', 'SetLocationController@listDrawingPlan')->name('set-location.listDrawingPlan');  
				##SET EMPLOYEE
				Route::resource('set-employee', 'SetEmployeeController'); 
				Route::get('datatable/set-employee', SetEmployeeController::class . '@indexData')->name('set-employee.index-data');
				Route::post('save-user/set-employee', SetEmployeeController::class . '@saveUserProject')->name('set-employee.save-user');
				Route::post('remove-user/set-employee', SetEmployeeController::class . '@destroyUserProject')->name('set-employee.remove-user');
				Route::post('set-default/set-employee', SetEmployeeController::class . '@setAsDefault')->name('set-employee.set-default');

				##SET CONTRACTOR
				Route::resource('set-contractor', 'SetContractorController'); 
				Route::get('datatable/set-contractor', SetContractorController::class . '@indexData')->name('set-contractor.index-data');
				Route::post('save-contractor/set-contractor', SetContractorController::class . '@saveContractorProject')->name('set-contractor.save-contractor');
				Route::post('remove-contractor/set-contractor', SetContractorController::class . '@destroyContractorProject')->name('set-contractor.remove-contractor');
				Route::post('update-contractor/set-contractor', SetContractorController::class . '@updateContractor')->name('set-contractor.update-contractor');


				##SET ISSUE
				Route::resource('set-issue', 'SetIssueController');
				Route::any('set-issue/Add', 'SetIssueController@show');
				Route::any('set-issue/storePriority', 'SetIssueController@storePriority')->name('set-issue.storePriority');
				Route::any('set-issue/removePriority', 'SetIssueController@removePriority')->name('set-issue.removePriority');
				Route::any('set-issue/setDefaultCon', 'SetIssueController@setDefaultCon')->name('set-issue.setDefaultCon');
				Route::any('set-issue/storeIssue', 'SetIssueController@storeIssue')->name('set-issue.storeIssue');

				##SET ISSUE
				Route::resource('set-document', 'SetDocumentController');

    		});

    	});

    	## HANDOVER SETTINGS ##
    	Route::group(['namespace' => 'HandoverSettings'], function () { 
    		Route::prefix('handover-setting')->group(function () {


			Route::resource('checklist-form', 'ChecklistFormController');
			Route::get('datatable/handover-form-list', ChecklistFormController::class . '@indexData')->name('checklist-form.index-data');

    		});

    	});


    	## PASSWORD ##
    	Route::group(['namespace' => 'Passwords'], function () { 

			Route::resource('update-password', 'UpdatePasswordController');

    	});

    	## HANDOVER SETTING ##
    	Route::group(['namespace' => 'Handover'], function(){
    		Route::resource('handover', 'HandoverController');
    		Route::post('/editHandover', HandoverController::class . '@editHandover')->name('handover.editHandover');
    		Route::post('/editHandoverSetting', HandoverController::class . '@editHandoverSetting')->name('handover.editHandoverSetting');
    	});

    	## SURVEY ##
    	Route::group(['namespace' => 'Survey'], function (){
    		Route::resource('survey', 'SurveyController');
    		Route::get('/publish/{id}', SurveyController::class . '@publish')->name('survey.publish');
    	});

    	## Key ##
    	Route::group(['namespace' => 'Key'], function (){
    		Route::resource('key', 'KeyController');
    	});

    	## ES ##
    	Route::group(['namespace' => 'ES'], function (){
    		Route::resource('es', 'ESController');
    	});

    	## WAIVER ##
    	Route::group(['namespace' => 'Waiver'], function (){
    		Route::resource('waiver', 'WaiverController');
    	});

    	## PHOTO ##
    	Route::group(['namespace' => 'Photo'], function (){
    		Route::resource('photo', 'PhotoController');
    	});

    	## ACCEPTANCE ##
    	Route::group(['namespace' => 'Acceptance'], function (){
    		Route::resource('acceptance', 'AcceptanceController');
    		Route::get('/editTermsConditions', AcceptanceController::class . '@editTermsConditions')->name('acceptance.editTermsConditions');
    		Route::post('/updateTermsConditions', AcceptanceController::class . '@updateTermsConditions')->name('acceptance.updateTermsConditions');
    	});


		## HANDOVER FORM ##
    	Route::group(['namespace' => 'HandoverForms'], function () { 
			Route::resource('handover-form', 'HandoverFormController');
			Route::get('handover-form/{id}/clone', HandoverFormController::class . '@clone')->name('handover-form.clone');
			Route::get('datatable/handover-form-list', HandoverFormController::class . '@indexData')->name('handover-form.index-data');
    	});



    	## ACCESS ITEMS ##
    	Route::group(['namespace' => 'KeyAccess'], function () { 
			Route::resource('key-access', 'KeyAccessController');
			Route::get('datatable/key-access', KeyAccessController::class . '@indexData')->name('key-access.index-data');
			Route::get('datatable/key-access/transaction/{id}', KeyAccessController::class . '@transactionData')->name('key-access.transaction.index-data');
			Route::get('key-access/{id}/transaction/{transaction_id}', KeyAccessController::class . '@trasactionShow')->name('key-access.transaction-details');

			Route::get('key-access/transaction/{id}/create', KeyAccessController::class . '@trasactionCreate')->name('key-access.transaction-create');
			Route::post('access-item/transaction/store', KeyAccessController::class . '@trasactionStore')->name('access-item.transaction-store');

			Route::get('access-item/batch-upload', KeyAccessController::class . '@createBatchUpload')->name('access-item.batch-upload.create');
			Route::get('datatable/batch-upload-data', KeyAccessController::class . '@batchUploadData')->name('access-item.batch-upload-data');
			Route::post('access-item/batch-upload/select', KeyAccessController::class . '@batchUploadSelect')->name('access-item.batch-upload-select');

			Route::post('access-item/batch-upload/select-key', KeyAccessController::class . '@batchUploadSelectKey')->name('access-item.batch-upload-select-key');

			Route::post('access-item/batch-upload/store', KeyAccessController::class . '@batchUploadStore')->name('access-item.batch-upload-store');




    	});

    	## BUYER ##
    	Route::group(['namespace' => 'Buyer'], function (){
    		Route::resource('buyer', 'BuyerController');
			Route::get('datatable/buyer', BuyerController::class . '@indexData')->name('buyer.index-data');

    	});

    	## BUYER ##
    	Route::group(['namespace' => 'PropertyUnits'], function (){
    		Route::resource('property-unit', 'PropertyUnitController');
			Route::get('property-unit/owner-info/{id}', PropertyUnitController::class . '@ownerInfo')->name('property-unit.owner-info');

			Route::get('datatable/property-unit', PropertyUnitController::class . '@indexData')->name('property-unit.index-data');

    	});

    	
    });
});

Route::get('importing/defect', function(){
	$clients = \App\Entity\Client::get();
	return view('importing_defect', compact('clients'));
});
Route::post('importing/defect', function(\Illuminate\Http\Request $request){
	$client = \App\Entity\Client::find($request->input('client_id'));
	if ($client && $request->hasFile('file')) {
		$file = \Excel::toArray(new \App\Imports\IssueSettingsImport, $request->file('file'));
        if (count($file)>0) {
        	if (count($file[0])>0) {
        		ini_set('max_execution_time', '3600');
	        	foreach ($file[0] as $key => $input) {
	        		$category = \App\Entity\SettingCategory::where('name', $input['category'])->where('client_id', $client->id)->first();
	        		if (!$category) {
	        			$category = \App\Entity\SettingCategory::create([
	        				'name' => $input['category'],
	        				'client_id' => $client->id,
	        			]);
	        		}
	        		$type = \App\Entity\SettingType::where('name', $input['type'])
	        			->where('client_id', $client->id)
	        			->where('category_id', $category->id)
	        			->first();
	        		if (!$type) {
	        			$type = \App\Entity\SettingType::create([
	        				'category_id' => $category->id,
	        				'name' => $input['type'],
	        				'client_id' => $client->id,
	        			]);
	        		}
	        		$issue = \App\Entity\SettingIssue::where('name', $input['issue'])
	        			->where('client_id', $client->id)
	        			->where('category_id', $category->id)
	        			->where('type_id', $type->id)
	        			->first();
	        		if (!$issue) {
	        			$issue = \App\Entity\SettingIssue::create([
	        				'category_id' => $category->id,
	        				'type_id' => $type->id,
	        				'name' => $input['issue'],
	        				'client_id' => $client->id,
	        			]);
	        		}
	        	}
	        }
        }

        
	}
})->name('importing.defect');