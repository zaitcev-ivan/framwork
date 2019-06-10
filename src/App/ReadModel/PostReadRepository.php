<?php

namespace App\ReadModel;

use App\ReadModel\Views\PostView;

class PostReadRepository
{
    private $posts;

    /**
     * PostReadRepository constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->posts = [
            new PostView(1, new \DateTimeImmutable(), 'The First Post', 'The First Post Content'),
            new PostView(2, new \DateTimeImmutable(), 'The Second Post', 'The Second Post Content'),
        ];
    }

    /**
     * @return PostView[]
     */
    public function getAll(): array
    {
        return array_reverse($this->posts);
    }

    /**
     * @param $id
     * @return PostView|null
     */
    public function find($id): ?PostView
    {
        foreach ($this->posts as $post) {
            if ($post->id === (int)$id) {
                return $post;
            }
        }
        return null;
    }
}