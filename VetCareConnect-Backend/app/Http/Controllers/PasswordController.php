<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Vet;
use App\Models\Password_reset;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\PasswordReset as PasswordResetMail;
use Illuminate\Support\Facades\Mail;


class PasswordController extends BaseController
{

    public function forgotPassword(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        $user = Vet::where('email', $request->email)->first();
        if ($user == null) {
            $user = Owner::where('email', $request->email)->first();
        }

        if ($user == null) {
                return $this->sendError('User not found','Nincs fiók ezzel az email címmel!', 404);
        }

        $token = Str::random(60);

        Mail::to($user)->send(new PasswordResetMail($token));

        $passwordResetExist = Password_reset::find($request->email);

        if ($passwordResetExist == null) {
            $passwordReset = new Password_reset();
            $passwordReset->email = $request->email;
            $passwordReset->token = $token;
            $passwordReset->created_at = Carbon::now('GMT+1')->timestamp;
            $passwordReset->save();
        } else {
            $passwordResetExist->token = $token;
            $passwordResetExist->created_at = Carbon::now('GMT+1')->timestamp;
            $passwordResetExist->save();
        }

        return $this->sendResponse('','Az email sikeresen elküldve!', 200);
    }

    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'role' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        $expiredTokens = Password_reset::where('created_at', '<', Carbon::now('GMT+1')->timestamp - (60 * 60))
            ->delete();

        $resetToken = Password_reset::where('token', '=', $request->bearerToken())
            ->where('email', '=', $request->email)
            ->get();

        if (count($resetToken) == 0) {
            return $this->sendError('Invalid Token','Hibás vagy lejárt jelszó visszaállító azonosító!', 401);
        }

        if ($request->role == 0) {
            Owner::where('email', '=', $request->email)
                ->update(['password' => bcrypt($request->password)]);

            Password_reset::where('email', '=', $request->email)->delete();

            return $this->sendResponse('','A jelszó módosult!', 200);
        } else {
            Vet::where('email', '=', $request->email)
                ->update(['password' => bcrypt($request->password)]);

            Password_reset::where('email', '=', $request->email)->delete();

            return $this->sendResponse('','A jelszó módosult!', 200);
        }


    }

}
