<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Redirect;
use Config;
use Google_Client as GoogleClient;
use Google_Service_Sheets as GoogleSpreadSheets;
use Google_Service_Sheets_ValueRange as ValueRange;
use Log;

use App\Lib\Grabber;

class GrabbersController extends Controller
{
    public function send(Request $request)
    {
        if ($r = $this->checkSession($request)) {
            return $r;
        }
        $content = [
            (string) Carbon::now()->toDateTimeString(),
            (double) (new Grabber)->grab(),
        ];

        $newRow = [
            $content
        ];

        $client = $this->setUpGoogleClient();
        $client->setAccessToken($request->session()->get('access_token'));

        $service = new GoogleSpreadSheets($client);

        $spreadsheetId = Config::get('google.spread_sheet_id');
        $range = 'A1:B1';
        $valueInputOption = 'RAW';
        $insertDataOption = 'INSERT_ROWS';

        $body = new ValueRange([
            'values' => $newRow
        ]);
        $params = array(
            'valueInputOption' => $valueInputOption
        );

        try {
            $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
        return Redirect::to(url('/'));
    }

    private function checkSession(Request $request)
    {
        // validate has access_token or not
        if ($request->session()->has('access_token') &&
            $request->session()->exists('access_token')) {

            // validate wether the token has expired or not?
            $time = $request->session()->get('access_token')['created'];
            $currentTime = Carbon::now()->subHour()->timestamp;
            if ($time > $currentTime) {
                return;
            }
            $request->session()->forget('access_token');
            return Redirect::route('grabber.oauth');
        }

        return Redirect::route('grabber.oauth');
    }

    public function auth(Request $request)
    {
        $code = $request->input('code');

        $client = $this->setUpGoogleClient();
        if (is_null($code)) {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $client->authenticate($code);
            session(['access_token' => $client->getAccessToken()]);

            return Redirect::route('grabber.send');
        }
    }

    private function setUpGoogleClient()
    {
        $url = route('grabber.oauth');
        $scopes = implode(' ', [
            GoogleSpreadSheets::SPREADSHEETS
        ]);

        $client = new GoogleClient();
        $client->setAuthConfigFile(Config::get('google.client_secret_path'));
        $client->setRedirectUri($url);
        $client->setScopes($scopes);
        $client->setAccessType('offline');

        return $client;
    }
}
