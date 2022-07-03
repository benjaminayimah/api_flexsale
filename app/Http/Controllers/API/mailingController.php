<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class mailingController extends Controller
{
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);
        try {
            $email = $request['email'];
            $data = [
                'title' => 'Waiting List',
                'email' => $email,
            ];
            Mail::send('layouts.waitingListMailer', $data, function($mail) {
                $mail->to('info@flexsale.store', 'Flexsale Mailer')->subject('New Waiting List');
            });
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error sending message!'
            ], 500);
        }
        return response()->json([
            'title' => 'Sent',
            'status' => 'Your email is added to our waiting list.'
        ], 200);
    }
}
