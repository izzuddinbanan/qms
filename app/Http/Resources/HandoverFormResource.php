<?php

namespace App\Http\Resources;

use App\Entity\DrawingPlan;
use App\Entity\HandoverFormSurvey;
use App\Entity\HandoverFormSurveyVersion;
use App\Http\Resources\BaseResource;
use App\Http\Resources\HandoverSurveyCollection;

class HandoverFormResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $handoverSetting = [
            "original_name"     => $this->original_name,
            "display_name"      => $this->display_name,
            "field_mandatory"   => $this->field_mandatory,
        ];

        switch ($this->original_name) {
            case 'key':
                $handoverSetting['form_details'] = $this->getKeyEsForm($this->drawing_plan_id, 'key');
                break;
            case 'es':
                $handoverSetting['form_details'] = $this->getKeyEsForm($this->drawing_plan_id, 'es');
                break;
            case 'survey':
                $handoverSetting['form_details'] = $this->getSurvey($this->drawing_plan_id);
                break;
            case 'acceptance':
                $handoverSetting['form_details'] = $this->getAcceptance($this->drawing_plan_id);
                break;
            case 'waiver':
                $handoverSetting['form_details'] = $this->getWaiver($this->drawing_plan_id);
                break;
            case 'photo':
                $handoverSetting['form_details'] = [];
                break;
        }
       
        
        return convert_null_to_string($handoverSetting);
    }

    public function getSurvey($drawing_plan_id)
    {
        $drawing_plan_id = DrawingPlan::where('id', $drawing_plan_id)->first();

        $project = $drawing_plan_id->drawingSet->project;

        $count_handoverFormSurveyVersion = HandoverFormSurveyVersion::where('project_id', $project->id)->where('status', 'publish')->count();
        if($count_handoverFormSurveyVersion>0)
        {
            $hadoverFormSurveyVersion = HandoverFormSurveyVersion::where('project_id', $project->id)->where('status', 'publish')->first();
            $handoverFormSurveyQuestion = HandoverFormSurvey::where('handover_form_survey_id', $hadoverFormSurveyVersion->id)->get();

            foreach($handoverFormSurveyQuestion as $question)
            {

                $item_arr[] = (object)[
                    'id'        => $question->id,
                    'question'  => $question->question,
                    'sequence'  => $question->sequence,
                    'type'      => $question->type,
                ];
            }
            
            $survey_details[] = (object)[
                'id'            => $hadoverFormSurveyVersion->id,
                'version'       => $hadoverFormSurveyVersion->version,
                'survey'        => $item_arr,
                'item_count'    => $handoverFormSurveyQuestion->count(),
            ];
        }
        else{
            $survey_details[] = [];
        }

        return $survey_details;
    }

    public function getAcceptance($drawing_plan_id)
    {
        $drawing_plan_id = DrawingPlan::where('id', $drawing_plan_id)->first();

        $project = $drawing_plan_id->drawingSet->project->HandoverFormAcceptance;

        return array(new HandoverAcceptanceResource($project));
    }

    public function getKeyEsForm($drawing_plan_id, $type)
    {
        $drawing_plan = DrawingPlan::where('id', $drawing_plan_id)->first();


        switch ($type) {
            case 'key':
                $form = $drawing_plan->drawingSet->keyForm;

                if(!$drawing_plan->drawingSet->keyForm) {
                    return $form = [];
                }
                break;
            case 'es':
                $form = $drawing_plan->drawingSet->esForm;
                
                if(!$drawing_plan->drawingSet->esForm) {
                    return $form = [];
                }

                break;
        }

        $section = $form->section;

        $section = [];

        $item_count = 0;
        
        foreach ($form->section as $key => $value) {

            $item_arr = [];
            foreach($value->item as $item){

                $item_count += 1;

                $item_arr[] = (object)[
                    'id'        => $item->id,
                    'name'      => $item->name,
                    'quantity'  => $item->quantity,
                ];
            }

            $section[] = (object)[
                'id'    => $value["id"],
                'name'  => $value["name"] ?? '',
                'seq'   => $value["seq"],
                'item'  => $item_arr,
            ];
        }

        $form_details[] = (object)[
            'id'            => $form->id,
            'name'          => $form->name,
            'meter_reading' => $form->meter_reading ? 'yes' : 'no',
            'description'   => $form->description,
            'section'       => $section,
            'item_count'    => $item_count,
            'car_park'      => $drawing_plan->car_park ?? '',
            'access_card'   => $drawing_plan->access_card ?? '',
            'key_fob'       => $drawing_plan->key_fob ?? '',
        ];

        return convert_null_to_string($form_details);

    }

    public function getWaiver($drawing_plan_id) {

        $drawing_plan_id = DrawingPlan::where('id', $drawing_plan_id)->first();

        $waiver = $drawing_plan_id->drawingSet->project->HandoverFormWaiver;

        return $waiver = (object)[
            'id'            => $waiver->id,
            'content'          => $waiver->description ?? '',
        ];
    }
}
