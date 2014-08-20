<?php

namespace ShoppingList\Model;

use Zend\Db\TableGateway\TableGateway;

class ShoppingListTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getItem($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveItem(ShoppingList $model) {
        $allData = get_object_vars($model);
        $data = array();
        foreach ($allData as $allDataName => $allDataValue) {
            if (isset($allDataValue)) {
                $data[$allDataName] = $allDataValue;
            }
        }

        $id = (int) $model->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getItem($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Shopping list id does not exist');
            }
        }
    }

    public function deleteItem($id) {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

}
