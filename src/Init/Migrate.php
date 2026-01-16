<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

class Migrate
{
    /**
     * Model to Init
     */
    private object $model;

    public function __construct(string $model)
    {
        $class = "\\LBM\\Init\\Model\\{$model}";

        if (!\class_exists($class)) {
            throw new \InvalidArgumentException("Unable to Initiate Model Migration: [{$class}]", 0);
        }

        $this->model = new $class();
    }

    /**
     * Initiate Model
     */
    public function migrate()
    {
        try {
            $this->model->init();
        } catch (\Throwable $th) {
            throw new \RuntimeException("Unable to Initiate Database Migrate Method: [{$th->getMessage()}]", (int) $th->getCode(), $th);
        }
    }
}