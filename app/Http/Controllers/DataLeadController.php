<?php

namespace App\Http\Controllers;

use App\Models\DataLead;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DataLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $data = $request->validate([
            'sumber_lead' => 'required',
            'jumlah_lead' => 'required',
        ]);

        $yesterday = Carbon::yesterday('Asia/Jakarta');
        $time = Carbon::now()->format('H:i:s');
        $data['created_at'] = "$request->date $time";
        $data['user_id'] = $request->user()->id;

        $lead = DataLead::create($data);

        return response()->json(['message' => 'berhasil disimpan'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DataLead $dataLead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataLead $dataLead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataLead $dataLead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = DataLead::destroy($id);

        return response()->json(['message' => 'berhasil dihapus'], 200);
    }
}
