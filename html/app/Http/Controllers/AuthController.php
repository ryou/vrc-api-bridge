<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public static function getHttpClient()
    {
        $baseUrl = config("app.vrc-api-base-uri");

        return new \GuzzleHttp\Client([
            "base_uri" => $baseUrl,
        ]);
    }

    public function login(Request $request)
    {
        // 入力チェック
        if (!$request->has(["id", "password"])) {
            return response(json_encode([ "message" => "input id and password" ]), 401);
        }
        $id = $request->id;
        $password = $request->password;

        $request->session()->put('id', $id);
        $request->session()->put('password', $password);

        $response = AuthController::getHttpClient()->request(
            "GET",
            "/api/1/config",
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
