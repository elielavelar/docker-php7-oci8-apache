<?php

namespace common\customassets\helpers;

use hail812\adminlte\widgets\Widget;
use yii\base\InvalidConfigException;
use yii\bootstrap4\BootstrapAsset;
use yii\helpers\ArrayHelper;
use Yii;

class Nav extends Widget
{
    /**
     * @var array list of items in the nav widget. Each array element represents a single
     * menu item which can be either a string or an array with the following structure:
     *
     * - label: string, required, the nav item label.
     * - url: optional, the item's URL. Defaults to "#".
     * - visible: bool, optional, whether this menu item is visible. Defaults to true.
     * - disabled: bool, optional, whether this menu item is disabled. Defaults to false.
     * - linkOptions: array, optional, the HTML attributes of the item's link.
     * - options: array, optional, the HTML attributes of the item container (LI).
     * - active: bool, optional, whether the item should be on active state or not.
     * - dropdownOptions: array, optional, the HTML options that will passed to the [[Dropdown]] widget.
     * - items: array|string, optional, the configuration array for creating a [[Dropdown]] widget,
     *   or a string representing the dropdown menu. Note that Bootstrap does not support sub-dropdown menus.
     * - encode: bool, optional, whether the label will be HTML-encoded. If set, supersedes the $encodeLabels option for only this item.
     *
     * If a menu item is a string, it will be rendered directly without HTML encoding.
     */
    public $items = [];
    /**
     * @var bool whether the nav items labels should be HTML-encoded.
     */
    public $encodeLabels = false;
    /**
     * @var bool whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive
     */
    public $activateItems = true;
    /**
     * @var bool whether to activate parent menu items when one of the corresponding child menu items is active.
     */
    public $activateParents = false;
    /**
     * @var string the route used to determine if a menu item is active or not.
     * If not set, it will use the route of the current request.
     * @see params
     * @see isItemActive
     */
    public $route;
    /**
     * @var array the parameters used to determine if a menu item is active or not.
     * If not set, it will use `$_GET`.
     * @see route
     * @see isItemActive
     */
    public $params;
    /**
     * @var string name of a class to use for rendering dropdowns within this widget. Defaults to [[Dropdown]].
     */
    public $dropdownClass = 'common\customassets\helpers\Dropdown';

    public $navClass = '';
    public $defaultClass = 'main-header navbar navbar-expand navbar-dark';

    public $defaultItems = [];
    public $endItems = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        $this->defaultItems = [
            [
                'label' => Html::icon('fas fa-bars'),
                'url' => '#',
                'encode' => false,
                'linkOptions' => [
                    'data'=> [
                        'widget' => 'pushmenu',
                    ],
                    'role' => 'button',
                    'class' => 'nav-link'
                ]
            ]
        ];

        $this->endItems = [
            [
                'label' => Html::icon('fas fa-th-large'),
                'url' => '#',
                'options' => [
                    'role' => 'button',
                ],
                'linkOptions' => [
                    'data' => [
                        'widget' => 'control-sidebar',
                        'slide' => true,
                    ]
                ]
            ]
        ];
        parent::init();
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }

        Html::addCssClass($this->options, ['widget' => 'navbar-nav']);
    }

    /**
     * Renders the widget.
     * @throws InvalidConfigException
     */
    public function run()
    {
        BootstrapAsset::register($this->getView());
        return $this->renderNav();
    }

    /**
     * Renders widget wrapper
     * @throws InvalidConfigException
     */
    public function renderNav(){
        return Html::tag('nav', $this->renderNavItems(), [
            'class' => $this->defaultClass
                .(' '.ArrayHelper::getValue($this->options,'navClass').' ')
                .(' navbar-'.ArrayHelper::getValue(Yii::$app->params, 'system.theme.navbar.color','default')),
        ]);
    }

    /**
     * Renders widget items.
     * @throws InvalidConfigException
     */
    public function renderNavItems()
    {
        $defaultItems = $this->renderItems($this->defaultItems);
        ArrayHelper::remove($this->options, 'navClass');
        Html::addCssClass($this->options, 'ml-auto');
        $items = $this->renderItems(ArrayHelper::merge($this->items, $this->endItems));
        return $defaultItems.$items;
    }
    /**
     * Renders widget items.
     * @throws InvalidConfigException
     */
    public function renderItems($optionItems)
    {
        $items = [];
        foreach ($optionItems as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $items[] = $this->renderItem($item);
        }
        return Html::tag('ul', implode("\n", $items), $this->options);
    }

    /**
     * Renders a widget's item.
     * @param string|array $item the item to render.
     * @return string the rendering result.
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function renderItem($item)
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        $disabled = ArrayHelper::getValue($item, 'disabled', false);
        $active = $this->isItemActive($item);

        if (empty($items)) {
            $items = '';
            Html::addCssClass($options, ['widget' => 'nav-item']);
            Html::addCssClass($linkOptions, ['widget' => 'nav-link']);
        } else {
            $linkOptions['data-toggle'] = 'dropdown';
            Html::addCssClass($options, ['widget' => 'dropdown nav-item']);
            Html::addCssClass($linkOptions, ['widget' => 'dropdown-toggle nav-link']);
            if (is_array($items)) {
                $items = $this->isChildActive($items, $active);
                $items = $this->renderDropdown($items, $item);
            }
        }

        if ($disabled) {
            ArrayHelper::setValue($linkOptions, 'tabindex', '-1');
            ArrayHelper::setValue($linkOptions, 'aria-disabled', 'true');
            Html::addCssClass($linkOptions, ['disable' => 'disabled']);
        } elseif ($this->activateItems && $active) {
            Html::addCssClass($linkOptions, ['activate' => 'active']);
        }

        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items, $options);
    }

    /**
     * Renders the given items as a dropdown.
     * This method is called to create sub-menus.
     * @param array $items the given items. Please refer to [[Dropdown::items]] for the array structure.
     * @param array $parentItem the parent item information. Please refer to [[items]] for the structure of this array.
     * @return string the rendering result.
     * @throws \Exception
     */
    protected function renderDropdown($items, $parentItem)
    {
        /** @var Widget $dropdownClass */
        $dropdownClass = $this->dropdownClass;
        return $dropdownClass::widget([
            'options' => ArrayHelper::getValue($parentItem, 'dropdownOptions', []),
            'items' => $items,
            'encodeLabels' => $this->encodeLabels,
            'clientOptions' => false,
            'view' => $this->getView(),
        ]);
    }

    /**
     * Check to see if a child item is active optionally activating the parent.
     * @param array $items @see items
     * @param bool $active should the parent be active too
     * @return array @see items
     */
    protected function isChildActive($items, &$active)
    {
        foreach ($items as $i => $child) {
            if (is_array($child) && !ArrayHelper::getValue($child, 'visible', true)) {
                continue;
            }
            if ($this->isItemActive($child)) {
                ArrayHelper::setValue($items[$i], 'active', true);
                if ($this->activateParents) {
                    $active = true;
                }
            }
            $childItems = ArrayHelper::getValue($child, 'items');
            if (is_array($childItems)) {
                $activeParent = false;
                $items[$i]['items'] = $this->isChildActive($childItems, $activeParent);
                if ($activeParent) {
                    Html::addCssClass($items[$i]['options'], ['activate' => 'active']);
                    $active = true;
                }
            }
        }
        return $items;
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param array $item the menu item to be checked
     * @return bool whether the menu item is active
     */
    protected function isItemActive($item)
    {
        if (!$this->activateItems) {
            return false;
        }
        if (isset($item['active'])) {
            return ArrayHelper::getValue($item, 'active', false);
        }
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}