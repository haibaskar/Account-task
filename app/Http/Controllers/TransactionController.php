<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Log a transaction
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'account_id' => 'required|exists:users,id',
            'type' => 'required|in:Credit,Debit',
            'amount' => 'required|numeric|min:0.01',
        ]);
    
        if($validated->fails()){
            $response = [
                'error'    => $validated->errors(),
            ];
            return response()->json($response);
        }

        $account = User::where('id', $request['account_id'])
            ->firstOrFail();

        if ($request['type'] === 'Debit' && $account->balance < $request['amount']) {
            return response()->json(['error' => 'Insufficient funds'], 400);
        }

        $transaction = Transaction::create([
            'id' => Str::uuid(),
            'account_id' => $request['account_id'],
            'type' => $request['type'],
            'amount' => $request['amount'],
            'description' => $request['description'],
        ]);

        $account->update([
            'balance' => $request['type'] === 'Credit'
                ? $account->balance + $request['amount']
                : $account->balance - $request['amount'],
        ]);

        return response()->json(['message' => 'Transaction recorded', 'data' => $transaction], 201);
    }

    /**
     * Get transactions with filters
     */
    public function index(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'account_id' => 'required',
            'from' => 'date',
            'to' => 'date|after_or_equal:from',
        ]);
    
        if($validated->fails()){
            $response = [
                'error'    => $validated->errors(),
            ];
            return response()->json($response);
        }
       
        $account_id=$request['account_id'];
        $from=$request['from'];
        $to=$request['to'];

        $transactions = Transaction::where('account_id', $account_id)
                                ->whereDate('created_at', '>=', $from)
                                ->whereDate('created_at', '<=', $to)
                                ->orderBy('created_at', 'asc')
                                ->get();

        $user = Auth::user();

        //Generate PDF Report
        $pdf = Pdf::loadView('pdf.transaction_report', compact('transactions','user'));
        $fileName = 'transaction_report_' . Carbon::now()->format('Ymd_His') . '.pdf';
        Storage::disk('public')->put('reports/' . $fileName, $pdf->output());
        $fileUrl = asset('public/storage/reports/' . $fileName);
       
        if($transactions->isEmpty()){
            return response()->json(['message' => 'Record not found.','data' => $transactions], 201);
        }
        
        return response()->json(['message' => 'Available records','DownloadReport'=>$fileUrl, 'data' => $transactions], 200);
    }
}

