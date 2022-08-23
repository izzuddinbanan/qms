<?php

Route::post('upload_image', 'UploadImageController@upload')->name('upload');
Route::post('delete_image', 'UploadImageController@destroy')->name('delete');

Route::post('upload_image_defect', 'UploadImageController@uploadDefect')->name('uploadDefect');
Route::post('delete_image_defect', 'UploadImageController@destroyDefect')->name('destroyDefect');

Route::post('upload_form', 'UploadFormController@upload')->name('form.upload');
Route::post('delete_form', 'UploadFormController@destroy')->name('form.delete');

Route::get('by_os', 'AnalyticsController@fetchUserPerOS')->name('by.os');
Route::get('by_users', 'AnalyticsController@fetchUsersAnnually')->name('by.user');
Route::get('by_new_users', 'AnalyticsController@fetchNewUsersAnnually')->name('by.user.new');
Route::get('by_seasons', 'AnalyticsController@fetchUserPerSeason')->name('by.season');

Route::post('by_users', 'AnalyticsController@fetchUsersByDate')->name('by.user.post');
Route::post('by_new_users', 'AnalyticsController@fetchNewUsersByDate')->name('by.user.new.post');
Route::post('by_os', 'AnalyticsController@fetchOSByDate')->name('by.os.post');

Route::get('outbox', 'MailController@getOutbox')->name('mail.outbox');
Route::get('outbox/total', 'MailController@getTotalOutbox')->name('mail.outbox.count');
Route::get('draft', 'MailController@getDraft')->name('mail.draft');
Route::get('draft/total', 'MailController@getTotalDraft')->name('mail.draft.count');
Route::post('draft', 'MailController@send')->name('mail.send');
Route::post('mails/delete', 'MailController@destroy')->name('mail.delete');

Route::get('annual_total_visit', 'AnalyticsController@getTotalVisitorAnnually')->name('visitor.annual');
Route::get('platform_total_visit', 'AnalyticsController@getVisitorByPlatform')->name('visitor.platform');


Route::post('dashboardIssue', 'DashboardController@dashboardIssue')->name('dashboardIssue');
