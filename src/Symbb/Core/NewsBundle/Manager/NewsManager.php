<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Manager;

use Symbb\Core\NewsBundle\Entity\Category;
use Symbb\Core\SystemBundle\Manager\AbstractManager;
use Ddeboer\Imap\Connection;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Email\To;
use Ddeboer\Imap\Search\Text\Body;
use Ddeboer\Imap\Search\Date\After;
use Ddeboer\Imap\Search\State\Undeleted;

use Symfony\Component\Security\Acl\Exception\Exception;
use Ddeboer\Imap\Exception\AuthenticationFailedException;

class NewsManager extends AbstractManager
{

    protected $feedReader;

    public function setFeedReader($reader){
        $this->feedReader = $reader;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findAll($page = 1, $limit = 20)
    {
        $qb = $this->em->getRepository('SymbbCoreNewsBundle:Category\Entry')->createQueryBuilder('s');
        $qb->select("s");
        $qb->addOrderBy("s.created", "desc");
        $query = $qb->getQuery();
        $objects = $this->createPagination($query, $page, $limit);
        return $objects;
    }

    public function collectNews()
    {
        $objects = array();

        $categories = $this->em->getRepository('SymbbCoreNewsBundle:Category')->findAll();

        /**
         * @var $categories Category[]
         */
        foreach($categories as $category){
            $sources = $category->getSources();
            foreach($sources as $source){
                $date = new \DateTime();
                if($source->getLastCall()){
                    $date->setTimestamp($source->getLastCall());
                } else {
                    $date->modify("- 2 weeks");
                }
                if($source instanceof Category\Source\Email){
                    // $connection is instance of \Ddeboer\Imap\Connection
                    try {
                        $connection = $this->getEmailConnection($source);
                        $category = $source->getCategory();
                        $mailboxes = $connection->getMailboxes();
                        foreach ($mailboxes as $mailbox) {
                            $search = new SearchExpression();
                            $search->addCondition(new After($date));
                            $search->addCondition(new Undeleted());
                            $messages = $mailbox->getMessages($search);
                            foreach ($messages as $message) {
                                $objects[] = array(
                                    'email_id' => $message->getId(),
                                    'title' => $message->getSubject(),
                                    'text' => $message->getBodyHtml(),
                                    'realSource' => $message->getFrom(),
                                    'date' => $message->getDate(),
                                    'type' => "email",
                                    'category' => array(
                                        "id" => $category->getId(),
                                        "name" => $category->getName()
                                    ),
                                    'source' => $source->getId()
                                );
                            }
                        }
                    } catch (\Exception $exp){
                        $objects[] = array(
                            'title' => "error while connection to email server",
                            "text" => $exp->getMessage(),
                            "date" => new \DateTime(),
                            'type' => "error",
                            'category' => array(
                                "id" => $category->getId(),
                                "name" => $category->getName()
                            ),
                            'source' => $source->getId()
                        );
                    }
                } else if($source instanceof Category\Source\Feed){
                    $reader = $this->feedReader;
                    $feed = $reader->getFeedContent($source->getUrl(), $date);
                    $items = $feed->getItems();
                    foreach($items as $item){
                        $objects[] = array(
                            'feed_id' => null,
                            'title' => (string)$item->getTitle(),
                            'text' => (string)$item->getDescription(),
                            'date' => $item->getUpdated(),
                            'realSource' => (string)$item->getLink(),
                            'type' => "feed",
                            'category' => array(
                                "id" => $category->getId(),
                                "name" => $category->getName()
                            ),
                            'source' => $source->getId()
                        );
                    }
                }
            }
        }

        usort($objects, function($a, $b){
            if ($a['date'] == $b['date']) {
                return 0;
            } else if($a['date'] >= $b['date']) {
                return -1;
            } else {
                return 1;
            }
        });

        return $objects;
    }

    protected function getEmailConnection(Category\Source\Email $source){

        if($source->isSsl()){
            $flags = "/imap/ssl/validate-cert";
        } else {
            if($source->getPort() == 143){
                $flags = "/imap/notls";
            } else {
                $flags = "/imap/ssl/novalidate-cert";
            }
        }

        $serverString = sprintf(
            '{%s:%s%s}INBOX',
            $source->getServer(),
            $source->getPort(),
            $flags
        );

        $resource = imap_open(
            $serverString,
            $source->getUsername(),
            $source->getPassword(),
            null,
            1,
            array()
        );

        if (false === $resource) {
            throw new AuthenticationFailedException($source->getUsername());
        }

        $check = imap_check($resource);
        $mailbox = $check->Mailbox;
        $connection = substr($mailbox, 0, strpos($mailbox, '}')+1);
        $connection = new Connection($resource, $connection);
        return $connection;
    }
}
