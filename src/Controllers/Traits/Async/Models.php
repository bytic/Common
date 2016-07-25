<?php

namespace ByTIC\Common\Controllers\Traits\Async;

trait Models
{

    public function order()
    {
        parse_str($_POST['order'], $order);
        $idFields = $order['item'];

        $fields = $this->getModelManager()->findByPrimary($idFields);
        if (count($fields) < 1) {
            $this->Async()->sendMessage('No fields', 'error');
        }

        foreach ($idFields as $pos => $idField) {
            $field = $fields[$idField];
            if ($field) {
                $field->pos = $pos + 1;
                $field->update();
            }
        }

        $this->Async()->sendMessage('Items reordered');
    }

}