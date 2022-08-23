<?php

namespace App\Http\Controllers\Manages\KeyAccess;

use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\ItemSubmitted;
use App\Entity\ItemSubmittedTransaction;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use File, Session;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Yajra\Datatables\Datatables;

class KeyAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('key-access.index');
    }

    public function indexData(){

        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();
        $unit = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('types', 'unit')->with(['unitOwner', 'itemSubmitted'])->select(['drawing_plans.*']);
        return Datatables::of($unit)
            ->addColumn('owner', function ($unit) {
                
                if($unit->unitOwner){
                    return $unit->unitOwner->name;// .'<br>('. $unit->unitOwner->email .')' ;
                }
                return '<label class="label label-success">VACANT</label>';

            })
            ->addColumn('item-total', function ($unit) {
                
                return '<font style="color : '. ($unit->itemSubmitted->count() == 0 ? 'red' : 'black') .'">' . $unit->itemSubmitted->count() . '</font>';

            })
            ->addColumn('action', function ($unit) {
                
                return '<a href="'. ($unit->unitOwner ? route('key-access.show', [$unit->id]) : 'javascript:void()') .'" data-popup="tooltip" title="'. trans('main.view') .'" data-placement="top" class="edit_button" style="color: '. ($unit->unitOwner ? '' : 'grey') .'"><i class="fa fa-eye fa-lg"></i></a>';

            })
            ->rawColumns(['owner', 'action', 'item-total'])
            ->make(true);
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

        if(!$DrawingPlan = DrawingPlan::find($id)) {
            return redirect()->route('key-access.index')->withErrors(trans('main.record-not-found'));
        }

        return view('key-access.show', compact('DrawingPlan'));
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
        //
    }


    public function transactionData($id){

        $transaction = ItemSubmittedTransaction::where('drawing_plan_id', $id)->select(['item_submitted_transaction.*']);

        return Datatables::of($transaction)
            ->editColumn('created_at', function ($transaction) {
                return $transaction->created_at->toDateTimeString();
            })
            ->editColumn('status', function ($transaction) {
                return $transaction->getStatus();
            })
            ->addColumn('action', function ($transaction) use($id) {

                $url = url('key-access/'. $id . '/transaction/' . $transaction->id);

                return '<a href="'. $url .'" data-popup="tooltip" title="'. trans('main.view') .'" data-placement="top"><i class="fa fa-eye fa-lg"></i></a>';
            })
            ->make(true);
    }


    public function trasactionShow($id, $transaction_id){


        if(!$DrawingPlan = DrawingPlan::find($id)) {
            return redirect()->route('key-access.show', $id)->withErrors(trans('main.record-not-found'));
        }


        if(!$transaction = ItemSubmittedTransaction::find($transaction_id)) {
            return redirect()->route('key-access.show', $id)->withErrors(trans('main.record-not-found'));
        }


        return view('key-access.transactions.index', compact('DrawingPlan', 'transaction'));
    }

    public function trasactionCreate($id) {

        if(!$DrawingPlan = DrawingPlan::find($id)) {
            return redirect()->route('key-access.show', $id)->withErrors(trans('main.record-not-found'));
        }

        $ItemSubmitted = ItemSubmitted::where('drawing_plan_id', $id)->get();

        return view('key-access.transactions.create', compact('DrawingPlan', 'ItemSubmitted'));

    }

    public function trasactionStore(Request $request) {

        

        if(!$DrawingPlan = DrawingPlan::find($request->input('drawing_plan_id'))) {
            return redirect()->route('key-access.show', $id)->withErrors(trans('main.record-not-found'));
        }


        switch ($request->input('type_transaction')) {
            case 'submit':
                $this->submitTransaction($request);
                break;

            case 'handover_submit':
                $this->handOverSubmitReturnTransaction($request);
                break;

            case 'handover_return':
                $this->handOverSubmitReturnTransaction($request);
                break;

            case 'return':
                $this->returnTransaction($request);
                break;

        }


        return redirect()->route('key-access.show', [$request->input('drawing_plan_id')])->with(['success-message' => 'Record successfully updated.']);
    }


    public function submitTransaction($request) {


        for($i = 0; $i < count($request->input('code')); $i++) {

            for($quantity = 0; $quantity < $request->input('quantity')[$i]; $quantity++) {

                ItemSubmitted::create([
                    'drawing_plan_id'   => $request->input('drawing_plan_id'),
                    'code'              => $request->input('code')[$i],
                    'name'              => $request->input('name')[$i],
                    'possessor'          => 'management',
                    'created_by'        => \Auth::user()->id,
                    'updated_by'        => \Auth::user()->id,
                ]);
            }
            
            $transaction_item[] = (object)[
                "code"      => $request->input('code')[$i],
                "name"      => $request->input('name')[$i],
                "quantity"  => $request->input('quantity')[$i],
            ];
            
        }

        ItemSubmittedTransaction::create([
            'items'     => $transaction_item,
            'code'      => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
            'status'    => 'receive',
            'drawing_plan_id'   => $request->input('drawing_plan_id'),
            'signature_submit'  => $this->decodeImagebase64 (explode(",", $request->input('signature_submitted'))[1]),
            'signature_receive' => $this->decodeImagebase64 (explode(",", $request->input('signature_received'))[1]),
            'signature_submit_datetime'  => now(),
            'signature_receive_datetime'  => now(),
            'name_submit'       => $request->input('submitted_name'),
            'name_receive'      => $request->input('received_name'),
            'internal_remarks'  => $request->input('remarks'),
            'created_by'        => \Auth::user()->id,
            'updated_by'        => \Auth::user()->id,
        ]);

    }


    public function handOverSubmitReturnTransaction($request) {

        foreach ($request->input('item_handover') as $itemId) {
            if(!$item = ItemSubmitted::where('id', $itemId)->where('drawing_plan_id', $request->input('drawing_plan_id'))->first()) {
                return redirect()->route('key-access.show', [$request->input('drawing_plan_id')])->with(['warning-message' => 'Record not found.']);

            }
        }

        foreach ($request->input('item_handover') as $itemId) {
            $item = ItemSubmitted::where('id', $itemId)
                    ->update([
                        'possessor'         => $request->input('type_transaction') == 'handover_submit' ? 'handler' : 'management',
                        'updated_by'        => \Auth::user()->id,
                    ]);

        }

        $item_transaction = ItemSubmitted::whereIn('id', $request->input('item_handover'))->groupBy('name')->groupBy('code')->get();

        $transaction_item = [];
        foreach ($item_transaction as $key => $value) {

            $temp_count = ItemSubmitted::whereIn('id', $request->input('item_handover'))->where('name', $value['name'])->count();

            $transaction_item[] = (object)[
                "code"      => $value["code"],
                "name"      => $value["name"],
                "quantity"  => $temp_count,
            ];

        }

            
        ItemSubmittedTransaction::create([
            'items'     => $transaction_item,
            'code'      => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
            'status'    => $request->input('type_transaction') == 'handover_submit' ? 'handover_submit' : 'handover_return',
            'drawing_plan_id'   => $request->input('drawing_plan_id'),
            'signature_submit'  => $this->decodeImagebase64 (explode(",", $request->input('signature_submitted'))[1]),
            'signature_receive' => $this->decodeImagebase64 (explode(",", $request->input('signature_received'))[1]),
            'signature_submit_datetime'  => now(),
            'signature_receive_datetime'  => now(),
            'name_submit'       => $request->input('submitted_name'),
            'name_receive'      => $request->input('received_name'),
            'internal_remarks'  => $request->input('remarks'),
            'created_by'        => \Auth::user()->id,
            'updated_by'        => \Auth::user()->id,
        ]);

    }

    public function returnTransaction($request) {

        foreach ($request->input('item_handover') as $itemId) {
            if(!$item = ItemSubmitted::where('id', $itemId)->where('drawing_plan_id', $request->input('drawing_plan_id'))->first()) {
                return redirect()->route('key-access.show', [$request->input('drawing_plan_id')])->with(['warning-message' => 'Record not found.']);

            }
        }

        $item_transaction = ItemSubmitted::whereIn('id', $request->input('item_handover'))->groupBy('name')->groupBy('code')->get();

        $transaction_item = [];
        foreach ($item_transaction as $key => $value) {

            $temp_count = ItemSubmitted::whereIn('id', $request->input('item_handover'))->where('name', $value['name'])->count();

            $transaction_item[] = (object)[
                "code"      => $value["code"],
                "name"      => $value["name"],
                "quantity"  => $temp_count,
            ];

        }


        foreach ($request->input('item_handover') as $itemId) {
            $item = ItemSubmitted::where('id', $itemId)
                    ->delete();

        }
            
        ItemSubmittedTransaction::create([
            'items'     => $transaction_item,
            'status'    => 'return',
            'code'      => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
            'drawing_plan_id'   => $request->input('drawing_plan_id'),
            'signature_submit'  => $this->decodeImagebase64 (explode(",", $request->input('signature_submitted'))[1]),
            'signature_receive' => $this->decodeImagebase64 (explode(",", $request->input('signature_received'))[1]),
            'signature_submit_datetime'  => now(),
            'signature_receive_datetime'  => now(),
            'name_submit'       => $request->input('submitted_name'),
            'name_receive'      => $request->input('received_name'),
            'internal_remarks'  => $request->input('remarks'),
            'created_by'        => \Auth::user()->id,
            'updated_by'        => \Auth::user()->id,
        ]);
    }



    public function decodeImagebase64 ($image) {

        $decoded_image = base64_decode($image);

        $image_name = time() . rand(10, 99) . '.png';

        $path = public_path('uploads/signatures');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0775, true);
        }
        Image::make($decoded_image)->save($path . DIRECTORY_SEPARATOR . $image_name);

        return $image_name;
    }


    public function createBatchUpload() {

        Session::forget('type_selected');
        Session::forget('unit_selected');
        Session::forget('key_selected');
        Session::forget('unit_key');

        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();
        
        $unit_management = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('types', 'unit')->with(['unitOwner', 'itemManagementSubmit'])->whereHas('itemManagementSubmit')->get();
        
        $unit_handler = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('types', 'unit')->with(['unitOwner', 'itemHandlerSubmit'])->whereHas('itemHandlerSubmit')->get();

        return view('key-access.batch-uploads.create', compact('unit_handler', 'unit_management'));
    }

    public function batchUploadData(){

        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();
        $unit = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('types', 'unit')->with(['unitOwner', 'itemSubmitted'])->whereHas('itemSubmitted')->select(['drawing_plans.*']);
        return Datatables::of($unit)
            ->addColumn('owner', function ($unit) {
                
                if($unit->unitOwner){
                    return $unit->unitOwner->name;// .'<br>('. $unit->unitOwner->email .')' ;
                }
                return '<label class="label label-success">VACANT</label>';

            })
            ->addColumn('item-total', function ($unit) {
                
                return '<font style="color : '. ($unit->itemSubmitted->count() == 0 ? 'red' : 'black') .'">' . $unit->itemSubmitted->count() . '</font>';

            })
            ->addColumn('checkbox', function ($unit) {
                
                return '<input type="checkbox" name="unit[]" value="'. $unit->id .'" class="check-unit">';    
            
            })
            ->rawColumns(['owner', 'checkbox', 'item-total'])
            ->make(true);
    }

    public function batchUploadSelect(Request $request) {


        switch ($request->input('type_transaction')) {
            case 'handover_submit':
                $rules = [
                    'key-submit'       => 'array|min:1|required',
                ];
                $this->validate($request, $rules, []);

                $unit_id = array_keys($request->input('key-submit'));
                $key_id = array_values($request->input('key-submit'));
                $unit_key = $request->input('key-submit');

                $units = DrawingPlan::whereIn('id', $unit_id)->with('itemManagementSubmit')->get();

                break;
            
            case 'handover_return':
                $rules = [
                    'key-return'       => 'array|min:1|required',
                ];
                $this->validate($request, $rules, []);

                $unit_id = array_keys($request->input('key-return'));
                $key_id = array_values($request->input('key-return'));
                $unit_key = $request->input('key-return');

                $units = DrawingPlan::whereIn('id', $unit_id)->with('itemHandlerSubmit')->get();

                break;
        }


        $key_id_selected = [];
        foreach ($key_id as $key => $key_id_arr) {
            foreach ($key_id_arr as $value) {
                $key_id_selected[] = $value;
            }
        }

        Session::put('type_selected', $request->input('type_transaction'));
        Session::put('key_selected', $key_id_selected);
        Session::put('unit_selected', $unit_id);
        Session::put('unit_key', $unit_key);

        return view('key-access.batch-uploads.batch-form', compact('units'));

    }

    // public function batchUploadSelectKey(Request $request) {

    //     $key_arr = explode(',', $request->input('key'));


    //     $key_id = [];
    //     $unit_id = [];
    //     foreach ($key_arr as $key => $value) {
            
    //         if(substr($value, 0, -2) == 'key'){
    //             $key_id[] = substr($value, 4);
    //         }else{
    //             $unit_id[] = $value;
    //         }
    //     }
    //     Session::forget('key_selected');
    //     Session::put('key_selected', $key_id);

    //     if(session('type_selected') == 'handover_submit'){

    //         $units = DrawingPlan::whereIn('id', explode(',', session('unit_selected')))->with('itemManagementSubmit')->get();
    //     }else {
    //         $units = DrawingPlan::whereIn('id', explode(',', session('unit_selected')))->with('itemHandlerSubmit')->get();
    //     }



    //     return view('key-access.batch-uploads.batch-form', compact('units'));

    // }


    public function batchUploadStore (Request $request) {

        $units = DrawingPlan::whereIn('id', session('unit_selected'))->get();

        foreach ($units as $key => $UnitKey) {


            foreach (session('unit_key')[$UnitKey->id] as $item) {

                $item = ItemSubmitted::where('id', $item)
                        ->update([
                            'possessor'         => session('type_selected') == 'handover_submit' ? 'handler' : 'management',
                            'updated_by'        => \Auth::user()->id,
                        ]);

            }

            $item_transaction = ItemSubmitted::whereIn('id', session('unit_key')[$UnitKey->id])->groupBy('name')->groupBy('code')->get();

            $transaction_item = [];
            foreach ($item_transaction as $key => $value) {

                $temp_count = ItemSubmitted::whereIn('id', session('unit_key')[$UnitKey->id])->where('name', $value['name'])->where('possessor', session('type_selected') == 'handover_submit' ? 'handler' : 'management')->count();

                $transaction_item[] = (object)[
                    "code"      => $value["code"],
                    "name"      => $value["name"],
                    "quantity"  => $temp_count,
                ];

            }

            ItemSubmittedTransaction::create([
                'code'      => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
                'items'     => $transaction_item,
                'status'    => session('type_selected'),
                'drawing_plan_id'   => $UnitKey->id,
                'signature_submit'  => $this->decodeImagebase64 (explode(",", $request->input('signature_submitted'))[1]),
                'signature_receive' => $this->decodeImagebase64 (explode(",", $request->input('signature_received'))[1]),
                'signature_submit_datetime'  => now(),
                'signature_receive_datetime'  => now(),
                'name_submit'       => $request->input('submitted_name'),
                'name_receive'      => $request->input('received_name'),
                'internal_remarks'  => $request->input('remarks'),
                'created_by'        => \Auth::user()->id,
                'updated_by'        => \Auth::user()->id,
            ]);

        }
        return redirect()->route('key-access.index')->with(['success-message' => 'Record successfully updated.']);

    }


}
