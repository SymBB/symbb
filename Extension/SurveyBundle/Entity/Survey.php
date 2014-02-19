<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\SurveyBundle\Entity;

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
     * @ORM\ManyToOne(targetEntity="SymBB\Core\ForumBundle\Entity\Post")
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end;

    /**
     * @ORM\OneToMany(targetEntity="SymBB\Extension\SurveyBundle\Entity\Vote", mappedBy="survey")
     */
    private $votes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();

    }
    ############################################################################
    # Default Get and Set
    ############################################################################

    public function getId()
    {
        return $this->id;

    }

    public function setId($value)
    {
        $this->id = $value;

    }

    public function setPost($object)
    {
        $this->post = $object;

    }

    /**
     * 
     * @return \SymBB\Core\ForumBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;

    }

    public function setEnd(\DateTime $value = null)
    {
        $this->end = $value;

    }

    /**
     * 
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;

    }

    public function setChoicesChangeable($value)
    {
        $this->choicesChangeable = $value;

    }

    public function getChoicesChangeable()
    {
        return $this->choicesChangeable;

    }

    public function setChoices($value)
    {
        $this->choices = $value;

    }

    public function getChoices()
    {
        return $this->choices;

    }

    public function setAnswers($value)
    {
        $this->answers = $value;

    }

    public function getAnswers()
    {
        return $this->answers;

    }

    public function setQuestion($value)
    {
        $this->question = $value;

    }

    public function getQuestion()
    {
        return $this->question;

    }

    public function setVotes($value)
    {
        $this->votes = $value;

    }

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

    public function getAnswersArray()
    {
        $answers = $this->getAnswers();
        $answers = nl2br($answers);
        $answers = explode('<br />', $answers);
        return $answers;

    }

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

    public function checkForVote($answerKey, \SymBB\Core\UserBundle\Entity\UserInterface $user)
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

    public function checkIfVoteable(\SymBB\Core\UserBundle\Entity\UserInterface $user)
    {

        $end = $this->getEnd();
        $now = new \DateTime();

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