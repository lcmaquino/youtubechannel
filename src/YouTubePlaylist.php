<?php

namespace Lcmaquino\YouTubeChannel;

class YouTubePlaylist
{
    /**
     * The unique YouTube identifier for the playlist.
     *
     * @var string
     */
    protected $id;

    /**
     * The videos in the playlist.
     *
     * @var array
     */
    protected $items;

    /**
     * The position of each video in playlist.
     *
     * @var array
     */
    protected $positions;

    /**
     * Create a new YouTubePlaylist instance.
     * 
     * @param  string  $id
     * @return $this
     */
    public function __construct($id = ''){
        $this->id = $id;
        $this->items = [];
        $this->positions = [];
        return $this;
    }

    /**
     * Set the playlist's id.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id = ''){
        $this->id = $id;
        return $this;
    }

    /**
     * Get the playlist's id.
     *
     * @return string
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Get the items in the playlist.
     *
     * @return array
     */
    public function getItems(){
        return $this->items;
    }

    /**
     * Get the position of each video in the playlist.
     *
     * @return array
     */
    public function getPositions(){
        return $this->positions;
    }

    /**
     * Get the video's position at the playlist.
     *
     * Returns false if the video was not found.
     * 
     * @param string $videoId
     * @return int|false
     */
    public function getVideoPosition($videoId = ''){
        return array_search($videoId, $this->positions);
    }

    /**
     * Get a video in the playlist.
     *
     * Returns null if the video was not found.
     * 
     * @param string $videoId
     * @return YouTubeVideo|null
     */
    public function getVideo($videoId = ''){
        $position = $this->getVideoPosition($videoId);
        return $position !== false ? $this->items[$position] : null;
    }

    /**
     * Get the video at a given position of the playlist.
     *
     * Returns null if the given position is invalid.
     * 
     * @param int $position
     * @return YouTubeVideo|null
     */
    public function getVideoAt($position = 0){
        return ($position >= 0 && $position < count($this->items)) ? $this->items[$position] : null;
    }

    /**
     * Insert a video in the playlist.
     *
     * @param YouTubeVideo $video
     * @param int $position
     * @return $this
     */
    public function insert(YouTubeVideo $video = null, $position = null){
        $videoId = empty($video) ? null : $video->getId();
        if ($position !== null) {
            $this->items[$position] = $video;
            $this->positions[$position] = $videoId;
        }else{
            $this->items[] = $video;
            $this->positions[] = $videoId;
        }
        return $this;
    }
}