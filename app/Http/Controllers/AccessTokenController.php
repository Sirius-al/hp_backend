<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Response;

class AccessTokenController extends \Laravel\Passport\Http\Controllers\AccessTokenController
{
    public function issueToken(ServerRequestInterface $request)
    {
        try {
            $username = $request->getParsedBody()['username'];
            $user = User::where('email', '=', $username)->firstOrFail();
            $tokenResponse = parent::issueToken($request);
            $content = $tokenResponse->getContent();
            $data = json_decode($content, true);
            if (isset($data["error"]))
                throw new OAuthServerException('The user credentials were incorrect.', 6, 'invalid_credentials', 401);
            $user = collect($user);
            $user->put('access_token', $data['access_token']);
            $user->put('refresh_token', $data['refresh_token']);
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) { // email notfound
            $response = ['message' => 'Email not Found'];
            return response()->json($response, 401);
        } catch (OAuthServerException $e) { //password not correct..token not granted
            $response = ['message' => 'Password not Matched'];
            return response()->json($response, 401);
        } catch (Exception $e) {
            $response = ['message' => 'invalid_credentials'];
            return response()->json($response, 401);
        }
    }
}
