<?php
namespace Flickr\Photogallery\Controller;

/*
 * This file is part of the Flickr.Photogallery package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

class FlickrController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \Flickr\Photogallery\Domain\Repository\FlickrRepository
     */
    protected $flickrRepository;

    /**
     * @return void
     */
    public function photosAction()
    {
        $albumId = $this->request->getInternalArgument('__albumId');
        $photos = $this->flickrRepository->showPhotos($albumId);
        $this->view->assign('photos', $photos);
    }

    /**
     * @param string $albumId
     * @return void
     */
    public function photosInlineAction($albumId)
    {
        $photos = $this->flickrRepository->showPhotos($albumId);
        $this->view->assign('photos', $photos);
        return $this->view->render("../Flickr/Photos");
    }

    /**
     * @return void
     */
    public function albumListAction()
    {
        $userId = $this->request->getInternalArgument('__userId');
        $albumlist = $this->flickrRepository->showAlbumList($userId);
        $this->view->assign('albumlist', $albumlist);
    }

}
