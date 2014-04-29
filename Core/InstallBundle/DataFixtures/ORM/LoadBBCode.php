<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\InstallBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadBBCode extends AbstractFixture
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        
        $setDefault = new \SymBB\Core\BBCodeBundle\Entity\Set();
        $setDefault->setName("Default");
        
        $setSignature = new \SymBB\Core\BBCodeBundle\Entity\Set();
        $setSignature->setName("Signature");
        
        $pos = 0;
        $bbcodeB = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeB->setName('Bold');
        $bbcodeB->setSearchRegex('#\[b\](.+)\[\/b\]#iUs');
        $bbcodeB->setReplaceRegex('<b>$1</b>');
        $bbcodeB->setButtonRegex('[b]{0}[/b]');
        $bbcodeB->setImage('/bundles/symbbcorebbcode/images/text_bold.png');
        $bbcodeB->setPosition($pos);
        $manager->persist($bbcodeB);
        $pos++;
        
        $bbcodeU = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeU->setName('U');
        $bbcodeU->setSearchRegex('#\[u\](.+)\[\/u\]#iUs');
        $bbcodeU->setReplaceRegex('<u>$1</u>');
        $bbcodeU->setButtonRegex('[u]{0}[/u]');
        $bbcodeU->setImage('/bundles/symbbcorebbcode/images/text_underline.png');
        $bbcodeU->setPosition($pos);
        $manager->persist($bbcodeU);
        $pos++;
        
        $bbcodeI = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeI->setName('I');
        $bbcodeI->setSearchRegex('#\[i\](.+)\[\/i\]#iUs');
        $bbcodeI->setReplaceRegex('<i>$1</i>');
        $bbcodeI->setButtonRegex('[i]{0}[/i]');
        $bbcodeI->setImage('/bundles/symbbcorebbcode/images/text_italic.png');
        $bbcodeI->setPosition($pos);
        $manager->persist($bbcodeI);
        $pos++;
        
        $bbcodeH1 = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeH1->setName('H1');
        $bbcodeH1->setSearchRegex('#\[h1\](.+)\[\/h1\]#iUs');
        $bbcodeH1->setReplaceRegex('<h1>$1</h1>');
        $bbcodeH1->setButtonRegex('[h1]{0}[/h1]');
        $bbcodeH1->setImage('/bundles/symbbcorebbcode/images/text_heading_1.png');
        $bbcodeH1->setPosition($pos);
        $manager->persist($bbcodeH1);
        $pos++;
        
        $bbcodeH2 = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeH2->setName('H2');
        $bbcodeH2->setSearchRegex('#\[h1\](.+)\[\/h1\]#iUs');
        $bbcodeH2->setReplaceRegex('<h1>$1</h1>');
        $bbcodeH2->setButtonRegex('[h1]{0}[/h1]');
        $bbcodeH2->setImage('/bundles/symbbcorebbcode/images/text_heading_1.png');
        $bbcodeH2->setPosition($pos);
        $manager->persist($bbcodeH2);
        $pos++;
        
        $bbcodeHr = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeHr->setName('Hr');
        $bbcodeHr->setSearchRegex('#\[hr\]#iUs');
        $bbcodeHr->setReplaceRegex('<hr>');
        $bbcodeHr->setButtonRegex('{0}[hr]');
        $bbcodeHr->setImage('/bundles/symbbcorebbcode/images/text_horizontalrule.png');
        $bbcodeHr->setPosition($pos);
        $manager->persist($bbcodeHr);
        $pos++;
        
        $bbcodeLink = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeLink->setName('Link');
        $bbcodeLink->setSearchRegex('#\[link(?|=[\'"]?+([^]"\']++)[\'"]?+]([^[]++)|](([^[]++)))\[/link]#iUs');
        $bbcodeLink->setReplaceRegex('<a href="$1">$2</a>');
        $bbcodeLink->setButtonRegex('[link]{0}[/link]');
        $bbcodeLink->setImage('/bundles/symbbcorebbcode/images/link_add.png');
        $bbcodeLink->setPosition($pos);
        $manager->persist($bbcodeLink);
        $pos++;
        
        $bbcodeImage = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeImage->setName('Image');
        $bbcodeImage->setSearchRegex('#\[img\](.+)\[\/img\]#iUs');
        $bbcodeImage->setReplaceRegex('<img src="$1" />');
        $bbcodeImage->setButtonRegex('[img]{0}[/img]');
        $bbcodeImage->setImage('/bundles/symbbcorebbcode/images/image_add.png');
        $bbcodeImage->setPosition($pos);
        $manager->persist($bbcodeImage);
        $pos++;
        
        $bbcodeQuote = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeQuote->setName('Quote');
        $bbcodeQuote->setSearchRegex('#\[quote\](.+)\[\/quote\]#iUs');
        $bbcodeQuote->setReplaceRegex('<blockquote>$1</blockquote>');
        $bbcodeQuote->setButtonRegex('[quote]{0}[/quote]');
        $bbcodeQuote->setImage('/bundles/symbbcorebbcode/images/comment.png');
        $bbcodeQuote->setPosition($pos);
        $manager->persist($bbcodeQuote);
        $pos++;
        
        $bbcodeList = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeList->setName('List');
        $bbcodeList->setSearchRegex('#\[list\](.+)\[\/list\]#iUs');
        $bbcodeList->setReplaceRegex('<ul>$1</ul>');
        $bbcodeList->setButtonRegex('[list]{0}[/list]');
        $bbcodeList->setImage('/bundles/symbbcorebbcode/images/text_list_bullets.png');
        $bbcodeList->setPosition($pos);
        $manager->persist($bbcodeList);
        $pos++;
        
        $bbcodeListItem = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeListItem->setName('List Item');
        $bbcodeListItem->setSearchRegex('#\[\*\](.+?)#iU');
        $bbcodeListItem->setReplaceRegex('<li>$1</li>');
        $bbcodeListItem->setButtonRegex('[*]{0}');
        $bbcodeListItem->setImage('/bundles/symbbcorebbcode/images/list-item.png');
        $bbcodeListItem->setPosition($pos);
        $manager->persist($bbcodeListItem);
        $pos++;
        
        
        $setDefault->addCode($bbcodeB);
        $setDefault->addCode($bbcodeU);
        $setDefault->addCode($bbcodeI);
        $setDefault->addCode($bbcodeH1);
        $setDefault->addCode($bbcodeH2);
        $setDefault->addCode($bbcodeHr);
        $setDefault->addCode($bbcodeLink);
        $setDefault->addCode($bbcodeImage);
        $setDefault->addCode($bbcodeQuote);
        $setDefault->addCode($bbcodeList);
        $setDefault->addCode($bbcodeListItem);
        
        $setSignature->addCode($bbcodeB);
        $setSignature->addCode($bbcodeU);
        $setSignature->addCode($bbcodeI);
        $setSignature->addCode($bbcodeH1);
        $setSignature->addCode($bbcodeH2);
        $setSignature->addCode($bbcodeHr);
        $setSignature->addCode($bbcodeLink);
        $setSignature->addCode($bbcodeImage);
        $setSignature->addCode($bbcodeQuote);
        $setSignature->addCode($bbcodeList);
        $setSignature->addCode($bbcodeListItem);
        
        $manager->persist($setDefault);
        $manager->persist($setSignature);
        
        $manager->flush();
        
    }
}