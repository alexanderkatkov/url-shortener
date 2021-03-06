<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'urls')]
#[ORM\Entity(repositoryClass: UrlRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'delete'],
    attributes: [
        'normalization_context' => ['groups' => ['url:read']],
        'denormalization_context' => ['groups' => ['url:write']],
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['shortcode' => 'exact'])]
class Url
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    /**
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Uuid()
     * })
     */
    #[Groups(["url:read"])]
    private Uuid $id;

    #[ORM\Column(name: 'url', type: 'string', length: 255)]
    /**
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Url(
     *         protocols = {"http", "https"},
     *         relativeProtocol = false,
     *         normalizer = "trim"
     *     )
     * })
     */
    #[Groups(["url:read", "url:write"])]
    private ?string $url;

    #[ORM\Column(name: 'shortcode', type: 'string', length: 8)]
    #[Groups(["url:read"])]
    private ?string $shortcode;

    #[ORM\Column(name: 'redirect_count', type: 'integer', options: ['default' => 0])]
    #[Groups(["url:read"])]
    private int $redirectCount = 0;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getShortcode(): ?string
    {
        return $this->shortcode;
    }

    public function setRedirectCount(int $count): self
    {
        $this->redirectCount = $count;

        return $this;
    }

    public function getRedirectCount(): int
    {
        return $this->redirectCount;
    }

    public function increaseRedirectCount(): self
    {
        $this->redirectCount++;

        return $this;
    }

    #[ORM\PrePersist()]
    public function generateShortcode(): void
    {
        $this->shortcode = hash('adler32', $this->url);
    }
}
