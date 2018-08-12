<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public static function getHttpClient()
    {
        $baseUrl = "https://api.vrchat.cloud/api/1/";

        return new \GuzzleHttp\Client([
            "base_uri" => $baseUrl,
        ]);
    }

    public static function getApiWithAuth(Request $request, $action, $params = [])
    {
        $apiKey = $request->session()->get('apiKey');
        $id = $request->session()->get('id');
        $password = $request->session()->get('password');

        $query = array_merge(["apiKey" => $apiKey], $params);

        $response = ApiController::getHttpClient()->request(
            "GET",
            $action,
            [
                "auth" => [$id, $password],
                "query" => $query,
                "http_errors" => false,
            ]
        );

        return [
            "body" => (string)$response->getBody(),
            "code" => $response->getStatusCode()
        ];
    }

    public function getUserByName(Request $request, $name)
    {
        $response = ApiController::getApiWithAuth($request, "users/${name}/name");

        return response($response["body"], $response["code"]);
    }

    public function getFriends(Request $request, $offline = "false")
    {
        $response = ApiController::getApiWithAuth(
            $request,
            "auth/user/friends",
            [
                "n" => "100",
                "offline" => $offline,
            ]
        );

        return response($response["body"], $response["code"]);
    }

    public function getWorldInfo(Request $request, $worldId)
    {
        $response = ApiController::getApiWithAuth(
            $request,
            "worlds/${worldId}"
        );

        return response($response["body"], $response["code"]);
    }

    public function getWorldInfoByInstanceId(Request $request, $worldId, $instanceId)
    {
        $response = ApiController::getApiWithAuth(
            $request,
            "worlds/${worldId}/${instanceId}"
        );

        return response($response["body"], $response["code"]);
    }
}
