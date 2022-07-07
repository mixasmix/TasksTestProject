<?php

namespace App\Entity;

use App\Repository\TagRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag implements JsonSerializable
{
    /**
     * @var string
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(
        type: Types::GUID,
        unique: true,
    )]
    private string $id;

    /**
     * @var string
     */
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    /**
     * @var User
     */
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $author;

    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'tags')]
    #[ORM\JoinTable(name: 'task_tag')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    private Collection $tasks;

    /**
     * @param string $name
     * @param User   $author
     * @param array  $tasks
     */
    public function __construct(string $name, User $author, array $tasks = [])
    {
        $this->name = $name;
        $this->author = $author;
        $this->createdAt = new DateTimeImmutable();
        $this->tasks = new ArrayCollection(array_unique($tasks, SORT_REGULAR));
    }

    public function addTask(Task $task): self
    {
        if (!$this->getTasks()->contains($task)) {
            $this->getTasks()->add($task);
        }

        return $this;
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
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param Task $task
     *
     * @return Tag
     */
    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'author' => $this->getAuthor(),
            'created_at' => $this->getCreatedAt()->format(DATE_ATOM),
        ];
    }
}
