<?php
/**
 * @package        mod_qlmodal
 * @copyright    Copyright (C) 2012 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

//no direct access
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die ('Restricted Access');

jimport('joomla.plugin.plugin');

class plgContentQlmodal extends JPlugin
{

    protected string $str_call_start = 'qlmodal';
    protected string $str_call_end = '/qlmodal';
    protected array $attributes_inline = ['id', 'class', 'style', 'type', 'text', 'title', 'layout', 'content'];
    protected array $attributes = [];
    protected string $layout = 'default';
    protected $articleParams = null;
    const TYPE_BUTTON = 'Button';
    const TYPE_CONTENT = 'Content';

    /**
     * constructor
     *setting language
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
        $this->layout = $this->params->get('layout', 'default');
    }

    /**
     * onContentPrepare :: some kind of controller of plugin
     */
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if ($context == 'com_finder.indexer') return true;
        if (strpos($article->text, $this->str_call_start) === false or strpos($article->text, $this->str_call_end) === false) return true;
        JFactory::getDocument()->addStyleSheet(JUri::base() . '/plugins/content/' . $this->_name . '/css/' . $this->_name . '.css');
        if (1 == $this->params->get('jQuery', 0)) JHtml::_('jquery.framework');
        if (1 == $this->params->get('bootstrap', 0)) JHtml::_('jquery.bootstrap');
        JFactory::getDocument()->addScript(JUri::base() . '/plugins/content/' . $this->_name . '/js/' . $this->_name . '.js');
        if (1 == $this->params->get('useStyles', 1)) $this->addStyles();
        $this->articleParams = $params;
        $this->article = $article;
        $article->text = $this->clearTags($article->text);
        $article->text = $this->replaceStartTags($article->text);
        $article->text = $this->replaceEndTags($article->text);
    }

    /*
     * method to get attributes
     */
    function replaceStartTags($str)
    {
        $matches = $this->getMatches($str, $this->str_call_start);
        $this->attributes = $matches;
        if (count($this->attributes) <= 0) {
            return $str;
        }

        foreach ($this->attributes as $k => $v) {
            if (4 > count($v)) {
                continue;
            }
            $this->getAttributes($v[1], $k);
            $this->attributes[$k]['content'] = $v[3];
            $str = str_replace($v[0], $this->getButton($this->attributes[$k]), $str);
            $str .= $this->getContent($this->attributes[$k]);
        }
        return $str;
    }

    /*
     * method to get attributes
     */
    function replaceEndTags($str)
    {
        return str_replace('{/qlmodal}', '</div>', $str);
    }

    /*
     * method to get attributes
     */
    function fillEmpty($array)
    {
        return $array;
    }

    /*
     * method to get attributes
     */
    function getAttributes($match, $key)
    {
        $this->attributes[$key] = array_fill_keys($this->attributes_inline, '');
        /*default values*/
        $this->attributes[$key]['toggle'] = 'modal';
        $this->attributes[$key]['class'] = 'modal fade';
        $this->attributes[$key]['aria'] = '';
        $this->attributes[$key]['id'] = 'uniqueCustomId';

        /*custom values*/
        $regex = '/(?<=\s)([a-z]*)?=?"([^"]*)(?=")/';
        preg_match_all($regex, $match, $matches, PREG_SET_ORDER);
        if (0 < count($matches)) foreach ($matches as $k => $v) {
            if (isset($v[1]) and isset($v[2])) $this->attributes[$key][$v[1]] = trim($v[2]);
            $this->attributes[$key]['content'] = $this->attributes[$key][3];
            if (!isset($this->attributes[$key]['layout'])) $this->attributes[$key]['layout'] = $this->layout;
        }
        if ('collapse' == $this->attributes[$key]['toggle']) {
            if ('modal fade' == $this->attributes[$key]['class']) $this->attributes[$key]['class'] = ' collapse';
            else $this->attributes[$key]['class'] .= ' collapse';
            $this->attributes[$key]['aria'] .= 'aria-expanded="false" aria-controls="' . $this->attributes[$key]['id'] . '"';
        }

        if ('button' == $this->attributes[$key]['type']) {
            $this->attributes[$key]['class'] = '';
            $this->attributes[$this->attributes[$key]['id']]['toggle'] = $this->attributes[$key]['toggle'];
            $this->attributes[$this->attributes[$key]['id']]['aria'] = $this->attributes[$key]['aria'];
        }
        if ('content' == $this->attributes[$key]['type'] and isset($this->attributes[$this->attributes[$key]['id']])) {
            $this->attributes[$key]['class'] = $this->attributes[$key]['toggle'] = $this->attributes[$this->attributes[$key]['id']]['toggle'];
            $this->attributes[$key]['aria'] = $this->attributes[$this->attributes[$key]['id']]['aria'];
        }
        if (false !== strpos($this->attributes[$key]['class'], 'modal')) $this->attributes[$key]['class'] = 'modal fade';

        //echo '<pre>';print_r($this->attributes);die;
        return $this->attributes;
    }

    /**
     * method to clear tags
     */
    function clearTags($str)
    {
        $str = str_replace('<p>{/qlmodal}', '{/qlmodal}', $str);
        $str = str_replace('{/qlmodal}</p>', '{/qlmodal}', $str);
        $str = str_replace('<p>{qlmodal', '{qlmodal', $str);
        $regex = '!{qlmodal\s(.*?)}</p>!';
        preg_match_all($regex, $str, $matches, PREG_SET_ORDER);
        if (0 < count($matches)) foreach ($matches as $k => $v) $str = preg_replace('?' . $v[0] . '?', '{qlmodal ' . $v[1] . '}', $str);
        return $str;
    }

    /**
     * method to get matches according to search string
     * @param $text string haystack
     * @param $searchString string needle, string to be searched
     */
    public function getMatches($text, $searchString)
    {
        $searchString = preg_replace('!{\?}!', ' ', $searchString);
        $searchString = preg_replace('?/?', '\/', $searchString);
        $regex = '~({' . $searchString . '+(.*?)})(.*?){' . $this->str_call_end . '}~is';
        preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
        return $matches;
    }

    function getButton($data): string
    {
        return $this->getHtml($data, self::TYPE_BUTTON);
    }

    function getContent($data): string
    {
        return $this->getHtml($data, self::TYPE_CONTENT);
    }

    /*
    * method to get html source code of gallery
    */
    function getHtml($data, $TYPE = '')
    {
        //echo '<pre>';print_r($data);die;
        $plgParams = $this->params;
        $articleParams = $this->articleParams;
        $article = $this->article;
        $attributes = $this->attributes;
        if (isset($data['layout'])) $this->layout = $data['layout'];
        ob_start();
        extract($data);
        $layoutpath = $this->getLayoutPath($this->layout, $TYPE);
        include $layoutpath;
        $strHtml = ob_get_contents();
        ob_end_clean();
        return $strHtml;
    }

    function getLayoutPath($layout, $type = '')
    {
        $tplName = JFactory::getApplication()->getTemplate('template')->template;
        $path = JPATH_BASE . '/templates/' . $tplName . '/html/plg_content_' . $this->_name;
        $pathOverride = $path . '/default' . ucwords($type) . '.php';
        $pathAlternative = $path . '/' . $layout . ucwords($type) . '.php';
        if (file_exists($pathAlternative)) $path = $pathAlternative;
        elseif (file_exists($pathOverride)) $path = $pathOverride;
        else $path = JPATH_BASE . '/plugins/content/' . $this->_name . '/html/default' . ucwords($type) . '.php';
        return $path;
    }

    function addStyles()
    {
        $styles = array();
        $styles[] = '.qlmodal .modal-header,.qlmodal .modal-body,.qlmodal .modal-footer{background:' . $this->params->get('background', '#fff') . ';}';
        $styles[] = '.qlmodal .modal-header *,.qlmodal .modal-body *,.qlmodal .modal-footer *{color:' . $this->params->get('color', '#666') . ';}';
        $styles[] = '.qlmodal .modal-header h1,.qlmodal .modal-header h2,.qlmodal .modal-header h3,.qlmodal .modal-header h4,.qlmodal .modal-header h5,';
        $styles[] = '.qlmodal .modal-body h1,.qlmodal .modal-body h2,.qlmodal .modal-body h3,.qlmodal .modal-body h4,.qlmodal .modal-body h5 {color:' . $this->params->get('hColor', '#666') . ';}';
        $styles[] = '.qlmodal .modal-header a,.qlmodal .modal-body a,.qlmodal .modal-footer a{color:' . $this->params->get('aColor', '#88f') . ';}';
        JFactory::getDocument()->addStyleDeclaration(implode("\n", $styles));
    }
}