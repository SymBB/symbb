<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="forum_topic_post_surveys")
 * @ORM\Entity()
 */
class Survey
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Post")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", unique=true, onDelete="cascade")
     */
    private $post;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $question;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $answers;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    private $choices = 1;

    /**
     * @ORM\Column(type="boolean")
     */
    private $choicesChangeable = true;

    /**
     * @ORM\Column(type="timestamp")
     */
    private $end = 0;

    /**
     * @ORM\OneToMany(targetEntity="Symbb\Extension\SurveyBundle\Entity\Vote", mappedBy="survey")
     */
    private $votes;

    /**
     *
     */
    public function __construct()
    {
        $this->votes = new ArrayCollection();

    }
    ############################################################################
    # Default Get and Set
    ############################################################################

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;

    }

    /**
     * @param $value
     */
    public function setId($value)
    {
        $this->id = $value;

    }

    /**
     * @param $object
     */
    public function setPost($object)
    {
        $this->post = $object;

    }

    /**
     * 
     * @return \Symbb\Core\ForumBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;

    }

    /**
     * @param null $value
     */
    public function setEnd($value = null)
    {
        if($value instanceof \DateTime){
            $value = $value->getTimestamp();
        }
        $this->end = $value;
    }

    /**
     * 
     * @return int
     */
    public function getEnd()
    {
        return $this->end;

    }

    /**
     * @param $value
     */
    public function setChoicesChangeable($value)
    {
        $this->choicesChangeable = $value;

    }

    /**
     * @return bool
     */
    public function getChoicesChangeable()
    {
        return $this->choicesChangeable;

    }

    /**
     * @param $value
     */
    public function setChoices($value)
    {
        $this->choices = $value;

    }

    /**
     * @return int
     */
    public function getChoices()
    {
        return $this->choices;

    }

    /**
     * @param $value
     */
    public function setAnswers($value)
    {
        $this->answers = $value;

    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;

    }

    /**
     * @param $value
     */
    public function setQuestion($value)
    {
        $this->question = $value;

    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;

    }

    /**
     * @param $value
     */
    public function setVotes($value)
    {
        $this->votes = $value;

    }

    /**
     * @param Vote $value
     */
    public function addVote(Vote $value)
    {
        $this->votes->add($value);

    }

    /**
     * 
     * @return Vote
     */
    public function getVotes()
    {
        return $this->votes;

    }
    ############################################################################

    /**
     * @return array|mixed|string
     */
    public function getAnswersArray()
    {
        $answers = $this->getAnswers();
        $answers = nl2br($answers);
        $answers = explode('<br />', $answers);
        return $answers;

    }

    /**
     * @param $number
     * @return float|int
     */
    public function getAnswerPercent($number)
    {
        $votes = $this->getVotes();
        $max = count($votes);
        $percent = 0;
        if ($max > 0) {
            $current = 0;
            foreach ($votes as $vote) {
                if ($vote->getAnswer() == $number) {
                    $current++;
                }
            }
            $percent = ($current * 100 / $max);
            $percent = round($percent);
        }

        return $percent;

    }

    /**
     * @param $answerKey
     * @param \Symbb\Core\UserBundle\Entity\UserInterface $user
     * @return bool
     */
    public function checkForVote($answerKey, \Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        $votes = $this->getVotes();
        foreach ($votes as $vote) {
            if (
                $vote->getUser() &&
                $vote->getUser()->getId() == $user->getId() &&
                $vote->getAnswer() == $answerKey
            ) {
                return true;
            }
        }
        return false;

    }

    /**
     * @param \Symbb\Core\UserBundle\Entity\UserInterface $user
     * @return bool
     */
    public function checkIfVoteable(\Symbb\Core\UserBundle\Entity\UserInterface $user)
    {

        $end = $this->getEnd();
        $now = new \DateTime();
        $now = $now->getTimestamp();

        // if the time is over, no new Votes are allowed
        if ($end && $now > $end) {
            return false;
        }

        // if the user can change the vote
        if ($this->choicesChangeable) {
            return true;
        }

        $votes = $this->getVotes();
        $count = 0;

        foreach ($votes as $vote) {
            if (
                $vote->getUser() &&
                $vote->getUser()->getId() == $user->getId()
            ) {
                $count++;
            }
        }

        // or if the user haven not used all choices
        if ($count < $this->getChoices()) {
            return true;
        }

        return false;

    }
}