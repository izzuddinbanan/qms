<?php

namespace App\Http\Controllers\Manages\ProjectSettings;

use App\Entity\RoleUser;
use App\Entity\SettingCategory;
use App\Entity\SettingType;
use App\Entity\SettingIssue;
use App\Entity\SettingPriority;
use App\Entity\CategoryProject;
use App\Entity\IssueProject;
use App\Entity\GroupProject;
use App\Entity\GroupContractor;
use App\Entity\PriorityProject;
use App\Entity\Project;
use App\Entity\PriorityType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetIssueController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = session('project_id');

        $RoleUser = RoleUser::find(session('role_user_id'));
        $data = SettingCategory::where('client_id', $RoleUser->client_id)->get();

        $categoryProject = CategoryProject::where('project_id', $id)->pluck('category_setting_id')->toArray();
        $priorityType = PriorityType::get();

        $project = Project::find($id);

        $priority = SettingPriority::where('client_id', $project->client_id)->get();

        // foreach ($priorityType as $key=>$value) {
        //     $priorityType[$key]["select"] = "";
        //     if($hasTask = $project->priority()->where('id', $value["id"])->exists()){
        //         $priorityType[$key]["select"] = "checked";
        //     }
        // }
        $listIssue = SettingCategory::with('hasTypes.hasIssues')->get();


        // data = [
        //         {text: "My Node", data: {addHTML: "<select><option>sad</option></select>"}, "icon" : "glyphicon glyphicon-file", state : {selected : true} },
        //         {text: "My Node", data: {addHTML: multiLineMarkup}},
        //         {text: "My Parent Node", data: {addHTML: "$10"}, children: [
        //                 {text: "My child Node",
        //                  data: {addHTML: multiLineMarkup},
        //                  id: "aChild"},
        //                 {text: "My child Node", data: {addHTML: "foobar"}}
        //             ]
        //         },
        //         {text: "No addHTML in data", data: {}},
        //         {text: "No data"},
        //         {text: "Zero (false) value addHTML", data: { addHTML: 0}},
        //         {text: "My Node", data: {addHTML: "$10"}}
        //     ];

        $issueArray = array();
        $ty = array();
        $iss = array();
        $group_contractor = GroupProject::where('project_id', $project->id)->with('groupDetails')->get();

        foreach ($listIssue as $categoryKey => $category) {

            $cat["text"] = $category->name;
            $cat["id"] = $category->id;
            $cat["icon"] = "glyphicon glyphicon-folder-open"; 

            $cat["children"] = array();

            array_push($issueArray, $cat);

            foreach ($category->hasTypes as $typeKey => $type) {

                $ty["text"] = $type->name; 
                $ty["icon"] = "glyphicon glyphicon-folder-open"; 
                $ty["children"] = array(); 

                $optionDropdown = '';
                $optionDropdown .= '<option value="0">Custom</option>';
                
                foreach($group_contractor as $group){
                    $optionDropdown .= '<option value="'. $group->group_id .'">'. $group->groupDetails->display_name .'</option>';
                }

                // $ty["data"] = ["addHTML" => "<select onchange='setDefault(". $type->id .")' id='type_". $type->id ."'>". $optionDropdown ."</select>"]; 
                

                array_push($issueArray[$categoryKey]["children"] ,$ty);

                foreach($type->hasIssues as $issue){

                    $iss["text"] = $issue->name; 
                    $iss["id"] = "issue_" . $issue->id; 
                    $iss["icon"] = "glyphicon glyphicon-file"; 

                    // return ($test[$categoryKey]["children"][$typeKey]["children"]);

                    $selected = "";
                    $opened = "";
                    $contrac = "";

                    if($issue_project = IssueProject::where('project_id', $id)->where('issue_setting_id', $issue->id)->first()){
                        $selected = "true";
                        $opened = "true";
                        $contrac = $issue_project->group_id;
                    }

                    $optionDropdown = '';
                    $optionDropdown .= '<option value="0">Please Select</option>';
                    foreach($group_contractor as $group){

                        $optionDropdown .= '<option value="'. $group->group_id .'" '. ($group->group_id ==  $contrac ? "selected" : "").' >'. $group->groupDetails->display_name .'</option>';
                    }

                    $iss["data"] = ["addHTML" => "<select  name='con[". $issue->id ."]' class='select-contactor type_". $type->id ."' onchange='selectConIssue(".$issue->id.")' id='conForIssue_". $issue->id ."'>".$optionDropdown ."</select>"]; 
                    $iss["state"] = ["selected" => $selected]; 
                    // $iss["opened"] = $opened; 

                    array_push($issueArray[$categoryKey]["children"][$typeKey]["children"] ,$iss);
                }
            }
        }

        foreach ($data as $key => $value) {
            $data[$key]["select"] = "";
            $data[$key]["group_id"] = "";

            if($checkCategory = CategoryProject::where('category_setting_id', $value["id"])->where('project_id', $project->id)->first()){
                $data[$key]["select"] = "checked";
                $data[$key]["group_id"] = $checkCategory->group_id;
            }
        }

        foreach ($priority as $key => $value) {
            $priority[$key]["select"] = "";
            if($checkPrio = PriorityProject::where('priority_id', $value["id"])->where('project_id', $project->id)->first()){
                $priority[$key]["select"] = "checked";
            }

        }


        return view('project-settings.set-issue.index', compact('data', 'categoryProject', 'id', 'priorityType', 'priority', 'group_contractor', 'listIssue', 'issueArray'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = session('project_id');
        $RoleUser = RoleUser::find(session('role_user_id'));


        if(!$SettingCategory =   SettingCategory::where('client_id', $RoleUser->client_id)->where('id',$request->input('category_id') )->first()){

            return array("errors" => 'Record not found.');
        }

        $categoryProject = CategoryProject::create([
            'project_id'            => $id,
            'category_setting_id'   => $request->input('category_id'),
        ]);
        return $SettingCategory;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $type = SettingType::where()
        return view('project.step7AddCategory');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project_id = session('project_id');
        if(!$categoryProject = CategoryProject::where('project_id', $project_id)->where('category_setting_id', $id)->first()){
            return array("errors" => 'Record not found.');
        }

        CategoryProject::where('project_id', $project_id)->where('category_setting_id', $id)->delete();
        

    }

    public function storePriority(Request $request){

        $id = session('project_id');

        $project = Project::find($id);
        PriorityProject::insert([
            'priority_id'   => $request->input('id'),
            'project_id'    => $id,
            'created_at'    => NOW(),
            'updated_at'    => NOW(),
        ]);
        // $project->priority()->attach($request->input('id'));
    } 

    public function removePriority(Request $request){

        $id = session('project_id');

        $priority = PriorityProject::where('project_id', $id)->where('priority_id', $request->input('id'))->delete();

    }   

    public function setDefaultCon(Request $request){


        $project_id = session('project_id');

        if(!$categoryProject = CategoryProject::where('project_id', $project_id)->where('category_setting_id', $request->input('category_id'))->first()){

            return array("errors" => 'Record not found.');
        }

        CategoryProject::where('project_id', $project_id)->where('category_setting_id', $request->input('category_id'))->update([
            'group_id'  => $request->input('group_id'),
        ]);

    }

    public function storeIssue(Request $request){
      
        $project_id = session('project_id');

        $issue_id = $request->input('issue_id');
        $issueIdArray = explode(",",$issue_id);

        if($issueIdArray[0] == ""){
            return back()->with(['warning-message' => "Please select at least one issue."]);
        }
        IssueProject::where('project_id', $project_id)->whereNotIn('issue_setting_id', $issueIdArray)->delete();


        foreach ($issueIdArray as $key => $value) {
           // return $request->input('contractor')[$value];
            if($issue = IssueProject::where('project_id', $project_id)->where('issue_setting_id',$value)->first()){

                $contractor = $request->input('contractor')[$value];
                if($contractor == 0){
                    $contractor = null;
                }
                IssueProject::where('project_id', $project_id)->where('issue_setting_id',$value)->update(['group_id' => $contractor ]);
            }else{

                if(isset($request->input('contractor')[$value])){
                    $contractor = $request->input('contractor')[$value];
                    if($contractor == 0){
                        $contractor = null;
                    }
                }else{
                    $contractor = null;
                }
                IssueProject::create([
                    'project_id'        => $project_id,
                    'issue_setting_id'  => $value,
                    // 'group_id'          => ($request->input('contractor')[$value] == 0 ? null : $request->input('contractor')[$value]),
                    'group_id'          => $contractor,
                ]);
            }
        }

        return back()->with(['success-message' => "Record sucessfulyy updated."]);
    }

}
