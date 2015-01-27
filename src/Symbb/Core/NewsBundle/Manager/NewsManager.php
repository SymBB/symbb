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
use Ddeboer\Imap\Server;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Email\To;
use Ddeboer\Imap\Search\Text\Body;
use Ddeboer\Imap\Search\Date\After;

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
                    $server = new Server($source->getServer());
                    // $connection is instance of \Ddeboer\Imap\Connection
                    $connection = $server->authenticate($source->getUsername(), $source->getPassword());
                    $mailboxes = $connection->getMailboxes();
                    foreach ($mailboxes as $mailbox) {
                        $search = new SearchExpression();
                        $search->addCondition(new After($date->format("Y-m-d")));
                        $messages = $mailbox->getMessages($search);
                        foreach ($messages as $message) {
                            $objects[] = array(
                                'email_id' => $message->getId(),
                                'title' => $message->getSubject(),
                                'text' => $message->getBodyHtml(),
                                'type' => "email",
                                'category' => $category->getId(),
                                'source' => $source->getId()
                            );
                        }
                    }
                } else if($source instanceof Category\Source\Feed){
                    $reader = $this->feedReader;
                    $feed = $reader->getFeedContent($source->getUrl(), $date);
                    $items = $feed->getItems();
                    foreach($items as $item){
                        $objects[] = array(
                            'feed_id' => null,
                            'title' => $item->getTitle(),
                            'text' => $item->getDescription(),
                            'type' => "feed",
                            'category' => $category->getId(),
                            'source' => $source->getId()
                        );
                    }
                }
            }
        }


        return $objects;
    }

}
