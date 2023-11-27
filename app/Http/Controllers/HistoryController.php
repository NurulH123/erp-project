<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function orderHistory(Request $request, $id)
    {
        $histories = History::where('order_id', $id)->get();
        $data = [];
        foreach ($histories as $history) {
            $noted['status'] = $history->status;
            if ($history->user == null) {

                $noted['username'] = 'Customer';

            } else {

                $noted['username'] = $history->user->username;
            }

            $noted['date'] = Carbon::parse($history->created_at)->translatedFormat('d/m/Y H:i');

            $data[] = $noted;
        }

        return response()->json(['data' => $data]);
    }
}
