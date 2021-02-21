<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function markSolved(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $report = Report::where('id', '=', $request->reportID)->first();
            if($report->report_type === 2){
                DB::table('reports')->where('report_type', '=', $report->report_type)->where('reports.link', '=', $report->link)->update(array('solved' => 1));
            }
            return response() -> json(['message' => "success"], 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 417);
        }
    }

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

    public function getReports(Request $request){
        try{
            //$reports = Report::where('solved', '=', 0)->get();
            $reports = DB::table('reports')->leftJoin('users as reported', function($join){
                $join->on('reports.reported_id', '=', 'reported.id');
            })->leftJoin('users as sender', function($join2){
                $join2->on('reports.sender_id', '=', 'sender.id');
            })->select('reports.updated_at', 'reports.reported_id', 'reports.sender_id', 'reports.reason', 'reports.report_type'
            ,'reports.link', 'sender.firstName as senderFName', 'sender.lastName as senderLName', 'reported.firstName as reportedFName',
            'reported.lastName as reportedLName', 'reports.id')->where('reports.solved', '=', 0)->get();
            return response()->json(['reports' => $reports], 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 417);
        }
    }
    public function getReportsResponse(Request $request){
        try{
            if($request->type === "POST"){
                $reports = DB::table('reports')->leftJoin('users as reported', function($join){
                    $join->on('reports.reported_id', '=', 'reported.id');
                })->leftJoin('users as sender', function($join2){
                    $join2->on('reports.sender_id', '=', 'sender.id');
                })->select('reports.id','reports.updated_at', 'reports.reported_id', 'reports.sender_id', 'reports.reason', 'reports.report_type'
                    ,'reports.link', 'sender.firstName as senderFName', 'sender.lastName as senderLName', 'reported.firstName as reportedFName',
                    'reported.lastName as reportedLName', 'reports.id', 'reports.reason', 'reported.link_profile as reportedProfile', 'sender.link_profile as senderProfile')->where('reports.solved', '=', 0)
                    ->where('reports.link', '=', $request->forID)->get();
            }
            return response()->json(['reports' => $reports], 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 417);
        }
    }
}
