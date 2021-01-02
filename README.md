## Introduction

YouTubeChannel adds to Laravel the service provider for OAuth 2.0 authentication with YouTube Google account.

It was created based on [GoogleOAuth2](https://github.com/lcmaquino/googleoauth2) that is a Laravel package for OAuth 2.0 authentication with Google account.

### Features
- The same features of [GoogleOAuth2](https://github.com/lcmaquino/googleoauth2);
- Check if an user is subscribed on a given YouTube channel;
- Get some YouTube channel statistics: subscription count, views count and videos count.

For more information about Google OAuth 2.0, please see https://developers.google.com/identity/protocols/oauth2/web-server

## Installation

```
$ cd /path/to/your/laravel/root
$ composer require lcmaquino/youtubechannel
$ php artisan vendor:publish --provider="Lcmaquino\YouTubeChannel\YouTubeChannelProvider"
```

Laravel should automatically include `Lcmaquino\YouTubeChannel\YouTubeChannelProvider`
as a service provider and include `YouTubeChannel` as an alias for `Lcmaquino\YouTubeChannel\Facades\YouTubeChannel::class`.

It can be done manually editing `config/app.php` to look like:
```
    'providers' => [

        //More Service Providers...

        /*
         * Package Service Providers...
         */
        Lcmaquino\YouTubeChannel\YouTubeChannelProvider::class,
    ],

    'aliases' => [

        //More aliases...

        'YouTubeChannel' => Lcmaquino\YouTubeChannel\Facades\YouTubeChannel::class,
    ],
```

## Configuration

Before using YouTubeChannel, you need to set up an [OAuth 2.0 client ID](https://support.google.com/cloud/answer/6158849?hl=en). The OAuth 2.0 client will provide a *client id*, a *client secret*, and a *redirect uri* for your application. These parameters and the *youtube channel id* should be placed in your `.env` Laravel configuration file.
```
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=
YOUTUBE_CHANNEL_ID=
```

It will be loaded by your application when reading the file `config/googleoauth2.php`:
```
<?php

return [
    'client_id' => env('GOOGLE_CLIENT_ID', ''),
    'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
    'redirect_uri' => env('GOOGLE_REDIRECT_URI', ''),
    'youtube_channel_id' => env('YOUTUBE_CHANNEL_ID', ''),
];
```

## Routing

Create two routes in `routes/web.php`:
```
Route::get('login/youtube', 'Auth\LoginController@redirectToProvider');
Route::get('login/youtube/callback', 'Auth\LoginController@handleProviderCallback');
```

Create `app/Http/Controllers/Auth/LoginController.php` and edit like this:

```
<?php

namespace App\Http\Controllers\Auth;

use Lcmaquino\YouTubeChannel\YouTubeChannelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function redirectToProvider(Request $request)
    {
        $ytm = new YouTubeChannelManager(config('googleoauth2'), $request);

        return $ytm->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $ytm = new YouTubeChannelManager(config('googleoauth2'),  $request);
        
        $user = $ytm->user();

        if(empty($user)) {
            //user not authenticaded

            //do something
        }else{
            //user authenticaded

            $subscribed = $ytm->isUserSubscribed();

            if ($subscribed === null) {
                //something went wrong

            } else {
                if ($subscribed) {
                    //user subscribed

                    //do something
                } else {
                    //user not subscribed

                    //do something
                }
            }
        }
    }
}
```

When you hit the route `login/youtube` it will redirect your request to 
Google authentication page. Google authentication will ask user 
for permission and then hit your callback route `login/youtube/callback`.

YouTubeChannel comes with a YouTubeChannel facade. So you could edit `app/Http/Controllers/Auth/LoginController.php` like this:
```
<?php

namespace App\Http\Controllers\Auth;

use YouTubeChannel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function redirectToProvider(Request $request)
    {
        return YouTubeChannel::redirect();
    }

    public function handleProviderCallback(Request $request)
    {       
        $user = YouTubeChannel::user();

        if(empty($user)) {
            //user not authenticaded

            //do something
        }else{
            //user authenticaded

            $subscribed = YouTubeChannel::isUserSubscribed();

            if ($subscribed === null) {
                //something went wrong

            } else {
                if ($subscribed) {
                    //user subscribed

                    //do something
                } else {
                    //user not subscribed

                    //do something
                }
            }
        }
    }
}
```

## Optional Parameters

To include any optional OAuth 2.0 parameters in the request, call the `with`
method with an associative array:
```
$params = [
    'approval_prompt' => 'force',
    'access_type' => 'offline'
];

return YouTubeChannel::with($params)->redirect();
```

## Access Scopes

The scopes are used by Google to limit your application access to the user account data.
Use the `scopes` method to set your scopes. The defaults are `openid`, `email` and `https://www.googleapis.com/auth/youtube.readonly`.
```
$scopes = [
    'profile',
    'openid',
    'email',
    'https://www.googleapis.com/auth/youtube',
    'https://www.googleapis.com/auth/youtube.readonly'
];

return YouTubeChannel::scopes($scopes)->redirect();
```

Read more about [YouTube Data API](https://developers.google.com/youtube/v3/guides/auth/server-side-web-apps?hl=pt-br) scopes.

## Stateless Authentication

The `stateless` method disable session state verification.
```
$user = YouTubeChannel::stateless()->user();
```

## Retrieving User Details

Once you have an authenticated `$user`, you can get more details about the user:
```
$user = YouTubeChannel::user();

$user->getSub(); //the unique Google identifier for the user.
$user->getName();
$user->getEmail();
$user->emailVerified();
$user->getPicture();
$user->getToken();
$user->getRefreshToken(); //not always provided
$user->getExpiresIn();
```

### Retrieving User Details From A Token

You can retrieve user details from a valid access `$token` using the 
`getUserFromToken` method:
```
$user = YouTubeChannel::getUserFromToken($token);
```

## Refreshing token

The access token expires periodically. So you need to get a new one.
You can get this using the `refreshUserToken` method:
```
$new_token = YouTubeChannel::refreshUserToken($refresh_token);
```

You should pay attetion to keep the user `$refresh_token` on your application.
If you lose it, then you can't get a new access token. In that case, the user 
has to log in again when the current access token expires.

You will notice that refresh token is not always provided on Google authentication.
You can force Google to do so using the `with` method (see **Optional Parameters**):
```
$params = [
    'approval_prompt' => 'force',
    'access_type' => 'offline',
];

return YouTubeChannel::with($params)->redirect();
```

## Revoking token

If you need to invalidate the access token and the refresh token, you can revoke
them using the `revokeToken` method:
```
if (YouTubeChannel::revokeToken($token)) {
    //token was revoked
}else{
    //token was not revoked
}
```

**Tips**
- You can use a valid access token or refresh token as `$token`.
- Remeber to revoke the token when the user decides to sign out/remove their data from your application.
- Keep in mind that the user always can revoke their token on https://myaccount.google.com/permissions.

## License

YouTubeChannel is open-sourced software licensed under the [GPL v3.0 or later](https://github.com/lcmaquino/googleoauth2/blob/main/LICENSE).