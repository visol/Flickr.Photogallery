<?php
namespace Flickr\Photogallery\Domain\Repository;

/*
 * This file is part of the Flickr.Photogallery package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class FlickrRepository extends Repository
{

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $albumId
     * @return void
     */
    public function showPhotos($albumId)
    {
        $apiKey = $this->settings['apiKey'];
        $xmlfile = 'https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key='.$apiKey.'&photoset_id='.$albumId.'&format=rest';
        $xml = simplexml_load_file($xmlfile);
        $check = $xml->err[0]['code'];
        if ($check=='1') {
            $output = $xml->err[0]['code'];
        } else {
            $count = count($xml->photoset[0]->children());
            if ($count>0) {
                $output = array();
                for($i = 0 ; $i < $count; $i++) {
                    $photoset = $xml->photoset[0]['id'];
                    $albumtitle = $xml->photoset[0]['title'];
                    $farm = $xml->photoset[0]->photo[$i]['farm'];
                    $server = $xml->photoset[0]->photo[$i]['server'];
                    $id = $xml->photoset[0]->photo[$i]['id'];
                    $secret = $xml->photoset[0]->photo[$i]['secret'];
                    $title = $xml->photoset[0]->photo[$i]['title'];
                    $url = 'http://c1.staticflickr.com/'.$farm.'/'.$server.'/'.$id.'_'.$secret;
                    $thumb = $url.'_q.jpg';
                    $big = $url.'_c.jpg';
                    $output[$i] = array(
                        'big' => $big,
                        'photoset' => $photoset,
                        'thumb' => $thumb,
                        'title' => $title,
                        'albumtitle' => $albumtitle
                    );
                }
            }
        }
        return $output;
    }

    /**
     * @param string $userId
     * @return void
     */
    public function showAlbumList($userId)
    {
        $apiKey = $this->settings['apiKey'];
        $userId = str_replace("@", "%40", $userId);
        $xmlfile = 'https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key='.$apiKey.'&user_id='.$userId.'&format=rest';
        $xml = simplexml_load_file($xmlfile);
        $check = $xml->err[0]['code'];
        if ($check=='1') {
            $output = $xml->err[0]['code'];
        } else {
            $count = count($xml->photosets->children());
            if ($count>0) {
                $output = array();
                for($i = 0 ; $i < $count; $i++) {
                    $title = $xml->photosets->photoset[$i]->title;
                    $datecreated = $xml->photosets->photoset[$i]['date_create'];

                    $farm = $xml->photosets->photoset[$i]['farm'];
                    $server = $xml->photosets->photoset[$i]['server'];
                    $primary = $xml->photosets->photoset[$i]['primary'];
                    $secret = $xml->photosets->photoset[$i]['secret'];
                    $img = 'http://c1.staticflickr.com/'.$farm.'/'.$server.'/'.$primary.'_'.$secret.'_m.jpg';

                    $photos = $xml->photosets->photoset[$i]['photos'];

                    $albumId = intval($xml->photosets->photoset[$i]['id']);

                    $output[$i] = array(
                        'title' => $title,
                        'photos' => $photos,
                        'image' => $img,
                        'date' => '@'.$datecreated,
                        'id' => $albumId
                    );
                }
            }
        }
        return $output;
    }

}