<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vet;
use App\Models\Owner;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminder;

class MailController extends BaseController
{
    public function AppointmentReminder(Request $request) {

        $cures = Cure::with('vet', 'pet.owner')
        ->where('date', 'like', now()->subDay()->toDateString().'%')
        ->get();

        foreach ($cures as $cure) {
            Mail::to($cure->pet->owner->email)->send(new AppointmentReminder());

            if ($cure->pet->owner->email == null) {
                return $this->sendError('User not found','Nincs fiók ezzel az email címmel!', 404);
            }

        }
        Mail::to($user)->send(new AppointmentReminder());
    }

    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return $this->sendError('','Hibás vagy lejárt megerősítő link!');
        }

        $user = Vet::find($user_id);
        if ($user == null) {
            $user = Owner::find($user_id);
        }

        if ($user == null) {
            return $this->sendError('','Hibás verifikáció!');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return view('EmailConfirmation'); 
    }

    public function resend($email) {
        $user = Vet::where('email', $email)->first();
        if ($user == null) {
            $user = Owner::where('email', $email)->first();
        }

        if ($user === null) {
            return $this->sendError('','Nincs ilyen felhasználó!');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->sendError('','Az email már meg lett erősítve!');
        }

        $user->sendEmailVerificationNotification();

        return $this->sendResponse('','Új megerősítő email sikeresen elküldve!');
    }
}
