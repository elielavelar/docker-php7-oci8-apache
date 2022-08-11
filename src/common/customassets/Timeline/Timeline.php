<?php

namespace common\customassets\Timeline;

use common\customassets\Timeline\Html;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class Timeline extends Widget
{
    public $sort = 'default';

    const SORT_DEFAULT= 'default';
    const SORT_REVERSE = 'reverse';

    const TYPE_NAVY = 'navy';
    const TYPE_LBLUE = 'light-blue';
    const TYPE_BLUE = 'blue';
    const TYPE_AQUA = 'aqua';
    const TYPE_RED = 'red';
    const TYPE_GREEN = 'green';
    const TYPE_YEL = 'yellow';
    const TYPE_PURPLE = 'purple';
    const TYPE_MAR = 'maroon';
    const TYPE_TEAL = 'teal';
    const TYPE_OLIVE = 'olive';
    const TYPE_LIME = 'lime';
    const TYPE_ORANGE = 'orange';
    const TYPE_FUS = 'fuchsia';
    const TYPE_BLACK = 'black';
    const TYPE_GRAY = 'gray';

    /**@var [] $items array of events
     *
     * @example
     *  'items'=>[
     *     '07.10.2014'=>[array of TimelineItems ],
     *     'some object'=>[array of TimelineItems ],
     *     '15.11.2014'=>[array of TimelineItems ],
     *     'some object'=>[array of TimelineItems ],
     *  ]
     *
     **/
    public $items = [];

    /**
     * string|\Closure that return string
     *
     * @example
     * 'defaultDateBg'=>function($data){
     *      if(is_string($data)){
     *          return insolita\wgadminlte\Timeline::TYPE_BLUE;
     *      }elseif($data->type==1){
     *          return insolita\wgadminlte\Timeline::TYPE_LBLUE;
     *      }else{
     *         return insolita\wgadminlte\Timeline::TYPE_TEAL;
     *      }
     * }
     **/
    public $defaultDateBg = self::TYPE_GREEN;

    /** callable function(obj) for prepare data
     *
     * @example
     * 'dateFunc'=>function($data){
     *     return date('d/m/Y', $data)
     * }
     *
     * @example
     * 'dateFunc'=>function($data){
     *     return is_object($data)?date('d/m/Y', $data->created):$data;
     * }
     *
     **/
    public $dateFunc = null;

    public function run()
    {
        TimelineAsset::register($this->getView());
        echo Html::tag('ul', $this->renderItems(), ['class' => 'timeline']);
    }

    public function renderItems()
    {
        $res = '';
        if (!empty($this->items)) {
            foreach ($this->items as $data => $events) {
                if (!empty($events)) {
                    if( $this->sort == self::SORT_DEFAULT){
                        $res .= $this->renderGroup($data);
                        foreach ($events as $event) {
                            $res .= $this->renderEvent($event);
                        }
                    } else {
                        $group = $this->renderGroup($data);
                        $items = '';
                        foreach ($events as $event) {
                            $items = $this->renderEvent($event).$items;
                        }
                        $res = $group.$items.$res;
                    }

                }
            }
        }
        return $res;
    }

    public function renderGroup($data)
    {
        $res = '';

        $realdata = is_null($this->dateFunc) ? $data : call_user_func($this->dateFunc, $data);
        if (is_string($this->defaultDateBg)) {
            //$res .= Html::tag('span', Html::icon('fas fa-chevron-up'), ['class' => 'to-multimedia bg-' . $this->defaultDateBg]);
            $res .= Html::tag(
                'span',
                Html::icon('fas fa-calendar') . ' ' . ' <span class="time-label-date">' . $realdata . '</span>',
                ['class' => 'bg-' . $this->defaultDateBg]
            );
        } elseif (is_callable($this->defaultDateBg)) {
            $class = call_user_func($this->defaultDateBg, $data);
            //$res .= Html::tag('span', Html::icon('fas fa-chevron-up'), ['class' => 'to-multimedia bg-' . $class]);
            $res .= Html::tag(
                'span',
                Html::icon('fas fa-calendar') . ' <span class="time-label-date">' . $realdata . '</span>',
                ['class' => 'bg-' . $class]
            );
        }

        return Html::tag('li', $res, ['class' => 'time-label']);
    }

    public function renderEvent($ev)
    {
        $res = '';
        if ($ev instanceof TimelineItem) {
            $res .= '<i class="' . $ev->iconClass . ' bg-' . $ev->iconBg .'"></i>';
            $item = '';
            $header = '';
            if ($ev->header) {
                $headerClass = ($ev->iconBg) ? 'bg-' . $ev->iconBg : '';
                switch(gettype($ev->header)){
                    case 'array':
                        $buttongroup = $this->_getHeaderButtons(ArrayHelper::getValue($ev->header, 'buttons', []));
                        $header .= Html::tag(
                            'h3',
                                $buttongroup.
                                ArrayHelper::getValue($ev->header, 'title', \Yii::t('app', 'Undefined'))
                                ,
                            [
                                'class' => 'timeline-title',
                            ]
                        );
                        break;
                    default:
                        $header .= Html::tag(
                            'h3',
                            $ev->header,
                            [
                                'class' => 'timeline-title'
                            ]
                        );
                }
                $item .= Html::tag('div', $header, ['class' => 'timeline-header ' . $headerClass . ' ' . (!$ev->body && !$ev->footer ? 'no-border' :
                        ''),]);
            }
            $item .= Html::tag('div', $ev->body, ['class' => 'timeline-body']);
            if ($ev->footer) {
                $item .= Html::tag('div', $ev->footer, ['class' => 'timeline-footer']);
            }
            $itemClass = ($ev->itemClass) ? $ev->itemClass : '';
            $res .= Html::tag('div', $item, ['class' => 'timeline-item ' . $itemClass]);

        } else {
            throw new InvalidConfigException('event must be instanceof TimelineItem');
        }

        return Html::tag('li', $res);
    }

    protected function _getHeaderButtons($buttons = []){
        try {
            $content = '<div class="timeline-header-button-group float-right">';
            if(gettype($buttons) == 'array'){
                foreach ($buttons as $button){
                    $visible = ArrayHelper::getValue($button, 'visible', true);
                    $disabled = ArrayHelper::getValue($button, 'disabled', false);
                    $icon = ArrayHelper::getValue($button, 'icon', false);
                    $url = ArrayHelper::getValue($button, 'url', null);
                    $options = ArrayHelper::getValue($button,'options', []);
                    $class =  ArrayHelper::getValue($options,'class', '').' timeline-header-button';
                    $class .= ($visible ? '' : ' invisible');
                    $class .= ($disabled ? ' disabled' : '');
                    $options['class'] = $class;
                    $content .= $disabled ?
                        Html::label(
                            ( $icon ? Html::icon($icon).' ' : ''). ArrayHelper::getValue($button, 'title',''),
                            null,
                            $options
                        )
                        : Html::a(
                        ( $icon ? Html::icon($icon).' ' : ''). ArrayHelper::getValue($button, 'title',''),
                        $url,
                        $options
                    );
                }
            } else {
                $content.= $buttons;
            }
            return $content.'</div>';
        } catch (\Exception $exception){
            throw $exception;
        }
    }
}