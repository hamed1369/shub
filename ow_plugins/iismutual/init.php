<?php

/**
 * iismutual
 */

OW::getRouter()->addRoute(new OW_Route('iismutual.admin', 'iismutual/admin', 'IISMUTUAL_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('iismutual.mutual.firends', 'iismutual/mutuals/:userId', 'IISMUTUAL_CTRL_Mutuals', 'index'));