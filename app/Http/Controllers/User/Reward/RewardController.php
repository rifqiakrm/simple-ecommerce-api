<?php

namespace App\Http\Controllers\User\Reward;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Module\Builder\PointTransactionBuilder;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
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

    public function index()
    {
        $rewards = Reward::all();
        return $this->successResponse(200, '', $rewards);
    }

    public function buy()
    {
        $rules = [
            'reward_id' => 'required',
        ];

        $messages = [
            'reward_id.required' => 'Tipe Reward tidak boleh kosong.',
        ];

        $validate = $this->validator($this->request->all(), $rules, $messages);

        if ($validate) {
            return $this->errorResponse(400, $validate);
        }

        $reward = Reward::where('id', $this->decode($this->request->reward_id))->first();

        if (!$reward) {
            return $this->errorResponse(400, 'Reward tidak ditemukan');
        }

        if ($this->request->user()->points < $reward->price) {
            return $this->errorResponse(400, 'Point tidak cukup. Tingkatkan lagi transaksi kamu.');
        }

        $builder = new PointTransactionBuilder($this->request->user());
        $builder->addDebit((int) $reward->price);
        $builder->setTrxId(null);
        $builder->setDescription('Beli ' . $reward->name . ' seharga ' . $reward->price . ' poin.');
        $builder = $builder->save();

        return $this->successResponse(200, 'Beli ' . $reward->name . ' seharga ' . $reward->price . ' poin berhasil!', null);
    }
}
