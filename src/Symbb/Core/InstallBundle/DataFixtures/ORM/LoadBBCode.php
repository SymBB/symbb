<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\InstallBundle\DataFixtures\ORM;

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

        $setDefault = new \Symbb\Core\BBCodeBundle\Entity\Set();
        $setDefault->setId('default');
        $setDefault->setName("Default");

        $setSignature = new \Symbb\Core\BBCodeBundle\Entity\Set();
        $setSignature->setId('signature');
        $setSignature->setName("Signature");

        $setPm = new \Symbb\Core\BBCodeBundle\Entity\Set();
        $setPm->setId('pm');
        $setPm->setName("Private Message");

        $pos = 0;
        $bbcodeSize = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeSize->setName('Font Size');
        $bbcodeSize->setSearchRegex('#\[size=(.+?)\]([\s\S]+?)\[\/size\]#');
        $bbcodeSize->setReplaceRegex('<span class="fontsize-$1">$2</span>');
        $bbcodeSize->setButtonRegex('[size={1}]{text}[/size]');
        $bbcodeSize->setImage('/bundles/symbbcorebbcode/images/font.png');
        $bbcodeSize->setPosition($pos);
        $bbcodeSize->setJsFunction('BBCodeEditor.prepareFontBtn');
        $manager->persist($bbcodeSize);
        $pos++;

        $bbcodeB = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeB->setName('Bold');
        $bbcodeB->setSearchRegex('#\[b\]([\s\S]+?)\[\/b\]#');
        $bbcodeB->setReplaceRegex('<b>$1</b>');
        $bbcodeB->setButtonRegex('[b]{text}[/b]');
        $bbcodeB->setImage('/bundles/symbbcorebbcode/images/text_bold.png');
        $bbcodeB->setPosition($pos);
        $manager->persist($bbcodeB);
        $pos++;

        $bbcodeU = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeU->setName('U');
        $bbcodeU->setSearchRegex('#\[u\]([\s\S]+?)\[\/u\]#');
        $bbcodeU->setReplaceRegex('<u>$1</u>');
        $bbcodeU->setButtonRegex('[u]{text}[/u]');
        $bbcodeU->setImage('/bundles/symbbcorebbcode/images/text_underline.png');
        $bbcodeU->setPosition($pos);
        $manager->persist($bbcodeU);
        $pos++;

        $bbcodeI = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeI->setName('I');
        $bbcodeI->setSearchRegex('#\[i\]([\s\S]+?)\[\/i\]#');
        $bbcodeI->setReplaceRegex('<i>$1</i>');
        $bbcodeI->setButtonRegex('[i]{text}[/i]');
        $bbcodeI->setImage('/bundles/symbbcorebbcode/images/text_italic.png');
        $bbcodeI->setPosition($pos);
        $manager->persist($bbcodeI);
        $pos++;

        $bbcodeH1 = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeH1->setName('H1');
        $bbcodeH1->setSearchRegex('#\[h([0-9]+)\](.+?)\[\/h([0-9]+)\]#');
        $bbcodeH1->setReplaceRegex('<h$1>$2</h$1>');
        $bbcodeH1->setButtonRegex('[h{1}]{text}[/h{1}]');
        $bbcodeH1->setImage('/bundles/symbbcorebbcode/images/text_heading_1.png');
        $bbcodeH1->setPosition($pos);
        $bbcodeH1->setJsFunction('BBCodeEditor.prepareHeaderBtn');
        $manager->persist($bbcodeH1);
        $pos++;

        $bbcodeColor = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeColor->setName('Color');
        $bbcodeColor->setSearchRegex('#\[color=(.+?)\]([\s\S]+?)\[\/color\]#');
        $bbcodeColor->setReplaceRegex('<span style="color:$1;">$2</span>');
        $bbcodeColor->setButtonRegex('[color={color}]{text}[/color]');
        $bbcodeColor->setImage('/bundles/symbbcorebbcode/images/color_wheel.png');
        $bbcodeColor->setPosition($pos);
        $bbcodeColor->setJsFunction('BBCodeEditor.prepareColorBtn');
        $manager->persist($bbcodeColor);
        $pos++;

        $bbcodeHr = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeHr->setName('Hr');
        $bbcodeHr->setSearchRegex('#\[hr\]#');
        $bbcodeHr->setReplaceRegex('<hr>');
        $bbcodeHr->setButtonRegex('{text}[hr]');
        $bbcodeHr->setImage('/bundles/symbbcorebbcode/images/text_horizontalrule.png');
        $bbcodeHr->setPosition($pos);
        $manager->persist($bbcodeHr);
        $pos++;

        $bbcodeLink = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeLink->setName('Link');
        $bbcodeLink->setSearchRegex('#\[link(?:=(.*)\](.*)|\]((.*)))\[\/link]#iUs');
        $bbcodeLink->setReplaceRegex('<a href="$1$3">$2$4</a>');
        $bbcodeLink->setButtonRegex('[link]{text}[/link]');
        $bbcodeLink->setImage('/bundles/symbbcorebbcode/images/link_add.png');
        $bbcodeLink->setPosition($pos);
        $bbcodeLink->setRemoveNewLines(true);
        $manager->persist($bbcodeLink);
        $pos++;

        $bbcodeImage = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeImage->setName('Image');
        $bbcodeImage->setSearchRegex('#\[img\](.+)\[\/img\]#iUs');
        $bbcodeImage->setReplaceRegex('<a href="$1" target="_blank" class="symbb_post_embeded_image_link"><img src="$1"></img></a>');
        $bbcodeImage->setButtonRegex('[img]{text}[/img]');
        $bbcodeImage->setImage('/bundles/symbbcorebbcode/images/image_add.png');
        $bbcodeImage->setPosition($pos);
        $bbcodeImage->setRemoveNewLines(true);
        $manager->persist($bbcodeImage);
        $pos++;

        $bbcodeQuote = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeQuote->setName('Quote');
        $bbcodeQuote->setSearchRegex('#\[quote(?:=(.+)\](.+)|\](([\s\S]*?)))\[\/quote]#iUs');
        $bbcodeQuote->setReplaceRegex('<blockquote cite="$1" ><p>$2$4</p><footer><cite>$1</cite></footer></blockquote>');
        $bbcodeQuote->setButtonRegex('[quote]{text}[/quote]');
        $bbcodeQuote->setImage('/bundles/symbbcorebbcode/images/comment.png');
        $bbcodeQuote->setPosition($pos);
        $manager->persist($bbcodeQuote);
        $pos++;


        $bbcodeCode = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeCode->setName('Code');
        $bbcodeCode->setSearchRegex('#\[code\](.+)\[\/code\]#');
        $bbcodeCode->setReplaceRegex('<pre class="prettyprint linenums lang-html" >$1</pre><script>prettyPrint()</script>');
        $bbcodeCode->setButtonRegex('[code]{text}[/code]');
        $bbcodeCode->setImage('/bundles/symbbcorebbcode/images/page_white_code.png');
        $bbcodeCode->setPosition($pos);
        $manager->persist($bbcodeCode);
        $pos++;


        $bbcodeList = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
        $bbcodeList->setName('List');
        $bbcodeList->setSearchRegex('#\[list\]([\s\S]+)\[\/list\]#iUs');
        $bbcodeList->setReplaceRegex('<ul>$1</ul>');
        $bbcodeList->setButtonRegex('[list]{text}[/list]');
        $bbcodeList->setImage('/bundles/symbbcorebbcode/images/text_list_bullets.png');
        $bbcodeList->setPosition($pos);
        $manager->persist($bbcodeList);
        $pos++;

        $bbcodeListItem = new \Symbb\Core\BBCodeBundle\Entity\BBCode();
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
        $setDefault->addCode($bbcodeColor);
        $setDefault->addCode($bbcodeCode);


        $setPm->addCode($bbcodeSize);
        $setPm->addCode($bbcodeB);
        $setPm->addCode($bbcodeU);
        $setPm->addCode($bbcodeI);
        $setPm->addCode($bbcodeH1);
        $setPm->addCode($bbcodeHr);
        $setPm->addCode($bbcodeLink);
        $setPm->addCode($bbcodeImage);
        $setPm->addCode($bbcodeQuote);
        $setPm->addCode($bbcodeList);
        $setPm->addCode($bbcodeListItem);
        $setPm->addCode($bbcodeColor);

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
        $manager->persist($setPm);

        $manager->flush();

    }
}