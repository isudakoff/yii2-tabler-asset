<?php
/**
 * @author Ilya Sudakov
 * @date 08-12-2018
 * @license https://github.com/isudakoff/yii2-tabler-asset/LICENSE
 * @copyright 2018 Ilya Sudakov
 */

namespace isudakoff\widgets;

use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;

/**
 * Renders a card.
 *
 * @example
 * ```
 * <?=
 * Card::widget([
 *     'cardHeaderOptions' => [
 *         'class' => 'bg-secondary',
 *     ],
 *     'cardTitle' => 'Top card',
 *     'cardBody' => $someHtml,
 * ]);
 * ?>
 * ```
 */
class Card extends Widget
{
    public $cardContainerOptions;

    public $useCardStatus = false;

    public $cardStatusOptions;

    public $cardTitle;

    public $cardTitleOptions;

    public $cardOptions;

    public $cardOptionsOptions;

    public $useCardHeader = true;

    public $cardHeaderOptions;

    public $cardAlert;

    public $cardAlertOptions;

    public $cardBody = '';

    public $cardBodyOptions;

    public $cardFooter;

    public $cardFooterOptions;

    /** methods * */
    public function init()
    {
        parent::init();

        if (empty($this->cardContainerOptions)) {
            Html::addCssClass($this->cardContainerOptions, 'card-default');
        }

        if (empty($this->cardAlertOptions)) {
            Html::addCssClass($this->cardAlertOptions, 'alert-danger');
        }

        Html::addCssClass($this->cardContainerOptions, 'card');
        Html::addCssClass($this->cardStatusOptions, 'card-status');
        Html::addCssClass($this->cardHeaderOptions, 'card-header');
        Html::addCssClass($this->cardTitleOptions, 'card-title');
        Html::addCssClass($this->cardAlertOptions, 'card-alert alert');
        Html::addCssClass($this->cardBodyOptions, 'card-body');
        Html::addCssClass($this->cardFooterOptions, 'card-footer');

        ob_start();
    }

    public function run()
    {
        $out = Html::beginTag('div', $this->cardContainerOptions);

            if ($this->useCardStatus) {
                $out .= Html::beginTag('div', $this->cardStatusOptions);
                $out .= Html::endTag('div');
            }

            if (!empty($this->cardTitle) && $this->useCardHeader) {
                $out .= Html::beginTag('div', $this->cardHeaderOptions);
                    $out .= Html::beginTag('h3', $this->cardTitleOptions);
                        $out .= $this->cardTitle;
                    $out .= Html::endTag('h3');
                    $out .= Html::beginTag('div', $this->cardOptionsOptions);
                        $out .= $this->cardOptions ?:
                            Html::a('<i class="fe fe-chevron-up">', '#', [
                                'class' => 'card-options-collapse',
                                'data-toggle' => 'card-collapse',
                            ]) . "\n" .
                            Html::a('<i class="fe fe-maximize">', '#', [
                                'class' => 'card-options-fullscreen',
                                'data-toggle' => 'card-fullscreen',
                            ]) . "\n" .
                            Html::a('<i class="fe fe-x">', '#', [
                                'class' => 'card-options-remove',
                                'data-toggle' => 'card-remove',
                            ]);
                    $out .= Html::endTag('div');

                    if (!empty($this->cardAlert)) {
                        $out .= Html::beginTag('div', $this->cardAlertOptions);
                            $out .= $this->cardAlert;
                        $out .= Html::endTag('div');
                    }

                $out .= Html::endTag('div');
            }

            $out .= Html::beginTag('div', $this->cardBodyOptions);

                if (!$this->useCardHeader) {
                    $out .= Html::beginTag('h3', $this->cardTitleOptions);
                        $out .= $this->cardTitle;
                    $out .= Html::endTag('h3');
                }

                $out .= $this->cardBody;
                $out .= ob_get_clean();
            $out .= Html::endTag('div');

            if (isset($this->cardFooter)) {
                $out .= Html::beginTag('div', $this->cardFooterOptions);
                    $out .= $this->cardFooter;
                $out .= Html::endTag('div');
            }

        $out .= Html::endTag('div');

        return $out;
    }
}
