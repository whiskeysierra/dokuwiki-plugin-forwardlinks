<?php
/**
 * DokuWiki Plugin forwardlinks (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Willi Schönborn <w.schoenborn@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_forwardlinks extends DokuWiki_Syntax_Plugin {

    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'block';
    }

    public function getSort() {
        return 304;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{forwardlinks>.+?\}\}', $mode, 'plugin_forwardlinks');
    }

    public function handle($match, $state, $pos, Doku_Handler &$handler) {
        // Take the id of the source
        // It can be a rendering of a sidebar
        global $INFO;
        global $ID;
        $id = $ID;
        // If it's a sidebar, get the original id.
        if ($INFO != null) {
            $id = $INFO['id'];
        }

        $match = substr($match, 15, -2); //strip {{forwardlinks> from start and }} from end
        $match = ($match == '.') ? $id : $match;

        if (strstr($match, ".:")) {
            resolve_pageid(getNS($id), $match, $exists);
        }
        return array($match);
    }

    public function render($mode, Doku_Renderer &$renderer, $data) {
        if ($mode != 'xhtml') {
            return false;
        }

        $pages = p_get_metadata($data[0], 'relation references');

        $renderer->doc .= '<div id="plugin__forwardlinks">' . DW_LF;

        if (empty($pages)) {
            global $lang;
            $renderer->doc .= "<strong>Plugin Forwardlinks: " . $lang['nothingfound'] . "</strong>";
        } else {
            $renderer->doc .= '<ul class="idx">';
            foreach ($pages as $page => $exists) {
                $name = p_get_metadata($page, 'title');

                if (empty($name)) {
                    $name = $page;
                }

                $renderer->doc .= '<li><div class="li">';
                $renderer->doc .= html_wikilink(':' . $page, $name, '');
                $renderer->doc .= '</div></li>';
            }
            $renderer->doc .= '</ul>';
        }

        $renderer->doc .= '</div>' . DW_LF;

        return true;
    }

}

// vim:ts=4:sw=4:et:
