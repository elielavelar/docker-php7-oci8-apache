<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace client\components;

/**
 * Description of CustomMenu
 *
 * @author avelare
 */
use webtoolsnz\AdminLte\widgets\Menu;

class CustomMenu extends Menu {
    public $route;
    protected function isItemActive($item) {
        if (isset($item['url']) && $this->route) {
            return $item["url"] == $this->route;
        } else {
            return parent::isItemActive($item);
        }
        return false;
        
    }
}
