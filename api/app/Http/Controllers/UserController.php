<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MapSettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * Authenticate user
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];
        $data = $request->toArray();

        $validator = Validator::make($data,  $rules);

        $errors = $this->getValidatorMsg($validator->errors()->getMessages());

        if (!isset($data['user_type']) || $data['user_type'] == "") {
            $errors[] = "Something is missing, please try again later.";
        }

        if (count($errors) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $errors)
            ]);
        }

        $checkUser = User::where('email', $data['email'])->get();
        if ($checkUser->count() == 0) {
            return response()->json([
                "success" => false,
                "message" => "Credentials are not valid."
            ]);
        }

        //check password and convert to array
        $user = ($checkUser->first())->toArray();
        if(!Hash::check($data['password'], $user['password'])){
            return response()->json([
                "success" => false,
                "message" => "Credentials are not valid."
            ]);
        }

        // get map settings
        $map = MapSettings::where('user_id', $user['id'])->get();

        $user['map_settings'] = $map->first();

        // remove password from response
        unset($user['password']);

        return response()->json([
            "success" => true,
            "message" => "Logged in successfully.",
            "data" => $user
        ]);
    }

    /**
     * Register User
     */
    public function register(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'company' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'password1' => 'required',
            'password2' => 'required',
            'address' => 'required'
        ];

        $data = $request->toArray();

        $validator = Validator::make($data,  $rules);

        $errors = $this->getValidatorMsg($validator->errors()->getMessages());

        if ($data['password1'] != $data['password2']) {
            $errors[] = "Passwords don't match.";
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is invalid.";
        }

        if ($data['email'] != "") {
            $checkIfMailExists = User::where('email', $data['email'])->get();
            if ($checkIfMailExists->count() > 0) {
                $errors[] = "Email is already exists.";
            }
        }

        if (!isset($data['user_type']) || $data['user_type'] == "") {
            $errors[] = "Something is missing, please try again later.";
        }

        if (count($errors) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $errors)
            ]);
        }

        $data['password'] = Hash::make($data['password1']);
        unset($data['password1']);
        unset($data['password2']);

        $addUser = new User($data);
        $addUser->save();

        // save map settings
        (new MapSettings([
            'user_id' => $addUser->id,
            'show_joystick' => false,
            'precision' => 3
        ]))->save();

        return response()->json([
            "success" => true,
            "message" => "Account created!"
        ]);
    }

    /**
     * @param array $msgs
     * 
     * @return array
     */
    private function getValidatorMsg(array $msgs): array
    {
        $result = [];
        foreach ($msgs as $msg) {
            $result[] = implode("\n", $msg);
        }

        return $result;
    }
}
