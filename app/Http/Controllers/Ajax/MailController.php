<?php

namespace App\Http\Controllers\Ajax;

use App\Entity\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\AdminReponded as AdminRepondedNotification;

class MailController extends Controller
{
    public function getOutbox()
    {
        $mails = Mail::with('recipient')->outbox()->latest()->paginate(20);

        return response()->json([
            'mails' => $mails,
        ]);
    }

    public function getDraft()
    {
        $mails = Mail::with('recipient')->draft()->latest()->paginate(20);

        return response()->json([
            'mails' => $mails,
        ]);
    }

    public function getTotalOutbox()
    {
        $total = Mail::outbox()->count();

        return response()->json([
            'total' => $total,
        ]);
    }

    public function getTotalDraft()
    {
        $total = Mail::draft()->count();

        return response()->json([
            'total' => $total,
        ]);
    }

    /**
     * @param Request $request
     */
    public function send(Request $request)
    {
        $mail = Mail::find($request->input('data.id'));
        try {
            $mail->recipient->notify(new AdminRepondedNotification($mail));
            $mail->forcefill(['status' => 'OUTBOX'])->save();
            return response()->json([
                'status'  => 'ok',
                'message' => 'Email sent.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'    => 'fail',
                'message'   => 'Failed to send.',
                'error_msg' => $e,
            ]);
        }
    }

    /**
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        foreach ($request->input('data') as $key => $value) {
            Mail::where('id', $value['id'])->delete();
        }
        return response()->json([
            'message' => 'Delete successful.',
        ]);
    }
}
