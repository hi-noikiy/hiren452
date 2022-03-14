<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AmpEmail\Model\Email\Old\Zend\Transport;

class Smtp extends \Zend_Mail_Transport_Smtp
{
    /**
     * @throws \Zend_Mail_Transport_Exception
     */
    protected function _buildBody() //@codingStandardsIgnoreLine
    {
        /** @var \Plumrocket\AmpEmail\Model\Email\Old\AmpMessage $mail */
        $mail = $this->_mail;

        /** @var \Zend_Mime_Part $ampHtml */
        if ($ampHtml = $mail->getBodyAmp()) {
            $text = $mail->getBodyText();
            $html = $mail->getBodyHtml();
            if (! $text && ! $html) {
                /**
                 * @see \Zend_Mail_Transport_Exception
                 */
                throw new \Zend_Mail_Transport_Exception('No body specified');
            }

            if (($ampHtml && $html) || ($ampHtml && $text)) {
                // Generate unique boundary for multipart/alternative
                $mime = new \Zend_Mime(null); //@codingStandardsIgnoreLine
                $boundaryLine = $mime->boundaryLine($this->EOL);
                $boundaryEnd  = $mime->mimeEnd($this->EOL);

                if ($text) {
                    $text->disposition = false;
                }
                if ($html) {
                    $html->disposition = false;
                }

                $body = $boundaryLine;
                if ($text) {
                    $body .= $text->getHeaders($this->EOL)
                        . $this->EOL
                        . $text->getContent($this->EOL)
                        . $this->EOL
                        . $boundaryLine;
                }

                $body .= $ampHtml->getHeaders($this->EOL)
                    . $this->EOL
                    . $ampHtml->getContent($this->EOL)
                    . $this->EOL;

                if ($html) {
                    $body .= $boundaryLine
                        . $html->getHeaders($this->EOL)
                        . $this->EOL
                        . $html->getContent($this->EOL)
                        . $this->EOL;
                }

                $body .= $boundaryEnd;

                $mp           = new \Zend_Mime_Part($body); //@codingStandardsIgnoreLine
                $mp->type     = \Zend_Mime::MULTIPART_ALTERNATIVE;
                $mp->boundary = $mime->boundary();

                $this->_isMultipart = true;

                $this->_parts = [$ampHtml, $html];
                $mail->setType(\Zend_Mime::MULTIPART_ALTERNATIVE);

                $this->_headers = $mail->getHeaders();

                return;
            }
        }

        parent::_buildBody();
    }
}
