<?php

namespace App\Http\Controllers;

use App\Entity\HandOverFormAcceptance;
use App\Entity\HandoverFormSubmission;
use Illuminate\Http\Request;
use PDF;

class TestingController extends Controller
{
    public function pdf()
    {
    	$form = HandoverFormSubmission::find(5);

    	// return date('d M Y, h:i a', strtotime($form->es_submission["meter_read"]["date"]["date"]));
    	// return $form->es_submission;
    	$project = $form->drawingPlan->drawingSet->project;
    	$unit = $form->drawingPlan;
    	$user = $form->drawingPlan->unitOwner;

    	$waiver = $project->HandoverFormWaiver;

    	$acceptance = HandOverFormAcceptance::find($form->acceptance_submission["form_id"]);

        $path = public_path('uploads/handover-form-submission');

        if (!\File::isDirectory($path)) {

            \File::makeDirectory($path, 0775, true);
        }

    	$unique_name = time() . rand(10, 9999) . '.pdf';

        $pdf = \PDF::loadView('pdf.handover-form-submissions.index', compact('project', 'unit', 'user', 'form', 'waiver', 'acceptance'));
        return $pdf->download($unique_name);

        $pdf->save("{$path}/${unique_name}");

        return $unique_name;

    }
}
