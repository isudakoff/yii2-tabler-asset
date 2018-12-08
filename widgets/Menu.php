<?php
/**
 * @author Ilya Sudakov
 * @date 08-12-2018
 * @license https://github.com/isudakoff/yii2-tabler-asset/LICENSE
 * @copyright 2018 Ilya Sudakov
 */

namespace isudakoff\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Menu as BaseMenu;

/**
 * Class Menu
 *
 * @package isudakoff\widgets
 */
class Menu extends BaseMenu
{
    public $linkTemplate = '<a href="{url}" class="nav-link">{icon} {label}</a>';

    public $submenuTemplate = "\n<div class='dropdown-menu dropdown-menu-arrow' {show}>\n{items}\n</div>\n";

    public $activateParents = true;

    public $defaultIconHtml = '<i class="fa fa-circle-o"></i> ';

    public $options = [
        'class' => 'nav nav-tabs border-0 flex-column flex-lg-row',
    ];

    public $itemOptions = [
        'class' => 'nav-item',
    ];

    public $dropdownItemOptions = [];

    /**
     * @var string the template used to render the body of a dropdown menu which is a link.
     * In this template, the token `{url}` will be replaced with the corresponding link URL;
     * while `{label}` will be replaced with the link text.
     * This property will be overridden by the `submenuTemplate` option set in individual menu items via [[items]].
     */
    public $submenuLinkTemplate = '<a href="{url}" class="dropdown-item">{label}</a>';

    /**
     * @var string is prefix that will be added to $item['icon'] if it exist.
     * By default uses for Font Awesome (http://fontawesome.io/)
     */
    public static $iconClassPrefix = 'fa fa-';

    private $noDefaultAction;

    private $noDefaultRoute;

    /**
     * Renders the menu.
     */
    public function run()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }

        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }

        $posDefaultAction = strpos($this->route, Yii::$app->controller->defaultAction);

        if ($posDefaultAction) {
            $this->noDefaultAction = rtrim(substr($this->route, 0, $posDefaultAction), '/');
        } else {
            $this->noDefaultAction = false;
        }

        $posDefaultRoute = strpos($this->route, Yii::$app->controller->module->defaultRoute);

        if ($posDefaultRoute) {
            $this->noDefaultRoute = rtrim(substr($this->route, 0, $posDefaultRoute), '/');
        } else {
            $this->noDefaultRoute = false;
        }

        $items = $this->normalizeItems($this->items, $hasActiveChild);

        if (!empty($items)) {
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'ul');

            echo Html::tag($tag, $this->renderItems($items), $options);
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        if (isset($item['items'])) {
            $labelTemplate = '<a href="{url}" class="nav-link" data-toggle="dropdown">{icon} {label}</a>';
            $linkTemplate = '<a href="{url}" class="nav-link" data-toggle="dropdown">{icon} {label}</a>';
        } else {
            $labelTemplate = $this->labelTemplate;
            $linkTemplate = $this->linkTemplate;
        }

        $replacements = [
            '{label}' => strtr($this->labelTemplate, [
                '{label}' => $item['label'],
            ]),
            '{icon}' => empty($item['icon']) ? $this->defaultIconHtml
                : '<i class="' . static::$iconClassPrefix . $item['icon'] . '"></i> ',
            '{url}' => isset($item['url']) ? Url::to($item['url']) : 'javascript:void(0);',
        ];

        $template = ArrayHelper::getValue($item, 'template', isset($item['url']) ? $linkTemplate : $labelTemplate);

        return strtr($template, $replacements);
    }

    protected function renderDropdownItem($item)
    {
        return strtr($this->submenuLinkTemplate, [
            '{label}' => strtr($this->labelTemplate, [
                '{label}' => $item['label'],
            ]),
            '{url}' => isset($item['url']) ? Url::to($item['url']) : 'javascript:void(0);',
        ]);
    }

    /**
     * Recursively renders the menu items (without the container tag).
     *
     * @param array $items the menu items to be rendered recursively
     *
     * @return string the rendering result
     */
    protected function renderItems($items)
    {
        $n = \count($items);
        $lines = [];

        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];

            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }

            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }

            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }

            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }

            $menu = $this->renderItem($item);

            if (!empty($item['items'])) {
                $menu .= strtr($this->submenuTemplate, [
                    '{items}' => $this->renderDropdownItems($item['items']),
                ]);

                if (isset($options['class'])) {
                    $options['class'] .= ' dropdown';
                } else {
                    $options['class'] = 'dropdown';
                }
            }

            $lines[] = Html::tag($tag, $menu, $options);
        }

        return implode("\n", $lines);
    }

    protected function renderDropdownItems($items)
    {
        $lines = [];

        foreach ($items as $i => $item) {
            $options = array_merge($this->dropdownItemOptions, ArrayHelper::getValue($item, 'options', []));

            $menu = $this->renderDropdownItem($item);

            $lines[] = Html::tag(null, $menu, $options);
        }

        return implode("\n", $lines);
    }

    /**
     * {@inheritdoc}
     */
    protected function normalizeItems($items, &$active)
    {
        foreach ($items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                unset($items[$i]);

                continue;
            }

            if (!isset($item['label'])) {
                $item['label'] = '';
            }

            $encodeLabel = $item['encode'] ?? $this->encodeLabels;
            $items[$i]['label'] = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $items[$i]['icon'] = $item['icon'] ?? '';
            $hasActiveChild = false;

            if (isset($item['items'])) {
                $items[$i]['items'] = $this->normalizeItems($item['items'], $hasActiveChild);

                if (empty($items[$i]['items']) && $this->hideEmptyItems) {
                    unset($items[$i]['items']);

                    if (!isset($item['url'])) {
                        unset($items[$i]);

                        continue;
                    }
                }
            }

            if (!isset($item['active'])) {
                if (($this->activateParents && $hasActiveChild) || ($this->activateItems && $this->isItemActive($item))) {
                    $active = $items[$i]['active'] = true;
                } else {
                    $items[$i]['active'] = false;
                }
            } elseif ($item['active']) {
                $active = true;
            }
        }

        return array_values($items);
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     *
     * @param array $item the menu item to be checked
     *
     * @return boolean whether the menu item is active
     */
    protected function isItemActive($item)
    {
        if (isset($item['url'][0])) {
            $route = $item['url'][0];

            if (isset($route[0]) && $route[0] !== '/' && Yii::$app->controller) {
                $route = ltrim(Yii::$app->controller->module->getUniqueId() . '/' . $route, '/');
            }

            $route = ltrim($route, '/');

            if ($route != $this->route && $route !== $this->noDefaultRoute && $route !== $this->noDefaultAction) {
                return false;
            }

            unset($item['url']['#']);

            if (\count($item['url']) > 1) {
                foreach (array_splice($item['url'], 1) as $name => $value) {
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
