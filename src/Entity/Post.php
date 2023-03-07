<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * 
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"posts", "users"})
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"posts", "users"})
     * @Assert\NotBlank
     * @Assert\Length(min = 1, max = 255)
     * @Assert\Type("string")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"posts", "users"})
     * @Assert\NotBlank
     * @Assert\Length(min = 100, max = 500)
     * @Assert\Type("string")
     */
    private $content;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"posts"})
     * @Assert\Type("integer")
     * @Assert\Range(min = 0, max = 500)
     */
    private $hourlyRate;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"posts"})
     * 
     */
    private $workType;

    /**
     * @ORM\Column(type="string", length=6)
     * @Groups({"posts"})
     * @Assert\NotBlank
     * @Assert\Length(min = 5, max = 5)
     * @Assert\Type("string")
     */
    private $postalCode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"posts"})
     * @Assert\Range(min = 0, max = 1000)
     * @Assert\Type("integer")
     */
    private $radius;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"posts"})
     */
    private $slug;
    

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"posts"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"posts"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"posts"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="posts")
     * @Groups({"posts"})
     * @Assert\NotBlank
     * 
     */
    private $tag;
   
    //function to sluggify the title and set it.
    public function sluggify(string $string): string
    {
      // Strip_tags is used to remove HTML and PHP tags.
      // Even tho pre_replace would remove chevrons, it won't remove the content of the tags
      // It does not delete the container content, and this content may contain malicious code.
      // This is the purpose of strip_tags.
      return preg_replace(
        ['/[^a-z0-9-]+/', '/-+/'],
        '-',
        strtolower(trim(strip_tags(str_replace('.', '', $string))))
      );
    }

    public function __construct()
    {
        $this->tag = new ArrayCollection();
       // $this->slug = $this->sluggify($this->getTitle());
        $this->createdAt = new \DateTime('now');
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getHourlyRate(): ?int
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(int $hourlyRate): self
    {
        $this->hourlyRate = $hourlyRate;

        return $this;
    }

    public function isWorkType(): ?bool
    {
        return $this->workType;
    }

    public function setWorkType(bool $workType): self
    {
        $this->workType = $workType;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getRadius(): ?int
    {
        return $this->radius;
    }

    public function setRadius(?int $radius): self
    {
        $this->radius = $radius;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug = $this->sluggify($this->getTitle());;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
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

    /**
     * @return Collection<int, Tag>
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tag->contains($tag)) {
            $this->tag[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tag->removeElement($tag);

        return $this;
    }

   
    
}
