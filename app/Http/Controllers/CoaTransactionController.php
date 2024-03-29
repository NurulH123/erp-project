<?php

namespace App\Http\Controllers;

use App\Models\CoaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

class CoaTransactionController extends Controller
{

    public function index()
    {
        $user = auth()->user()->employee;
        $companyId = $user->company->id;

        $sort = request('sort') ?? '5';
        // $search = request('search') ?? '';

        $coaTransaction = CoaTransaction::where('companiable_id', $companyId)
                            ->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $coaTransaction,
        ]);
    }

    public function updateTransaction(Request $request, CoaTransaction $transaction)
    {
        $data = $request->only('type', 'nominal', 'desc');
        $user = auth()->user()->employee;
        $company = $user->company;

        $validator = Validator::make($data, [
            'type' => 'required|in:cash,bank',
            'nominal' => 'required'
        ], [
            'type.required' => 'Tipe Masih Kosong',
            'type.in' => 'Tipe Tidak Sesuai',
            'nominal.required' => 'Nominal Harus Diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        // mengambil jumlah uang yg harus dibayar
        $classParent = $transaction->invoiceable;
        $coaTransaction = $classParent->coaTransaction;
        $class = get_class($classParent);

        if ($coaTransaction->sum('nominal') == 0) {
            $transaction->update($data);
        } else {

            // create transaksi
            $data['companiable_id'] = $company->id;
            $data['companiable_type'] = get_class($company);
            $data['debet'] = $transaction->debet;
            $data['kredit'] = $transaction->kredit;

            
            $classParent->coaTransaction()->create($data);
        }

        $newParent = $class::find($classParent->id);
        $sumCoa = $newParent->coaTransaction->sum('nominal');
        $isPayed = $sumCoa >= $classParent->total_pay ? true : false;
        $transaction = $classParent->update(['is_payed' => $isPayed]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi Telah Ditambahkan',
            // 'data_transaction' => $transaction
        ], Response::HTTP_CREATED);
    }
}
