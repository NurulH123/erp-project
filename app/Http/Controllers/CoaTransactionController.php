<?php

namespace App\Http\Controllers;

use App\Models\COA;
use App\Models\CoaTransaction;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CoaTransactionController extends Controller
{
    public function index()
    {
        $user = auth()->user()->employee;
        $companyId = $user->company->id;

        $sort = request('sort') ?? '5';
        
        $coaTransaction = CoaTransaction::where('companiable_id', $companyId)
                            ->with([
                                'invoiceable:id,code_transaction,total_pay', 
                                'kredit:id,code,name_account',
                                'debet:id,code,name_account',
                                'user:id,username',
                            ])
                            ->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $coaTransaction,
        ]);
    }

    public function updateTransaction(Request $request, CoaTransaction $transaction)
    {
        $user = auth()->user()->employee;
        $company = $user->company;

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:cash,bank',
            'nominal' => 'required',
            'debet' => 'required',
            'kredit' => 'required'
        ], [
            'type.required' => 'Tipe Masih Kosong',
            'type.in' => 'Tipe Tidak Sesuai',
            'nominal.required' => 'Nominal Harus Diisi',
            'debet.required' => 'Debet Harus Diisi',
            'kredit.required' => 'Kedit Harus Diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed', 
                'message' => $validator->errors()
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        // mengambil jumlah uang yg harus dibayar
        $data = $request->only('type', 'nominal', 'desc', 'debet', 'kredit');
        $debet = $request->input('debet');
        $kredit = $request->input('kredit');

        $classParent = $transaction->invoiceable;
        $coaTransaction = $classParent->coaTransaction;
        $class = get_class($classParent);

        if ($coaTransaction->sum('nominal') == 0) {
            $data['user_id'] = auth()->user()->id;
            $transaction->update($data);
        } else {

            // create transaksi
            $data['companiable_id'] = $company->id;
            $data['companiable_type'] = get_class($company);
            $data['debet'] = $debet;
            $data['kredit'] = $kredit;
            $data['user_id'] = auth()->user()->id;
            
            $classParent->coaTransaction()->create($data);
        }

        $newParent = $class::find($classParent->id);
        $sumCoa = $newParent->coaTransaction->sum('nominal');
        $isPayed = $sumCoa >= $classParent->total_pay ? true : false;
        $transaction = $classParent->update(['is_payed' => $isPayed]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi Telah Ditambahkan',
        ], Response::HTTP_CREATED);
    }

    public function show(CoaTransaction $transaction)
    {
        return response()->json([
            'status' => 'success',
            'data' => $transaction
        ]);
    }
}
