<?php

namespace App\Http\Controllers\Module\Builder;

use App\Models\UserBalance;
use App\User;

/**
 * Balance Builder Class.
 *
 *
 */
class BalanceTransactionBuilder
{
    /**
     * User Model.
     *
     * @var \App\User
     */
    protected $user;

    /**
     * Account balance.
     *
     * @var decimal
     */
    protected $balance = 0.00;

    /**
     * Last user transaction.
     *
     * @var \App\Models\UserBalance
     */
    protected $last;

    /**
     * Data payload.
     *
     * @var array
     */
    protected $payload;

    /**
     * Class constructor.
     *
     * @param \App\Models\User $user Selected User
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->initializeData();
    }

    /**
     * Initialize balance and setup Transaction payload.
     *
     * @return void
     */
    private function initializeData()
    {
        $this->last = $this->getLastBalance();
        $this->balance = $this->user->balance;

        $this->setUpPayload();
    }

    /**
     * Setup payload to be stored in database.
     */
    protected function setUpPayload()
    {
        $this->payload = [
            'user_id' => $this->user->id,
            'amount' => 0.00,
            'balance' => is_null($this->last) ? $this->balance : $this->last->balance,
            'type' => '',
            'description' => '',
        ];
    }

    /**
     * Get last transaction.
     *
     * @return \App\Models\UserBalance
     */
    public function getLastBalance()
    {
        return $this->user->balance()->orderBy('id', 'desc')->first();
    }

    /**
     * Set amount, set type to db and decrease balance.
     *
     * @param decimal $amount Transaction amount
     */
    public function addDebit($amount = 0.000)
    {
        $this->payload['amount'] = $amount;
        $this->payload['type'] = 'db';
        $this->payload['balance'] -= $amount;
    }

    /**
     * Set amount, set type to cr and increase balance.
     *
     * @param decimal $amount Transaction amount
     */
    public function addCredit($amount = 0.000)
    {
        $this->payload['amount'] = $amount;
        $this->payload['type'] = 'cr';
        $this->payload['balance'] += $amount;
    }

    /**
     * Set payload description.
     *
     * @param string $description Transaction note
     */
    public function setDescription($description = '')
    {
        $this->payload['description'] = $description;
    }

    /**
     * Store payload to database.
     *
     * @return \App\Models\UserBalance
     */
    public function save()
    {
        \DB::transaction(function () {
            $transaction = $this->last = UserBalance::create($this->payload);

            $this->user->balance = $this->last->balance;
            $this->user->save();
            $this->initializeData();
        });

        return $this->last;
    }

    /**
     * Get last transaction.
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * Get balance of Account.
     *
     * @return int Account balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set payload attribute.
     *
     * @param mix $key Payload key.
     * @param mix $value Payload value
     *
     * @return void.
     */
    public function setAttribute($key = '', $value = '')
    {
        $this->payload[$key] = $value;
    }
}
