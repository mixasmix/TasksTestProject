<?php

namespace App\Entity;

use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task implements JsonSerializable
{
    /**
     * @var string
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[ORM\Column(
        type: 'uuid',
        unique: true,
    )]
    private string $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'tasks')]
    private Collection $tags;

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    private User $author;

    /**
     * @var TaskStatus
     */
    #[ORM\Column(type: 'task_status_type')]
    private TaskStatus $status;

    /**
     * @var TaskPriority
     */
    #[ORM\Column(type: 'task_priority_type')]
    private TaskPriority $priority;

    /**
     * @param string       $name
     * @param Collection   $tags
     * @param User         $author
     * @param TaskStatus   $status
     * @param TaskPriority $priority
     */
    public function __construct(
        string $name,
        Collection $tags,
        User $author,
        TaskStatus $status,
        TaskPriority $priority,
    ) {
        $this->name = $name;
        $this->tags = $tags;
        $this->author = $author;
        $this->status = $status;
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @return TaskPriority
     */
    public function getPriority(): TaskPriority
    {
        return $this->priority;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return TaskStatus
     */
    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'tags' => $this->getTags()->toArray(),
            'author' => $this->getAuthor(),
            'status' => $this->getStatus()->getValue(),
            'priority' => $this->getPriority()->getValue(),
        ];
    }
}
