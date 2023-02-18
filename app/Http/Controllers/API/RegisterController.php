<?php
 
namespace App\Http\Controllers\API;
 
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Mail\ForgotPassword;
use App\Mail\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
 
class RegisterController extends BaseController
{

    /**
   * Register api
   * @return \Illuminate\Http\Response
   */
  public function register(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'name' => 'required',
          'email' => 'required|email',
          'password' => 'required',
          'c_password' => 'required|same:password',
      ]);
      if($validator->fails()){
          return $this->sendError('Validation Error.', $validator->errors());     
      }
      $input = $request->all();
      $input['password'] = bcrypt($input['password']);
      $user = User::create($input);
      $success['token'] =  $user->createToken('MyApp')->plainTextToken;
      $success['name'] =  $user->name;
      return $this->sendResponse($success, 'User register successfully.');
  }

  /**
   * Login api
   * @return \Illuminate\Http\Response
   */
  public function login(Request $request)
  {
      if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
          $user = Auth::user();
          $user->tokens()->delete();
          $success['token'] =  $user->createToken('MyApp')->plainTextToken;
          $success['name'] =  $user->name;
          return $this->sendResponse($success, 'User login successfully.');
      }
      else{
          return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
      }
  }

  /**
   * Logout api
   * @return \Illuminate\Http\Response
   */
  public function logout(Request $request)
  {
          $user = User::find($request->id);
          $user->tokens()->where('id', $request->token)->delete();
          $success['id'] =  $request->id;
          return $this->sendResponse($success, 'User logout successfully. Token cleared.');  
  }

  public function forgotPassword(Request $request)
  {
    $user = User::where('email', $request->email)->first();
    $user = User::findOrFail($user->id);
    $remember_token = Str::random(30);
    $user->remember_token = $remember_token;
    $user->save();

    Mail::to($user->email)->send(new ForgotPassword($user));

    $success['remember_token'] = $remember_token;

    return $this->sendResponse($success, 'Forgot password email sent successfully');  
  }

  public function passwordReset(Request $request)
  {
    $remember_token = $request['remember_token'];

    $user = User::where('remember_token', $remember_token)->first();

    $user = User::find($user->id);
    
    $newPassword = Str::random(12);

    $user->password = bcrypt($newPassword);
    $user->remember_token = null;

    $user->save();

    Mail::to($user->email)->send(new PasswordReset($newPassword));


    return "Temporary password emailed successfully";
  }
}