<?php
/**
 * Created by JetBrains PhpStorm.
 * User: helian
 * Date: 24/04/12
 * Time: 10:49
 * To change this template use File | Settings | File Templates.
 */

namespace Esimed\CarLocation\BackendBundle\Entity;

abstract class EntityArray {
    /**
     * Transforme une entity en array
     * @param $fields
     * @return array
     */
    public function toArray($fieldsToDisplay) {
        $fields = array();

        //pour chaque champ a afficer
        foreach($fieldsToDisplay as $field) {
            if (is_array($field)) {
                $method = 'get' . ucfirst($field[1]);
                $function = $field[0];
                $name = $field[1];
            } else {
                $method = 'get' . ucfirst($field);
                $function = '__toString';
                $name = $field;
            }

            //si la methode existe
            if (is_callable(array($this, $method))) {
                $response = $this->$method();

                if (is_object($response) && is_callable(array($response, $function)) ) {
                    $fields[$name] = $response->$function();
                } else  {
                    $fields[$name] = $response;
                }
            }
        }
        return $fields;
    }
}