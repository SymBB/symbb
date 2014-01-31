<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\Entity\Post;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as BaseFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="forum_topic_post_files")
 * @ORM\Entity()
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class File
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $changed;

    /**
     * @ORM\ManyToOne(targetEntity="SymBB\Core\ForumBundle\Entity\Post", inversedBy="files")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="cascade", nullable=false)
     */
    private $post;

    /**
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="symbb_post_file", fileNameProperty="imageName")
     *
     * @var BaseFile $image
     */
    protected $image;

    /**
     * @ORM\Column(type="string", length=255, name="image_name", nullable=true)
     *
     * @var string $imageName
     */
    protected $imageName;

    public function getId()
    {
        return $this->getId();

    }

    public function getImage()
    {
        return $this->image;

    }

    public function setImage($image)
    {
        $this->image = $image;
        if ($this->image) {
            $this->changed = new \DateTime();
        }

    }

    public function getImageName()
    {
        return $this->imageName;

    }

    public function setImageName($name)
    {
        $this->imageName = $name;

    }

    public function setPost(\SymBB\Core\ForumBundle\Entity\Post $object)
    {
        $this->post = $object;

    }

    /**
     * @return \SymBB\Core\ForumBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;

    }
}