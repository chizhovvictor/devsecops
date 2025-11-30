<?php

declare(strict_types=1);

namespace App\Model;

use App\Kernel\Attribute\LifecycleCallbacks;
use App\Kernel\Attribute\Table;
use App\Kernel\Model\Model;

#[Table(name: 'gallery', primaryKey: 'id')]
#[LifecycleCallbacks(createdCallbackMethod: 'createdCallbackMethod')]
class Gallery extends Model
{
    private ?int $id = null;

    private ?User $user = null;

    private string $file;

    private ?\DateTimeInterface $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Create callback.
     */
    public function createdCallbackMethod(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
