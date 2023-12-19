<?php
namespace App\Sourcing\Exception;

class EntityNotFoundException extends \Exception
{
    public function __construct(string $entity, string $id)
    {
        parent::__construct(sprintf('Entity "%s" with id "%s" not found', $entity, $id));
    }
}