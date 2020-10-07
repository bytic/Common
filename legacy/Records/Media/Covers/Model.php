<?php
namespace ByTIC\Common\Records\Media\Covers;

/**
 * Class Model
 * @package ByTIC\Common\Records\Media\Covers
 * @deprecated use media library repo
 */
class Model extends \ByTIC\Common\Records\Media\Images\Model
{		
	protected $_mediaType = 'covers';

	public function setName($name)
	{
		parent::setName($name);
		$this->url = $this->_model->getCoverURL($this->_type, $this->name);
		$this->path = $this->_model->getCoverPath($this->_type, $this->name);
	}	
}