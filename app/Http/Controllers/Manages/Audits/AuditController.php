<?php

namespace App\Http\Controllers\Manages\Audits;

use App\Entity\User;
use App\Entity\Audit;
use App\Entity\RoleUser;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;


class AuditController extends Controller
{

    public function index(Request $request)
    {
        return view('audit.index');
    }

    public function indexData () {


    	if(role_user()->role_id == 1) {

            $user_id = User::select('id')->get();

        } else {

        	$user_id = RoleUser::where('client_id', role_user()->client_id)->select('user_id')->groupBy('user_id')->get();
        }


        $audit = Audit::leftJoin('users', 'audits.user_id', '=', 'users.id')->whereIn('user_id', $user_id)->select(['audits.created_at', 'audits.auditable_type', 'audits.event', 'audits.old_values', 'audits.new_values', 'users.name as name','users.email as email']);

        return Datatables::of($audit)
        	->editColumn('created_at', function ($audit) {
                
                return $audit->created_at->toDateTimeString();
            })
            ->editColumn('auditable_type', function ($audit) {
                	
                $type = explode('\\', $audit->auditable_type);
                return end($type); 
            })
            ->addColumn('old_values', function ($audit) {
                return $this->prettyPrint( $audit->old_values );
            })
            ->editColumn('new_values', function ($audit) {
                return $this->prettyPrint( $audit->new_values );
            })
            ->rawColumns(['old_values'])
            ->make(true);

    }

    function prettyPrint( $json )
	{
	    $result = '';
	    $level = 0;
	    $in_quotes = false;
	    $in_escape = false;
	    $ends_line_level = NULL;
	    $json_length = strlen( $json );

	    for( $i = 0; $i < $json_length; $i++ ) {
	        $char = $json[$i];
	        $new_line_level = NULL;
	        $post = "";
	        if( $ends_line_level !== NULL ) {
	            $new_line_level = $ends_line_level;
	            $ends_line_level = NULL;
	        }
	        if ( $in_escape ) {
	            $in_escape = false;
	        } else if( $char === '"' ) {
	            $in_quotes = !$in_quotes;
	        } else if( ! $in_quotes ) {
	            switch( $char ) {
	                case '}': case ']':
	                    $level--;
	                    $ends_line_level = NULL;
	                    $new_line_level = $level;
	                    break;

	                case '{': case '[':
	                    $level++;
	                case ',':
	                    $ends_line_level = $level;
	                    break;

	                case ':':
	                    $post = " ";
	                    break;

	                case " ": case "\t": case "\n": case "\r":
	                    $char = "";
	                    $ends_line_level = $new_line_level;
	                    $new_line_level = NULL;
	                    break;
	            }
	        } else if ( $char === '\\' ) {
	            $in_escape = true;
	        }
	        if( $new_line_level !== NULL ) {
	            $result .= "\n".str_repeat( "\t", $new_line_level );
	        }
	        $result .= $char.$post;
	    }

	    return $result;
	}
}
