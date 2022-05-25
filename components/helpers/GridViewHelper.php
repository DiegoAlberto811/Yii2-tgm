<?php
namespace app\components\helpers;

use yii\helpers\ArrayHelper;

class GridViewHelper
{
    public static function columnEditItem()
    {
        return self::columnActionItem([
            'icon' => 'edit',
            'columnClass' => 'info-column item-edit',
        ]);
    }

    public static function columnDeleteItem()
    {
        return self::columnActionItem([
            'icon' => 'trash',
            'columnClass' => 'danger-column item-delete',
        ]);
    }

    public static function columnActionItem($options)
    {
        $icon = ArrayHelper::getValue($options, 'icon', '');
        $label = ArrayHelper::getValue($options, 'label', ' ');
        $columnClass = ArrayHelper::getValue($options, 'columnClass', '');

        return [
            'label' => $label,
            'format' => 'raw',
            'attribute' => ArrayHelper::getValue($options, 'attribute', 'attribute'),
            'value' => function($model) use ($icon) {
                $icon = is_callable($icon) ? $icon($model) : $icon;
                return '<span class="glyphicon glyphicon-' . $icon . '"></span>';
            },
            'headerOptions' => ['style' => 'width:0', 'class' => 'text-center'],
            'header' => ArrayHelper::getValue($options, 'header'),
            'contentOptions' => [
                'class' => 'text-center ' . $columnClass,
            ],
        ];
    }
}