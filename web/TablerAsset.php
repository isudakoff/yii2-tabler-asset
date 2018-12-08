<?php
/**
 * @author Ilya Sudakov
 * @date 08-12-2018
 * @license https://github.com/isudakoff/yii2-tabler-asset/LICENSE
 * @copyright 2018 Ilya Sudakov
 */

namespace isudakoff\web;

use yii\web\AssetBundle;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\web\YiiAsset;

/**
 * Tabler AssetBundle
 * @since 0.1
 * @package isudakoff\web
 */
class TablerAsset extends AssetBundle
{
    public $sourcePath = '@vendor/tabler/tabler/dist/assets';

    public $css = [
        'css/tabler.css',
        'css/dashboard.css',
    ];

    public $js = [
        'js/cored.js',
        'js/dashboard.js',
        'js/require.min.js',
    ];

    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
    ];
}
