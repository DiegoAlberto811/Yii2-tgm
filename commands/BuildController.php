<?php
namespace app\commands;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class BuildController extends Controller
{
    public $jsEntries = [
        '@app/web/js/common.js' => [
            '@libs/_common/config.js',
            '@libs/_common/common.js',
            '@libs/jquery/jquery.blockUI.js',
            '@libs/jquery/jquery.quickfit.js',
            '@libs/mailcheck/mailcheck.min.js',
            '@libs/backbone/underscore-min.js',
            '@libs/manage-admin/app.js'
        ],

        '@app/web/js/embed.min.js' => [
            '@libs/backbone/underscore-min.js',
            '@libs/embed/embed.js',
        ],
    ];

    public $cssEntries = [
        '@app/web/css/public.css' => [
            '@app/static/css/public.css',
            // '@app/static/css/font-awesome.min.css',
        ],
        '@app/web/css/manage.css' => [
            '@app/static/css/manage.css',
            '@app/static/css/font-awesome.min.css',
        ],
    ];

    /**
     * Builds required javascript files
     */
    public function actionIndex($file = null)
    {
        \Yii::setAlias('@libs', '@app/static/libs');

        /** common js bundles */
        foreach ($this->jsEntries as $jsFile => $list) {
            $this->buildFile($jsFile);
        }

        /** common css bundles */
        foreach ($this->cssEntries as $cssFile => $list) {
            $this->buildCssFile($cssFile);
        }

        /** action js files */
        foreach (glob(\Yii::getAlias('@app/static/js-app') . DIRECTORY_SEPARATOR . '*') as $dir) {
            if (is_dir($dir)) {
                $moduleDir = basename($dir);
                @mkdir(\Yii::getAlias("@app/web/js/{$moduleDir}"));
                foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $cdir) {
                    if (is_dir($cdir)) {
                        $controllerDir = basename($cdir);
                        @mkdir(\Yii::getAlias("@app/web/js/{$moduleDir}/{$controllerDir}"));
                        foreach (glob($cdir . DIRECTORY_SEPARATOR . '*.js') as $jsFile) {
                            $baseJsFile = basename($jsFile);
                            $filename = \Yii::getAlias("@app/web/js/{$moduleDir}/{$controllerDir}/{$baseJsFile}");
                            echo "@app/web/js/{$moduleDir}/{$controllerDir}/{$baseJsFile} ... ";
                            system('uglifyjs -m -c unused=false "' . $jsFile . '" -o "' . $filename . '"');
                            echo "ok\n";
                        }
                    }
                }
            }
        }
    }

    protected function buildFile($file)
    {
        $list = ArrayHelper::getValue($this->jsEntries, $file);
        if (!$list) {
            return;
        }

        $minifier = new JS(array_map(function($item) {
            return \Yii::getAlias($item);
        }, $list));

        echo "$file ... ";

        $filename = \Yii::getAlias($file);
        $minifier->minify($filename);

        system('uglifyjs -m -c unused=false "' . $filename . '" -o "' . $filename . '"');

        echo "ok\n";
    }

    protected function buildCssFile($file)
    {
        $list = ArrayHelper::getValue($this->cssEntries, $file);
        if (!$list) {
            return;
        }

        $minifier = new CSS(array_map(function($item) {
            return \Yii::getAlias($item);
        }, $list));

        echo "$file ... ";

        $filename = \Yii::getAlias($file);
        $minifier->minify($filename);

        system('uglifycss "' . $filename . '" --output "' . $filename . '"');

        echo "ok\n";
    }
}