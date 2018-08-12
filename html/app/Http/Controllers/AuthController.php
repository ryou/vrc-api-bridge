<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public static function getHttpClient()
    {
        $baseUrl = "https://api.vrchat.cloud/api/1/";

        return new \GuzzleHttp\Client([
            "base_uri" => $baseUrl,
        ]);
    }

    public function login(Request $request, $id, $password)
    {
        $request->session()->put('id', $id);
        $request->session()->put('password', $password);

        $response = AuthController::getHttpClient()->request(
            "GET",
            "config",
            [ "http_errors" => false ]
        );
        $body = (string)$response->getBody();
        $code = $response->getStatusCode();

        if ($code !== 200)
        {
            return response($body, $code);
        }

        $body = json_decode($body);
        $request->session()->put('apiKey', $body->apiKey);

        return response(json_encode([ "message" => "login success." ]), 200);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();

        return response(json_encode([ "message" => "logout complete." ]), 200);
    }
}
