<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Block\Adminhtml\System\Config\Form\Fieldset;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\View\Helper\Js as JsHelper;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Fieldset as AbstractFieldset;

/**
 * Config form fieldset renderer
 */
class Carriers extends AbstractFieldset
{
    /**
     * @var SecureHtmlRenderer
     */
    private $secureRenderer;

    /**
     * @param Context $context
     * @param Session $authSession
     * @param JsHelper $jsHelper
     * @param SecureHtmlRenderer $secureRenderer
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        JsHelper $jsHelper,
        SecureHtmlRenderer $secureRenderer,
        array $data = []
    ) {
        $this->secureRenderer = $secureRenderer;

        parent::__construct(
            $context,
            $authSession,
            $jsHelper,
            $data,
            $secureRenderer
        );
    }

    /**
     * Retrieve header title part of html for fieldset
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        $html = '<div class="config-heading"><div class="button-container">';
        $html .= $this->getButtonHtml($element);
        $html .= $this->getEventListenerHtml($element);
        $html .= '</div><div class="heading">';
        $html .= '<strong>' . $element->getData('legend') . '</strong>';

        if ($element->getData('comment')) {
            $html .= '<span class="heading-intro">' . $element->getData('comment') . '</span>';
            $element->unsetData('comment');
        }

        return $html . '</div></div>';
    }

    /**
     * Retrieve button html
     *
     * @param AbstractElement $element
     * @return string
     */
    private function getButtonHtml($element)
    {
        return '<button type="button" class="button action-configure" ' .
            'id="' . $element->getHtmlId() . '-head">' .
            '<span class="state-closed">' . __('Configure') . '</span>' .
            '<span class="state-opened">' . __('Close') . '</span>' .
            '</button>';
    }

    /**
     * Retrieve event listener js
     *
     * @param AbstractElement $element
     * @return string
     */
    private function getEventListenerHtml($element)
    {
        $htmlId = $element->getHtmlId();
        return /* @noEscape */ $this->secureRenderer->renderEventListenerAsTag(
            'onclick',
            'event.preventDefault();' .
            "Fieldset.toggleCollapse('" . $htmlId . "', '" .
                $this->getUrl('*/*/state') . "'); return false;",
            'button#' . $htmlId . '-head'
        );
    }

    /**
     * Collapsed or expanded fieldset when page loaded?
     *
     * @param AbstractElement $element
     * @return bool
     */
    protected function _isCollapseState($element)
    {
        $element->setData('expanded', false);

        return parent::_isCollapseState($element);
    }
}
