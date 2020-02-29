<?php

namespace App\Traits\Helpers;

use App\Models\Transaction;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Vinkla\Hashids\Facades\Hashids;

trait Helper
{
    public function userExist($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return true;
        }
        return false;
    }

    public function validator($request, $rules, $messages)
    {
        $validator = \Validator::make($request, $rules, $messages);

        if ($validator->fails()) {
            return $validator->errors()->first();
        } else {
            return false;
        }
    }

    public function decode($str)
    {
        $hashed = Hashids::decode($str);
        if (count($hashed) == 0) {
            return null;
        }
        return $hashed[0];
    }

    public function encode($str)
    {
        return Hashids::connection('main')->encode($str);
    }

    public function storeImage($path, $value)
    {
        $image = \Image::make($value)->orientate();
        $imageName = $value->hashName();
        $destinationPath = $path;
        $value = $path . '/' . $value->hashName();
        $image->save($destinationPath . $imageName);

        return $value;
    }

    public function generateTrxID()
    {
        $code = 'TRX-' . date('YmdHis') . '-' . rand(00000000, 99999999);
        $search = Transaction::where('trx_id', $code)->first();

        if ($search) {
            return $this->generateTrxID();
        }

        return $code;
    }

    /**
     * Rupiah Format Currency
     *
     * @param decimal $number Number payload.
     *
     * @return string
     */
    public function rupiahFormat($number = 0.00)
    {
        $result = "Rp " . number_format($number, 0, ',', '.');

        return $result;
    }

    /**
     * Send Request
     * Guzzle HTTP global request function
     *
     * @param   $url, $method, $headers, $query, $json
     *
     * @return  \Illuminate\Http\Response
     */
    public function send($method, $url, $headers, $json, $query = [])
    {
        try {
            $client = new Client;
            $res = $client->request($method, $url, [
                'query' => $query,
                'headers' => $headers,
                'json' => $json,
                'request.options' => array(
                    'exceptions' => false,
                ),
            ]);
            return json_decode($res->getBody(), true);
        } catch (RequestException $e) {
            return json_decode($e->getResponse()->getBody()->getContents(), true);
        }
    }

    /**
     * Send Request
     * Guzzle HTTP global request function
     *
     * @param   $url, $method, $headers, $query, $json
     *
     * @return  \Illuminate\Http\Response
     */
    public function report($desc, $details, $webhook = null)
    {
        $details = json_encode($details, true);

        $data = "payload=" . json_encode(array(
            "channel" => "#" . env('LOG_SLACK_WEBHOOK_CHANNEL'),
            "text" => $desc,
            "attachments" => [
                [
                    "color" => "#eb2f06",
                    "text" => '*Details :* ' . $details,
                    "image_url" => "http://my-website.com/path/to/image.jpg",
                    "thumb_url" => "http://example.com/path/to/thumb.png",
                    "footer" => "Slack API",
                    "footer_icon" => "https://platform.slack-edge.com/img/default_application_icon.png",
                    "ts" => strtotime(date('Y-m-d H:i:s')),
                ],
            ],
            "username" => 'App Bots',
            "icon_url" => 'https://emojis.slackmojis.com/emojis/images/1450319444/53/whoa.jpg',
        ));

        // You can get your webhook endpoint from your Slack settings
        if (config('app.env') != 'local') {
            $ch = curl_init(env('LOG_SLACK_WEBHOOK_URL'));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
        }
    }
}
