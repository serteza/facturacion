<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use App\User;

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
                'name' => 'required',
                'password' => 'required',
        ]);
            
        $user = User::select(['id','name', 'rol','password'])
            ->where('name', $request->name)->first();

        if($user == null){
            return response()->json(['error'=>'Unauthorized'],401);
        }else{
            if(Hash::check($request->password, $user->password)){
                try {
                    // Attempt to verify the credentials and create a token for the user
                    if  (!$token = JWTAuth::fromUser($user)) {
                        return $this->onUnauthorized();
                    }
                } catch (JWTException $e) {
                    // Something went wrong whilst attempting to encode the token
                    return $this->onJwtGenerationError();
                }
                return $this->onAuthorized($token, $user);
            }else{
                return response()->json(['error'=>'Unauthorized'],401);
            }
        }

    }

    /**
     * What response should be returned on invalid credentials.
     *
     * @return JsonResponse
     */
    protected function onUnauthorized()
    {
        return new JsonResponse([
            'message' => 'invalid_credentials'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * What response should be returned on error while generate JWT.
     *
     * @return JsonResponse
     */
    protected function onJwtGenerationError()
    {
        return new JsonResponse([
            'message' => 'could_not_create_token'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * What response should be returned on authorized.
     *
     * @return JsonResponse
     */
    protected function onAuthorized($token, $user)
    {
        unset($user->password);
        return new JsonResponse([
            'message' => 'token_generated',
            'user' => $user,
            'data' => [
                'token' => $token,
            ]
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only('name', 'password');
    }

    /**
     * Invalidate a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteInvalidate()
    {
        $token = JWTAuth::parseToken();

        $token->invalidate();

        return new JsonResponse(['message' => 'token_invalidated']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function patchRefresh()
    {
        $token = JWTAuth::parseToken();

        $newToken = $token->refresh();

        return new JsonResponse([
            'message' => 'token_refreshed',
            'data' => [
                'token' => $newToken
            ]
        ]);
    }
    public function getUserData()
    {
        /*return new JsonResponse([
            'message' => 'authenticated_user',
            'data' => JWTAuth::parseToken()->authenticate()
        ]);*/

        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
    
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
    
            return response()->json(['token_expired'], $e->getStatusCode());
    
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
    
            return response()->json(['token_invalid'], $e->getStatusCode());
    
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
    
            return response()->json(['token_absent'], $e->getStatusCode());
    
        }
    
        unset($user->password);
        unset($user->deleted_at);
        unset($user->created_at);
        unset($user->updated_at);
        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }
    /**
     * Get authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUser($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if(($user->role == 1 ) || ($user->id == $id)){
            $user = User::find($id);
            return response()->json(['user'=>$user],200);
        }else{
            return response()->json(['error'=>'Unauthorized'],401);
        }
    }

    public function createUser(Request $req){

        $this->validate($request, [
            'email' => 'required',
            'name' => 'required',
            'rol' => 'required',
            'password' => 'required',
        ]);
        
        $currentUser = JWTAuth::parseToken()->authenticate();

        if($currentUser->rol == "1" || $currentUser->rol == "2"){
            //error_log("entro en rol 1 o 2");
            $newUser = new User();
            $findUser = User::where('name', $req->name)->orWhere('email', $req->email)->first();
            if($currentUser->rol == "1" ){
                //error_log("entro en rol 1");
                //error_log($req->rol);
                if($req->rol > 1 && $req->rol < 4){
                    //error_log("rol a crear es entre 2 y 3");
                    if(is_null($findUser)){
                        $newUser->email = $req->email;
                        $newUser->name = $req->name;
                        $newUser->rol = $req->rol;
                        $newUser->password = Hash::make($req->password);
                        $newUser->created_at = date('Y-m-d H:m:s');
                        $newUser->updated_at = date('Y-m-d H:m:s');
                        $newUser->save();

                        unset($newUser->password);
                        unset($newUser->deleted_at);
                        unset($newUser->created_at);
                        unset($newUser->updated_at);
                        return response()->json(['user'=>$newUser],200);
                    } else {
                        return response()->json(['user'=>'El usuario que intento crear ya existe'],200);
                    }
                    
                }else{
                    return response()->json(['error'=>'Forbidden'],403);
                }
                
            }else {
                if($req->rol == 2 || $req->rol == 3){
                    //error_log("el rol es entre 2 y 3");
                    if(is_null($findUser)){
                        $newUser->email = $req->email;
                        $newUser->name = $req->name;
                        $newUser->rol = $req->rol;
                        $newUser->password = Hash::make($req->password);
                        $newUser->created_at = date('Y-m-d H:m:s');
                        $newUser->updated_at = date('Y-m-d H:m:s');
                        $newUser->save();

                        unset($newUser->password);
                        unset($newUser->deleted_at);
                        unset($newUser->created_at);
                        unset($newUser->updated_at);
                        return response()->json(['user'=>$newUser],200);
                    } else {
                        return response()->json(['user'=>'El usuario que intento crear ya existe'],200);
                    }
                }else{
                    //error_log("el rol a crear no es ni 2 ni 3");
                    return response()->json(['error'=>'Forbidden'],403);
                }
            }
            
        } else {
            //error_log("no es ni 1 ni 2");
            return response()->json(['error'=>'Forbidden'],403);
        }
    }

}
