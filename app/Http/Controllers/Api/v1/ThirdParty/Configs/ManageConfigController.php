<?php

namespace App\Http\Controllers\Api\v1\ThirdParty\Configs;

use App\Entity\Project;
use App\Entity\IssueProject;
use App\Entity\SettingIssue;
use Illuminate\Http\Request;
use App\Entity\SettingCategory;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\v1\BaseApiController;

class ManageConfigController extends BaseApiController
{
    /**
     * @SWG\Post(
     *     path="/third-party/configs/defect",
     *     summary="Third Party Get Defect Picklist",
     *     method="post",
     *     tags={"Picklists (Third Party)"},
     *     description="This API will retrieve defects picklist",
     *     operationId="defect",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(property="project_id",type="string",example="e31dc2ca-9d5e-a2ea-7e58-4c5fc0e20e1f"),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function defect(Request $request)
    {
        $rules = [
            'project_id' => 'exists:projects,project_id',
        ];

        $validator = Validator::make($request->input(), $rules, [
            'project_id.exists' => 'The project_id :input does not exist.',
        ]);

        $data = $this->data;

        if ($validator->fails()) {

            return third_party_response('999999', 'failed', $validator->errors()->first(), []);
        }

        $project = Project::where('project_id', $request->input('project_id'))->first();

        if (!$project) {
            return third_party_response('999999', 'failed', 'The project_id ' . $request->input('project_id') . ' does not exist.', []);
        }

        $issue_setting_ids = IssueProject::where('project_id', $project->id)->where('unit_owner', true)->pluck('issue_setting_id')->toArray();

        $result = [
            'category' => [],
        ];

        if (count($issue_setting_ids) > 0) {
            $issue_category_ids = SettingIssue::whereIn('id', $issue_setting_ids)->groupBy('category_id')->pluck('category_id')->toArray();

            $setting_categories = SettingCategory::whereIn('id', $issue_category_ids)->select('id', 'name')->get();

            if (count($setting_categories) > 0) {
                foreach ($setting_categories as $category_key => $setting_category) {
                    array_push($result['category'], $setting_category);
                    $types = [];
                    $setting_types = $setting_category->hasTypes()->select('id', 'name')->get();
                    if (count($setting_types) > 0) {
                        foreach ($setting_types as $type_key => $setting_type) {
                            array_push($types, $setting_type);
                            $issues = [];
                            $setting_issues = $setting_type->hasIssues()->select('id', 'name')->get();

                            if (count($setting_issues) > 0) {
                                foreach ($setting_issues as $key => $setting_issue) {
                                    array_push($issues, $setting_issue);
                                }
                            }
                            $types[$type_key]->issues = $issues;
                        }
                    }
                    $result['category'][$category_key]->types = $types;
                }
            }
        }

        return third_party_response('', 'success', '', $result);
    }
}
