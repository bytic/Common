<?php

namespace ByTIC\Common\Records\Emails\Builder;

use Nip\Records\RecordManager;

/**
 * Class BuilderAwareTrait
 * @package ByTIC\Common\Records\Emails\Builder
 */
trait BuilderAwareTrait
{

    /**
     * @param $type
     * @return bool
     */
    public function createEmail($type)
    {
        $builder = $this->createEmailBuilder($type);
        return $builder->save();
    }

    /**
     * @param $type
     * @return ViewBuilder
     */
    public function createEmailBuilder($type)
    {
        $class = $this->generateEmailBuilderClass($type);
        /** @var ViewBuilder $builder */
        $builder = new $class();
        $builder->setItem($this);

        return $builder;
    }

    /**
     * @param $type
     * @return string
     */
    protected function generateEmailBuilderClass($type)
    {
        $base = $this->getManager()->getModelNamespace();
        return $base . 'EmailBuilder\\' . $this->generateEmailBuilderClassName($type);
    }

    /**
     * @return RecordManager
     */
    abstract public function getManager();

    /**
     * @param $type
     * @return mixed
     */
    protected function generateEmailBuilderClassName($type)
    {
        return inflector()->classify($type);
    }
}
