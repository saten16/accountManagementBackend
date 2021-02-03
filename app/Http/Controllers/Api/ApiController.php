<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounts;
use App\Models\Transactions;

use Validator; 
use DB;
use Illuminate\Support\Str;

class ApiController extends Controller {

    protected $transactionsModel;
    protected $accountsModel;

    function __construct() {
        $this->transactionsModel = new Transactions();
        $this->accountsModel = new Accounts();
    }

    // Healhcheck to make sure the service is responsive.
    function ping(Request $request) {
        return response()->json(['msg' => 'The service is up and running.'], 200);
    }

    // Creates a new transaction which updates the current account balance.
    function amount(Request $request) {

        $receivedData = $request->all();

        $validator = Validator::make($receivedData, [
            'account_id'=>'required|uuid',
            'amount'=>'required|integer'
        ]);

        if ($validator->passes()) {

            $insertAccountsData = [];
            $transactionData = [];

            $response_array = [];

            if ($this->accountsModel::where('account_id', $receivedData['account_id'])->exists()) {

                $oldAccountData = DB::table('accounts')->orderBy('balance', 'desc')->first();
                $oldBalance = (int)$oldAccountData->balance;

                $newBalance = $oldBalance + $receivedData['amount'];
                $this->accountsModel::where('account_id', '=', $receivedData['account_id'])->update(array('balance' => $newBalance));

            }else {
                $insertAccountsData['account_id'] = $receivedData['account_id'];
                $insertAccountsData['balance'] = $receivedData['amount'];

                $this->accountsModel->account_id = $receivedData['account_id'];
                $this->accountsModel->balance = $receivedData['amount'];
                $this->accountsModel::insert($insertAccountsData);
            }

            $transactionData['transaction_id'] = (string)Str::uuid();
            $transactionData['account_id'] = $receivedData['account_id'];
            $transactionData['amount'] = $receivedData['amount'];

            $this->transactionsModel::insert($transactionData);

            $response_array['account_id'] = $receivedData['account_id'];
            $response_array['amount'] = $receivedData['amount'];

            return response()->json($response_array, 200);

        } else {
            return response()->json(['err' => 'Mandatory body parameters missing ( account_id, amount ) or have incorrect type.'], 400);
        }
    }

    // Returns the transaction.
    public function get_transaction_data($transactionID = null) {

        if (is_null($transactionID) || !isset($transactionID)) {
            return response()->json(['err' => 'Transaction not found'], 404);
        }

        $transactionData = $this->transactionsModel::find($transactionID);

        if (is_null($transactionData)) {
            return response()->json(['err' => 'Transaction not found'], 404);
        }else {

            $sendData = [];
            $sendData['account_id'] = $transactionData->account_id;
            $sendData['amount'] = (int)$transactionData->amount;

            return response()->json($sendData, 200);
        }
    }

    // Returns the current account balance.
    public function get_account_balance($accountID = null) {

        if (is_null($accountID) || !isset($accountID)) {
            return response()->json(['err' => 'Account not found'], 404);
        }

        $accountData = $this->accountsModel::find($accountID);

        if (is_null($accountData)) {
            return response()->json(['err' => 'Account not found'], 404);
        }else {

            $sendData = [];
            $sendData['balance'] = (int)$accountData->balance;

            return response()->json($sendData, 200);
        }
    }

    // Returns accounts with the max number of transactions.
    public function max_transaction_volume(Request $request) {

        $allTransactionsData = $this->transactionsModel::all()->groupBy('account_id');

        $maxVolume = 0;
        $accountsIDs = [];

        foreach ($allTransactionsData as $accountID => $transactions) {
            if (count($transactions) > $maxVolume) {
                $maxVolume = count($transactions);
            }
        }

        foreach ($allTransactionsData as $accID => $transaction) {
            if (count($transaction) == $maxVolume) {
                array_push($accountsIDs, $accID);
            }
        }

        $responseArray = [];
        $responseArray['maxVolume'] = $maxVolume;
        $responseArray['accounts'] = $accountsIDs;

        return response()->json($responseArray, 200);
    }
}
