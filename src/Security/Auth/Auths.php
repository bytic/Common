<?php

namespace ByTIC\Common\Security\Auth;

use ByTIC\Common\Records\Records;

class Auths extends Records
{

    /**
     * @return self
     */
    public function getCurrent()
    {
        $model = $this->getModel();
        if ($_SESSION[$model]) {
            $item = $this->findOne($_SESSION[$model]['id']);
            if ($item) {
                $item->authenticated(true);
            }
            $this->_current = $item;
        } else {
            $this->_current = $this->getNew();
        }

        return $this->_current;
    }
}
