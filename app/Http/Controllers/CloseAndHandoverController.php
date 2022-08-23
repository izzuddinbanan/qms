<?php

namespace App\Http\Controllers;

use Auth;
use File;
use Validator;
use Carbon\Carbon;
use App\Entity\Role;
use App\Entity\Submission;
use App\Entity\Attribute;
use App\Entity\FormSection;
use App\Entity\DrawingPlan;
use App\Entity\FormVersion;
use Illuminate\Http\Request;
use App\Entity\IssueFormSubmission;
use App\Entity\FormAttributeLocation;
use Intervention\Image\Facades\Image;


class CloseAndHandoverController extends Controller
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
    public function index($id)
    {
        if(!$drawing_plan = DrawingPlan::find($id))
        {
            return back()->withErrors('Unit not found.');
        }

        $drawing_set = $drawing_plan->drawingSet;

        $form = $drawing_plan->drawingSet->close_and_handover_form->latestVersion;

        $roles = Role::whereIn('name', [
            'admin',
            'inspector',
            'owner'
        ])->select('id', 'name', 'display_name')->get();

        $form_version = FormVersion::with([
            'formGroup',
            'forms.formAttributes.locations',
            'forms.formAttributes.attribute',
            'forms.formAttributes.roles'
        ])->find($form[0]->id);

        $sections = FormSection::where('form_version_id', $form_version->id)->select('id', 'name', 'sequence')->get();

        $option = Attribute::all();

        return view('close_and_handover.index', compact('drawing_plan', 'form_version', 'option', 'sections', 'roles'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
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
        //
    }

    public function submit($id, Request $request)
    {   
        $user = Auth::user();
        $submission_type = "Close And Handover";

        if(!$plan = DrawingPlan::find($request->input('drawing_plan_id'))){
            return back()->withErrors('Drawing Plan not found.');
        }

        if($plan->ready_to_handover != 0)
        {
            return back()->withErrors('Drawing Plan is already closed and ready to handover.');
        } 

        // check if all location of the unit is ready to handover
        foreach($plan->location as $locations)
        {
            // if($locations->status_id != 2)
            // {
            //     return back()->withErorrs('Please make sure all location is ready to handover.');
            // }
        }

        //setup reference value
        $last = IssueFormSubmission::where('drawing_plan_id', $request->input('data.drawing_plan_id'))->count() + 1;
        $date = Carbon::now()->format('dmy');
        $form_group_id = $request->input('data.form_id');
        $reference_no = "$date-F$form_group_id-DP$request->input('data.drawing_plan_id')-R$last";

        try {
            \DB::beginTransaction();

            $submission = IssueFormSubmission::create([
                'reference_no'          => $reference_no,
                'drawing_plan_id'       => $request->input('drawing_plan_id'),
                'user_id'               => $user->id,
                'form_version_id'       => $request->input('form_id'),
                'remarks'               => $request->input('remarks') ?? '',
                'submission_type'       => $submission_type,   
                'accept_issue'          => [],
                'redo_issue'            => [],
            ]);
            
            foreach ($request->input() as $key => $value) 
            {
                if($key!="_token" && $key!="drawing_plan_id" && $key!="form_id")
                {
                    $form_attribute_location = FormAttributeLocation::find($key);
                    $form_attribute = $form_attribute_location->formAttribute;

                    switch ($form_attribute->attribute_id) {
                        case 1: // long text
                            $form_detail[] = (object)[
                                "form_attribute_location_id"    => $key,
                                "value"                         => $value,
                            ];
                            break;
                        case 2: // short text
                            $form_detail[] = (object)[
                                "form_attribute_location_id"    => $key,
                                "value"                         => $value,
                            ];
                            break;
                        case 9: // dropdown box
                            $form_detail[] = (object)[
                                "form_attribute_location_id"    => $key,
                                "value"                         => $value,
                            ];
                            break;
                        case 3: // signature
                            $image_file = $this->base64_to_jpeg($value,123);
                            
                            $name_unique = 'signature_' . time() . rand(10, 99) . '.png';
                            $store_path = Submission::FILE_PATH . '/';
                            $path = public_path($store_path);
                            
                            if (! File::isDirectory($path)) {
                                File::makeDirectory($path, 0775, true);
                            }
                            
                            $image = Image::make($image_file);
                            
                            $size['width'] = $image->width();
                            $size['height'] = $image->height();
                            
                            $image->save($path . DIRECTORY_SEPARATOR . '' . $name_unique);
                            
                            $form_detail[] = (object)[
                                "form_attribute_location_id"    => $key,
                                "value"                         => asset($store_path) . '/' . $name_unique,
                            ];
                            break;
                        case 5: // date
                            
                            $date_input = new Carbon($value);                        
                            $form_detail[] = (object)[
                                "form_attribute_location_id"    => $key,
                                "value"                         => date("d-m-Y", strtotime($value)),
                            ];
                            break;
                        
                        case 6: // checkbox
                            $form_detail[] = (object)[
                                "form_attribute_location_id"    => $key,
                                "value"                         => $value == 1 ? 1 : 0,
                            ];
                            break;
                        case 7: // choice
                            $form_detail[] = (object)[
                                "form_attribute_location_id"    => $key,
                                "value"                         => $value == 1 ? 1 : 0,
                            ];
                            break;
                    }    
                }
            }

            $submission->update([
                'details'           => $form_detail,
            ]);

            $plan->update([
                'ready_to_handover' => 1,
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }

        return redirect()->route('unit.show', [$plan->id])->with(['success-message' => 'Form submitted successfully.']);

        // return url('unit.index')->with(['success-message' => 'Form submitted successfully.']);
    }

    function base64_to_jpeg($base64_string, $output_file) {

        $output_file = public_path('uploads/154141252885.png');
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' ); 

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );

        // clean up the file resource
        fclose( $ifp ); 

        return $output_file; 
    }

}
