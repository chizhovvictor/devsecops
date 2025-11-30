<?php

declare(strict_types=1);

namespace App\DataTransform\Comment;

use App\DTO\Comment as CommentDTO;
use App\Kernel\Component\Request;
use App\Kernel\Contract\DataTransform;
use App\Model\Comment;

class ModelToDTO implements DataTransform
{
    /**
     * @param Comment $object
     * @param Request $request
     * @param array $context
     * @return CommentDTO
     */
    public function transform($object, Request $request, array $context = []): object
    {
        $output = new CommentDTO();
        $output->setUser($object->getUser());
        $output->setId($object->getId());
        $output->setMessage($object->getMessage());
        $output->setCreatedAt($object->getCreatedAt());

        return $output;
    }
}
