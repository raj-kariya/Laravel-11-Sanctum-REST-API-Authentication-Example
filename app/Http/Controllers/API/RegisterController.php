<?php
   
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'data' => $validator->errors()
            ], 400);     
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
   
        return response()->json([
            'success' => true,
            'data' => $success,
            'message' => 'User registered successfully.'
        ], 200);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            return response()->json([
                'success' => true,
                'data' => $success,
                'message' => 'User login successfully.'
            ], 200);
        } 
        else{ 
            return response()->json([
                'success' => false,
                'message' => 'Unauthorised.',
                'data' => ['error' => 'Unauthorised']
            ], 401);
        } 
    }
}