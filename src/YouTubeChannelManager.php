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
     * Get the subscriptions URL for YouTube Data v3 API.
     *
     * @return string
     */
    protected function getSubscriptionsUrl()
    {
        return 'https://www.googleapis.com/youtube/v3/subscriptions';
    }

    /**
     * Get the channels URL for YouTube Data v3 API.
     *
     * @return string
     */
    protected function getChannelsUrl()
    {
        return 'https://www.googleapis.com/youtube/v3/channels';
    }

    /**
     * Get the videos URL for YouTube Data v3 API.
     *
     * @return string
     */
    protected function getVideosUrl()
    {
        return 'https://www.googleapis.com/youtube/v3/videos';
    }

    /**
     * Get the playlist items URL for YouTube Data v3 API.
     *
     * @return string
     */
    protected function getPlaylistItemsUrl()
    {
        return 'https://www.googleapis.com/youtube/v3/playlistItems';
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
     * Get the GET fields for the videos request.
     *
     * @param string $token
     * @param string $id
     * @return array
     */
    protected function getVideosFields($token = '', $id = '')
    {
        return [
            'part' => 'snippet',
            'id' => $id,
            'access_token' => $token,
        ];
    }

    /**
     * Get the GET fields for the playlist items request.
     *
     * @param string $token
     * @param string $id
     * @param integer $maxResults
     * @param string $pageToken
     * @return array
     */
    protected function getPlaylistItemsFields($id = '', $token = '', $maxResults = 5, $pageToken = '')
    {
        return [
            'part' => 'snippet',
            'playlistId' => $id,
            'access_token' => $token,
            'pageToken' => $pageToken,
            'maxResults' => $maxResults,
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
     * Get the videos reponse for the given token and video ID.
     *
     * @param string $token
     * @param string $id
     * @return array|null
     */
    protected function getVideosResponse($token = '', $id = '')
    {
        $response = (empty($token) || empty($id)) ? null : 
            $this->getHttpClient()->get(
                $this->getVideosUrl(),
                $this->getVideosFields($token, $id)
            );

        return $response;
    }

    /**
     * Get the playlist items reponse for the given token and playlist ID.
     *
     * @param string $token
     * @param string $id
     * @return array|null
     */
    protected function getPlaylistItemsResponse($id = '', $token = '', $maxResults = 5, $pageToken = '')
    {
        $response = (empty($token) || empty($id)) ? null : 
            $this->getHttpClient()->get(
                $this->getPlaylistItemsUrl(),
                $this->getPlaylistItemsFields($id, $token, $maxResults, $pageToken)
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
     * Get information about a YouTube video.
     *
     * @param string $id
     * @param string $token
     * @return YouTubeVideo|null
     */
    public function video($id = '', $token = '') {
        $token = empty($token) ? (empty($this->user) ? null : $this->user->token) : $token;
        $response = $this->getVideosResponse($token, $id);

        return isset($response['items'][0]) ? (new YouTubeVideo($response)) : null;
    }

    /**
     * Get information about a YouTube playlist.
     *
     * @param string $id
     * @param string $token
     * @param integer $start
     * @param string $end
     * @return void
     */
    public function playlist($id = '', $token = '', $start = 0, $end = null) {
        $token = empty($token) ? (empty($this->user) ? null : $this->user->token) : $token;
        $maxResults = 50;
        $nextPageToken = '';
        $pageStart = intdiv($start - 1, $maxResults) + 1;
        $response = $this->getPlaylistItemsResponse($id, $token);

        if (isset($response['items'])) {
            $playlist = new YouTubePlaylist($response['items'][0]['snippet']['playlistId']);
            $end = ($end && $end <= $response['pageInfo']['totalResults']) ? $end : $response['pageInfo']['totalResults'];
            $pageEnd = intdiv($end - 1, $maxResults) + 1;
    
            for ($p = 1; $p <= $pageEnd; $p++) {
                $response = $this->getPlaylistItemsResponse($id, $token, $maxResults, $nextPageToken);

                if (empty($response['items'])) {
                    //Something went wrong on this response.
                    //Lets break the loop and returns what we have so far.
                    break; 
                }

                if ($p >= $pageStart) {
                    foreach ($response['items'] as $item) {
                        $position = $item['snippet']['position'];
                        if ($position >= $start && $position <= $end) {
                            $video = $this->video($item['snippet']['resourceId']['videoId'], $token);
                            $playlist->insert($video, $position);
                        }
                    }
                }
                
                $nextPageToken = $p < $pageEnd ? $response['nextPageToken'] : '';
            }
    
            return $playlist;
        }else{
            return null;
        }
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