<?php

namespace App\Http\Controllers\User\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Module\Builder\BalanceTransactionBuilder;
use App\Http\Controllers\Module\Builder\PointTransactionBuilder;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;

class TransactionController extends Controller
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

    public function buy()
    {
        $rules = [
            'products' => 'required',
            'products.*.id' => 'required',
            'products.*.pcs' => 'required|numeric|min:1',
        ];

        $messages = [
            'products.required' => 'Produk tidak boleh kosong.',
            'products.*.id.required' => 'Produk tidak boleh kosong.',
            'products.*.pcs.required' => 'Jumlah Produk tidak boleh kosong.',
            'products.*.pcs.min' => 'Minimum pembelian adalah 1 produk.',
        ];

        $validate = $this->validator($this->request->all(), $rules, $messages);

        if ($validate) {
            return $this->errorResponse(400, $validate);
        }

        $trx_id = $this->generateTrxId();
        $price = 0;

        foreach ($this->request->products as $product) {
            $search = Product::where('id', $this->decode($product['id']))->first();

            if (!$search) {
                return $this->errorResponse('404', 'Produk tidak ditemukan.');
            }

            $price += $search->price * $product['pcs'];
        }

        if ($this->request->user()->balance < $price) {
            return $this->errorResponse('400', 'Saldo tidak cukup. Top up saldo kamu sekarang.');
        }

        Transaction::create([
            "user_id" => $this->request->user()->id,
            "trx_id" => $trx_id,
        ]);

        foreach ($this->request->products as $product) {
            $search = Product::where('id', $this->decode($product['id']))->first();

            $balance = new BalanceTransactionBuilder($this->request->user());
            $balance->addDebit($search->price * $product['pcs']);
            $balance->setDescription('Pembelian Produk ' . $search->name . ' sejumlah ' . $product['pcs'] . ' buah. Harga Beli :' . $this->rupiahFormat($search->price * $product['pcs']));
            $balance = $balance->save();

            TransactionDetail::create([
                "trx_id" => $trx_id,
                "balance_id" => $balance->id,
                "product_id" => $search->id,
                "pcs" => $product['pcs'],
                "price" => $search->price * $product['pcs'],
            ]);
        }

        $mod = $price % 100000;

        if ($mod > 0) {
            $price = $price - $mod;
        }

        $points = $price / 100000 * 10;

        if ($points > 0) {
            $builder = new PointTransactionBuilder($this->request->user());
            $builder->addCredit($points);
            $builder->setTrxId($trx_id);
            $builder->setDescription('Mendapatkan poin  sebesar ' . $points . ' dari transaksi ' . $trx_id);
            $builder = $builder->save();

            $message = 'Transaksi berhasil! Saldo kamu saat ini : ' . $this->rupiahFormat($balance->balance) . '. Kamu juga mendapatkan ' . $points . ' poin dari pembelian kamu!';
        } else {
            $message = 'Transaksi berhasil! Saldo kamu saat ini : ' . $this->rupiahFormat($balance->balance);
        }

        return $this->successResponse(200, $message, null);

    }

    public function history()
    {
        return $this->successResponse(200, '', $this->request->user()->transactions()->paginate(10));
    }
}
