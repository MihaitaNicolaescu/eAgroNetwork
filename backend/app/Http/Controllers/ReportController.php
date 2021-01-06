<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    public function sendReport(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $report = new Report();
            $report->reported_id = $request->reported_id;
            $report->sender_id = $recived['id'];
            $report->reason = $request->reason;
            $report->report_type = $request->type;
            $report->link = $request->link;
            $report->save();
            return response() -> json(['message' => "success"], 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 417);
        }
    }
}
