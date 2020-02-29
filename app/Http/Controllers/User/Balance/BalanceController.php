<?php

namespace App\Http\Controllers\User\Balance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Module\Builder\BalanceTransactionBuilder;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * Class constructor.
     *
     * @param \Illuminate\Http\Request $request User Request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->posted = $request->except('_token', '_method');
    }

    public function topup()
    {
        $rules = [
            'balance' => 'required|numeric',
        ];

        $messages = [
            'balance.required' => 'Harga produk tidak boleh kosong.',
            'balance.numeric' => 'Harga produk tidak boleh mengandung huruf.',
        ];

        $validate = $this->validator($this->request->all(), $rules, $messages);

        if ($validate) {
            return $this->errorResponse(400, $validate);
        }

        $balance = new BalanceTransactionBuilder($this->request->user());
        $balance->addCredit($this->request->balance);
        $balance->setDescription('Top Up Balance ' . $this->rupiahFormat($this->request->balance));
        $balance = $balance->save();

        return $this->successResponse(200, 'Top Up ' . $this->rupiahFormat($this->request->balance) . ' Berhasil!', null);
    }

    public function history()
    {
        return $this->successResponse(200, '', $this->request->user()->balance()->orderBy('id', 'desc')->paginate(10));
    }
}
