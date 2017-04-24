<?php
class DS_MANAGER_INSTAGRAM {
	
	// EIGENSCHAFTEN
	// --------------------------------------------------------------------
	private $instagram;
	private $authToken;
	private $images;
	private $imagesToShow;
	
	// KONSTRUKTOR
	// --------------------------------------------------------------------
	function __construct($instagram, $data, $imagesToShow = 12) {
		$this->imagesToShow = $imagesToShow;
		$this->images = array();
		$this->instagram = $instagram;
		$this->authToken = $data;
		$instagram->setAccessToken ( $data );
		$this->setImages($data->user->id);
	}
	
	// GET DATA
	// --------------------------------------------------------------------
	function serialize() {
        $data = array(
            'TOKEN' => $this->authToken,
        	'IMGTOSHOW' => $this->imagesToShow
        );
		return $data;
	}
	/*
	 * SET IMAGE URL's FROM INSTAGRAM MEDIA
	 * --------------------------------------------------------------------
	 *
	 */
	function setImages($userID) {
		if($this->imagesToShow == 1)
			$this->imagesToShow++;
		$media = $this->instagram->pagination($this->instagram->getUserMedia($userID, 1), $this->imagesToShow);
		foreach ( $media->data as $imedia ) {
			if (! ($imedia->type === 'image'))
				continue;
			if(!in_array($imedia->images, $this->images))
				array_push($this->images, $imedia->images);
		}
	}
	/* EXPAND THE SHOWN IMAGES BY $moreImagesToShow
	 * 
	 */
	function expandImageList($moreImagesToShow = 1) {
		$this->imagesToShow = $this->imagesToShow+$moreImagesToShow;
		$this->setImages($this->authToken->user->id);
	}
	/*
	 * $size = 'thumbnail' OR 'low_resolution' OR 'standard_resolution'
	 */
	public function getImageURLs($size = 'standard_resolution') {
		$URLs = array();
		foreach ( $this->images as $image ) {
			$URLs[] = $image->$size->url;
		}
		return $URLs;
	}
}

?>