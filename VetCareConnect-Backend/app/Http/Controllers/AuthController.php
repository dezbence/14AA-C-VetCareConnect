<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Owner;
use App\Models\Vet;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class AuthController extends BaseController
{

    public function register(Request $request) {

        $ownerValidatorFields = [
            'name' => 'required',
            'email' => 'required|email|unique:App\Models\Owner,email|unique:App\Models\Vet,email',
            'postal_code' => 'required',
            'phone' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'role' => 'required|integer'
        ];

        $vetValidatorFields = $ownerValidatorFields;
        $vetValidatorFields += [
            'stamp_number' => 'required',
            'address' => 'required'
        ];

        $validatorMessages = [
            'name.required' => "Kötelező kitölteni!",

            'email.required' => "Kötelező kitölteni!",
            'email.email' => "Hibás az email cím!",
            'email.unique' => "Az email cím már használatban van!",

            'address.required' => "Kötelező kitölteni!",

            'phone.required' => "Kötelező kitölteni!",

            'password.required' => "Kötelező kitölteni!",

            'confirm_password.required' => "Kötelező kitölteni!",
            'confirm_password.same' => "A két jelszó nem egyezik!",

            'role.required' => "Kötelező kitölteni!",
            'role.integer' => "Csak szám lehet!",

            'stamp_number.required' => "Kötelező kitölteni!",

            'postal_code.required' => "Kötelező kitölteni!",
        ];

        if ($request['role'] == 0) {
            $validator = Validator::make($request->all(), $ownerValidatorFields, $validatorMessages);
        } else {
            $validator = Validator::make($request->all(), $vetValidatorFields, $validatorMessages);
        }

         if ($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
         }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        if ($input['role'] == 0) {
            unset($input['role']);
            unset($input['confirm_password']);

            $owner = Owner::create($input)->sendEmailVerificationNotification();
        } else {
            unset($input['role']);
            unset($input['confirm_password']);

            $vet = Vet::create($input)->sendEmailVerificationNotification();

        }

        return $this->sendResponse('','Sikeres regisztráció!');
    }


    public function login(Request $request){

        $validatorFields = [
            'email' => 'required',
            'password'=> 'required'
        ];

        $validator = Validator::make($request->all(),  [
            'email' => 'required',
            'password'=> 'required'
        ]);

        if ($validator->fails()){
           return $this->sendError('Bad request', $validator->errors(), 400);
        }

        if (Auth::guard('owner')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            $user = Auth::guard('owner')->user();
            $success['token'] = $user->createToken('Secret')->plainTextToken;
            $success['name'] = $user->name;
            $success['role'] = 0;


            if ($user['email_verified_at'] == null) return $this->sendError('Not verified','Az email cím nincs megerősítve!',403);

            return $this->sendResponse($success,'Sikeres bejelentkezés!');

        } elseif (Auth::guard('vet')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            $user = Auth::guard('vet')->user();
            $success['token'] = $user->createToken('Secret')->plainTextToken;
            $success['name'] = $user->name;
            $success['role'] = 1;

            if ($user['email_verified_at'] == null) return $this->sendError('Not verified','Az email cím nincs megerősítve!',403);

            return $this->sendResponse($success,'Sikeres bejelentkezés!');

        } else {
            return $this->sendError('Unauthorized','Sikertelen bejelentkezés!',401);
        }

    }


    public function adminLogin(Request $request){

        $validatorFields = [
            'username' => 'required',
            'password'=> 'required'
        ];

        $validator = Validator::make($request->all(), $validatorFields);

        if ($validator->fails()){
           return $this->sendError('Bad request', $validator->errors(), 400);
        }

        if (Auth::guard('admin')->attempt([
            'username' => $request->username,
            'password' => $request->password
        ])) {

            $user = Auth::guard('admin')->user();
            $success['token'] = $user->createToken('Secret')->plainTextToken;
            $success['name'] = $user->username;
            $success['role'] = 2;

            return $this->sendResponse($success,'Sikeres bejelentkezés!');

        }

        return $this->sendError('Unauthorized','Sikertelen bejelentkezés!',401);

    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return $this->sendResponse('' ,'Sikeres kijelentkezés!');
    }

    public function logoutAllDevice(){
        Auth::user()->tokens()->delete();
        return $this->sendResponse('' ,'Sikeres kijelentkezés az összes eszközön!');
    }
}
