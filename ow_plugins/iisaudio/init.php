<?php

/**
 * @author Milad Heshmati <milad.heshmati@gmail.com>
 * @package ow_plugins.iisaudio
 * @since 1.0
 */

OW::getRouter()->addRoute(new OW_Route('iisaudio.add_audio', 'audio/add', 'IISAUDIO_CTRL_Audio', 'addAudio'));
OW::getRouter()->addRoute(new OW_Route('iisaudio-audio', 'audio', 'IISAUDIO_CTRL_Audio', 'viewList'));
OW::getRouter()->addRoute(new OW_Route('iisaudio-audio-delete-item', 'audio/delete/:id', 'IISAUDIO_CTRL_Audio', 'deleteItem'));
IISAUDIO_CLASS_EventHandler::getInstance()->init();
