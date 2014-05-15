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
        $setDefault->setId('default');
        $setDefault->setName("Default");
        
        $setSignature = new \SymBB\Core\BBCodeBundle\Entity\Set();
        $setSignature->setId('signature');
        $setSignature->setName("Signature");
        
        $pos = 0;
        $bbcodeSize = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeSize->setName('Font Size');
        $bbcodeSize->setSearchRegex('#\[size=(.+)\]([\s\S]+)\[\/size\]#iUs');
        $bbcodeSize->setReplaceRegex('<span class="fontsize-$1">$2</span>');
        $bbcodeSize->setButtonRegex('[size={1}]{text}[/size]');
        $bbcodeSize->setImage('/bundles/symbbcorebbcode/images/font.png');
        $bbcodeSize->setPosition($pos);
        $bbcodeSize->setJsFunction('BBCodeEditor.prepareFontBtn');
        $manager->persist($bbcodeSize);
        $pos++;
        
        $bbcodeB = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeB->setName('Bold');
        $bbcodeB->setSearchRegex('#\[b\]([\s\S]+)\[\/b\]#iUs');
        $bbcodeB->setReplaceRegex('<b>$1</b>');
        $bbcodeB->setButtonRegex('[b]{text}[/b]');
        $bbcodeB->setImage('/bundles/symbbcorebbcode/images/text_bold.png');
        $bbcodeB->setPosition($pos);
        $manager->persist($bbcodeB);
        $pos++;
        
        $bbcodeU = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeU->setName('U');
        $bbcodeU->setSearchRegex('#\[u\]([\s\S]+)\[\/u\]#iUs');
        $bbcodeU->setReplaceRegex('<u>$1</u>');
        $bbcodeU->setButtonRegex('[u]{text}[/u]');
        $bbcodeU->setImage('/bundles/symbbcorebbcode/images/text_underline.png');
        $bbcodeU->setPosition($pos);
        $manager->persist($bbcodeU);
        $pos++;
        
        $bbcodeI = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeI->setName('I');
        $bbcodeI->setSearchRegex('#\[i\]([\s\S]+)\[\/i\]#iUs');
        $bbcodeI->setReplaceRegex('<i>$1</i>');
        $bbcodeI->setButtonRegex('[i]{text}[/i]');
        $bbcodeI->setImage('/bundles/symbbcorebbcode/images/text_italic.png');
        $bbcodeI->setPosition($pos);
        $manager->persist($bbcodeI);
        $pos++;
        
        $bbcodeH1 = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeH1->setName('H1');
        $bbcodeH1->setSearchRegex('#\[h([0-9]+)\](.+)\[\/h([0-9]+)\]#iUs');
        $bbcodeH1->setReplaceRegex('<h$1>$2</h$1>');
        $bbcodeH1->setButtonRegex('[h{1}]{text}[/h{1}]');
        $bbcodeH1->setImage('/bundles/symbbcorebbcode/images/text_heading_1.png');
        $bbcodeH1->setPosition($pos);
        $bbcodeH1->setJsFunction('BBCodeEditor.prepareHeaderBtn');
        $manager->persist($bbcodeH1);
        $pos++;
        
        $bbcodeHr = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeHr->setName('Hr');
        $bbcodeHr->setSearchRegex('#\[hr\]#iUs');
        $bbcodeHr->setReplaceRegex('<hr>');
        $bbcodeHr->setButtonRegex('{text}[hr]');
        $bbcodeHr->setImage('/bundles/symbbcorebbcode/images/text_horizontalrule.png');
        $bbcodeHr->setPosition($pos);
        $manager->persist($bbcodeHr);
        $pos++;
        
        $bbcodeLink = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeLink->setName('Link');
        $bbcodeLink->setSearchRegex('#\[link(?:=(.*)\](.*)|\]((.*)))\[\/link]#iUs');
        $bbcodeLink->setReplaceRegex('<a href="$1$3">$2$4</a>');
        $bbcodeLink->setButtonRegex('[link]{text}[/link]');
        $bbcodeLink->setImage('/bundles/symbbcorebbcode/images/link_add.png');
        $bbcodeLink->setPosition($pos);
        $bbcodeLink->setRemoveNewLines(true);
        $manager->persist($bbcodeLink);
        $pos++;
        
        $bbcodeImage = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeImage->setName('Image');
        $bbcodeImage->setSearchRegex('#\[img\](.+)\[\/img\]#iUs');
        $bbcodeImage->setReplaceRegex('<img src="$1" />');
        $bbcodeImage->setButtonRegex('[img]{text}[/img]');
        $bbcodeImage->setImage('/bundles/symbbcorebbcode/images/image_add.png');
        $bbcodeImage->setPosition($pos);
        $bbcodeImage->setRemoveNewLines(true);
        $manager->persist($bbcodeImage);
        $pos++;
        
        $bbcodeQuote = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeQuote->setName('Quote');
        $bbcodeQuote->setSearchRegex('#\[quote(?:=(.+)\]([\s\S]*)|\](([\s\S]*?)))\[\/quote]#');
        $bbcodeQuote->setReplaceRegex('<blockquote cite="$1" ><p>$2$4</p><footer><cite>$1</cite></footer></blockquote>');
        $bbcodeQuote->setButtonRegex('[quote=]{text}[/quote]');
        $bbcodeQuote->setImage('/bundles/symbbcorebbcode/images/comment.png');
        $bbcodeQuote->setPosition($pos);
        $manager->persist($bbcodeQuote);
        $pos++;
        
        $bbcodeList = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeList->setName('List');
        $bbcodeList->setSearchRegex('#\[list\]([\s\S]+)\[\/list\]#iUs');
        $bbcodeList->setReplaceRegex('<ul>$1</ul>');
        $bbcodeList->setButtonRegex('[list]{text}[/list]');
        $bbcodeList->setImage('/bundles/symbbcorebbcode/images/text_list_bullets.png');
        $bbcodeList->setPosition($pos);
        $manager->persist($bbcodeList);
        $pos++;
        
        $bbcodeListItem = new \SymBB\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeListItem->setName('List Item');
        $bbcodeListItem->setSearchRegex('#\[\*\](.*)#');
        $bbcodeListItem->setReplaceRegex('<li>$1</li>');
        $bbcodeListItem->setButtonRegex('[*] {text}');
        $bbcodeListItem->setImage('/bundles/symbbcorebbcode/images/list-item.png');
        $bbcodeListItem->setPosition($pos);
        $manager->persist($bbcodeListItem);
        $pos++;
        
        
        $setDefault->addCode($bbcodeSize);
        $setDefault->addCode($bbcodeB);
        $setDefault->addCode($bbcodeU);
        $setDefault->addCode($bbcodeI);
        $setDefault->addCode($bbcodeH1);
        $setDefault->addCode($bbcodeHr);
        $setDefault->addCode($bbcodeLink);
        $setDefault->addCode($bbcodeImage);
        $setDefault->addCode($bbcodeQuote);
        $setDefault->addCode($bbcodeList);
        $setDefault->addCode($bbcodeListItem);
        
        $setSignature->addCode($bbcodeB);
        $setSignature->addCode($bbcodeU);
        $setSignature->addCode($bbcodeI);
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