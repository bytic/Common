<?php
namespace ByTIC\Common\Records\Media\Covers;

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