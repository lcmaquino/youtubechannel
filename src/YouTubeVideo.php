<?php

namespace Lcmaquino\YouTubeChannel;

class YouTubeVideo
{
    /**
     * The unique YouTube identifier for the video.
     *
     * @var string
     */
    protected $id;

    /**
     * The unique YouTube identifier for the channel.
     *
     * @var string
     */
    protected $channelId;

    /**
     * The channel's title.
     *
     * @var string
     */
    protected $channelTitle;

    /**
     * The unique YouTube identifier for the video's category.
     *
     * @var string
     */
    protected $categoryId;

    /**
     * The date when the video was published.
     *
     * @var string
     */
    protected $publishedAt;

    /**
     * The video's title.
     *
     * @var string
     */
    protected $title;

    /**
     * The video's description.
     *
     * @var string
     */
    protected $description;

    /**
     * The video's thumbnails.
     *
     * @var array
     */
    protected $thumbnails;

    /**
     * The video's tags.
     *
     * @var array
     */
    protected $tags;

    /**
     * Create a new YouTubeVideo instance.
     * 
     * The $response parameter should be an associative array with the response 
     * for the video request on YouTube Data API v3.
     * 
     * @param  array  $response
     * @return $this
     */
    public function __construct($response = []){
        $item = $response['items'][0];
        $this->id = $item['id'];
        $snippet = $item['snippet'];
        $this->channelId = $snippet['channelId'];
        $this->channelTitle = $snippet['channelTitle'];
        $this->categoryId = $snippet['categoryId'];
        $this->publishedAt = $snippet['publishedAt'];
        $this->title = $snippet['title'];
        $this->description = $snippet['description'];
        $this->thumbnails = $snippet['thumbnails'];
        $this->tags = $snippet['tags'];
        return $this;
    }

    /**
     * Set the video's id.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id = ''){
        $this->id = $id;
        return $this;
    }

    /**
     * Get the video's id.
     *
     * @return string
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Set the channels's id.
     *
     * @param string $id
     * @return $this
     */
    public function setChannelId($id = ''){
        $this->channelId = $id;
        return $this;
    }

    /**
     * Get the channels's id.
     *
     * @return string
     */
    public function getChannelId(){
        return $this->channelId;
    }

    /**
     * Set the channels's title.
     *
     * @param string $title
     * @return $this
     */
    public function setChannelTitle($title = ''){
        $this->channelTitle = $title;
        return $this;
    }

    /**
     * Get the channels's title.
     *
     * @return string
     */
    public function getChannelTitle(){
        return $this->channelTitle;
    }

    /**
     * Set the video's category ID.
     *
     * @param string $id
     * @return $this
     */
    public function setCategoryId($id = ''){
        $this->categoryId = $id;
        return $this;
    }

    /**
     * Get the videos's category ID.
     *
     * @return string
     */
    public function getCategoryId(){
        return $this->categoryId;
    }

    /**
     * Set the date when the video was published.
     *
     * @param string $time
     * @return $this
     */
    public function setPublishedAt($time = ''){
        $this->publishedAt = $time;
        return $this;
    }

    /**
     * Get the date when the video was published.
     *
     * @return string
     */
    public function getPublishedAt(){
        return $this->publishedAt;
    }

    /**
     * Set the video's title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title = ''){
        $this->title = $title;
        return $this;
    }

    /**
     * Get the video's title.
     *
     * @return string
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * Set the video's description.
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description = ''){
        $this->description = $description;
        return $this;
    }

    /**
     * Get the video's description.
     *
     * @return string
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * Set the video's thumbnails.
     *
     * @param array $thumbnails
     * @return $this
     */
    public function setThumbnails($thumbnails = []){
        $this->thumbnails = $thumbnails;
        return $this;
    }

    /**
     * Get the video's thumbnails.
     *
     * @return array
     */
    public function getThumbnails(){
        return $this->thumbnails;
    }

    /**
     * Set the video's tags.
     *
     * @param array $tags
     * @return $this
     */
    public function setTags($tags = []){
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get the video's tags.
     *
     * @return array
     */
    public function getTags(){
        return $this->tags;
    }
}