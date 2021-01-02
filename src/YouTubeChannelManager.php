<?php

namespace Lcmaquino\YouTubeChannel;

use Lcmaquino\GoogleOAuth2\GoogleOAuth2Manager;
use Lcmaquino\GoogleOAuth2\HttpClient;

use Illuminate\Http\Request;

class YouTubeChannelManager extends GoogleOAuth2Manager
{
    /**
     * The YouTube channel ID.
     *
     * @var string
     */
    protected $channelId;

    /**
     * Create a new YouTubeChannelManager instance.
     * 
     * @param  array  $config
     * @param  Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct($config = [], Request $request)
    {
        $this->request = $request;
        $this->setConfig($config);
        $this->scopes = [
            'openid',
            'email',
            'https://www.googleapis.com/auth/youtube.readonly'
        ];
        $this->httpClient = new HttpClient();
        $this->user = null;
    }

    /**
     * Set the YouTube channel ID.
     *
     * @param string $channelId
     * @return $this
     */
    public function setChannelId($channelId = '') {
        $this->channelId = $channelId;
        return $this;
    }

    /**
     * Get the YouTube channel ID.
     *
     * @return string
     */
    public function getChannelId() {
        return $this->channelId;
    }

    /**
     * Set the configuration of the Google OAuth 2.0 client and the YouTube
     * channel ID.
     *
     * The $config parameter should be formatted as:
     *   [
     *     'client_id' => string,
     *     'client_secret' => string,
     *     'redirect_uri' => string,
     *     'youtube_channel_id' => string,
     *   ]
     *
     * @param array  $config
     * @return $this
     */
    public function setConfig($config = []){
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->redirectUri = $config['redirect_uri'];
        $this->channelId = $config['youtube_channel_id'];
        return $this;
    }

    /**
     * Get the configuration of the Google OAuth 2.0 client and the YouTube
     * channel ID.
     * @return void
     */
    public function getConfig(){
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'client_redirect' => $this->redirectUri,
            'youtube_channel_id' => $this->channelId,
        ];
    }

    /**
     * Get the subscriptions URL for YouTube DATA v3 API.
     *
     * @return string
     */
    protected function getSubscriptionsUrl()
    {
        return 'https://www.googleapis.com/youtube/v3/subscriptions';
    }

    /**
     * Get the channels URL for YouTube DATA v3 API.
     *
     * @return string
     */
    protected function getChannelsUrl()
    {
        return 'https://www.googleapis.com/youtube/v3/channels';
    }

    /**
     * Get the GET fields for the subscriptions request.
     *
     * @param string $token
     * @param string $channelId
     * @return array
     */
    protected function getSubscriptionsFields($token = '')
    {
        return [
            'part' => 'snippet',
            'mine' => 'true',
            'forChannelId' => $this->channelId,
            'access_token' => $token,
        ];
    }

    /**
     * Get the GET fields for the channels statistics request.
     *
     * @param string $token
     * @param string $channelId
     * @return array
     */
    protected function getChannelStatisticsFields($token = '')
    {
        return [
            'part' => 'statistics',
            'id' => $this->channelId,
            'access_token' => $token,
        ];
    }

    /**
     * Get the subscriptions reponse for the given token.
     * 
     * @param string $token
     * @return array|null
     */
    protected function getSubscriptionsResponse($token = '')
    {
        $response = (empty($token) || empty($this->channelId)) ? null : 
            $this->getHttpClient()->get(
                $this->getSubscriptionsUrl(),
                $this->getSubscriptionsFields($token)
            );

        return $response;
    }

    /**
     * Get the channel statistics reponse for the given token.
     *
     * @param string $token
     * @return array|null
     */
    protected function getChannelStatisticsResponse($token = '')
    {
        $response = (empty($token) || empty($this->channelId)) ? null : 
            $this->getHttpClient()->get(
                $this->getChannelsUrl(),
                $this->getChannelStatisticsFields($token)
            );

        return $response;
    }

    /**
     * Check if an user is subscribed on the given YouTube channel.
     * 
     * Returns true if the user is subscribed and false otherwise.
     * If $token is empty and there is a cached $this->user, then this
     * method will use their token.
     * Returns null if some error has happened while trying to get user's
     * subscriptions.
     * 
     * @param string $token
     * @param string $channelId
     * @return boolean|null
     */
    public function isUserSubscribed($token = '') {
        $token = empty($token) ? (empty($this->user) ? null : $this->user->token) : $token;
        $response = $this->getSubscriptionsResponse($token);

        return isset($response['error']) ? null : !empty($response['items']);
    }

    /**
     * Get the channel statistics for an given YouTube channel.
     * 
     * If $token is empty and there is a cached $this->user, then this
     * method will use their token.
     * Returns null if some error has happened while trying to get the data.
     * 
     * @param string $token
     * @return array|null
     */
    public function getChannelStatistics($token = ''){
        $token = empty($token) ? (empty($this->user) ? null : $this->user->token) : $token;
        $stats = [
            'viewCount',
            'subscriberCount',
            'videoCount'
        ];

        $response = $this->getChannelStatisticsResponse($token);

        return isset($response['items'][0]['statistics']) ? 
            $this->formatStatistics($stats, $response['items'][0]['statistics']) : null;
    }

    /**
     * Format the value for the keys $stats of array $values.
     * 
     * It will be like (number,decimals)<k|M>.
     * Examples: 120 => '120', 1200 => '1,2k' 1200000 => '1,2M'.
     * 
     * @param array $stats
     * @param array $values
     * @return array
     */
    protected function formatStatistics($stats = [], $values = []){
        $newArray = [];

        foreach ($values as $key => $value){
            if(in_array($key, $stats)) {
                $val = 0;
                $factor = '';
                $decimals = 1;
                if ($value < 1000) {
                    $val = $value;
                    $decimals = 0;
                }elseif($value < 1000000){
                    $val = $value/1000;
                    $factor = 'k';
                }else{
                    $val = $value/1000000;
                    $factor = 'M';
                }
                $val = number_format($val, $decimals, ',', '.');
                $newArray[$key] = $val . $factor;
            }
        }

        return $newArray;
    }
}