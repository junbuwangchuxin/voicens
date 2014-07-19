<?php

namespace Slackiss\Bundle\VoicensBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @ORM\Table(name="voicens_member")
 * @UniqueEntity(
 *     fields={"username", "email"},
 *     message="用户名和电子信箱不能重复"
 * )
 */
class Member extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->created = new \DateTime();
        $this->modified = $this->created;
        $this->enabled = true;
        $this->status = true;
        $this->remark = "";
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetimetz")
     */
    protected $created;

    /**
     *
     * @ORM\Column(name="modified", type="datetimetz")
     */
    protected $modified;

    /**
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

    /**
     * @ORM\Column(name="remark", type="text",nullable=true)
     */
    protected $remark;

    /**
     * @Assert\NotBlank(message="用户名不可为空")
     * @Assert\Length(
     *     min="4",
     *     max="36",
     *     minMessage="用户名不能少于4个字符",
     *     maxMessage="用户名不能多于36个字符"
     * )
     * @Assert\Regex(
     *    pattern="/^[A-z0-9]*$/i",
     *    message="用户名只能使用英文字母和数字"
     * )
     */
    protected $username;

    /**
     *
     * @Assert\Length(
     *     min="6",
     *     minMessage="密码不能低于6个字符"
     * )
     *
     */
    protected $password;

    /**
     * @Assert\Email(
     *    checkMX=true,
     *    message="请使用合法的电子信箱"
     * )
     */
    protected $email;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Member
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Member
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
   {
        return $this->modified;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Member
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Member
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set remark
     *
     * @param string $remark
     * @return Member
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

}
