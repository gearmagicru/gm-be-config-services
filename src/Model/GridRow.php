<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Config\Services\Model;

use Gm;
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель данных профиля службы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Config\Services\Model
 * @since 1.0
 */
class GridRow extends FormModel
{
    /**
     * Имя службы.
     * 
     * @see ControlRow::afterValidate()
     * 
     * @var null|string
     */
    protected ?string $serviceName = null;

   /**
     * {@inheritdoc}
     */
    public function getIdentifier(): mixed
    {
        return Gm::$app->router->get('id');
    }

    /**
     * {@inheritdoc}
     */
    public function get(mixed $identifier = null): ?static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                if ($message['success']) {
                    if (!$this->bootstrapBackend && !$this->bootstrapFrontend) {
                        $message['title']   = $this->module->t('Remove from startup');
                        $message['message'] = $this->module->t('Service "{0}" successfully removed from startup', [$this->serviceName]);
                    } else {
                        $message['title'] = $this->module->t('Add to startup');
                        if ($this->bootstrapBackend && $this->bootstrapFrontend) {
                            $message['message'] = $this->module->t('Service "{0}" successfully added to startup for Frontend and Backend', [$this->serviceName]);
                        } else {
                            if ($this->bootstrapBackend)
                                $message['message'] = $this->module->t('Service "{0}" successfully added to startup for Backend, but remove for Frontend', [$this->serviceName]);
                            else
                                $message['message'] = $this->module->t('Service "{0}" successfully added to startup for Frontend, but remove for Backend', [$this->serviceName]);
                        }
                    }
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'bootstrapBackend'  => 'bootstrapBackend',
            'bootstrapFrontend' => 'bootstrapFrontend'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate(array &$attributes): bool
    {
        $this->bootstrapBackend  = (int) $this->bootstrapBackend;
        $this->bootstrapFrontend = (int) $this->bootstrapFrontend;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            /** @var array $services Все службы */
            $services = Gm::$app->services->config->getAll();
            /** @var string|null $service Идентификатор службы */
            $service = $this->getIdentifier();

            // проверка службы
            if (empty($service)) {
                $this->setError($this->module->t('Unknown service name'));
                return false;    
            }
            if (!isset($services[$service])) {
                $this->setError($this->module->t('Unknown service name'));
                return false;    
            }
            $this->serviceName = $service;
        }
        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function save(bool $useValidation = false, array $attributeNames = null): bool|int|string
    {
        if (!$this->beforeSave(false)) {
            return false;
        }
        Gm::$app->services->setBootstrap(
            $this->serviceName,
            [BACKEND => $this->bootstrapBackend, FRONTEND => $this->bootstrapFrontend]
        );
        $this->afterSave(
            false,
            ['bootstrapBackend' => $this->bootstrapBackend, 'bootstrapFrontend' => $this->bootstrapFrontend],
            true
        );
        return true;
    }
}
