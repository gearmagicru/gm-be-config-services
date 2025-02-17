<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Config\Services\Controller;

use Gm;
use Gm\Panel\Http\Response;
use Gm\Panel\Helper\ExtGrid;
use Gm\Panel\Helper\HtmlGrid;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Widget\TabGrid;
use Gm\Panel\Data\Model\FormModel;
use Gm\Panel\Controller\GridController;
use Gm\Panel\Helper\HtmlNavigator as HtmlNav;

/**
 *  Контроллер списка служб.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Config\Services\Controller
 * @since 1.0
 */
class Grid extends GridController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Backend\Config\Services\Extension
     */
    public BaseModule $module;

     /**
     * {@inheritdoc}
     */
    public function translateAction(mixed $params, string $default = null): ?string
    {
        switch ($this->actionName) {
            // изменение записи по указанному идентификатору
            case 'update':
                /** @var FormModel $model */
                $model = $this->lastDataModel;
                if ($model instanceof FormModel) {
                    $bb = $model->bootstrapBackend; // автозагрузка слжбы на backend
                    $bf = $model->bootstrapFrontend; // автозагрузка слжбы на frontend
                    if ($bb !== null && $bf !== null) {
                        if (!$bb && !$bf) {
                            return $this->module->t('removing service {0} from startup', [$model->getIdentifier()]);
                        } elseif ($bb && $bf) {
                            return $this->module->t('adding service {0} to startup', [$model->getIdentifier()]);
                        }
                    }
                }

            default:
                return parent::translateAction($params, $default);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabGrid
    {
        /** @var TabGrid $tab Сетка данных (Gm.view.grid.Grid GmJS) */
        $tab = parent::createWidget();

        // столбцы (Gm.view.grid.Grid.columns GmJS)
        $tab->grid->columns = [
            ExtGrid::columnNumberer(),
            [
                'text'    => ExtGrid::columnInfoIcon($this->t('Name')),
                'cellTip' => HtmlGrid::tags([
                      HtmlGrid::header('header'),
                      HtmlGrid::fieldLabel($this->t('Class'), '{className}'),
                ]),
                'dataIndex' => 'name',
                'filter'    => ['type' => 'string'],
                'width'     => 200
            ],
            [
                'text'      => '#Class',
                'dataIndex' => 'className',
                'cellTip'   => '{className}',
                'filter'    => ['type' => 'string'],
                'width'     => 220
            ],
            [
                'text'        => ExtGrid::columnIcon('g-icon-m_frontend', 'svg'),
                'tooltip'     => '#The service is in the startup for Frontend',
                'xtype'       => 'g-gridcolumn-switch',
                'dataIndex'   => 'bootstrapFrontend',
                'filter'    => ['type' => 'boolean'],
                'collectData' => ['bootstrapBackend']
            ],
            [
                'text'        => ExtGrid::columnIcon('g-icon-m_backend', 'svg'),
                'tooltip'     => '#The service is in the startup for Backend',
                'xtype'       => 'g-gridcolumn-switch',
                'dataIndex'   => 'bootstrapBackend',
                'filter'    => ['type' => 'boolean'],
                'collectData' => ['bootstrapFrontend']
            ],
            [
                'xtype'     => 'g-gridcolumn-checker',
                'text'      => ExtGrid::columnIcon('gm-config-services__icon-config', 'svg'),
                'tooltip'   => '#The service has its own configurator',
                'align'     => 'center',
                'dataIndex' => 'ownerConfig',
                'filter'    => ['type' => 'boolean'],
                'width'     => 50
            ]
        ];

        // панель инструментов (Gm.view.grid.Grid.tbar GmJS)
        $tab->grid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'columns' => [
                    'items' => [
                        'refresh',
                        '-',
                        'cleanup' => [
                            'tooltip'    => $this->t('Remove services from startup'),
                            'msgConfirm' => $this->t('Do you really want to remove all services from startup?')
                        ],
                        '-',
                        'profiling',
                        'columns'
                    ]
                ],
                'search'
            ], [
                'route' =>  Gm::alias('@route')
            ])
        ];

        // контекстное меню записи (Gm.view.grid.Grid.popupMenu GmJS)
        $tab->grid->popupMenu = [];

        // 2-й клик по строке сетки
        $tab->grid->rowDblClickConfig = [
            'allow' => false
        ];
        // количество строк в сетке
        $tab->grid->store->pageSize = Gm::$app->services->config->getCount();
        // поле аудита записи
        $tab->grid->logField = 'index';
        // плагины сетки
        $tab->grid->plugins = 'gridfilters';
        // класс CSS применяемый к элементу body сетки
        $tab->grid->bodyCls = 'g-grid_background';
        // маршрутизация
        $tab->grid->router->route = $this->module->route('/grid');
        $tab->grid->router->rules = [
            'data'      => '{route}/data',
            'clear'     => '{route}/clear',
            'updateRow' => '{route}/update/{id}'
        ];
        // сортировка сетки по умолчанию
        $tab->grid->sorters = [ 
            ['property' => 'index', 'direction' => 'ASC']
         ];

        // панель навигации (Gm.view.navigator.Info GmJS)
        $tab->navigator->info['tpl'] = HtmlNav::tags([
            HtmlNav::header('{name}'),
            ['fieldset',
                [
                    HtmlNav::fieldLabel($this->t('Name'), '{name}'),
                    HtmlNav::fieldLabel(
                        $this->t('Visible'),
                        HtmlNav::tplChecked('visible==1')
                    )
                ]
            ]
        ]);

        $tab
            ->addCss('/grid.css')
            ->addRequire('Gm.view.grid.column.Switch');
        return $tab;
    }

    /**
     * {@inheritdoc}
     */
    public function clearAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        Gm::$app->services->resetBootstrap();
        // всплывающие сообщение
        $response
            ->meta
                ->cmdPopupMsg($this->t('All services removed from startup'), $this->t('Remove from startup'), 'accept');
        return $response;
    }
}
