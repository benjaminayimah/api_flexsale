<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use Session;
use Redirect;
use Input;
use App\User;
use Stripe\Error\Card;
use Cartalyst\Stripe\Stripe;


class checkoutController extends Controller
{
    public function store(Request $request){
        // \Stripe\Stripe::setApiKey('sk_test_V2u8P8DOyoNC15DzIK6ofje000gYl0viIp');

        // $session = \Stripe\Checkout\Session::create([
        //     'payment_method_types' => ['card'],
        //     'line_items' => [[
        //       'price_data' => [
        //         'currency' => 'usd',
        //         'product_data' => [
        //           'name' => 'T-shirt',
        //         ],
        //         'unit_amount' => 2000,
        //       ],
        //       'quantity' => 1,
        //     ]],
        //     'mode' => 'payment',
        //     'success_url' => 'https://example.com/success?session_id={CHECKOUT_SESSION_ID}',
        //     'cancel_url' => 'https://example.com/cancel',
        //   ]);


          /*$validator = Validator::make($request->all(), [
            'card_no' => 'required',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required',
            'cvvNumber' => 'required',
            //'amount' => 'required',
            ]);
            $input = $request->all();
            if ($validator->passes()) {
            $input = array_except($input,array('_token'));

           $stripe = Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            try {
            $token = $stripe->tokens()->create([
            'card' => [
            'number' => $request->get('card_no'),
            'exp_month' => $request->get('ccExpiryMonth'),
            'exp_year' => $request->get('ccExpiryYear'),
            'cvc' => $request->get('cvvNumber'),
            ],
            ]);


           if (!isset($token['id'])) {
            return redirect()->route('addmoney.paymentstripe');
            }
            $charge = $stripe->charges()->create([
            'card' => $token['id'],
            'currency' => 'USD',
            'amount' => 20.49,
            'description' => 'wallet',
            ]);

            if($charge['status'] == 'succeeded') {

            echo "<pre>";
            print_r($charge);exit();
            return redirect()->route('addmoney.paymentstripe');
            } else {
            \Session::put('error','Money not add in wallet!!');
            return redirect()->route('addmoney.paymentstripe');
            }
            } catch (Exception $e) {
            \Session::put('error',$e->getMessage());
            return redirect()->route('addmoney.paymentstripe');
            } catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
            \Session::put('error',$e->getMessage());
            return redirect()->route('addmoney.paywithstripe');
            } catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
            \Session::put('error',$e->getMessage());
            return redirect()->route('addmoney.paymentstripe');
            }
            }*/

    }
}
