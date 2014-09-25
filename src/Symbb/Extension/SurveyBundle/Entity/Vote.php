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

/**
 * @ORM\Table(name="forum_topic_post_survey_votes")
 * @ORM\Entity()
 */
class Vote
{
    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="SymBB\Extension\SurveyBundle\Entity\Survey", inversedBy="votes")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="cascade")
     */
    private $survey;

    /**
     * @ORM\ManyToOne(targetEntity="SymBB\Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    private $answer;
    
    
    ############################################################################
    # Default Get and Set
    ############################################################################
    public function getId(){return $this->id;}
    public function setId($value){$this->id = $value;}
    public function setSurvey(Survey $value){$this->survey = $value;}
    public function getSurvey(){return $this->survey;}
    public function setUser(\SymBB\Core\UserBundle\Entity\User $value){$this->user = $value;}
    public function getUser(){return $this->user;}
    public function setAnswer($value){$this->answer = $value;}
    public function getAnswer(){return (int)$this->answer;}
    ############################################################################
 
}