<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public static function getHttpClient()
    {
        $baseUri = config("app.vrc-api-base-uri");

        return new \GuzzleHttp\Client([
            "base_uri" => $baseUri,
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
        $oneHourAgo = DB::raw('SUBDATE(NOW(), INTERVAL 1 HOUR)');

        // 1時間以内にDBに登録されたデータがあればそれを使用
        $world = DB::table('worlds')->where('id', $worldId)->whereTime('updated_at', '>=', $oneHourAgo)->first();
        $body = null;
        $code = "200";

        if (is_null($world))
        {
            $response = ApiController::getApiWithAuth(
                $request,
                "worlds/${worldId}"
            );

            if ($response["code"] == "200") {
                $now = DB::raw('NOW()');
                $willUpdate = DB::table("worlds")->where("id", $worldId)->exists();

                if ($willUpdate)
                {
                    DB::table('worlds')
                        ->where("id", $worldId)
                        ->update(
                            [
                                'json' => $response["body"],
                                'updated_at' => $now,
                            ]
                        );
                }
                else
                {
                    DB::table('worlds')
                        ->insert(
                            [
                                'id' => $worldId,
                                'json' => $response["body"],
                                'updated_at' => $now,
                                'created_at' => $now,
                            ]
                        );
                }
            }
            $body = $response["body"];
            $code = $response["code"];
        }
        else
        {
            $body = $world->json;
        }

        return response($body, $code);
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
