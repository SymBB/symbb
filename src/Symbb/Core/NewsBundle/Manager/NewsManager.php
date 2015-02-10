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

use Symbb\Core\SystemBundle\Utils;
use Symfony\Component\Security\Acl\Exception\Exception;
use Ddeboer\Imap\Exception\AuthenticationFailedException;

class NewsManager extends AbstractManager
{

    protected $feedReader;

    public function setFeedReader($reader){
        $this->feedReader = $reader;
    }

    public function find($id)
    {
        $post = $this->em->getRepository('SymbbCoreNewsBundle:Category\Entry')->find($id);
        return $post;
    }

    public function remove(Category\Entry $object)
    {
        $this->em->remove($object);
        $this->em->remove($object->getTopic());
        $this->em->flush();
        return true;
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
        $qb->where("s.topic IS NOT NULL");
        $qb->addOrderBy("s.date", "desc");
        $query = $qb->getQuery();
        $objects = $this->createPagination($query, $page, $limit);
        return $objects;
    }

    public function collectNews($page = 1, $limit = 20, &$errors)
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
                $source->setLastCall(new \DateTime());
                if($source instanceof Category\Source\Email){
                    // $connection is instance of \Ddeboer\Imap\Connection
                    try {
                        $connection = $this->getEmailConnection($source);
                        $category = $source->getCategory();
                        $mailboxes = $connection->getMailboxes();
                        foreach ($mailboxes as $mailbox) {
                            if(strpos($mailbox->getName(), "INBOX") === 0){
                                $search = new SearchExpression();
                                $search->addCondition(new After($date));
                                $search->addCondition(new Undeleted());
                                $messages = $mailbox->getMessages($search);
                                foreach ($messages as $message) {
                                    // imap filter will only check the day not the time...
                                    if($message->getDate() <= $date){
                                        continue;
                                    }
                                    $text = $message->getBodyHtml();
                                    if(empty($text)){
                                        $text = $message->getBodyText();
                                    }
                                    $text = Utils::purifyHtml($text);
                                    $objects[] = array(
                                        'email_id' => $message->getId(),
                                        'title' => $message->getSubject(),
                                        'text' => $text,
                                        'realSource' => $message->getFrom(),
                                        'date' => $message->getDate(),
                                        'type' => "email",
                                        'category' => $category,
                                        'source' => $source
                                    );
                                }
                            }
                        }
                    } catch (\Exception $exp){
                        $errors[] = array(
                            'title' => "error while connection to email server",
                            "text" => $exp->getMessage(),
                            "date" => new \DateTime()
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
                            'category' => $category,
                            'source' => $source
                        );
                    }
                }
                $this->em->persist($source);
            }
        }

        foreach($objects as $object){
            $entry = new Category\Entry();
            $entry->setCategory($object["category"]);
            $entry->setSource($object["source"]);
            $entry->setType($object["type"]);
            $entry->setTitle($object["title"]);
            $entry->setText($object["text"]);
            $entry->setDate($object["date"]);
            $this->em->persist($entry);
        }

        $this->em->flush();

        $qb = $this->em->getRepository('SymbbCoreNewsBundle:Category\Entry')->createQueryBuilder('e');
        $qb->select("e");
        $qb->where("e.topic IS NULL");
        $qb->addOrderBy("e.date", "desc");
        $query = $qb->getQuery();
        $objects = $this->createPagination($query, $page, $limit);

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
