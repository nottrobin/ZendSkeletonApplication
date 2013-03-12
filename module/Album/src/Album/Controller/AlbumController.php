<?php
/**
 * Created by JetBrains PhpStorm.
 * User: framework
 * Date: 11/03/13
 * Time: 15:35
 * To change this template use File | Settings | File Templates.
 */

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;
use Zend\Http\Request as HttpRequest;

class AlbumController extends AbstractActionController
{
    protected $albumTable;

    public function indexAction()
    {
        return new ViewModel(array('albums' => $this->getAlbumTable()->fetchAll()));
    }

    public function addAction()
    {
        return $this->_albumForm(new Album(), $this->getRequest());
    }

    private function _albumForm(Album $album, HttpRequest $request)
    {
        $filter = $album->getInputFilter();

        if($request->isPost()) {
            $filter->setData($request->getPost());

            if ($filter->isValid()) {
                $album->exchangeArray($request->getPost());
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        } else {
            $filter->setData(get_object_vars($album));
        }

        return new ViewModel(array(
            'id' => $album->id,
            'errors' => $filter->getMessages(),
            'values' => $filter->getValues()
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $album = $this->getAlbumTable()->getAlbum($id);

        if (!$album) {
            throw new \Exception('Nope...');
        }

        return $this->_albumForm($album, $this->getRequest());
    }

    public function deleteAction()
    {
        $this->getAlbumTable()->deleteAlbum($this->params()->fromRoute('id', 0));

        return $this->redirect()->toRoute('album');
    }

    public function errorAction()
    {

    }

    public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }

        return $this->albumTable;
    }
}
