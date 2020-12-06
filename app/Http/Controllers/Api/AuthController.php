<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $registrationData = $request->all();
        $validate = Validator::make($registrationData, [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required',
            'phone' => 'required|digits_between:10,13',
        ]); //membuat rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $registrationData['password'] = bcrypt($request->password); //enkripsi password
        $user = User::create($registrationData); //membuat user baru
        $user->sendApiEmailVerificationNotification();
        return response([
            'message' => 'Register Success, a verification email has been sent to your email address',
            'user' => $user,
        ], 200); //return data user dalam bentuk json
    }

    public function login(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 400); //return error invalid input

        if (!Auth::attempt($loginData))
            return response([
                'message' => 'Invalid Credentials'
            ], 401); //return error gagal login

        $user = Auth::user();

        if ($user->email_verified_at == NULL) {
            return response([
                'message' => 'Please Verify Your Email'
            ], 401); //return error gagal login
        }

        $token = $user->createToken('Authentication Token')->accessToken; //generate token

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ], 200); //return data user dan token dalam bentuk json
    }

    public function details()
    {
        $user = Auth::user();
        if (is_null($user)) {
            return response([
                'message' => 'User Not Found',
                'user' => null,
            ], 404);
        }
        return response([
            'message' => 'User Data Retrieved',
            'user' => $user,
        ], 200); //return data user dan token dalam bentuk json
    }

    public function update(Request $request)
    {
        $user = Auth::User();

        $id = $user->id;

        if (is_null($user))
            return response(['message' => 'User not found!'], 404);

        $data = $request->all();
        $validate = Validator::make($data, [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns',
            'phone' => 'required|digits_between:10,13',
            'image64' => 'nullable',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        if (isset($data['image64'])) {
            $file = base64_decode($data['image64']);
            $f = finfo_open();

            $mime_type = finfo_buffer($f, $file, FILEINFO_MIME_TYPE);
            $extension = explode('/', $mime_type)[1];
            $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp'];

            if (!in_array($extension, $allowedExtensions)) {
                return response([
                    'message' => 'Invalid File Type',
                    'data' => null,
                ], 400);
            }

            $fileName = 'user' . $id . '.' . $extension;
            // $filePath = public_path('img');
            $actualFilePath = '\\' . $fileName;

            // $file->move($filePath, $fileName);
            file_put_contents(public_path('img') . '\\' . $fileName, $file);

            $user->image_path = $fileName;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];


        if ($user->save())
            return response(['message' => 'Update Success'], 200);

        return response(['message' => 'Update User Failed'], 400);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function uploadImg(Request $request)
    {
        $user = Auth::User();

        $id = $user->id;
        if (is_null($user))
            return response(['message' => 'User not found!'], 404);

        $data = $request->all();
        $validate = Validator::make($data, [
            'image64' => 'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $file = base64_decode($data['image64']);
        $f = finfo_open();

        $mime_type = finfo_buffer($f, $file, FILEINFO_MIME_TYPE);
        $extension = explode('/', $mime_type)[1];
        $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp'];

        if (!in_array($extension, $allowedExtensions)) {
            return response([
                'message' => 'Invalid File Type',
                'data' => null,
            ], 400);
        }

        $fileName = 'user' . $id . '.' . $extension;
        // $filePath = public_path('img');
        $actualFilePath = '\\' . $fileName;

        // $file->move($filePath, $fileName);
        file_put_contents(public_path('img') . '\\' . $fileName, $file);

        $user->image_path = $fileName;


        if ($user->save())
            return response(['message' => "Upload success!"], 200);

        return response(['message' => 'Upload Image Failed'], 400);
    }
}
