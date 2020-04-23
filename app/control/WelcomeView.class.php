<?php

use Adianti\Widget\Template\THtmlRenderer;

/**
 * WelcomeView
 *
 * @version    1.0
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class WelcomeView extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();

        $html1 = new THtmlRenderer('app/resources/system_welcome_pt.html');

        // replace the main section variables
        $html1->enableSection('main', array());

        $panel1 = new TPanelGroup('Bem-vindo!');
        $panel1->add($html1);

        $vbox = TVBox::pack($panel1);
        $vbox->style = 'display:block; width: 100%';

        // add the template to the page
        parent::add($vbox);
    }
}
