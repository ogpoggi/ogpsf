<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="products")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="product")
     */
    private $reviews;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductTag", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tag;

    private $moyenne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="product")
     */
    private $images;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductLike", mappedBy="product")
     */
    private $productLikes;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug(){
        if(empty($this->slug)){
            $slugify =  new Slugify();
            $this->slug = $slugify->slugify($this->title);
        }
    }

    /**
     * @param User $user
     * @return mixed|null
     * recupér le comment d'un auteur par rapport a son annonce
     */
    public function getCommentFromAuthor(User $user){
        foreach ($this->reviews as $review){
            if ($review->getAuthor() == $user) return $review;
        }
        return null;
    }

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->productLikes = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }

    public function getTag(): ?ProductTag
    {
        return $this->tag;
    }

    public function setTag(?ProductTag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getMoyenne(){
        $notes = 0;
        $compteur = 0;
        $reviews = $this->getReviews();
        foreach ($reviews as $review){
            $notes+=$review->getRating();
            $compteur++;
        }
        $moyenne = $notes/$compteur;
        return $moyenne;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|ProductLike[]
     */
    public function getProductLikes(): Collection
    {
        return $this->productLikes;
    }

    public function addProductLike(ProductLike $productLike): self
    {
        if (!$this->productLikes->contains($productLike)) {
            $this->productLikes[] = $productLike;
            $productLike->setProduct($this);
        }

        return $this;
    }

    public function removeProductLike(ProductLike $productLike): self
    {
        if ($this->productLikes->contains($productLike)) {
            $this->productLikes->removeElement($productLike);
            // set the owning side to null (unless already changed)
            if ($productLike->getProduct() === $this) {
                $productLike->setProduct(null);
            }
        }

        return $this;
    }

    public function isLikedByUser(User $user): bool {
        foreach ( $this->productLikes as $like) {
            if ($like->getUser() === $user) return true;
        }
        return false;
    }
}
