<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\components;

/**
 * Description of CustomMenu
 *
 * @author avelare
 */
use hail812\adminlte\widgets\Menu;

class CustomMenu extends Menu {
    public $route;
    public $options = [
        'class' => 'nav nav-pills nav-sidebar flex-column nav-compact nav-child-indent nav-collapse-hide-child',
        'data-widget' => 'treeview',
        'role' => 'menu',
        'data-accordion' => 'false'
    ];
    protected function isItemActive($item) {
        if (isset($item['url']) && $this->route) {
            return $item["url"] == $this->route;
        } else {
            return parent::isItemActive($item);
        }
        return false;
    }
}
