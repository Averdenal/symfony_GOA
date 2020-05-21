<?php


namespace App\Controller\api;


use App\Entity\Comment;
use Symfony\Component\Security\Core\Security;

class AddCommentApiController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
    }

    public function __invoke(Comment $data)
    {
        $data->setCreatedAt(new \DateTime("now"))
            ->setUser($this->security->getUser());
        return $data;
    }
}