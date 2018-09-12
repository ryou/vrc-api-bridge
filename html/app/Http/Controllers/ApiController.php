<?php

namespace App\Http\Controllers;

use App\Models\World;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public static function getApiWithAuth(Request $request, string $action, array $params = [])
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

    public function getUserByName(Request $request, string $name)
    {
        $response = ApiController::getApiWithAuth($request, "users/${name}/name");

        return response($response["body"], $response["code"]);
    }

    public function getFriends(Request $request, string $offline = "false")
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

    public function getWorldInfo(Request $request, string $worldId)
    {
        $world = null;
        try
        {
            $world = World::findOrFail($worldId);
        }
        catch (ModelNotFoundException $e)
        {
            $world = new World([
                "id" => $worldId
            ]);
        }

        // 新規作成時か古い情報の際に、更新する
        $updatedAt = $world->updated_at;
        $beforeOneHour = Carbon::now()->subHour();
        $code = "200";
        if ($updatedAt === null || $beforeOneHour > $updatedAt)
        {
            $response = ApiController::getApiWithAuth(
                $request,
                "worlds/${worldId}"
            );

            $code = $response["code"];
            if ($code !== "200")
            {
                $world->json = $response["body"];
                $world->save();
            }
        }

        return response($world->json, $code);
    }

    public function getWorldInfoByInstanceId(Request $request, string $worldId, string $instanceId)
    {
        $response = ApiController::getApiWithAuth(
            $request,
            "worlds/${worldId}/${instanceId}"
        );

        return response($response["body"], $response["code"]);
    }
}
