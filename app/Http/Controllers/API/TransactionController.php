<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaction = Transaction::orderBy('time', 'DESC')->get();
        
        return response()->json([
            'message' => 'List transaction order by time',
            'data' => $transaction
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'title' => 'required|min:3',
            'amount' => 'required|digits_between:1,9999999',
            'type' => 'required|in:expense,revenue'
        ],[
            'amount.digits_between' => 'Amount must be a number'
        ]);

        if($validatedData->fails()) {
            return response()->json($validatedData->errors(), 422);
        }

        try {
            $transaction = Transaction::create($request->all());

            return response()->json([
                'message' => 'Transaction created',
                'data' => $transaction
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Failed ' . $e->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        return response()->json([
            'message' => 'Detail transaction',
            'data' => $transaction
        ], 200);
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
        $transaction = Transaction::findOrFail($id);
        $validatedData = Validator::make($request->all(), [
            'title' => 'required|min:3',
            'amount' => 'required',
            'type' => 'required|in:expense,revenue'
        ]);

        if($validatedData->fails()) {
            return response()->json($validatedData->errors(), 422);
        }

        try {
            $transaction->update($request->all());

            return response()->json([
                'message' => 'Transaction Updated',
                'data' => $transaction
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Failed ' . $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        try {
            $transaction->delete();

            return response()->json([
                'message' => 'Transaction Deleted'
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Failed' . $e->errorInfo
            ]);
        }
    }
}
