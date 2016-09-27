<?php
namespace Projek\Slim\Database;

trait SoftDelete
{
    /**
     * @var bool
     */
    protected $destructive = false;

    protected function softDelete($terms)
    {
        if (false === $this->destructive) {
            return $this->update(['deleted_at' => $this->freshDate()], $terms);
        }
    }
}